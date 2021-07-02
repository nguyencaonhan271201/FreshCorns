<?php 
    session_start();
    require_once ("db.php");
    require_once ("filemanager.php");
    require_once ("post.php");
  
  if (Post::checkPost($_POST)) {
    $media = null;
      if (isset($_FILES['postFile'])) {
        if (checkFile($_FILES['postFile'])) $media = saveFile($_FILES['postFile'],"imgs/posts/");
      };

    /*if (setRow($conn,
    "INSERT INTO posts(user,movie_id,movie_type,content,media,mode) VALUES (?,?,?,?,?,?)",
    "iiissi",
    array("1",$_POST['postMvId'],$_POST['postMvType'],$_POST['postCap'],$media,"1")
    ) != false) echo true;
    else echo false;*/
    $myPost = new Post($conn);
    if ($myPost->createPost($_POST['postUser'],$_POST['postMvId'],$_POST['postMvType'],$_POST['postCap'],$media,$_POST['postMode'])) echo true;
  }
  else echo false;
?>