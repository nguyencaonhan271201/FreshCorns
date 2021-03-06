<?php
include('db.php');
include('../../classes/Post.php');
include('../../classes/Movies.php');
include('../../classes/Profile.php');
include('../../classes/User.php');
include('../../classes/Validate.php');
include('../../classes/ThisUser.php');
session_start();

if (isset($_POST['header_search'])) {
    $search = $_POST['q'];
    $search = "%{$search}%";
    $query = "SELECT p.ID, p.display_name, p.profile_image FROM profiles p, users u WHERE u.ID = p.ID AND (p.display_name LIKE ? OR u.username LIKE ? OR p.email LIKE ?)"; 
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $search, $search, $search);
    $stmt->execute();
    $results = $stmt->get_result();
    $results = $results->fetch_all(MYSQLI_ASSOC);

    //Sort the result by similarity
    usort($results, function ($a, $b) use ($query) {
        similar_text($_POST['q'], $a['display_name'], $percentA);
        similar_text($_POST['q'], $b['display_name'], $percentB);

        return $percentA === $percentB ? 0 : ($percentA > $percentB ? -1 : 1);
    });

    echo json_encode($results);
} elseif (isset($_POST['post_share'])) {
    $postID = $_POST['postID'];
    $mode = $_POST['mode'];
    $post = new Post($conn);
    $post->sharePost($postID, $mode);
} elseif (isset($_POST['trending_movies'])) {
    echo json_encode(Movies::getTrendingMovies($conn));
} elseif (isset($_POST['create_account'])) {
    if ($_POST['csrf'] == $_SESSION['csrf_token']) {
        $errors = [];
        $this_user = ThisUser::getInstance();
        $this_user->setConn($conn);
        $this_user->CheckRegisterUser($_POST, $_FILES, $errors);
        echo json_encode($errors);
    } 
}