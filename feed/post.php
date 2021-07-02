<?php
  require_once('db.php');

  class Post {

    public $post_id;
    public $post_user;
    public $post_movie_id;
    public $post_movie_type;
    public $post_content;
    public $post_media = null;
    public $post_mode = 1;
    public $post_share_from = null;
    public $post=[];
    public $conn;  
    public $errors = [];

    public function __construct($conn) {
      $this->conn = $conn;
    }
    

    public static function checkPost($POST) {
        if (!isset($POST['postCap']) && !isset($POST['postMvId'])){
            return false;
        }
        if ($_SESSION['user_id']!==$POST['postUser']){          
            return false;
        }
        return true;
    }

    public function createPost($post_user,$post_movie_id,$post_movie_type,$post_content,$post_media,$post_mode){
      $sql = "INSERT INTO posts(user,movie_id,movie_type,content,media,mode) VALUES (?,?,?,?,?,?)";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("iiissi",$post_user,$post_movie_id,$post_movie_type,$post_content,$post_media,$post_mode);
      $stmt->execute();
      if($stmt->affected_rows == 1) {
          $this->post_user = $post_user;
          $this->post_movie_id = $post_movie_id;
          $this->post_movie_type = $post_movie_type;
          $this->post_content = $post_content;
          $this->post_media = $post_media;
          $this->post_media = $post_media;
          $this->post_mode = $post_mode;
          return true;
      }
      else return false;
    }

    public static function getAllPostsPublic($conn){
      return getRows($conn,
        "select p.*,pf.display_name,pf.profile_image
        from posts p, profiles pf
        where p.user = pf.ID","",array());
    }

    public function getSinglePost($id){
      $this->post_id=$id;
      $this->post = getRows($this->conn,
      "select p.*,pf.display_name,pf.profile_image
      from posts p, profiles pf
      where p.user = pf.ID and p.ID=?","i",array($this->post_id));
    }
  }

?>