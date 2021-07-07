<?php
class Profile {
    public $user_id;
    public $display_name;
    public $description;
    public $profile_image;
    public $profile_cover;
    public $email;
    public $date_of_birth;
    public $gender;
    public $info = [];

    public function __construct($user_id, $display_name, $description, $gender, $profile_image, $profile_cover, $email, $date_of_birth) {
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
                        echo "<script>window.location.href = 'feeds.php'</script>";
                    }
                    else{
                        header("Location: feeds.php");
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
}