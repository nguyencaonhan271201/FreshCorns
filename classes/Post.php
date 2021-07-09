<?php
class Post {
    /*public $id;
    public $user;
    public $content;
    public $media;
    public $model;
    public $share_from;
    public $date_created;
    public $conn;
    public $post = [];
    public $posts = [];*/

    public $post_id;
    public $post_user;
    public $post_movie_id;
    public $post_movie_type;
    public $post_content;
    public $post_media = null;
    public $post_mode = 1;
    public $post_share_from = null;
    public $post=[];

    public function __construct($conn) {
        $this->conn = $conn;
    }
    /*
    public function setID($id) {
        $this->id = $id;
    }
    public function createPost($SESSION, $POST, $FILES, &$errors) {
        $this->id = $SESSION['user_id'];

        $post_content = $POST['post_content'];
        $post_mode = $POST['post-type'];

        $image = "";
        Validate::validatePost($POST, $FILES, $image, $errors);

        if (empty($errors)) {
            $query = "INSERT INTO posts(user, content, media, movie_id, movie_type, mode) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $default = 0;
            $stmt->bind_param("issiii", $this->id, $post_content, $image, $default, $default, $post_mode);
            $stmt->execute();
            if ($stmt->affected_rows == 1) {
                if (headers_sent()) {
                    echo "<script>window.location.href = 'feeds.php';</script>";
                }
                else{
                    header("Location: feeds.php");
                }
            } else {
                var_dump($stmt);
                $errors['create-post-execute-err'] = "Server error. Please try again later!";
            } 
        }
    }
    public function getPost() {
        $query = "SELECT p.ID, p.user, p.content, p.media, p.mode, p.share_from, p.date_created, u.display_name, u.profile_image
        FROM posts p, profiles u WHERE p.user = u.ID AND p.ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $this->post = $stmt->get_result()->fetch_assoc();
    }
    public function editPost($POST, $FILE, &$errors) {
        $image = "";

        Validate::validateEditPost($POST, $FILE, $image, $errors);

        if (empty($errors)) {
            $id = $POST['edit_post'];
            $content = $POST['edit_post_content'];
            $mode = $POST['edit-post-type'];

            $query = "UPDATE posts SET content = ?, media = ?, mode = ? WHERE ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssii", $content, $image, $mode, $id);
            $stmt->execute();
            if ($stmt->affected_rows != -1 && $stmt->errno == 0) {
                if (headers_sent()) {
                    echo "<script>window.location.href = 'feeds.php';</script>";
                }
                else{
                    header("Location: feeds.php");
                }
            } else {
                $errors['edit-post-execute-err'] = "Server error. Please try again later!";
                $this->post = [
                    'ID' => $id,
                    'content' => $content,
                    'media' => $image,
                    'mode' => $mode,
                ];
            } 
        } else {
            $this->post = [
                'ID' => $POST['edit_post'],
                'content' => $POST['edit_post_content'],
                'media' => $image,
                'mode' => $POST['edit-post-type'],
            ];
        }
    }
    public function getPosts() {
        $query = "SELECT p.ID, p.user, p.content, p.media, p.mode, p.share_from, p.date_created, u.display_name, u.profile_image,
        (SELECT COUNT(*) FROM comments c WHERE c.post = p.ID) AS number_of_comments
        FROM posts p, profiles u WHERE p.user = u.ID ORDER BY p.date_created DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $this->posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        for ($i = 0; $i < count($this->posts); $i++) {
            if ($this->posts[$i]['share_from'] != null) {
                $query = "SELECT p.ID, p.user, p.content, p.media, p.mode, p.share_from, p.date_created, u.display_name, u.profile_image
                FROM posts p, profiles u WHERE p.user = u.ID AND p.ID = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("i", $this->posts[$i]['share_from']);
                $stmt->execute();
                $this->posts[$i]['original'] = $stmt->get_result()->fetch_assoc();
            }
        }
    }
    public function showPosts() {
        foreach ($this->posts as $post) {
            $post_image = $post['media'];
            $profile_image = $post['profile_image'];
            $user = $post['user'];
            $ID = $post['ID'];
            $content = $post['content'];
            $display_name = $post['display_name'];
            $date_created = $post['date_created'];

            $icon = "";
            if ($post['mode'] == 0) {
                $icon = "<i class='fa fa-globe' aria-hidden='true'></i>";
            } else if ($post['mode'] == 1) {
                $icon = "<i class='fas fa-user-friends' aria-hidden='true'></i>";
            } else {
                $icon = "<i class='fa fa-lock' aria-hidden='true'></i>";
            }

            $content_html = "";
            $read_more_html = "";

            if (strlen($content) > 200) {
                $display_content = substr($content, 0, 200);
                $content_html = "{$display_content} ... ";
                $read_more_html = "<a href='#' data-id={$ID} data-type=0 class='read-more'><b>Read more</b></a>";
            } else {
                $content_html = $content;
            }

            $media_html = "";
            if ($post_image != "") {
                $media_html = "<a class='post-img m-0 p-0 pt-1 pb-1' href=''>
                    <img src='{$post_image}' alt=''></img>
                </a>";
            }

            echo "<div class='post-box m-3 p-0' data-id={$ID}>
            <div class='d-flex flex-row pl-4 pr-4 pt-4 pb-1'>
                <a href='profile.php?id={$user}' class='post-header-img'>
                    <img class='rounded-circle d-inline-block profile-img' src='{$profile_image}'> 
                </a>
                <div class='post-header-info ml-2'>
                    <div>
                        <a href='profile.php?id={$user}'><b>{$display_name}</b></a>
                        <p class='m-0'>
                            <span>{$date_created}</span>
                            <span>·</span>
                            {$icon}
                        </p>
                    </div>
                </div>
            </div>
            <p class='post-content mt-2 pl-4 pr-4 pb-2'>
                <span>{$content_html}</span>
                {$read_more_html}
            </p>
            {$media_html}
            <div class='reaction-block pl-4 pr-4 pt-2 pb-2 d-flex justify-content-between'>
                <span>
                    <i class='fa fa-thumbs-up' aria-hidden='true'></i>
                    1
                </span>
                <a href='#'>
                    3 lượt chia sẻ
                </a>
            </div>
            <hr class='ml-4 mr-4 mt-1 mb-1'>
            <div class='button-block pl-4 pr-4 pt-2 pb-2 d-flex justify-content-between align-items-center'>
                <button type='button' class='btn btn-dark text-center btn-block'><i class='fa fa-thumbs-up' aria-hidden='true'></i> Like</button>
                <button type='button' class='btn btn-dark text-center btn-block'><i class='fa fa-comment' aria-hidden='true'></i> Comment</button>
                <button type='button' class='btn btn-dark text-center btn-block'><i class='fa fa-share' aria-hidden='true'></i> Share</button>
            </div>

            <p class='d-none full-content'>{$content}</p>
        </div>";
        }
    }
    */
    public function sharePost($postID, $mode) {
        $query = "INSERT INTO posts(user, content, mode, share_from) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $defaultContent = "";
        $stmt->bind_param("isis", $_SESSION['user_id'], $defaultContent, intval($mode), $postID);
        $stmt->execute();
        if ($stmt->affected_rows == 1) {
            echo "true";
        } else {
            echo "false";
        } 
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
        "select p.*, (SELECT COUNT(*) FROM comments c WHERE c.post = p.ID) AS number_of_comments, pf.display_name,pf.profile_image 
        from posts p JOIN profiles pf ON p.user = pf.ID WHERE p.mode = 1 AND p.share_from IS NULL 
        OR p.mode = 1 AND EXISTS (SELECT * FROM relationships r WHERE r.user2 = (SELECT user FROM posts WHERE ID = p.share_from) 
        AND r.user1 = ? ORDER BY p.date_created ASC)","i",
        array($user_id));

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

