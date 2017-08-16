<?php require 'Header.php';?>   
<div class="content">
<?php
    $pages = array("Home.php", "CV.php", "Projects.php");
    function insert_page($pid)
    {
        global $pages;
        require 'Pages/'.$pages[$pid];
    }
    if(array_key_exists('page_id', $_REQUEST))
        $pid = $_REQUEST['page_id'];
    else
        $pid = 2;
    if($pid >= count($pages) || $pid < 0 || !is_numeric($pid))
    {
        echo 'Invalid Page ID '.$pid;
    }
    else
    {
        insert_page($pid);
    }
?>
</div>
<?php require 'Footer.php';?>
