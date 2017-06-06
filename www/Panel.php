<?php
    require "Header.php";
    require "FileDB.php";
    ensure_https();
    
    session_start();
?>
<head>
    <link rel="stylesheet" type="text/css" href="Main.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <title>Management Panel</title>
</head>
<?php
    function show_login()
    {
        require "Login.php";
        exit(0);
    }
    
    function is_logged_in()
    {
        if(!isset($_SESSION["user_info"]))
            return false;
        if($_SESSION["user_info"] === false)
            return false;
        return true;
    }
    
    $user_info = [];
    if(isset($_REQUEST["key"]))
    {
        unset($_SESSION["user_info"]);
    }
    if(!is_logged_in())
    {
        // Check login POST
        if(isset($_REQUEST["key"]))
        {
            $user_info = get_user($_REQUEST["key"]);
            if(count($user_info) != 0)
            {
                $_SESSION["user_info"] = $user_info;
            }
            // Redirect to remove key from url
            header("Location: Panel.php");
            exit(0);
        }
        else
        {
            show_login();
        }
    }
    else
    {
        // fill user info array
        $user_info = $_SESSION["user_info"];
        
        // Handle actions with the a parameter in the request uri
        if(isset($_REQUEST["a"]))
        {
            // Logout action
            if($_REQUEST["a"] === "logout")
            {
                // Clear session user
                unset($_SESSION["user_info"]);
                show_login();
            }
        }
    }
    
    function generate_image_list()
    {
        // Errors are really bad in this scenario since they get injected into the generated JS code
        //error_reporting(0);
        ini_set('display_errors', 'Off');

        global $user_info;
        global $data_dir;
        $con = open_db();
        
        $query = "SELECT id,ofn,extension,date FROM tdrz_files WHERE owner=? ORDER BY date DESC";
        $stmt = $con->prepare($query);
        $owner_id = intval($user_info["id"]);
        $stmt->bind_param("i", $owner_id);
        $stmt->bind_result($id, $ofn, $ext, $date);
        if(!$stmt->execute())
            echo $stmt->error;
        
        $uri_base = $_SERVER["HTTP_HOST"];
        
        echo "\n";
        while($stmt->fetch())
        {
            $file = $data_dir."/".$id.".".$ext;
            $data = [];
            $data["url"] = "http://$uri_base/$id";
            $data["id"] = $id;
            $data["ofn"] = $ofn;
            $data["ext"] = $ext;
            $data["date"] = $date;
            $data["thumb"] = get_thumbnail_url($file);
            echo "addImage(";
            echo json_encode($data);
            echo ");\n";
        }
    }
?>

<body>
<div class="main" id="panel">
    <div id="bg"></div>
    <div id="inner">
        <div id="top">
            <div class="progress_bar">
                <div class="progress_bar_inner">
                </div>
            </div>
            <a href="#" class="upload" onclick="popupUpload()" style="flex-basis: 400px">upload <i class="fa fa-upload"></i></a>
            <!--<a href="#" class="search">search <i class="fa fa-search"></i></a>-->
            <?php 
                if(check_flags(1))
                { 
                    echo "<a href=\"#\" class=\"extra\" id=\"open_extra\">
                    extra <i class=\"fa fa-cog\"></i></a>";
                }
            ?>
            <a href="?a=logout">log out <i class="fa fa-sign-out"></i></a>
        </div>
        <div class="img_list">
        </div>
    </div>
</div>
<div class="dropdown" id="img_dropdown">
    <a class="open">open <i class="fa fa-share-square-o"></i></a>
    <a class="link">link <i class="fa fa-retweet"></i></i></a>
    <a class="remove">
        <div class="remove_a">remove <i class="fa fa-times"></i></div>
        <div class="remove_b">remove? <i class="fa fa-times"></i></div>
    </a>
</div>
<div class="dropdown" id="extra_dropdown">
    <a href="#" onclick="fixDatabase()">fix database <i class="fa fa-bug"></i></a>
    <a href="#" onclick="clearThumbs()">clear thumbnail cache <i class="fa fa-thumbs-down"></i></a>
</div>
<div class="img_tooltip">
test tooltip
</div>
</body>

