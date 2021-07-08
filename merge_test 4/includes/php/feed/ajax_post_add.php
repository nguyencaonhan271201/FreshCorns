<?php 
    session_start();
    require_once ("../db.php");
    require_once ("../filemanager.php");
    require_once ("../../../classes/Post.php");
      
  if (Post::checkPost($_POST)) {
    $media = null;
    if (isset($_FILES['postFile'])) {
        if (checkFile($_FILES['postFile'])) $media = saveFile($_FILES['postFile'],"assets/images/posts/",dirname(__DIR__,3).'/');
    };

    $myPost = new Post($conn);
    if ($myPost->createPost($_POST['postUser'],$_POST['postMvId'],$_POST['postMvType'],$_POST['postCap'],$media,$_POST['postMode'])) echo true;
  }
  else echo false;
?>