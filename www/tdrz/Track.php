<?php
    // Connect to the database
    $con = open_db();
    
    $query = "INSERT INTO tdrz_visits(host, ip, last_visited, count, last_page) VALUES (?, ?, NOW(), 1, ?) 
        ON DUPLICATE KEY UPDATE ip = ?,count = count + 1, last_visited=NOW(), last_page=?";
    $stmt = $con->prepare($query);
    
    $remote_ip = $_SERVER["REMOTE_ADDR"];
    $remote_host = gethostbyaddr($remote_ip);
    $req_uri = $_SERVER["REQUEST_URI"];
    
    $stmt->bind_param("sssss", $remote_host, $remote_ip, $req_uri, $remote_ip, $req_uri);
    $stmt->execute();
    $stmt->close();
?>