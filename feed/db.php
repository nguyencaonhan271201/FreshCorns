<?php
    $host = "localhost";
    $user = "root";
    $pw = "";
    $db = "cs204_final_project";

    // #1: create the conn object
    $conn = new mysqli($host, $user, $pw, $db);    
    mysqli_set_charset($conn,"utf8mb4");

    
?>