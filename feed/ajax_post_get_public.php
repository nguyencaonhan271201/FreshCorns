<?php 
    session_start();
    require_once ("../includes/php/db.php");
    require_once ("post.php");
    /*echo json_encode(
        getRows($conn,"select p.*,u.username
    from posts p, users u
    where p.user = u.ID","",array())
    );*/
    echo json_encode(Post::getAllPostsPublic($conn));
?>