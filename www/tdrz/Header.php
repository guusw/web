<?php
    error_reporting(E_ALL);
    ini_set("display_errors", '1');

    // Configuration
    require "Config.php";

    $db_instance = false;
    // Opens a database connection
    // !! don't close this as it is reused for every call to open_db
    function open_db()
    {
        global $db_host;
        global $db_port;
        global $db_user;
        global $db_pw;
        global $db_name;
        global $db_instance;
        if($db_instance == false)
            $db_instance = mysqli_connect($db_host, $db_user, $db_pw, $db_name, $db_port);
        return $db_instance;
    }
    
    // Makes sure that the client is using a https connection
    // if they are not, return response code 400 and a link to the https page equivalent
    function ensure_https()
    {
        if(!array_key_exists("HTTPS", $_SERVER))
        {
            $uri = $_SERVER["REQUEST_URI"];
            $host = $_SERVER["HTTP_HOST"];
            echo "Page not available over HTTP<br><a href=\"https://$host$uri\">Use HTTPS instead.</a>";
            http_response_code(400);
            exit(0);
        }
    }
?>
