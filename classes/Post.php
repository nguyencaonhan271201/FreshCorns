<?php
class Post {
    public $id;
    public $user;
    public $content;
    public $media;
    public $model;
    public $share_from;
    public $date_created;
    public $conn;
    public $post = [];
    public $posts = [];

    public function __construct($conn) {
        $this->conn = $conn;
    }

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
            $query = "INSERT INTO posts(user, content, media, mode) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("issi", $this->id, $post_content, $image, $post_mode);
            $stmt->execute();
            if ($stmt->affected_rows == 1) {
                if (headers_sent()) {
                    echo "<script>window.location.href = 'index.php';</script>";
                }
                else{
                    header("Location: index.php");
                }
            } else {
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
                    echo "<script>window.location.href = 'index.php';</script>";
                }
                else{
                    header("Location: index.php");
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
        $query = "SELECT p.ID, p.user, p.content, p.media, p.mode, p.share_from, p.date_created, u.display_name, u.profile_image
        FROM posts p, profiles u WHERE p.user = u.ID ORDER BY p.date_created DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $this->posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
}