<?php

class ThisUser extends User {
    private static $instances = [];
    
    public static function getInstance() : ThisUser
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    public function checkSignIn($POST, &$errors) {
        $username = $POST['username'];
        $password = $POST['password'];

        //Check username
        if ($username == "") {
            $errors["username"] = "This field cannot be empty.";
        }

        //Check password
        if ($password == "") {
            $errors["password"] = "This field cannot be empty.";
        }

        //Pass all the tests
        if (empty($errors)) {
            $query = "SELECT ID, username, password FROM users WHERE username = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $results = $stmt->get_result();
            //Check
            if ($results->num_rows == 0) {
                $errors["no_result"] = true;
            } else {
                $row = $results->fetch_assoc();
                //Valid sign in information
                if (!password_verify($password, $row['password'])) {
                    $errors["wrong_pass"] = "Password is not correct.";
                } else {
                    $this->id = $row['ID'];
                    $this->retrieveInfo();
                    //Sign in allowed
                    $_SESSION['signed_in'] = true;
                    $_SESSION['username'] = $this->username;
                    $_SESSION['name'] = $this->profile->display_name;
                    $_SESSION['user_id'] = $this->id;
                    $_SESSION['profile_img'] = $this->profile->profile_image;
                    if (headers_sent()) {
                        echo "<script>window.location.href = 'index.php';</script>";
                    }
                    else{
                        header("Location: index.php");
                    }
                }
            }
        }
    }

    public function registerUser($username, $hash, $display, $email, $dob, $gender, &$errors) {
        $query = "INSERT INTO users(username, password) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $username, $hash);
        $stmt->execute();
        if ($stmt->affected_rows == 1) {
            $get_id = $stmt->insert_id;
            $query = "INSERT INTO profiles(ID, display_name, email, date_of_birth, gender) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("issss", $get_id, $display, $email, $dob, $gender);
            $stmt->execute();
            if ($stmt->affected_rows == 1) {
                $this->id = $get_id;
                $this->retrieveInfo();
                if (headers_sent()) {
                    echo "<script>window.location.href = 'index.php';</script>";
                }
                else{
                    header("Location: index.php");
                }
            } else {
                $query = "DELETE FROM profiles WHERE ID = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("i", $get_id);
                $stmt->execute();
                $errors['execute_err'] = "Server error. Please try again later!";
            }
        } else {
            $errors['execute_err'] = "Server error. Please try again later!";
        } 
    }

    public function checkRegisterUser($POST, $FILE, &$errors) {
        Validate::validateProfileInput($POST, $errors, $this->conn, true);
        //Passed all the tests
        if (empty($errors)) {
            //Everything is good. Register user
            $username = $POST['username'];
            $display = $POST['display'];
            $email = $POST['email'];
            $password1 = $POST['password1'];
            $hash = password_hash($password1, PASSWORD_DEFAULT);
            $dob = $POST['dob'];
            $gender = $POST['gender'];

            $this->registerUser($username, $hash, $display, $email, $dob, $gender, $errors);
        }
    }

    public function checkEditProfile($POST, $FILES, &$errors, &$profile_image, &$cover_image) {
        $this->id = $_SESSION['user_id'];
        $this->getProfile();
        return $this->profile->checkEditProfile($POST, $FILES, $errors, $this->conn, $profile_image, $cover_image);
    }

    public function changePassword($POST, &$errors, $conn, $username) {
        $password1 = $POST['new-password'];
        $password2 = $POST['new-password-confirm'];

        if (strlen($password1) < 6) {
            $errors["new-password"] = "Password length must be at least 6 characters.";
        } else if ($password2 != $password1) {
            $errors["new-password-confirm"] = "Password confirmation is wrong.";
        } else if (!filter_var($password1, FILTER_SANITIZE_STRING)) {
            $errors["new-password"] = "New password is invalid.";
        }

        if (empty($errors)) {
            $hash = password_hash($password1, PASSWORD_DEFAULT);
            $query = "UPDATE users SET password = ? WHERE username = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $hash, $username);
            $stmt->execute();
            
            if ($stmt->affected_rows != -1 && $stmt->errno == 0) {
                if (headers_sent()) {
                    echo "<script>window.location.href = 'index.php'</script>";
                }
                else{
                    header("Location: index.php");
                }
            } else {
                $errors['execute_err'] = true;
            }
        }
    }
}