<?php
session_start();

if (isset($_POST['page'])) {
    $page = $_POST['page'];
    $sessions = [];
    switch ($page) {
        case "chat":
            if (isset($_SESSION['user_id'])) {
                $sessions['user_id'] = $_SESSION['user_id'];
                $sessions['name'] = $_SESSION['name'];
                $sessions['profile_img'] = $_SESSION['profile_img'];
            }
            break;
    }
    echo json_encode($sessions);
}