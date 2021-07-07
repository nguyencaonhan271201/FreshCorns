<?php 
    session_start();
    require_once ("../db.php");
    require_once ("../../../classes/Post.php");
    if (isset($_SESSION['user_id'])) echo json_encode(Post::getAllPostsFollowing($conn,$_SESSION['user_id']));
?>