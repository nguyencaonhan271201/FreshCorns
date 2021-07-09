<?php 
    session_start();
    require_once ("../db.php");
    require_once ("../../../classes/Post.php");
    $result = Post::getAllPostsPublic($conn, $_SESSION['user_id']);
    echo json_encode($result);
?>