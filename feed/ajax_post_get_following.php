<?php 
    session_start();
    require_once ("../includes/php/db.php");
    require_once ("post.php");
    if (isset($_SESSION['user_id'])) echo json_encode(Post::getAllPostsFollowing($conn,$_SESSION['user_id']));
?>