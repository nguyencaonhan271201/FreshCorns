<?php
class Profile {
    public $user_id;
    public $display_name;
    public $description;
    public $profile_image;
    public $profile_cover;
    public $email;
    public $date_of_birth;
    public $date_joined;
    public $gender;
    public $info = [];
    public $followers = [];
    public $following = [];
    public $conn;

    public function __construct($conn, $user_id) {
        $this->conn = $conn;
        $sql = "SELECT display_name, email, date_of_birth, gender, IFNULL(description, '') AS description, profile_image, profile_cover,
        (SELECT date_created FROM users WHERE users.ID = profiles.ID) AS date_joined FROM profiles WHERE ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $results = $stmt->get_result();
        if ($results->num_rows == 1) {
            $results = $results->fetch_assoc();
            $this->user_id = $user_id;
            $this->display_name = $results['display_name'];
            $this->description = $results['description'];
            $this->profile_image = $results['profile_image'];
            $this->profile_cover = $results['profile_cover'];
            $this->email = $results['email'];
            $this->date_of_birth = $results['date_of_birth'];
            $this->date_joined = $results['date_joined'];
        }
    }

    public function updateInfo($user_id, $display_name, $description, $gender, $profile_image, $profile_cover, $email, $date_of_birth) {
        $this->user_id = $user_id;
        $this->display_name = $display_name;
        $this->description = $description;
        $this->profile_image = $profile_image;
        $this->profile_cover = $profile_cover;
        $this->gender = $gender;
        $this->email = $email;
        $this->date_of_birth = $date_of_birth;
    }

    public function getJobEducationInfo() {
        
    }

