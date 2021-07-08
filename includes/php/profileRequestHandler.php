<?php 
    session_start();
    require_once ("db.php");
    require_once ("../../classes/Post.php");
    require_once ("../../classes/Profile.php");
    if (isset($_POST['load_posts'])) {
        $user = $_POST['user_id'];
        echo json_encode(Post::getPostsProfile($conn, $user));
    } elseif (isset($_POST['add_info'])) {
        if ($_POST['csrf'] == $_SESSION['csrf_token']) {
            $errors = [];
            $profile = new Profile($conn, $_SESSION['user_id']);
            $profile->checkInfo($_POST, $errors);
            echo json_encode($errors);
        } 
    } elseif (isset($_POST['single_info'])) {
        $id = $_POST['id'];
        $profile = new Profile($conn, $_SESSION['user_id']);
        $result = $profile->getSingleInfo($id);
        echo json_encode($result);
    } elseif (isset($_POST['edit_info'])) {
        if ($_POST['csrf'] == $_SESSION['csrf_token']) {
            $errors = [];
            $profile = new Profile($conn, $_SESSION['user_id']);
            $profile->editInfo($_POST, $errors);
            echo json_encode($errors);
        } 
    } elseif (isset($_POST['delete_info'])) {
        if ($_POST['csrf'] == $_SESSION['csrf_token']) {
            $errors = [];
            $profile = new Profile($conn, $_SESSION['user_id']);
            $profile->deleteInfo($_POST['id'], $errors);
            echo json_encode($errors);
        } 
    }
?>