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
        // public $info = new Infor[];
    
        public function __construct($conn, $user_id = null) {
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
            if($results->num_rows != 0) {
                $results = $results->fetch_all(MYSQLI_ASSOC);
                //var_dump($results);
                foreach ($results as $user_id) {
                    array_push($this->followers, $user_id['user1']);
                }
              }
            return 0;
        }

        //$user_id is user1 ($_SESSION['user_id']); $this->user_id is user2
        //follow
        public function addRelationship($user_id){
            $sql = "INSERT INTO relationships(`user1`, `user2`) VALUES (?,?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii",$user_id, $this->user_id);
            $stmt->execute();
            if ($stmt->affected_rows == 1){
                header("Location:profile.php?user_id=".$this->user_id);
            }
        }

        public function deleteRelationship($user_id){
            $sql = "DELETE FROM relationships WHERE user1 = ? AND user2 = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii",$user_id, $this->user_id);
            $stmt->execute();
            if ($stmt->affected_rows == 1){
                header("Location:profile.php?user_id=".$this->user_id);
            }
        }

    }
?>