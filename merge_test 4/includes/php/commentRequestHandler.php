<?php

include('db.php');
include('../../classes/Comment.php');
session_start();

//Add new comment
if (isset($_POST['add'])) {
    $content = $_POST['content'];
    $post = $_POST['post'];
    $parent = $_POST['parent'];
    $comment = new Comment($conn);
    $comment->createComment($content, $post, $parent, $_SESSION['user_id']);
} else if (isset($_POST['get_all'])) {
    $post = $_POST['post'];
    if (isset($_SESSION['user_id']))
        echo json_encode(Comment::getComments($conn, $post, $_SESSION['user_id']));   
    else
        echo json_encode(Comment::getComments($conn, $post, null));
} else if (isset($_POST['comment_react'])) {
    $comment = $_POST['comment'];
    echo json_encode(Comment::performReaction($conn, $comment, $_SESSION['user_id']));
} else if (isset($_POST['comment_delete'])) {
    $comment = $_POST['comment'];
    Comment::performDeletion($conn, $comment, $_SESSION['user_id']);
} else if (isset($_POST['comment_edit'])) {
    $comment = $_POST['comment'];
    $content = $_POST['content'];
    Comment::performEdit($conn, $comment, $content, $_SESSION['user_id']);
}