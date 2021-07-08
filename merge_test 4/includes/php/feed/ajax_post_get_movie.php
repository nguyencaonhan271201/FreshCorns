<?php 
    session_start();
    require_once ("../db.php");
    require_once ("../../../classes/Post.php");
    if (isset($_GET['movie_id']) && isset($_GET['movie_type'])) echo json_encode(Post::getMoviePosts($conn, $_GET['movie_id'],$_GET['movie_type']));
?>