<?php
class Validate {
    public static function validateProfileInput($POST, &$errors, $conn, $checkDuplicate = true) {
        $username = $POST['username'];
        $display = $POST['display'];
        $email = $POST['email'];

        $password1 = $POST['password1'];
        $password2 = $POST['password2'];
        
        //Check username
        if (strlen($username) < 6) {
            $errors["username"] = "Username must be at least 6 characters.";
        }

        //Check display name
        if (strlen(trim($display)) == 0 || !filter_var($display, FILTER_SANITIZE_STRING)) {
            $errors["display"] = "Display name is not valid.";
        }

        //Check email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Email is not valid.";
        }

        //Check password if password has value
        if (strlen($password1) < 6) {
            $errors["password1"] = "Password length must be at least 6 characters.";
        } else if ($password2 != $password1) {
            $errors["password2"] = "Password confirmation is wrong.";
        } else if (!filter_var($password1, FILTER_SANITIZE_STRING)) {
            $errors["password1"] = "Password is invalid.";
        }

        if (!isset($_POST['dob']) || $_POST['dob'] == null) {
            $errors["dob"] = "Date of birth is invalid.";
        }

        if (!isset($_POST['gender']) || !in_array($_POST['gender'], ['male', 'female'])) {
            $errors["gender"] = "Gender is invalid.";
        }
    }

    public static function validateProfileEdit($POST, &$errors, $conn) {
        $display= $POST['display'];
        $email = $POST['email'];
        $dob = $POST['dob'];
        $gender = $POST['gender'];
        $description = $POST['description'];
        
        //Check display name
        if (strlen(trim($display)) == 0 || !filter_var($display, FILTER_SANITIZE_STRING)) {
            $errors["display"] = "Display name is not valid.";
        }

        //Check email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Email is not valid.";
        }

        //Check gender
        if (!isset($_POST['gender']) || !in_array($_POST['gender'], ['male', 'female'])) {
            $errors["dob"] = "Gender is invalid.";
        }

        //Check description
        if (strlen(trim($description)) >= 255 || !filter_var($description, FILTER_SANITIZE_STRING)) {
            $errors["description"] = "Description is not valid.";
        }
    }

    public static function validateProfileImage($FILES, &$errors, $type) {
        switch ($type) {
            case 0:
                $file = $FILES['profile-image'];
                $string = "profile-image";
                break;
            case 1:
                $file = $FILES['cover-image'];
                $string = "cover-image";
                break;
        }


        $fname = $file['name'];
        $ftype = $file['type'];
        $ftmp = $file['tmp_name'];
        $ferr = $file['error'];
        $fsize = $file['size'];
        $allowed_ext = ['png', 'jpg', 'jpeg', '.bmp', '.PNG', '.JPG', '.JPEG', '.BMP'];

        //Check if there are errors on upload
        if ($ferr != 0) {
            $errors[$string] = "File error.";
            $profileErr = true;
        }

        //Check file type and extension
        $ftype = explode("/", $ftype);
        if ($ftype[0] != 'image' || !in_array(end($ftype), $allowed_ext)) {
            $errors[$string] = "Images only.";
            $profileErr = true;
        }

        //Check file size
        if ($fsize > 5242880) {
            $errors[$string] = "File is too large.";
            $profileErr = true;
        }

        //Error by upload constraints of server
        if ($file['error'] == 1) {
            $errors[$string] = "Upload error.";
            $profileErr = true;
        }

        if (!isset($profileErr)) {
            $newFilename = uniqid('', true) . "." . end($ftype);
            $get_folder = explode("-", $string);
            $dest = "assets/images/{$get_folder[0]}/" . $newFilename;
            if(move_uploaded_file($ftmp, $dest)) {
                return $dest;
            }
        }        
    }

    public static function validateImageMessage($file) {
        $fname = $file['name'];
        $ftype = $file['type'];
        $ftmp = $file['tmp_name'];
        $ferr = $file['error'];
        $fsize = $file['size'];
        $allowed_ext = ['png', 'jpg', 'jpeg', '.bmp', '.PNG', '.JPG', '.JPEG', '.BMP'];
        if ($ferr != 0) {
            echo json_encode(false);
            return;
        }

        //Check file type and extension
        $ftype = explode("/", $ftype);
        if ($ftype[0] != 'image' || !in_array(end($ftype), $allowed_ext)) {
            echo json_encode(false);
            return;
        }

        //Check file size
        if ($fsize > 5242880) {
            echo json_encode(false);
            return;
        }

        //Passed all the test
        $newFilename = uniqid('', true) . "." . end($ftype);
        $dest = "./../../assets/images/messages/" . $newFilename;
        if(move_uploaded_file($ftmp, $dest)) {
            echo $dest;
        }
    }

    public static function validateCreateChatRoom($roomname, $members, $roomtype, $thumbnail, &$errors) {
        //Check room name
        if ($roomtype == 2 && (!filter_var($roomname, FILTER_SANITIZE_STRING) || (strlen(trim($roomname)) == 0 ))) {
            $errors["roomname"] = "Room name is not valid.";
        }

        if (!$thumbnail) {
            return "https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/chat%2Bicon-1320184411998302345.png?alt=media&token=6fa148a8-101f-41dd-ba38-c803e7fabbb5";
        } else {
            //Check thumbnail
            $fname = $thumbnail['name'];
            $ftype = $thumbnail['type'];
            $ftmp = $thumbnail['tmp_name'];
            $ferr = $thumbnail['error'];
            $fsize = $thumbnail['size'];
            $allowed_ext = ['png', 'jpg', 'jpeg', '.bmp', '.PNG', '.JPG', '.JPEG', '.BMP'];
            if ($ferr != 0) {
                $errors["thumbnail"] = "Thumbnail is invalid!";
                return false;
            }

            //Check file type and extension
            $ftype = explode("/", $ftype);
            if ($ftype[0] != 'image' || !in_array(end($ftype), $allowed_ext)) {
                $errors["thumbnail"] = "Thumbnail file type is not supported!";
                return false;
            }

            //Check file size
            if ($fsize > 5242880) {
                $errors["thumbnail"] = "Thumbnail surpassed size limit";
                return false;
            }

            //Passed all the test
            $newFilename = uniqid('', true) . "." . end($ftype);
            $dest = "./../../assets/images/chat_room_thumbnails/" . $newFilename;
            if(move_uploaded_file($ftmp, $dest)) {
                return $dest;
            }
        }
    }

    public static function validateEditChatRoom($roomname, $old_thumbnail, $thumbnail, &$errors) {
        //Check room name
        if (!filter_var($roomname, FILTER_SANITIZE_STRING) || (strlen(trim($roomname)) == 0 )) {
            $errors["roomname"] = "Room name is not valid.";
        }

        if (!$thumbnail) {
            return $old_thumbnail;
        } else {
            //Check thumbnail
            $fname = $thumbnail['name'];
            $ftype = $thumbnail['type'];
            $ftmp = $thumbnail['tmp_name'];
            $ferr = $thumbnail['error'];
            $fsize = $thumbnail['size'];
            $allowed_ext = ['png', 'jpg', 'jpeg', '.bmp', '.PNG', '.JPG', '.JPEG', '.BMP'];
            if ($ferr != 0) {
                $errors["thumbnail"] = "Thumbnail is invalid!";
                return false;
            }

            //Check file type and extension
            $ftype = explode("/", $ftype);
            if ($ftype[0] != 'image' || !in_array(end($ftype), $allowed_ext)) {
                $errors["thumbnail"] = "Thumbnail file type is not supported!";
                return false;
            }

            //Check file size
            if ($fsize > 5242880) {
                $errors["thumbnail"] = "Thumbnail surpassed size limit";
                return false;
            }

            //Passed all the test
            $newFilename = uniqid('', true) . "." . end($ftype);
            $dest = "./../../assets/images/chat_room_thumbnails/" . $newFilename;
            if(move_uploaded_file($ftmp, $dest)) {
                return $dest;
            }
        }
    }

    public static function validatePost($POST, $FILE, &$image, &$errors) {
        //Validate post content
        if (!isset($POST['post-type']) || $POST['post-type'] < 0 || $POST['post-type'] > 2) {
            $errors["post-type"] = "Post mode is not valid";
        }
        
        $post_content = $POST['post_content'];
        //Check content
        if (strlen(trim($post_content)) == 0 || !filter_var($post_content, FILTER_SANITIZE_STRING)) {
            $errors["post-content"] = "Post content is not valid";
        }

        if (empty($errors)) {
            //Validate Image
            $file = $FILE['post-image'];
            $fname = $file['name'];
            $ftype = $file['type'];
            $ftmp = $file['tmp_name'];
            $ferr = $file['error'];
            $fsize = $file['size'];
            $allowed_ext = ['png', 'jpg', 'jpeg', '.bmp', '.PNG', '.JPG', '.JPEG', '.BMP'];

            if ($ftmp == "" && $ferr == 0 || $ftmp == "" && $ferr == 4)
                return;

            if ($ftmp == "" && $ferr > 0) {
                $errors["post-image"] = "File error.";
                $profileErr = true;
            }
    
            //Check if there are errors on upload
            if ($ferr != 0) {
                $errors["post-image"] = "File error.";
                $profileErr = true;
            }
    
            //Check file type and extension
            $ftype = explode("/", $ftype);
            if ($ftype[0] != 'image' || !in_array(end($ftype), $allowed_ext)) {
                $errors["post-image"] = "Images only.";
                $profileErr = true;
            }
    
            //Check file size
            if ($fsize > 5242880) {
                $errors["post-image"] = "File is too large.";
                $profileErr = true;
            }
    
            //Error by upload constraints of server
            if ($file['error'] == 1) {
                $errors["post-image"] = "Upload error.";
                $profileErr = true;
            }
    
            if (!isset($profileErr)) {
                $newFilename = uniqid('', true) . "." . end($ftype);
                $dest = "assets/images/posts/" . $newFilename;
                if(move_uploaded_file($ftmp, $dest)) {
                    $image = $dest;
                }
            }        
        }
    }

    public static function validateEditPost($POST, $FILE, &$image, &$errors) {
        //Validate post content
        if (!isset($POST['edit-post-type']) || $POST['edit-post-type'] < 0 || $POST['edit-post-type'] > 2) {
            $errors["edit-post-type"] = "Post mode is not valid";
        }
        
        $post_content = $POST['edit_post_content'];
        //Check content
        if (strlen(trim($post_content)) == 0 || !filter_var($post_content, FILTER_SANITIZE_STRING)) {
            $errors["edit-post-content"] = "Post content is not valid";
        }

        $image = $POST['post-old-image'];

        if (empty($errors)) {
            //Validate Image
            $file = $FILE['edit-post-image'];
            $fname = $file['name'];
            $ftype = $file['type'];
            $ftmp = $file['tmp_name'];
            $ferr = $file['error'];
            $fsize = $file['size'];
            $allowed_ext = ['png', 'jpg', 'jpeg', '.bmp', '.PNG', '.JPG', '.JPEG', '.BMP'];

            var_dump($file);

            if ($ftmp == "" && $fsize == 0)
                return;

            if ($ftmp == "" && $ferr > 0) {
                $errors["edit-post-image"] = "File error.";
                $profileErr = true;
                return;
            }
    
            //Check if there are errors on upload
            if ($ferr != 0) {
                $errors["edit-post-image"] = "File error.";
                $profileErr = true;
                return;
            }
    
            //Check file type and extension
            $ftype = explode("/", $ftype);
            if ($ftype[0] != 'image' || !in_array(end($ftype), $allowed_ext)) {
                $errors["edit-post-image"] = "Images only.";
                $profileErr = true;
                return;
            }
    
            //Check file size
            if ($fsize > 5242880) {
                $errors["edit-post-image"] = "File is too large.";
                $profileErr = true;
                return;
            }
    
            //Error by upload constraints of server
            if ($file['error'] == 1) {
                $errors["edit-post-image"] = "Upload error.";
                $profileErr = true;
                return;
            }
    
            if (!isset($profileErr)) {
                $newFilename = uniqid('', true) . "." . end($ftype);
                $dest = "assets/images/posts/" . $newFilename;
                if(move_uploaded_file($ftmp, $dest)) {
                    $image = $dest;
                }
            }        
        }
    }
}