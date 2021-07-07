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
        if (!isset($POST['postCap']) && !isset($POST['postMvId'])) return false;
        if ($_SESSION['user_id']!==(int)$POST['postUser']) return false;
        if ($POST['postMode']!=1&&$POST['postMode']!=2&&$POST['postMode']!=3) return false;
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
          $this->post_mode = $post_mode;
          return true;
      }
      else return false;
    }

    public function editPost($post_movie_id,$post_movie_type,$post_content,$post_media,$post_mode){
      $sql = "
      UPDATE posts 
      SET movie_id = ?,movie_type = ?,content = ?,media = ?,mode = ?
      WHERE id = ?";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("iissii",$post_movie_id,$post_movie_type,$post_content,$post_media,$post_mode,$this->post_id);
      $stmt->execute();
      if($stmt->affected_rows == 1) {
          $this->post_movie_id = $post_movie_id;
          $this->post_movie_type = $post_movie_type;
          $this->post_content = $post_content;
          $this->post_media = $post_media;
          $this->post_mode = $post_mode;
          return true;
      }
      else return false;
    }

    public function deletePost(){
      $sql = "
      DELETE FROM posts
      WHERE id = ?";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("i",$this->post_id);
      $stmt->execute();
      if($stmt->affected_rows == 1) return true;
      else return false;
    }

    public static function getAllPostsPublic($conn, $user_id){
      $posts = getRows($conn,
        "select p.*, pf.display_name,pf.profile_image
        from posts p, profiles pf
        where p.user = pf.ID and p.mode = 1 AND p.share_from IS NULL
        OR EXISTS (SELECT * FROM relationships r WHERE r.user2 = (SELECT user FROM posts WHERE ID = p.share_from) AND r.user1 = ?)
        ORDER BY p.date_created ASC","i",
        array($user_id));

        for ($i = 0; $i < count($posts); $i++) {
          if ($posts[$i]['share_from'] != null) {
              $query = "SELECT p.ID, p.user, p.content, p.movie_id, p.movie_type, p.media, p.mode, p.share_from, p.date_created, u.display_name, u.profile_image
              FROM posts p, profiles u WHERE p.user = u.ID AND p.ID = ? AND p.mode=1";
              $stmt = $conn->prepare($query);
              $stmt->bind_param("i", $posts[$i]['share_from']);
              $stmt->execute();
              $posts[$i]['original'] = $stmt->get_result()->fetch_assoc();
          }
      }

      return $posts;
    }

    public static function getAllPostsFollowing($conn,$user_id){
      $posts = getRows($conn,
        "SELECT DISTINCT p.*, pf.display_name, pf.profile_image
        FROM posts p JOIN profiles pf
        ON p.user = pf.ID WHERE p.ID IN (SELECT ID FROM posts WHERE user = ? AND (mode = 2 OR mode = 3))
        OR p.ID IN
        (SELECT p1.ID
        FROM posts p1, relationships r
        WHERE p1.user = r.user2 AND r.user1 = ?
        AND (p1.share_from IS NULL
        OR EXISTS (SELECT * FROM relationships r1 WHERE r1.user2 = (SELECT user FROM posts WHERE ID = p1.share_from) AND r1.user1 = ?))
        AND (p1.mode = 2 OR p1.mode = 3))
        ORDER BY p.date_created ASC","iii",array($user_id,$user_id,$user_id));

        for ($i = 0; $i < count($posts); $i++) {
          if ($posts[$i]['share_from'] != null) {
              $query = "SELECT p.ID, p.user, p.content, p.movie_id, p.movie_type, p.media, p.mode, p.share_from, p.date_created, u.display_name, u.profile_image
              FROM posts p, profiles u WHERE p.user = u.ID AND p.ID = ?";
              $stmt = $conn->prepare($query);
              $stmt->bind_param("i", $posts[$i]['share_from']);
              $stmt->execute();
              $posts[$i]['original'] = $stmt->get_result()->fetch_assoc();
          }
      }

      return $posts;
    }

    public function getSinglePost($id){
      $this->post_id=$id;
      $this->post = getRows($this->conn,
      "select p.*,pf.display_name,pf.profile_image
      from posts p, profiles pf
      where p.user = pf.ID and p.ID=?","i",array($this->post_id));

      if ($this->post[0]['share_from'] != null) {
        $query = "SELECT p.ID, p.user, p.content, p.movie_id, p.movie_type, p.media, p.mode, p.share_from, p.date_created, u.display_name, u.profile_image
        FROM posts p, profiles u WHERE p.user = u.ID AND p.ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->post[0]['share_from']);
        $stmt->execute();
        $this->post[0]['original'] = $stmt->get_result()->fetch_assoc();
      }
    }

    public function likePost($user){      
      $temp = getRows($this->conn,
      "select *
      from post_reactions pr
      where pr.post = ? and pr.user = ?","ii",array($this->post_id,$user));
      
      if (empty($temp)){
        if (setRow($this->conn,
        "INSERT INTO post_reactions(post,user)VALUES (?,?)",
        "ii",array($this->post_id,$user)) != false) return 1;
      }
      else{
        if (setRow($this->conn,
        "DELETE FROM post_reactions WHERE post = ? and user = ?",
        "ii",array($this->post_id,$user)) != false) return 1;
      }
      return 0;
    }

  }

?>