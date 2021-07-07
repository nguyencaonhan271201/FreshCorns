<?php
    include "includes/header.php";  
    if (!$_SESSION['signed_in']) {
        if (headers_sent()) {
            echo "<script>window.location.href = 'feeds.php';</script>";
        }
        else{
            header("Location: feeds.php?");
        }
    }

    $errors = [];
    $profile_image = ""; //Variable to keep track of the old profile image in case the user do not update profile image
    $cover_image = ""; //Variable to keep track of the old cover image in case the user do not update profile image
    $info = []; //To gather info back and display inside inputs

    $info = $this_user->checkEditProfile($_POST, $_FILES, $errors, $profile_image, $cover_image);

    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
?>

<div id="main-container" class="container mt-5">
<h1 class="text-center">Edit Profile</h1>
    <?php if(!empty($errors) && !isset($errors["execute_err"])):?>
        <div class="row">
            <div class="col-md-8 col-sm-12 col-12 offset-md-2 alert alert-danger mt-3" role="alert">
                Information for your account is not valid. Please try again.
            </div>
        </div>
        <?php elseif(isset($errors["execute_err"])): ?>
        <div class="row">
            <div class="col-md-8 col-sm-12 col-12 offset-md-2 alert alert-danger mt-3" role="alert">
                <?php echo $errors["execute_err"]; ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="row mb-5 p-1">
        <div class="col-md-8 col-sm-12 col-12 offset-md-2 grey-form">
            <form method="post" action="edit_profile.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
                <div class="form-group">
                    <label for="display">Display Name</label>
                    <input class="form-control" type="text" name="display" placeholder="Display Name" value="<?php
                        if (isset($info['display'])) {
                            echo $info['display'];
                        }
                    ?>" data-toggle="tooltip" data-placement="right" title="Display name must not be a blank string.">
                    <p class="error"><?php if(isset($errors['display'])) {echo $errors['display'];}?></p>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input class="form-control" type="email" name="email" placeholder="Email" value="<?php
                        if (isset($info['email'])) {
                            echo $info['email'];
                        }
                    ?>">
                    <p class="error"><?php if(isset($errors['email'])) {echo $errors['email'];}?></p>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input class="form-control" type="date" name="dob" value="<?php 
                        if (isset($info['dob'])) {
                            echo $info['dob'];
                        }
                    ?>">
                    <p class="error"><?php if(isset($errors['dob'])) {echo $errors['dob'];}?></p>
                </div>
                <div class="form-group">
                    <label for="type">Gender</label>
                    <div class="form-check">
                        <input type="radio" name="gender" value="male" <?php 
                            if((isset($info['gender']) && $info['gender'] == 'male') || !isset($info['gender']))
                                echo "checked";
                        ?>>
                        <label class="form-check-label">
                            Male
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="gender" value="female" <?php 
                            if(isset($info['gender']) && $info['gender'] == 'female')
                                echo "checked";
                        ?>>
                        <label class="form-check-label">
                            Female
                        </label>
                    </div>
                    <p class="error"><?php 
                        if(isset($errors['gender']))
                            echo $errors['gender'];
                    ?></p>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description"><?php if (isset($info['description'])) {echo $info['description'];}?></textarea>
                    <p class="error"><?php if(isset($errors['description'])) {echo $errors['description'];}?></p>
                </div>
                <div class="form-group">
                    <label for="image">Profile Image <em>(optional)</em></label>
                    <input id="image-choose" class="form-control" type="file" name="profile-image" 
                    accept="image/*" onchange="loadFile(event, 0)">
                    <small class="form-text text-muted">
                        A default profile image will be used in case you do not use your own. Maximum size allowed: 5MB
                    </small>
                    <div class="review-group-profile mt-2 text-center">
                        <?php if(!isset($profile_image) || $profile_image == ""): ?>
                            <img id="review-profile-image" src="https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/istockphoto-1223671392-612x612.jpg?alt=media&token=e9312c19-c34e-4a87-9a72-552532766cde">
                        <?php else: ?>
                            <img id="review-profile-image" src="<?php echo $profile_image; ?>">
                        <?php endif; ?>
                    </div>
                    <p class="error"><?php if(isset($errors['profile-image'])) {echo $errors['profile-image'];}?></p>
                </div>
                <div class="form-group">
                    <label for="image">Cover Image <em>(optional)</em></label>
                    <input id="image-choose" class="form-control" type="file" name="cover-image"
                    accept="image/*" onchange="loadFile(event, 1)">
                    <small class="form-text text-muted">
                        A default cover image will be used in case you do not use your own. Maximum size allowed: 5MB
                    </small>
                    <div class="review-group-cover mt-2 text-center">
                        <?php if(!isset($cover_image) || $cover_image == ""): ?>
                            <img id="review-cover-image" src="https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/fbdefault.png?alt=media&token=7248bbab-cc3e-420e-8a94-4c3b23a5f0f6">
                        <?php else: ?>
                            <img id="review-cover-image" src="<?php echo $cover_image; ?>">
                        <?php endif; ?>
                    </div>
                    <p class="error"><?php if(isset($errors['cover-image'])) {echo $errors['cover-image'];}?></p>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12 col-12">
                        <button id="create" class="btn btn-info btn-block" role="submit" name="update" value="<?php
                            if (isset($profile_image) && isset($cover_image)) {
                                echo $profile_image . '|' . $cover_image;
                            }
                        ?>">Update Profile</button>
                    </div>
                    <div class="col-md-6 col-sm-12 col-12">
                        <a class="btn btn-danger btn-block" role="button" href="feeds.php">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="includes/js/loadFile.js"></script>
<script>
    function changePasswordTicked(e) {
        let passwordBox = document.querySelector("#password1");
        let passwordConfirm = document.querySelector("#password2");
        passwordBox.value = "";
        passwordConfirm.value = "";
        passwordBox.readOnly = !e.target.checked;
        passwordConfirm.readOnly = !e.target.checked;
    }
</script>

<?php
    include "includes/footer.php";  
?>