<?php 
    session_start();
    require_once ("../includes/php/db.php");
    require_once ("post.php");
    echo json_encode(Post::getAllPostsPublic($conn));
?>