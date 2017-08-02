<link rel="stylesheet" type="text/css" href="Style/Gallery.css">
<script>
    var gallery_data = [];
    function create_gallery(id)
    {
        var autoInterval;
        var autoIntervalDuration = 5000;
        var fadeLength = 500;
        var delayLength = 0;
        var current_image = 0;
        var frame = $("#image_frame_" + id);
        var left = frame.children('.left');
        var right = frame.children('.right');
        var frame_items = frame.children('ul').children('li');
        var indicator = frame.children('.count');
        var playButton = frame.children('.middle');
        
        // Fills the gallery item list
        gallery_data[id] = [];
        for(var i = 0;;i++)
        {
            var c = frame_items.slice(i, i+1);
            if(c.length == 0)
                break;
            var obj = { node: c, index: i };
            if(obj.node.children('video').length != 0)
            {
                obj.video = obj.node.children('video')[0];
                obj.video.addEventListener('play', function(){
                    playButton.fadeOut();
                });
                var show = function(){
                    if(obj.index == get_current().index)
                        playButton.fadeIn();
                };
                obj.video.addEventListener('pause', show);
                obj.video.addEventListener('ended', show);
            }
            gallery_data[id].push(obj);
        }
        
        var image_count = gallery_data[id].length;
        
        function set_indicator()
        {
            indicator.text((current_image + 1) + "/" + image_count);
        }
        set_indicator();
        
        
        function get_current()
        {
            return gallery_data[id][current_image];
        }
        
        if(!get_current().video)
            playButton.hide();
        
        // Don't allow navigation on 1 or 0 picture(s)
        if(image_count > 1)
        {
            frame_items.hide();
            get_current().node.show();
            
            function get_next(idx, add)
            {
                var next = idx + add;
                if(next >= gallery_data[id].length)
                    return 0;
                else if(next < 0)
                    return gallery_data[id].length-1;
                return next;
            }
            function start_interval()
            {
                autoInterval = window.setInterval(function()
                {
                    var current = get_current();
                    if(current.video)
                    {
                        if(!current.video.paused && !current.video.ended || document.fullScreen || document.webkitIsFullScreen)
                        {
                            return;
                        }
                    }
                    gallery_nav(1, false);
                }, autoIntervalDuration);
            }
            function gallery_nav(dir, manual)
            {
                var current = get_current();
                if(current.video)
                    current.video.pause();
                current.node.stop();
                current.node.css("opacity", "1");
                var next = get_next(current_image, dir);
                current.node.fadeOut(fadeLength);
                current_image = next;
                current = get_current();
                current.node.fadeIn(fadeLength);
                set_indicator();
                
                if(current.video)
                {
                    playButton.fadeIn();
                }
                else
                {
                    playButton.fadeOut();
                }
                
                // Reset auto timer?
                if(manual)
                {
                    clearInterval(autoInterval);
                    start_interval();
                }
            }
            start_interval();
        
            // Register onclick events for gallery buttons
            left[0].onclick = function(){gallery_nav(-1, true)};
            right[0].onclick = function(){gallery_nav(1, true)};            
        }
        
        // Play button for video elements
        playButton[0].onclick = function(){
            var current = get_current();
            if(current.video && !current.video.playing)
            {
                current.video.play();
                playButton.fadeOut();
            }
            
        };
    }
</script>
<?php
    function insert_element($p)
    {
        $ext = pathinfo($p, PATHINFO_EXTENSION);
        echo "<li>";
        if($ext == "webm" || $ext == "mp4")
        {
            echo "<video src=\"".$p."\" controls preload=\"none\" style=\"width:100%; height:100%\"></video>";
        }
        else
        {
            echo "<img src=\"".$p."\"></img>";
        }
        echo "</li>";
    }
?>
