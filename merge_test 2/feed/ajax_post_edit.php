<?php 
    session_start();
    require_once ("../includes/php/db.php");
    require_once ("filemanager.php");
    require_once ("post.php");
    var_dump($_POST);
    var_dump($_FILES);

  if (Post::checkPost($_POST)) {
    $media = null;    
    if (isset($_POST['postFile'])) {
      if ($_POST['postFile']!='undefined') $media= $_POST['postFile'];
    }
    else if (isset($_FILES['postFile'])) {
      if (checkFile($_FILES['postFile'])) $media = saveFile($_FILES['postFile'],"assets/images/posts/",dirname(__DIR__,1).'/');
    }

    $myPost = new Post($conn);
    $myPost->post_id = $_POST['postId'];
    if ($myPost->editPost($_POST['postMvId'],$_POST['postMvType'],$_POST['postCap'],$media,$_POST['postMode'])) echo true;
  }
  else echo false;

?>