<?php
    function message_body_begin()
    {
        echo "<body style=\"padding:10;font-family: Consolas;\">";
    }
    function message_body_end()
    {
        echo "</body>";
    }
    function message($msg)
    {
        echo "$msg</br>";
    }
?>