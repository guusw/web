<?php
    // Random alphanumerical(lower&upper case) string with given length
    function random_string($len)
    {
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $res = "";
        for($i = 0; $i < $len; $i++)
        {
            $res = $res.$chars[rand(0, strlen($chars)-1)];
        }
        return $res;
    }
    
    // Non exisiting filename in folder $targetFolder with any extension
    function generate_filename($targetFolder)
    {
        $filename = "";
        do
        {
            $filename = random_string(8);
        }
        while(count(glob($targetFolder."/".$filename.".*")) > 0);
        return $filename;
    }
    
    // Creates a uri formatted like https://hostname:port/
    // leaves out the port if it is the default for the given procol (http or https)
    function format_public_uri($hostname, $ssl)
    {
        $port_part = "";
        if($ssl)
        {
            global $server_public_https;
            $proto_part = "https://";
            if($server_public_https !== 443)
                $port_part = ":".$server_public_https;
        }
        else
        {
            global $server_public_http;
            $proto_part = "http://";
            if($server_public_http !== 443)
                $port_part = ":".$server_public_http;
        }
        
        return $proto_part.$hostname.$port_part;
    }
?>