    public function updateUserInfo($conn) {
        $query = "UPDATE profiles 
                SET display_name = ?, email = ?, date_of_birth = ?, gender = ?, description = ?, profile_image = ?,
                profile_cover = ? WHERE ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssi", $this->display_name, $this->email, $this->date_of_birth, $this->gender,
        $this->description, $this->profile_image, $this->profile_cover, $this->user_id);
        $stmt->execute();
        return $stmt; 
    }

    public function checkEditProfile($POST, $FILES, &$errors, $conn, &$profile_image, &$cover_image) {
        if (!isset($POST['update'])) {
            $profile_image = $this->profile_image;
            $cover_image = $this->profile_cover;
            return [
                "display" => $this->display_name, 
                "email" => $this->email, 
                "dob" => $this->date_of_birth,
                "gender" => $this->gender,
                "description" => $this->description,
                "profile_image" => $this->profile_image,
                "profile_cover" => $this->profile_cover,
            ];
        } else if ($POST['csrf'] == $_SESSION['csrf_token']) {
            $display= $POST['display'];
            $email = $POST['email'];
            $dob = $POST['dob'];
            $gender = $POST['gender'];
            $description = $POST['description'];
            
            $get_params = explode("|", $POST['update']);
            $profile_image = $get_params[0];
            $cover_image = $get_params[1];

            Validate::validateProfileEdit($POST, $errors, $conn);

            if ($FILES['profile-image']['tmp_name'] != '') {
                $profile_image = Validate::validateProfileImage($FILES, $errors, 0);
            } elseif ($FILES['profile-image']['tmp_name'] == '' && $profile_image == '') {
                $profile_image = "https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/istockphoto-1223671392-612x612.jpg?alt=media&token=e9312c19-c34e-4a87-9a72-552532766cde"; //Default
            } elseif ($FILES['profile-image']['error'] == 1) {
                $errors['profile-image'] = "Upload error.";
            }

            if ($FILES['cover-image']['tmp_name'] != '') {
                $cover_image = Validate::validateProfileImage($FILES, $errors, 1);
            } elseif ($FILES['cover-image']['tmp_name'] == '' && $cover_image == '') {
                $cover_image = "https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/fbdefault.png?alt=media&token=7248bbab-cc3e-420e-8a94-4c3b23a5f0f6"; //Default
            } elseif ($FILES['cover-image']['error'] == 1) {
                $errors['cover-image'] = "Upload error.";
            }

            //Pass all the tests
            if (empty($errors)) {
                //Everything is good. Update user information
                $this->display_name = $display;
                $this->email = $email;
                $this->date_of_birth = $dob;
                $this->gender = $gender;
                $this->description = $description;
                $this->profile_image = $profile_image;
                $this->profile_cover = $cover_image;
                $stmt = $this->updateUserInfo($conn);
    
                //Check execute result
                if ($stmt->affected_rows == -1 || $stmt->errno > 0) {
                    $errors['execute_err'] = "Server error. Please try again later!";
                } else {
                    $_SESSION['name'] = $display;
                    $_SESSION['profile_img'] = $profile_image;
                    if (headers_sent()) {
                        echo "<script>window.location.href = 'profile.php'</script>";
                    }
                    else{
                        header("Location: profile.php");
                    }
                }
            }
        }
        return [
            "display" => $this->display_name, 
            "email" => $this->email, 
            "dob" => $this->date_of_birth,
            "gender" => $this->gender,
            "description" => $this->description,
            "profile_image" => $this->profile_image,
            "profile_cover" => $this->profile_cover,
        ];
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

    public function addRelationship($user_id) {
        $sql = "SELECT * FROM relationships WHERE user1 = ? AND user2 = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii",$user_id, $this->user_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows == 0) {
            $sql = "INSERT INTO relationships(`user1`, `user2`) VALUES (?,?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii",$user_id, $this->user_id);
            $stmt->execute();
            if ($stmt->affected_rows == 1){
                if (headers_sent()) {
                    echo "<script>window.location.href = 'profile.php?user_id={$this->user_id}';</script>";
                }
                else{
                    header("Location:profile.php?user_id=".$this->user_id);
                }
            }
            else return false;
        }
        else 
        {
            return false;
        }
    }

    public function deleteRelationship($user_id){
        $sql = "DELETE FROM relationships WHERE user1 = ? AND user2 = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii",$user_id, $this->user_id);
        $stmt->execute();
        if ($stmt->affected_rows == 1){
            if (headers_sent()) {
                echo "<script>window.location.href = 'profile.php?user_id={$this->user_id}';</script>";
            }
            else{
                header("Location:profile.php?user_id=".$this->user_id);
            }
        }
        else return false;
    }

    public function checkInfo($POST, &$errors) {
        //Get input
        $info = $POST['info'];
        $start = $POST['start'];
        $end = $POST['end'];
        
        //Check info
        if ($info == "" || !filter_var($info, FILTER_SANITIZE_STRING)) {
            $errors["info"] = "Information is not valid.";
        }

        //Check type
        if (!isset($_POST['type']) || !in_array($_POST['type'], [0, 1])) {
            $errors["type"] = "Info type is not valid.";
        } else {
            $type = $_POST['type'];
        }

        //Check start year
        if ($start < 1900 || !filter_var($start, FILTER_VALIDATE_INT)) {
            $errors["year"] = "Start year is not valid.";
        }

        //Check end year
        if ($end != "") {
            if ($end < 1900 || !filter_var($end, FILTER_VALIDATE_INT)) {
                $errors["year"] = "End year is not valid.";
            }
        }

        if (empty($errors)) {
            $query = "INSERT INTO job_education_info(user_profile, info, type, start_year, end_year) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $check_end = $end == "" ? NULL : $end;
            $stmt->bind_param("isiii", $_SESSION['user_id'], $info, $type, $start, $check_end);
            $stmt->execute();
            if ($stmt->affected_rows != 1) {
                $errors['execute_err'] = "Server error. Please try again later!";
            }
        }
    }

    public static function getInfo($conn, $user) {
        $sql = "SELECT * FROM job_education_info WHERE user_profile = ? ORDER BY start_year DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user);
        $stmt->execute();
        $results = $stmt->get_result();
        return $results->fetch_all(MYSQLI_ASSOC);
    }

    public function getSingleInfo($id) {
        $sql = "SELECT * FROM job_education_info WHERE ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $results = $stmt->get_result();
        if ($results->num_rows == 1) {
            return $results->fetch_assoc();
        } else return [];
    }

    public function editInfo($POST, &$errors) {
        //Get input
        $info = $POST['info'];
        $start = $POST['start'];
        $end = $POST['end'];

        $id = $POST['id'];
        
        //Check info
        if ($info == "" || !filter_var($info, FILTER_SANITIZE_STRING)) {
            $errors["info"] = "Information is not valid.";
        }

        //Check type
        if (!isset($_POST['type']) || !in_array($_POST['type'], [0, 1])) {
            $errors["type"] = "Info type is not valid.";
        } else {
            $type = $_POST['type'];
        }

        //Check start year
        if ($start < 1900 || !filter_var($start, FILTER_VALIDATE_INT)) {
            $errors["year"] = "Start year is not valid.";
        }

        //Check end year
        if ($end != "") {
            if ($end < 1900 || !filter_var($end, FILTER_VALIDATE_INT)) {
                $errors["year"] = "End year is not valid.";
            }
        }

        if (empty($errors)) {
            $query = "UPDATE job_education_info SET info = ?, type = ?, start_year = ?, end_year = ? WHERE ID = ?";
            $stmt = $this->conn->prepare($query);
            $check_end = $end == "" ? NULL : $end;
            $stmt->bind_param("siiii", $info, $type, $start, $check_end, $id);
            $stmt->execute();
            if ($stmt->affected_rows == -1 || $stmt->errno > 0) {
                $errors['execute_err'] = "Server error. Please try again later!";
            }
        }
    }

    public function deleteInfo($id, &$errors) {
        $sql = "DELETE FROM job_education_info WHERE ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->affected_rows != 1) {
            $errors['execute_err'] = "Server error. Please try again later!";
        }
    }
}