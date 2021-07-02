<?php
class Comment {
    public $id;
    public $user;
    public $post;
    public $content;
    public $parent;
    public $date_created;
    public $conn;
    public $comment = [];
    public $comments = [];

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createComment($content, $post, $parent, $user) {
        $parent = $parent == 0? NULL : $parent;
        $query = "INSERT INTO comments(user, post, content, parent) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iisi", $user, $post, $content, $parent);
        $stmt->execute();
        if ($stmt->affected_rows == 1) {
            $this->getSingleComment($stmt->insert_id);
            echo json_encode($this->comment);
        }
        else 
            echo json_encode($this->comment);
    }

    public function getSingleComment($id) {
        $this->id = $id;
        $query = "SELECT c.*, f.display_name, f.profile_image
        FROM comments c, profiles f
        WHERE c.user = f.ID AND c.ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $this->comment = $stmt->get_result()->fetch_assoc();
    }

    // public static function getCommentsOfPost($conn, $post_id) {
    //     $query = "SELECT c.*, f.display_name, f.profile_image
    //     FROM comments c, profiles f
    //     WHERE c.user = f.ID AND c.post = ? AND c.parent IS NULL";
    //     $stmt = $conn->prepare($query);
    //     $stmt->bind_param("i", $post_id);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    // public static function getSubCommentsByParent($conn, $parent) {
    //     $query = "SELECT c.*, f.display_name, f.profile_image
    //     FROM comments c, profiles f
    //     WHERE c.user = f.ID AND c.parent = ?";
    //     $stmt = $conn->prepare($query);
    //     $stmt->bind_param("i", $parent);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public static function getComments($conn, $post_id, $thisUser) {
        if ($thisUser == null) {
            $thisUser = 0;
        }
        $query = "SELECT c.*, f.display_name, f.profile_image, p.user AS author,
        (SELECT COUNT(*) FROM comment_reactions r WHERE r.comment = c.ID) AS number_of_likes,
        (SELECT COUNT(*) FROM comment_reactions r WHERE r.comment = c.ID AND r.user = ?) AS current_user_likes
        FROM comments c, profiles f, posts p
        WHERE c.user = f.ID AND p.ID = c.post AND c.post = ?
        ORDER BY c.date_created ASC";
        $stmt = $conn->prepare($query);
        
        $stmt->bind_param("ii", $thisUser, $post_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function performReaction($conn, $comment, $user) {
        $returnObject = [];
        $query = "SELECT * FROM comment_reactions WHERE user = ? AND comment = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user, $comment);
        $stmt->execute();
        if ($stmt->get_result()->num_rows == 0) {
            //Like
            $returnObject['type'] = 0;

            //Perform like
            $query = "INSERT INTO comment_reactions(user, comment) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $user, $comment);
            $stmt->execute();
            if ($stmt->affected_rows == 1) {
                $returnObject['success'] = true;
            }
            else 
                $returnObject['success'] = false;
        } else {
            //Dislike
            $returnObject['type'] = 1;

            //Perform like
            $query = "DELETE FROM comment_reactions WHERE user = ? AND comment = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $user, $comment);
            $stmt->execute();
            if ($stmt->affected_rows == 1) {
                $returnObject['success'] = true;
            }
            else 
                $returnObject['success'] = false;
        }

        return $returnObject;
    }

    public static function performDeletion($conn, $comment, $user) {
        $query = "SELECT * FROM comments WHERE user = ? AND ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user, $comment);
        $stmt->execute();
        if ($stmt->get_result()->num_rows == 0) {
            echo "false";
            return;
        } else {
            //Perform like
            $query = "DELETE FROM comments WHERE ID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $comment);
            $stmt->execute();
            if ($stmt->affected_rows == 1)
                echo "true";
            else 
                echo "false";
        }
    }

    public static function performEdit($conn, $comment, $content, $user) {
        $query = "SELECT * FROM comments WHERE user = ? AND ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user, $comment);
        $stmt->execute();
        if ($stmt->get_result()->num_rows == 0) {
            echo "false";
            return;
        } else {
            //Perform like
            $query = "UPDATE comments SET content = ? WHERE ID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $content, $comment);
            $stmt->execute();
            if ($stmt->affected_rows != -1 && $stmt->errno == 0)
                echo "true";
            else 
                echo "false";
        }
    }
}