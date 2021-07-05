<?php


class ChatRoom {
    public $id;
    public $type;
    public $room_name;
    public $date_created;
    public $users = [];
    public $chatrooms = [];
    public $chatroom = [];
    public $conn;

    function __construct($conn) {
        $this->conn = $conn;
    }

    function setID($id) {
        $this->id = $id;
    }

    function setProperties($type, $room_name, $users) {
        $this->type = $type;
        $this->room_name = $room_name;
        $this->users = $users;
    }

    function getRoomsOfUser($user_id) {
        $query = "SELECT * FROM chat_rooms WHERE ID IN 
        (SELECT room FROM chat_room_participate WHERE user = ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $results = $stmt->get_result();
        $rooms = $results->fetch_all(MYSQLI_ASSOC);
        for ($i = 0; $i < count($rooms); $i++) {
            $query = "SELECT p.display_name, p.profile_image
            FROM chat_room_participate c, profiles p
            WHERE c.user = p.ID AND c.room = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $rooms[$i]['ID']);
            $stmt->execute();
            $results = $stmt->get_result();
            $rooms[$i]['members'] = $results->fetch_all(MYSQLI_ASSOC);
        }
        $this->chatrooms = $rooms;
        return $this->chatrooms;
    }

    function getRoom() {
        $query = "SELECT * FROM chat_rooms WHERE ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $results = $stmt->get_result();
        $rooms = $results->fetch_assoc();
        $query = "SELECT p.display_name, p.profile_image
        FROM chat_room_participate c, profiles p
        WHERE c.user = p.ID AND c.room = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $results = $stmt->get_result();
        $rooms['members'] = $results->fetch_all(MYSQLI_ASSOC);
        $this->chatroom = $rooms;
        return $this->chatroom;
    }

