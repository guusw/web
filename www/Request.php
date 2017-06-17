<?php
    require "Header.php";
    require "FileDB.php";
    require "ErrMsg.php";

    $file_request = $_GET["f"];
    
    // Get optional extension from file path
    $url_extension = strrpos($file_request, ".");
    if($url_extension !== false)
    {
        $fn = substr($file_request, 0, $url_extension);
        $url_extension = substr($file_request, $url_extension+1);
    }
    else
    {
        $fn = $file_request;
    }
    
    $sub_domains = explode(".",$_SERVER['HTTP_HOST']);
    $sub = array_shift($sub_domains);
    if($sub == "code")
    {
        $full_path = "code/$fn";
        if(file_exists($full_path))
        {
            require "code/view.php";
        }
        else
        {
            show404();
        }
        exit(0);
    }
    
    function range_request_failure()
    {
        http_response_code(416);
        echo "Invalid range";
        exit(0);
    }

    function serve_file($file, $ofn)
    {
        // Don't send php errors in the file stream
        ini_set('display_errors', '1');
        
        // retreive mime type from file
        $fi = new finfo(FILEINFO_MIME);
        $type = $fi->file($file);
        
        if(strpos($type, "text/") !== false)
        {
            // Is code/text viewing enabled
            // disable with cv=0 / enabled by default
            $code = !isset($_GET['cv']) || count($_GET['cv']) == 0 || $_GET['cv'] != 0;
            if($code)
            {
                global $friendly_name;
                $full_path = $file;
                $friendly_name = $ofn;
                require "code/view.php";
                exit(0);
            }
            else
            {
                $type = "application/octet-stream";
            }
        }
        
        // File max length
        $file_length = filesize($file);
        
        // Set header fields
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        //header("Cache-Control: public"); // needed for i.e.
        header("Content-Type: $type");
        header("Content-Length:".$file_length);
        header("Content-Disposition: filename=$ofn");
        
        // Satisfy range request?
        if(isset($_SERVER["HTTP_RANGE"]))
        {
            http_response_code(206);
            $ranges = explode(",", $_SERVER["HTTP_RANGE"]);
            if(count($ranges) > 1)
                range_request_failure(); // Only allow single range requests
                        
            // Open the file
            $fp = fopen($file, "rb");
            
            foreach($ranges as $range)
            {
                $matches = [];
                if(!preg_match("#([0-9]*)-([0-9]*)#", $range, $matches))
                    range_request_failure();
                //var_dump($matches);
                
                $range_start = (strlen($matches[1]) == 0) ? 0 : intval($matches[1]);
                $range_end = (strlen($matches[2]) == 0) ? ($file_length-1) : intval($matches[2]);
                if(strlen($matches[1]) == 0)
                {
                    $range_start = $file_length - $range_end;
                    $range_end = $file_length-1;
                }
                $range_length = ($range_end+1) - $range_start;
                //echo "Read length = $range_length\n";
                if($range_length <= 0)
                    range_request_failure();
                if($range_start < 0 || $range_end >= $file_length)
                    range_request_failure();
                
                // Partial Content
                header("Content-Range: bytes ".$range_start."-".$range_end."/$file_length");
                header("Content-Length: $range_length");
                
                fseek($fp, $range_start);
                // Output a range of the file to the output stream
                echo fread($fp, $range_length);
                break;
            }
        }
        else
        {
            //header("Accept-Ranges: bytes");
            readfile($file);
        }
    }

    function show404()
    {
        global $file_request;
        message_body_begin();
        message("Request: $file_request");
        message("This file does not exist or has been removed");
        message_body_end();
    }
    
    function show_extension_mismatch($match)
    {
        global $url_extension;
        message_body_begin();
        message("File extension in url does not match the file which is [$match], [$url_extension] provided");
        message_body_end();
    }
    
    $file_info = find_file($fn);
    if(isset($file_info["extension"]))
    {
        $ext = $file_info["extension"];
        $file = "$data_dir/$fn.$ext";

        // Check extension if one is in the url
        if($url_extension !== false && $url_extension !== $ext)
        {
            show_extension_mismatch($ext);
            exit(0);
        }
        serve_file($file, $file_info["ofn"]);
        
        exit(0);
    }
    else
    {
        show404();
    }
?>
