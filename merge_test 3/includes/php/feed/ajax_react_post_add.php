<?php
  session_start();
  require_once ("../db.php");
  require_once ("../../../classes/Post.php");
  
  if (isset($_GET['post_id']) && isset($_SESSION['user_id'])){
    $post = new Post($conn);
    $post->post_id=$_GET['post_id'];
    echo $post->likePost($_SESSION['user_id']);
  }
  else echo 0;
?>