    function createChatRoom($roomname, $members, $thumbnail) {
        $result = [];
        $errors = [];
        $type = count($members) > 1? 2 : 1;
        $thumbnail_url = Validate::validateCreateChatRoom($roomname, $members, $type, $thumbnail, $errors);
        if (!$thumbnail_url || !empty($errors)) {
            $result['roomID'] = 0;
            $result['errors'] = $errors;
            return $result;
        } else {
            //relative URL
            if (substr($thumbnail_url, 0, 1) == ".") {
                $thumbnail_url = substr($thumbnail_url, 8);
            } 
            if ($type == 1) {
                //If the room is 2-member room (type 1), check for duplicate
                $query = "SELECT R1.room FROM (SELECT cp.room FROM chat_room_participate cp, chat_rooms cr 
                WHERE cp.room = cr.ID AND cr.type = 1 AND cp.user = ?) R1 
                JOIN (SELECT cp.room FROM chat_room_participate cp, chat_rooms cr 
                WHERE cp.room = cr.ID AND cr.type = 1 AND cp.user = ?) R2 
                WHERE R1.room = R2.room";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("ii", $_SESSION['user_id'], $members[0]);
                $stmt->execute(); 
                $results = $stmt->get_result();
                if ($results->num_rows == 1) {
                    $row = $results->fetch_assoc();
                    $errors['duplicate_room'] = true;
                    $result['roomID'] = $row['room'];
                    $result['errors'] = $errors;
                    return $result;
                }
            }

            //Create chat room
            //Insert new record into database
            if ($type == 1) {
                $query = "INSERT INTO chat_rooms(type, room_name) VALUES (?, ?)";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("is", $type, $roomname);
            } else {
                $query = "INSERT INTO chat_rooms(type, room_name, thumbnail) VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("iss", $type, $roomname, $thumbnail_url);
            }
            $stmt->execute();
            if ($stmt->affected_rows == 1) {
                $get_id = $stmt->insert_id;
                array_push($members, $_SESSION['user_id']); //Add self to list of members
                foreach ($members as $member) {
                    $query = "INSERT INTO chat_room_participate(room, user) VALUES (?, ?)";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bind_param("ii", $get_id, $member);
                    $stmt->execute();
                    if ($stmt->affected_rows != 1) {
                        //Error occured
                        $query = "DELETE FROM chat_rooms WHERE ID = ?";
                        $stmt = $this->conn->prepare($query);
                        $stmt->bind_param("i", $get_id);
                        $stmt->execute();
                        $errors['execute_err'] = "Server error. Please try again later!";
                        $result['roomID'] = 0;
                        $result['errors'] = $errors;
                        return $result;
                    }
                }
                //Pass all the tests
                $result['roomID'] = $get_id;
                $result['errors'] = $errors;
                return $result;
            } else {
                $errors['execute_err'] = "Server error. Please try again later!";
                $result['roomID'] = 0;
                $result['errors'] = $errors;
                return $result;
            } 


        }
    }

    function validateImageMessage($file) {
        Validate::validateImageMessage($file);
    }

    function searchMembers($search) {
        $originalSearch = $search;
        $search = "%{$search}%";
        $query = "SELECT * FROM users u, profiles p WHERE u.ID = p.ID AND (u.username LIKE ? OR p.display_name LIKE ?) AND u.ID <> ?"; 
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $search, $search, $_SESSION['user_id']);
        $stmt->execute();
        $results = $stmt->get_result();
        $results = $results->fetch_all(MYSQLI_ASSOC);

        usort($results, function ($a, $b) use ($originalSearch) {
            similar_text($originalSearch, $a['display_name'], $percentA);
            similar_text($originalSearch, $b['display_name'], $percentB);
    
            return $percentA === $percentB ? 0 : ($percentA > $percentB ? -1 : 1);
        });

        return $results;
    }

    function getBasicInfo() {
        $query = "SELECT * FROM chat_rooms WHERE ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $results = $stmt->get_result();
        $rooms = $results->fetch_assoc();
        return $rooms;
    }

    function editChatRoom($roomname, $oldThumbnail, $thumbnail) {
        $result = [];
        $errors = [];
        $thumbnail_url = Validate::validateEditChatRoom($roomname, $oldThumbnail, $thumbnail, $errors);
        if (!empty($errors)) {
            $result['errors'] = $errors;
            return $result;
        } else {
            if (substr($thumbnail_url, 0, 1) == ".") {
                $thumbnail_url = substr($thumbnail_url, 8);
            } 
            $query = "UPDATE chat_rooms SET room_name = ?, thumbnail = ? WHERE ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssi", $roomname, $thumbnail_url, $this->id);
            $stmt->execute();
            if ($stmt->affected_rows == -1 || $stmt->errno > 0) {
                $errors['execute_err'] = "Server error. Please try again later!";
                $result['errors'] = $errors;
                return $result;
            } else {
                $result['errors'] = $errors;
                return $result;
            }
        }
    }

    function leaveRoom($user) {
        $query = "DELETE FROM chat_room_participate WHERE room = ? AND user = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $this->id, $user);
        $stmt->execute();
        if ($stmt->affected_rows == -1 || $stmt->errno > 0) {
            return false;
        } else {
            return true;
        }
    }

    function searchMemberAdd($search) {
        $originalSearch = $search;
        $search = "%{$search}%";
        $query = "SELECT * FROM users u, profiles p WHERE u.ID = p.ID AND (u.username LIKE ? OR p.display_name LIKE ?) AND 
        u.ID NOT IN (SELECT user FROM chat_room_participate WHERE room = ?)"; 
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $search, $search, $this->id);
        $stmt->execute();
        $results = $stmt->get_result();
        $results = $results->fetch_all(MYSQLI_ASSOC);

        usort($results, function ($a, $b) use ($originalSearch) {
            similar_text($originalSearch, $a['display_name'], $percentA);
            similar_text($originalSearch, $b['display_name'], $percentB);
    
            return $percentA === $percentB ? 0 : ($percentA > $percentB ? -1 : 1);
        });
        
        return $results;
    }

    function addMember($user) {
        $query = "INSERT INTO chat_room_participate(room, user) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $this->id, $user);
        $stmt->execute();
        if ($stmt->affected_rows != 1) {
            return false;
        }
        return true;
    }
}