<?php 
    session_start();
    require_once ("../db.php");
    require_once ("../../../classes/Post.php");
    echo json_encode(Post::getAllPostsPublic($conn, $_SESSION['user_id']));
?>