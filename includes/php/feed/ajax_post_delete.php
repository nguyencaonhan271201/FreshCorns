<?php 
    session_start();
    require_once ("../db.php");
    require_once ("../../../classes/Post.php");

  if (isset($_GET['postId'])) {
    $myPost = new Post($conn);
    $myPost->post_id = $_GET['postId'];
    if ($myPost->deletePost()) echo true;
  }
  else echo false;
?>