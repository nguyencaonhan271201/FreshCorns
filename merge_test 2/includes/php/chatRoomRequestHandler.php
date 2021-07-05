<?php
include('../../classes/ChatRoom.php');
include('../../classes/Validate.php');
include('db.php');
session_start();

if (isset($_POST['list_rooms'])) {
    $user_id = $_SESSION['user_id'];
    $chat_room = new ChatRoom($conn);
    echo json_encode($chat_room->getRoomsOfUser($user_id));
} else if (isset($_POST['single_room'])) {
    $room_id = $_POST['room_ID'];
    $chat_room = new ChatRoom($conn);
    $chat_room->setID($room_id);
    echo json_encode($chat_room->getRoom());
} else {
    if ($_POST['csrf'] == $_SESSION['csrf_token']) {
        if (isset($_POST['file_upload'])) {
            $file = $_FILES['file'];
            $chat_room = new ChatRoom($conn);
            $chat_room->validateImageMessage($file);
        } else if (isset($_POST['member_search'])) {
            $chat_room = new ChatRoom($conn);
            $query = $_POST['q'];
            echo json_encode($chat_room->searchMembers($query));
        } else if (isset($_POST['create_chat_room'])) {
            $roomname = $_POST['room_name'];
            $members = json_decode($_POST['members']);
            $thumbnail = false;
            if (isset($_FILES['thumbnail'])) {
                $thumbnail = $_FILES['thumbnail'];
            }
            $chat_room = new ChatRoom($conn);
            $results = $chat_room->createChatRoom($roomname, $members, $thumbnail);
            echo json_encode($results);
        } else if (isset($_POST['room_edit_get'])) {
            $room_id = $_POST['room_ID'];
            $chat_room = new ChatRoom($conn);
            $chat_room->setID($room_id);
            echo json_encode($chat_room->getBasicInfo());
        } else if (isset($_POST['edit_chat_room'])) {
            $roomname = $_POST['room_name'];
            $thumbnail = false;
            if (isset($_FILES['thumbnail'])) {
                $thumbnail = $_FILES['thumbnail'];
            }
            $old_thumbnail = $_POST['old_thumbnail'];
            $room_id = $_POST['room_ID'];
            $chat_room = new ChatRoom($conn);
            $chat_room->setID($room_id);
            $results = $chat_room->editChatRoom($roomname, $old_thumbnail, $thumbnail);
            echo json_encode($results);
        } else if (isset($_POST['leave_room'])) {
            $room_id= $_POST['room_ID'];
            $user_id = $_SESSION['user_id'];
            $chat_room = new ChatRoom($conn);
            $chat_room->setID($room_id);
            echo json_encode($chat_room->leaveRoom($user_id));
        } else if (isset($_POST['member_search_add'])) {
            $chat_room = new ChatRoom($conn);
            $query = $_POST['q'];
            $chat_room->setID($_POST['room']);
            echo json_encode($chat_room->searchMemberAdd($query));
        } else if (isset($_POST['chat_room_member_add'])) {
            $id = $_POST['id'];
            $chat_room = new ChatRoom($conn);
            $chat_room->setID($_POST['room']);
            echo json_encode($chat_room->addMember($id));
        }
    }
}