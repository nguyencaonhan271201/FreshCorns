<?php
    session_start();
    $_SESSION = [];
    session_destroy();
    if (headers_sent()) {
        echo "<script>window.location.href = 'feeds.php';</script>";
    }
    else{
        header('Location: feeds.php');
    }