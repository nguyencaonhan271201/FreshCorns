<?php
    include "includes/header.php";  

    if (isset($_POST['submit']) && $_POST['csrf'] == $_SESSION['csrf_token']) {
        $this_user->CheckRegisterUser($_POST, $_FILES, $errors);
    }
    
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
?>

<div id="main-container" class="container mt-5">
    <h1 class="text-center">Sign Up</h1>
    <p class="text-center">Join Fresh Corns</p>
    <?php if(!empty($errors) && !isset($errors["execute_err"])):?>
        <div class="row">
            <div class="col-md-8 col-sm-12 col-12 offset-md-2 alert alert-danger mt-3" role="alert">
                Information for your new account is not valid. Please try again.
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
            <form method="post" action="signup.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
                <div class="form-group">
                    <label for="display">Display Name</label>
                    <input class="form-control" type="text" name="display" placeholder="Display Name" value="<?php
                        if (isset($_POST['display'])) {
                            echo $_POST['display'];
                        }
                    ?>" data-toggle="tooltip" data-placement="right" title="Display name must not be a blank string.">
                    <p class="error"><?php if(isset($errors['display'])) {echo $errors['display'];}?></p>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input class="form-control" type="email" name="email" placeholder="Email" value="<?php
                        if (isset($_POST['email'])) {
                            echo $_POST['email'];
                        }
                    ?>">
                    <p class="error"><?php if(isset($errors['email'])) {echo $errors['email'];}?></p>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input class="form-control data-tooltip" type="text" name="username" placeholder="Username" 
                    value="<?php
                        if (isset($_POST['username'])) {
                            echo $_POST['username'];
                        }
                    ?>" data-toggle="tooltip" data-placement="right" title="Username must be at least 6 characters.">
                    <p class="error"><?php if(isset($errors['username'])) {echo $errors['username'];}?></p>
                </div>
                <div class="form-group">
                    <label for="password1">Password</label>
                    <input class="form-control" type="password" name="password1" placeholder="Password"
                    data-toggle="tooltip" data-placement="right" title="Password length must be at least 6 characters.">
                    <p class="error"><?php if(isset($errors['password1'])) {echo $errors['password1'];}?></p>
                </div>
                <div class="form-group">
                    <label for="password2">Confirm Password</label>
                    <input class="form-control" type="password" name="password2" placeholder="Confirm Password">
                    <p class="error"><?php if(isset($errors['password2'])) {echo $errors['password2'];}?></p>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input class="form-control" type="date" name="dob" value="<?php 
                        if (isset($_POST['dob'])) {
                            echo $_POST['dob'];
                        }
                    ?>">
                    <p class="error"><?php if(isset($errors['dob'])) {echo $errors['dob'];}?></p>
                </div>
                <div class="form-group">
                    <label for="type">Gender</label>
                    <div class="form-check">
                        <input type="radio" name="gender" value="male" <?php 
                            if((isset($_POST['gender']) && $_POST['gender'] == 'male') || !isset($_POST['gender']))
                                echo "checked";
                        ?>>
                        <label class="form-check-label">
                            Male
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="gender" value="female" <?php 
                            if(isset($_POST['gender']) && $_POST['gender'] == 'female')
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
                <button class="btn btn-dark btn-block" role="submit" name="submit">Create Account</button>
            </form>
        </div>
    </div>
</div>

<script src="includes/js/loadFile.js"></script>

<?php
    include "includes/footer.php"; 
?>

<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>