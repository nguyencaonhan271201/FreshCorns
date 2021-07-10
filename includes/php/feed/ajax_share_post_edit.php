<?php 
    session_start();
    require_once ("../db.php");
    require_once ("../../../classes/Post.php");
    $sql = "
      UPDATE posts 
      SET mode = ?
      WHERE id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ii",$_POST['postMode'],$_POST['postId']);
      $stmt->execute();
      if($stmt->affected_rows == 1) return true;
      else return false;
?>