<script>
    function copyTextToClipboard(text) 
    {
        var textArea = document.createElement("textarea");
        
        // Place in top-left corner of screen regardless of scroll position.
        textArea.style.position = 'fixed';
        textArea.style.top = 0;
        textArea.style.left = 0;

        // Ensure it has a small width and height. Setting to 1px / 1em
        // doesn't work as this gives a negative w/h on some browsers.
        textArea.style.width = '2em';
        textArea.style.height = '2em';

        // We don't need padding, reducing the size if it does flash render.
        textArea.style.padding = 0;

        // Clean up any borders.
        textArea.style.border = 'none';
        textArea.style.outline = 'none';
        textArea.style.boxShadow = 'none';

        // Avoid flash of white box if rendered for any reason.
        textArea.style.background = 'transparent';

        textArea.value = text;

        document.body.appendChild(textArea);

        textArea.select();

        try 
        {
            var successful = document.execCommand('copy');
            var msg = successful ? 'successful' : 'unsuccessful';
            console.log('Copying text command was ' + msg);
        }
        catch (err) 
        {
            console.log('Oops, unable to copy');
        }

        document.body.removeChild(textArea);
    }
    
    var imgDropdown;
    var extraDropdown;
    var tooltip;
    var removeButtonState = 0;
    var uploadButton = 0;
    var uploadInProgress = false;
    function setRemoveButton(btn, state)
    {
        var a = btn.getElementsByClassName("remove_a")[0].style;
        var b = btn.getElementsByClassName("remove_b")[0].style;
        if(state == 0)
        {
            a.display = "block";
            b.display = "none";
        }
        else
        {
            a.display = "none";
            b.display = "block";
        }
        removeButtonState = state;
    }
    function addImage(imgData, location)
    {
        if(imgData.duplicate)
            return;
        
        var item = document.createElement("div");
        item.className = "img_item";
        
        var content = "<div class=\"overlay\" style=\"top:0;\">" + imgData.ext + "</div>" +
            "<div class=\"overlay\" style=\"bottom:0;width:100%;\">" + imgData.ofn + "</div>" +
            "<div data-id=\"" + imgData.id + "\" data-url=\"" + imgData.url + "\" class=\"clicker\"></div>";
            
        if(imgData.thumb && imgData.thumb.length > 0)
        {
            content += "<img src=\"" + imgData.thumb + "\"></img>";
        }
        else
        {
            content += "<div class=\"no_thumb\"></div>";
        }
        item.innerHTML = content;
        
        // Setup the imgDropdown menu for the item
        var clicker = item.getElementsByClassName("clicker")[0];
        clicker.onclick = function(e)
        {
            console.log(this.dataset.id);
            
            // Show imgDropdown menu at location
            imgDropdown.style.left = e.pageX - 2;
            imgDropdown.style.top = e.pageY - 2;
            imgDropdown.style.visibility = "visible";
            
            var openButton = imgDropdown.getElementsByClassName("open")[0];
            var linkButton = imgDropdown.getElementsByClassName("link")[0];
            var removeButton = imgDropdown.getElementsByClassName("remove")[0];
            
            // Reset button state
            setRemoveButton(removeButton, 0);
            
            var _clicker = this; // clicker closure
            // Bind button actions
            linkButton.onclick = function()
            {
                copyTextToClipboard(_clicker.dataset.url);
                // Hide imgDropdown
                imgDropdown.style.visibility = "hidden";
            } 
            openButton.onclick = function()
            {
                window.open(_clicker.dataset.url);
                // Hide imgDropdown
                imgDropdown.style.visibility = "hidden";
            }
            removeButton.onclick = function(e)
            {
                var remove = _clicker.getElementsByClassName("remove")[0];
                if(removeButtonState == 0)
                {
                    // Show warning
                    setRemoveButton(this, 1);
                }
                else
                {
                    // Actually remove
                    removeImage(_clicker);
                    
                    // Hide imgDropdown
                    imgDropdown.style.visibility = "hidden";
                }
            };
        };
        
        clicker.onmouseenter = function(e)
        {
            var nameOverlay = this.parentNode.getElementsByClassName("overlay")[1];
            tooltip.textContent = nameOverlay.textContent;
        }
        clicker.onmousemove = function(e)
        {
            if(imgDropdown.style.visibility == "hidden")
            {
                tooltip.style.left = e.pageX + 25;
                tooltip.style.top = e.pageY + 25;
                tooltip.style.visibility = "visible";
            }
        }
        clicker.onmouseout = function()
        {
            tooltip.style.visibility = "hidden";
        }
        
        var container = document.getElementsByClassName("img_list")[0];
        if(!location)
            location = 0;
        if(location < 0 && container.firstChild)
            container.insertBefore(item, container.firstChild);
        else
            container.appendChild(item);
    }
    function removeImage(clicker)
    {
        console.log("Removing file with id", clicker.dataset.id);
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "API.php");
        xhr.addEventListener("readystatechange", function () 
        {
            if (this.readyState === 4) 
            {
                console.log(this.responseText);
                if(this.status == 200)
                {
                    var n = clicker;
                    while(n != document)
                    {
                        if(n.matches(".img_item"))
                        {
                            n.parentNode.removeChild(n);
                            break;
                        }
                        n = n.parentNode;
                    }
                    console.log("Removed item " + clicker.dataset.id);
                }
            }
        });
        
        var data = new FormData();
        data.append("a", "remove");
        data.append("id", clicker.dataset.id);
        xhr.send(data);
    }
    function updateUploadProgress(inProgress, count, length)
    {
        var progressBar = document.getElementsByClassName("progress_bar")[0];
        var uploadButton = document.getElementsByClassName("upload")[0];
        if(inProgress)
        {
            if(!uploadInProgress)
            {
                uploadInProgress = true;
                progressBar.style.display = "block";
                uploadButton.style.display = "none";
            }
            var barInner = progressBar.getElementsByClassName("progress_bar_inner")[0];
            var rate = count / length;
            barInner.style.width = (rate*100) + "%";
        }
        else
        {
            if(uploadInProgress)
            {
                uploadInProgress = false;
                progressBar.style.display = "none";
                uploadButton.style.display = "block";
            }
        }
    }
    function uploadImage(file)
    {
        if(uploadInProgress)
            return false;
        
        console.log("Uploading new file", file);
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "API.php");
        xhr.addEventListener("readystatechange", function () 
        {
            if (this.readyState === 4) 
            {
                var result = JSON.parse(this.responseText);
                console.log(result);
                if(result.status != null && result.status == 0)
                {
                    addImage(result, -1);
                }
                else
                {
                    showStatusMessage("Failed to upload image [" + result.status + "]: " + result.error);
                }
                updateUploadProgress(false);
            }
        });
        xhr.upload.onprogress = function (e) 
        {
            updateUploadProgress(true, e.loaded, e.total);
        };
        
        var data = new FormData();
        data.append("a", "upload");
        data.append("file", file);
        xhr.send(data);
        updateUploadProgress(true, 0,1);
        
        return true;
    }
    function popupUpload()
    {
        var input = document.createElement("input");
        input.type = "file";
        input.click();
        
        input.onchange = function()
        {
            if(input.files.length > 0)
            {
                uploadImage(input.files[0]);
            }
        };
    }
    function popupExtra(e)
    {
        // Show imgDropdown menu at location
        extraDropdown.style.left = e.pageX - 2;
        extraDropdown.style.top = e.pageY - 2;
        extraDropdown.style.visibility = "visible";
    }
    function showStatusMessage(inner)
    {
        var status = document.createElement("div");
        status.className = "status";
        var txtNode = document.createElement("center");
        txtNode.innerHTML = inner;
        status.appendChild(txtNode);
        document.body.appendChild(status);
        
        window.setTimeout(function(){status.parentNode.removeChild(status);}, 3000);
        status.onclick = function()
        {
            status.parentNode.removeChild(status);
        };
    }
    function fixDatabase()
    {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "API.php");
        xhr.addEventListener("readystatechange", function () 
        {
            if (this.readyState === 4) 
            {
                var result = JSON.parse(this.responseText);
                console.log(result);
                if(result.status != null && result.status == 0)
                {
                    showStatusMessage("Fixed " + result.count + " database entries");
                }
                else
                {
                    showStatusMessage("Failed to fix database [" + result.status + "]: " + result.error);
                }
            }
        });
        
        var data = new FormData();
        data.append("a", "update");
        xhr.send(data);
    }
    function clearThumbs()
    {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "API.php");
        xhr.addEventListener("readystatechange", function () 
        {
            if (this.readyState === 4) 
            {
                var result = JSON.parse(this.responseText);
                console.log(result);
                if(result.status != null && result.status == 0)
                {
                    showStatusMessage("Removed " + result.count + " thumbnails");
                }
                else
                {
                    showStatusMessage("Failed to clear thumbnail cache [" + result.status + "]: " + result.error);
                }
            }
        });
        
        var data = new FormData();
        data.append("a", "clear_thumbs");
        xhr.send(data);
    }
    
    window.onload = function()
    {
        // Setup drop zone for uploader
        var dropzone = document.getElementsByClassName('upload')[0];

        // Optional. Show the copy icon when dragging over.  Seems to only work for chrome.
        dropzone.addEventListener('dragover', function(e) 
        {
            e.stopPropagation();
            e.preventDefault();
            e.dataTransfer.dropEffect = "copy";
        });

        // Get file data on drop
        dropzone.addEventListener("drop", function(e)
        {
            e.stopPropagation();
            e.preventDefault();
            var files = e.dataTransfer.files; // Array of all files
            if(files.length > 0)
            {
                uploadImage(files[0]);
            }
        });
        
        // Get the imgDropdown menu
        imgDropdown = document.getElementById("img_dropdown");
        imgDropdown.style.visibility = "hidden";
        
        extraDropdown = document.getElementById("extra_dropdown");
        extraDropdown.style.visibility = "hidden";
        
        <?php 
            // Admin buttons
            if(check_flags(1))
            { 
                echo "var extraButton = document.getElementById(\"open_extra\");";
                echo "extraButton.onclick = popupExtra;";
            }
        ?>
        
        // Get the tooltip
        tooltip = document.getElementsByClassName("img_tooltip")[0];
        
        // Add initial items
        <?php generate_image_list(); ?>
    };
    window.onclick = function(e) 
    {
        var n = e.target;
        
        var hideA = true;
        var hideB = true;
        while(n != document && n != null)
        {
            if(n.matches(".imgDropdown,.clicker,.remove_a"))
                hideA = false;
            if(n.matches(".extraDropdown,#open_extra"))
                hideB = false;
            n = n.parentNode;
        }
        
        // Hide drop down
        if(hideA)
            imgDropdown.style.visibility = "hidden";
        if(hideB)
            extraDropdown.style.visibility = "hidden";
    }
    window.onresize = function(event) 
    {
        imgDropdown.style.visibility = "hidden";
        extraDropdown.style.visibility = "hidden";
    }
</script>
