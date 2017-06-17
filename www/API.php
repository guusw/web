<?php
    require_once "Header.php";
    require_once "FileDB.php";
    
    // API return object
    $result = array("status" => 0);

    // Status codes:
    // 0 = ok
    // 1 = need key or invalid key
    // 2 = file database error
    // 3 = file upload error
    // 4 = unknown command
    // 5 = access denied
    const API_OK = 0;
    const API_INVALID_KEY = 1;
    const API_DB_ERROR = 2;
    const API_UPLOAD_ERROR = 3;
    const API_UNKOWN_COMMAND = 4;
    const API_ACCESS_DENIED = 5;
    
    // Exits the script returning the result array as json data
    // also returns the code parameter as result code
    function api_return($code = 0)
    {
        global $result;
        $result["status"] = $code;
        if($code != 0 && !isset($result["error"]))
        {
            if($code == 5)
            {
                $result["error"] = "Access denied";
            }
        }
        $result_json = json_encode($result);
        echo $result_json;
        header("Content-Type", "application/json");
        exit(0);
    }

    function require_key()
    {
        // Try to get user from current session first
        session_start();
        if(isset($_SESSION["user_info"]))
        {
            return $_SESSION["user_info"];
        }
        
        if(!isset($_REQUEST["key"]))
        {
            api_return(1);
        }
        
        $user_info = get_user($_REQUEST["key"]);
        if(count($user_info) == 0)
        {
            api_return(1);
        }
        
        return $user_info;
    }
    
    ensure_https();
    $user_info = require_key();
    
    // This function removes all the thumbnails from the thumbs folder
    function clear_thumbs_cache()
    {
        global $result;
        global $data_dir;
        
        if(!check_flags(UFLAG_ADMIN))
            api_return(5);
        
        $tdir = "$data_dir/thumbs/*.jpg";
        $thumbs = glob($tdir);
        $result["count"] = count($thumbs);
        foreach($thumbs as $thumb)
        {
            unlink($thumb);
            
        }
        api_return(0);
    }
    // This function allows the database to be rebuild or fixed by scanning all the files in the data folder
    // it adds non-existing files to the database and fixes any extension+md5 mismatches
    function update_database()
    {
        global $result;
        global $data_dir;
        global $user_info;
        
        if(!check_flags(UFLAG_ADMIN))
            api_return(5);
        
        // Default assigned file owner
        $default_owner = 0;

        $con = open_db();
        $query1 = "SELECT hash,extension,ofn FROM tdrz_files WHERE id=?";
        $stmt1 = $con->prepare($query1);
        $stmt1->bind_param("s", $id);
        $stmt1->bind_result($hash, $ext, $ofn);
    
        $query2 = "INSERT INTO tdrz_files(id, hash, ofn, extension, date, owner)";
        $query2 .= " VALUES(?,?,?,?,?,$default_owner)";
        $query2 .= " ON DUPLICATE KEY UPDATE hash=?, ofn=?, date=?, extension=?";
        $stmt2 = $con->prepare($query2);
        $stmt2->bind_param("sssssssss", 
                    $id, $hash, $ofn, $ext, $filetime,
                    $hash, $ofn, $filetime, $ext);
        
        $fixed_files = 0;
        $files = glob($data_dir."/*.*");
        foreach($files as $file)
        {
            $id = pathinfo($file, PATHINFO_FILENAME);
            $filetime = date("Y-m-d H:i:s", filemtime($file));
            $curr_ext = pathinfo($file, PATHINFO_EXTENSION);
            $ofn = basename($file);
            $stmt1->execute();
            $update = false;
        
            //echo "Processing file $file ($id)";
            //echo "filetime: $filetime";

            if($stmt1->fetch())
            {
                $stmt1->store_result();
                $curr_hash = md5_file($file);
                if(strcmp($hash, $curr_hash) != 0)
                {
                    //echo "Hash changed $hash -> $curr_hash";
                    $hash = $curr_hash;
                    $update = true;
                }

                if($ext != $curr_ext)
                {
                    //echo "Ext changed $ext -> $curr_ext";
                    $ext = $curr_ext;
                    $update = true;
                }
                $stmt1->free_result();
            }
            else
            {
                //echo "New file";
                $hash = md5_file($file);
                $ext = $curr_ext;
                $update = true;
            }
            
            // Check if the entry needs to be updated
            if($update)
            {
                $stmt2->execute();
                $fixed_files += 1;
            }
        }
        $result["count"] = $fixed_files;
        
        $stmt1->close();
        $stmt2->close();
        api_return(0);
    }
    function upload_file($file, $rename)
    {
        global $result;
        global $data_dir;
        global $user_info;
        $original_name = $file["name"];
        $tmp_name = $file["tmp_name"];
        
        $extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $file_type = $file["type"];
        
        if($file["error"] != 0)
        {
            api_return(4);
        }
        
        $dst_id = generate_filename($data_dir);
        if($rename)
        {
            $original_name = "$dst_id.$extension";
        }
        $dst_path = "$data_dir/$dst_id.$extension";
        
        $uri_base = "http://".$_SERVER["HTTP_HOST"]."/";
        
        $hash = md5_file($tmp_name);
        $existing = find_file_by_hash($hash, $user_info["id"]);
        // Check if file already exists
        if(isset($existing["id"]))
        {
            $dst_id = $existing["id"];
            $original_name = $existing["ofn"];
            $extension = $existing["extension"];
            // Reassign destination path so the correct thumbnail is aquired
            $dst_path = "$data_dir/$dst_id.$extension";
            // Mark as duplicate upload
            $result["duplicate"] = true;
        }
        else
        {
            // Upload a new file
            if(!register_file($dst_id, $hash, $extension, $original_name, $user_info["id"]))
                api_return(3);
            move_uploaded_file($tmp_name, $dst_path);
        }
        
        $result["url"] = $uri_base.$dst_id;
        $result["id"] = $dst_id;
        $result["ofn"] = $original_name;
        $result["ext"] = $extension;
        $result["thumb"] = get_thumbnail_url($dst_path);
        api_return(0);
    }
    function remove_file($id)
    {
        global $result;
        global $data_dir;
        global $user_info;
        if(!isset($_POST["id"]))
        {
            api_return(2);
        }
        
        $id = $_POST["id"];
        $file_info = find_file($_POST["id"]);
        if(count($file_info) == 0)
        {
            api_return(2);
        }
        $ext = $file_info["extension"];
        $file = "$data_dir/$id.$ext";
        
        // Remove the physical file
        unlink($file);
        
        // Remove it from the database as well
        if(!deregister_file($id, $user_info["id"]))
        {
            $result["error"] = "deregister_file Failed";
            api_return(2);
        }
        api_return(0);
    }
    
    // Handle action commands
    if(isset($_REQUEST["a"]))
    {
        if($_REQUEST["a"] == "update")
        {
            update_database();
        }
        else if($_REQUEST["a"] == "clear_thumbs")
        {
            clear_thumbs_cache();
        }
        
        // Handle POST actions
        if(isset($_POST["a"]))
        {
            if($_POST["a"] == "upload")
            {
                if(!isset($_FILES["file"]))
                {
                    $result["error"] = "No \"file\" parameter provided";
                    api_return(3);
                }
                $file = $_FILES["file"];
                $rename = false;
                if(isset($_POST["rename"]))
                {
                    if($_POST["rename"] == "auto")
                    {
                        $rename = true;
                    }
                }
                upload_file($file, $rename);
            }
            else if($_POST["a"] == "remove")
            {
                if(!isset($_POST["id"]))
                {
                    api_return(2);
                }
                remove_file($_POST["id"]);
            }
        }
    }
    api_return(4);
?>