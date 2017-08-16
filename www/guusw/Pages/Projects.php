<h1>Projects</h1>
<div>
    <center>
    <div id="pblock">
    <?php
        $dit = new RecursiveDirectoryIterator("Pages/Projects/", FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS);
        class page
        {
            public $path;
            public $meta;
            public $id;
            function __construct($p,$m) 
            {
                $this->path = $p;
                $this->meta = $m;
            }
        };
        $projects = array();
        foreach($dit as $fileInfo)
        {
            $p = $dit->getPath()."/".$fileInfo->getFilename();
            $meta = get_meta_tags($p);
            
            if(array_key_exists("do-not-show", $meta))
                continue;
            
            array_push($projects, new page($p, $meta));
        }
        
        uasort($projects, function($a, $b){return $a->meta['index'] > $b->meta['index'];});
        for($i = 0; $i < count($projects); $i++)
        {
            $projects[$i]->id = $i;
        }
    ?>
    
    <div style="display:table;display: inline-block; text-align: left;margin:10px;">
        <div class="project_list" style="display:table-cell;">
            <?php
                foreach($projects as $project)
                {
                    echo "<a onclick=\"show_project(".$project->id.")\" href=\"#project_".$project->id."\" class=\"project_item\" id=\"project_button_".$project->id."\">";
                    echo "<img src=\"".$project->meta['image']."\"></img>";
                    echo "<div class=\"project_bgbar\"></div>";
                    echo "<div class=\"project_title\">".$project->meta['title']."</div>";
                    echo "<div class=\"project_eye\"><i class=\"fa fa-eye\"></i></div>";
                    echo "</a>";
                }
            ?>
            <!--
            <a href="#" class="project_item">
                <img src="Images/img0.png"></img>
                <div class="project_title">Raytracer</div>
            </a>
            -->
        </div>
    </div></center> 
    
    <script>
        
    </script>
    
    <div class="project_container">
        <?php
            function detail_table_entry($project, $name, $key)
            {   
                if(!array_key_exists($key, $project->meta))
                    return;
                echo $name." <ul>";
                $items = explode(",", $project->meta[$key]);
                foreach($items as $item)
                {
                    echo "<li>".trim($item, " ")."</li>";
                }
                echo "</ul>";
            }
            foreach($projects as $project)
            {
                echo "<div class=\"project\" id=\"project_".$project->id."\">";
                echo "<div class=\"title\"><h3>".$project->meta['title']."</h3></div>";
                echo "<div class=\"indent\" style=\"margin-right: 4em;\">";
                
                if(!array_key_exists("no-details", $project->meta))
                {
                    // Generate Description table
                    echo "<div class=\"project_details\"><h4>Details</h4><div class =\"indent\"><table>";
                    echo "<tr><td>";
                    detail_table_entry($project, "Languages", "lang");
                    detail_table_entry($project, "Platforms", "platform");
                    echo "</td><td>";
                    detail_table_entry($project, "Project Duration", "duration");
                    detail_table_entry($project, "Tools Used", "tools");
                    echo "</td></tr>";
                    echo "</table></div></div>";
                }
                
                echo "<h4>Description</h4>";
                echo "<div class =\"indent\">";
                // Insert content
                require $project->path;
                echo "</div>";
                echo "</div>";
                echo "</div>\n";
            }
        ?>
        <script>
            var items = document.getElementsByClassName("project_item");
            var selectedProject;
        
            function validate_project_id(id)
            {
                return id >= 0 && id < items.length;
            }
            function set_eye(base, selected)
            {
                var eye = base.getElementsByClassName("project_eye")[0];
                if(selected)
                {
                    eye.style.color = "#00ff00";
                    eye.style.opacity = "1.0";
                    selectedProject = base;
                }
                else
                {
                    eye.style.color = "#ffffff";
                    eye.style.opacity = "0.5";
                }
            }
            for(var i = 0; i < items.length; i++)
            {
                items[i].addEventListener("click", function()
                {
                    if(selectedProject)
                    {
                        set_eye(selectedProject, false);
                    }
                    set_eye(this, true);
                }); 
                items[i].onmouseover = function()
                {
                    var img = this.getElementsByTagName("img")[0];
                    var blur_amt = 5;
                    img.style.webkitFilter = "blur(" + blur_amt + "px) grayscale(50%)";
                    img.style.filter = "blur(" + blur_amt + "px) grayscale(50%)";
                };
                items[i].onmouseleave = function()
                {
                    var img = this.getElementsByTagName("img")[0];
                    img.style.webkitFilter = "none";
                    img.style.filter = "none";
                };
            }
        
            var container = $(".project_container");
            var active_project_view;
            var projects = [];
            container.hide();
            $(document).ready(function(){
                <?php
                    echo "\n";
                    foreach($projects as $project)
                    {
                        $varname = "p_".$project->id;
                        echo "var p_".$project->id." = $(\"#project_".$project->id."\");\n";
                        echo "projects[".$project->id."] = ".$varname.";";
                        echo $varname.".hide();\n";
                    }
                ?>
                
                if(window.location.hash.length > 0)
                {
                    var re = new RegExp("#project_([0-9+])");
                    var m = window.location.hash.match(re);
                    if(m.length > 0)
                    {
                        var id = m[1];
                        if(!validate_project_id(id))
                            return;
                        var proj = $(window.location.hash);
                        var eye_button = $("#project_button_" + id)[0];
                        set_eye(eye_button, true);
                        show_project(id);
                    }
                    
                }
            });
            function show_project_p(p)
            {
                if(active_project_view && active_project_view.get(0) == p.get(0))
                    return;
                container.show();
                var speed = 200;
                var v = function()
                { 
                    //$("body").animate({ scrollTop: $("#pblock").offset().top }, 100); 
                    $('html, body').animate({
                        scrollTop: $(".project_container").offset().top
                    }, 200);
                };
                if(active_project_view)
                {
                    active_project_view.slideUp(speed, function(){  
                        p.slideDown(speed, v);
                    });
                    
                    var videos = active_project_view.find("video");
                    videos.each(function(){
                        $(this)[0].pause();
                    });
                }
                else
                {
                    p.slideDown(speed, v);
                }
                active_project_view = p;
            }
            function show_project(i)
            {
                show_project_p(projects[i]);
            }
            function show_project1(i)
            {
                set_eye(selectedProject,false);
                show_project(i);
                set_eye($("#project_button_" + i)[0],true);
            }
        </script>
        <!--
        <div class="project">
            <div class="title"><h3>Raytracer</h3></div>
            <div class="indent">
                <p>Raytracer text goes here.</br>blabalbalb</p>
                <p>Another paragraph.</p>
            </div>
        </div>
        -->
    </div>
    </div>
</div>
