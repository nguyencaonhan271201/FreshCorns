<?php 
    session_start();
    require_once ("../includes/php/db.php");
    require_once ("post.php");

  if (isset($_GET['postId'])) {
    $myPost = new Post($conn);
    $myPost->post_id = $_GET['postId'];
    if ($myPost->deletePost()) echo true;
  }
  else echo false;
?>