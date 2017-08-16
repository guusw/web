<?php
    include_once "GalleryScript.php";
    if(!isSet($gallery_id))
    {
        $gallery_id = 0;
    }
    $gallery_id++;
?>

<div class="image_frame" id="image_frame_<?php echo $gallery_id; ?>">
    <ul>
        <?php
            $paths = explode(",", $gallery_path);
            foreach($paths as $path)
            {
                if(is_dir($path))
                {
                    $dit = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS);
                    foreach($dit as $fileInfo)
                    {
                        if(!$fileInfo->isFile())
                            continue;
                        insert_element($dit->getPath()."/".$fileInfo->getFileName());
                    }
                }
                else
                {
                    insert_element($path);
                }
            }
        echo "\n";
        ?>
    </ul>
    <div class="left"><a class="fa fa-angle-double-left"></a></div>
    <div class="middle">
        <a class="fa fa-play"></a>
    </div>
    <div class="right"><a class="fa fa-angle-double-right"></a></div>
    <div class="count">0/0</div>
</div>
<script>
    $(document).ready(function(){create_gallery(<?php echo $gallery_id; ?>);});
</script>
