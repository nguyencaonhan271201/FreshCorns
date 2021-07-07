<?php 
    session_start();
    require_once ("../includes/php/db.php");
    require_once ("filemanager.php");
    require_once ("post.php");
      
  if (Post::checkPost($_POST)) {
    $media = null;
      if (isset($_FILES['postFile'])) {
        if (checkFile($_FILES['postFile'])) $media = saveFile($_FILES['postFile'],"imgs/posts/");
      };

    $myPost = new Post($conn);
    if ($myPost->createPost($_POST['postUser'],$_POST['postMvId'],$_POST['postMvType'],$_POST['postCap'],$media,$_POST['postMode'])) echo true;
  }
  else echo false;
?>