<?php
include "Profile.php";

class User {
    public $username;
    public $id;
    public $profile;
    public $date_created;
    public $friends = [];
    public $conn;

    public function __construct() {

    }

    public function setConn($conn) {
        $this->conn = $conn;
    }

    public function retrieveInfo() {
        $query = "SELECT username, date_created FROM users WHERE ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $info = $stmt->get_result()->fetch_assoc();
        if (!empty($info)) {
            $this->username = $info['username'];
            $this->date_created = $info['date_created'];
            $this->getProfile();
        }
    }

    public function getProfile() {
        $query = "SELECT display_name, email, date_of_birth, gender, IFNULL(description, '') AS description, profile_image, profile_cover
        FROM profiles WHERE ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if (!empty($result)) {
            $this->profile = new Profile(
                $this->id,
                $result['display_name'],
                $result['description'],
                $result['gender'],
                $result['profile_image'],
                $result['profile_cover'],
                $result['email'],
                $result['date_of_birth']
            );
        }
    }
}