    public static function getAllPostsFollowing($conn,$user_id){
      $posts = getRows($conn,
        "SELECT DISTINCT p.*, (SELECT COUNT(*) FROM comments c WHERE c.post = p.ID) AS number_of_comments, pf.display_name, pf.profile_image
        FROM posts p JOIN profiles pf
        ON p.user = pf.ID WHERE p.ID IN (SELECT ID FROM posts WHERE user = ? AND (mode = 2 OR mode = 3))
        OR p.ID IN
        (SELECT p1.ID
        FROM posts p1, relationships r
        WHERE p1.user = r.user2 AND r.user1 = ?
        AND (p1.share_from IS NULL
        OR EXISTS (SELECT * FROM relationships r1 WHERE r1.user2 = (SELECT user FROM posts WHERE ID = p1.share_from) AND r1.user1 = ?))
        AND (p1.mode = 1 OR p1.mode = 2))
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

    public static function getMoviePosts($conn,$movie_id,$movie_type){
        $posts = getRows($conn,
        "select p.*, pf.display_name,pf.profile_image
        from posts p, profiles pf
        where p.user = pf.ID and p.movie_id = ? and p.movie_type = ?","ii",
        array($movie_id,$movie_type));
        return $posts;
    }

    public function getSinglePost($id){
      $this->post_id = $id;
      $this->post = getRows($this->conn,
      "select p.*, (SELECT COUNT(*) FROM comments c WHERE c.post = p.ID) AS number_of_comments, pf.display_name,pf.profile_image
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

    public static function getPostsProfile($conn, $user_id) {
        if ($user_id != $_SESSION['user_id']) {
            $posts = getRows($conn,
            "SELECT DISTINCT p.*, pf.display_name, pf.profile_image
            FROM posts p JOIN profiles pf
            ON p.user = pf.ID WHERE p.user = ?
            AND (p.ID IN (SELECT ID FROM posts WHERE user = ? AND mode = 1 OR mode = 2 AND share_from IS NULL)
            OR p.ID IN
            (SELECT p.ID
            FROM posts p
            WHERE user = ? AND mode = 1 OR mode = 2 AND share_from IS NOT NULL
            AND (SELECT mode FROM posts p2 WHERE p2.ID = p.share_from) = 1
            OR EXISTS (SELECT * FROM relationships WHERE user1 = ? AND user2 = (
                SELECT user FROM posts p1 WHERE p1.ID = p.share_from
            ))))
            AND p.ID IN (SELECT ID FROM posts WHERE user = ?)
            ORDER BY p.date_created ASC","iiiii",array($user_id, $user_id, $user_id, $_SESSION['user_id'], $user_id));
        } else {
            $posts = getRows($conn,
            "SELECT p.*, pf.display_name, pf.profile_image FROM posts p JOIN profiles pf
            ON p.user = pf.ID WHERE p.user = ?","i",array($user_id));
        }
        
        for ($i = 0; $i < count($posts); $i++) {
          if ($posts[$i]['share_from'] != null) {
              $query = "SELECT p.ID, p.user, p.content, p.movie_id, p.movie_type, p.media, p.mode, p.share_from, p.date_created, u.display_name, u.profile_image
              FROM posts p JOIN profiles u ON p.user = u.ID WHERE p.ID = ?";
              $stmt = $conn->prepare($query);
              $stmt->bind_param("i", $posts[$i]['share_from']);
              $stmt->execute();
              $posts[$i]['original'] = $stmt->get_result()->fetch_assoc();
          }
      }

      return $posts;
    }
}