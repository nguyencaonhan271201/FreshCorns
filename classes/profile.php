<?php
    class Profile{
        public $display_name;
        public $description;
        public $profile_img;
        public $profile_cover;
        public $email;
        public $date_of_birth;
        public $user_id;
        public $conn;
        public $followers = [];
        public $following = [];
        // public $info = Infor[];
    
        public function __construct($conn, $user_id) {
            $this->conn = $conn;
            $sql = "SELECT * FROM profiles WHERE ID = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $results = $stmt->get_result();
            if($results->num_rows == 1) {
                $results = $results->fetch_assoc();
                $this->user_id = $results['ID'];
                $this->display_name = $results['display_name'];
                $this->description = $results['description'];
                $this->profile_img = $results['profile_image'];
                $this->profile_cover = $results['profile_cover'];
                $this->email = $results['email'];
                $this->date_of_birth = $results['date_of_birth'];
              }


          }

        // public function Profile($conn, $user_id){
        //     $this->conn = $conn;
        //     $sql = "SELECT * FROM Profiles WHERE ID = ?";
        //     $stmt = $this->conn->prepare($sql);
        //     $stmt->bind_param("i", $user_id);
        //     $stmt->execute();
        //     $results = $stmt->get_result();
        //     if($results->num_rows == 1) {
        //         $results = $results->fetch_assoc();
        //       }
        //     $this->user_id = $results['ID'];
        //     $this->display_name = $results['display_name'];
        //     $this->description = $results['description'];
        //     $this->profile_img = $results['profile_img'];
        //     $this->profile_cover = $results['profile_cover'];
        //     $this->email = $results['email'];
        //     $this->date_of_birth = $results['date_of_birth'];
        // }

        public function getProfile($user_id){
            $sql = "SELECT * FROM profiles WHERE ID = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $results = $stmt->get_result();
            if($results->num_rows == 1) {
                $results = $results->fetch_assoc();
              }
            return $results;
        }

        public function editProfile($display_name, $description, $profile_img, $profile_cover, $email, $date_of_birth){

        }

        public function getFollowing(){
            $sql = "SELECT * FROM relationships WHERE user1 = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $this->user_id);
            $stmt->execute();
            $results = $stmt->get_result();
            if($results->num_rows != 0) {
                $results = $results->fetch_all(MYSQLI_ASSOC);
                foreach ($results as $user_id) {
                    array_push($this->following, $user_id['user2']);
                }
                
              }
            return 0;
        }

        public function getFollowers(){
            $sql = "SELECT * FROM relationships WHERE user2 = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $this->user_id);
            $stmt->execute();
            $results = $stmt->get_result();
            //var_dump($results);
            if($results->num_rows != 0) {
                $results = $results->fetch_all(MYSQLI_ASSOC);
                foreach ($results as $user_id) {
                    array_push($this->followers, $user_id['user1']);
                }
              }
            return 0;
        }

    }
?>