<?php
    include "includes/header.php";  

    $errors = [];

    if (isset($_POST['submit']) && $_POST['csrf'] == $_SESSION['csrf_token']) {
        $this_user->checkSignIn($_POST, $errors);
    }

    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;

?>

<div class="container-fluid m-0 global-container">
    <div class="row">
        <div class="col-md-6 col-sm-12 sign-in-col-bg">
        </div>
        <div class="col-md-6 col-sm-12 d-flex align-items-center">
            <div class="col-md-8 offset-md-2 col-sm-10 offset-1 m-0">
                <span class="sign-in-logo"><?php echo file_get_contents("assets/images/logo.svg");?></span>
                <h2 class="mb-4 mt-4">Join <b>Fresh Corns</b> today.</h2>
                <form method="post" action="signin.php" class="sign-in-form">
                    <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
                    <div class="form-group">
                        <!-- <label for="username">Username</label> -->
                        <input class="form-control" type="text" name="username" placeholder="Username" value="<?php
                            if (isset($_POST['username'])) {
                                echo $_POST['username'];
                            }
                        ?>">
                        <p class="error"><?php 
                            if(isset($errors['username'])) {echo $errors['username'];}
                        ?>
                        </p>
                    </div>
                    <div class="form-group">
                        <!-- <label for="password">Password</label> -->
                        <input class="form-control" type="password" name="password" placeholder="Password">
                        <p class="error"><?php if(isset($errors['wrong_pass'])) {echo $errors['wrong_pass'];}?></p>
                    </div>
                    <button class="btn btn-primary btn-block" role="submit" name="submit">Sign In</button>
                    <?php if (isset($errors['execute_err'])): ?>
                        <p class="error mt-1"><?php echo $errors['execute_err'];?></p>
                    <?php endif; ?>
                    <hr>
                    <a class="btn btn-block btn-success" type="button" id="btn-create" data-toggle="modal" data-target="#registerBox">Create New Account</a>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="registerBox" tabindex="-1" role="dialog" aria-labelledby="registerBox" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Create your account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="signup.php" id="create-form" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
                    <div class="form-group">
                        <!-- <label for="display">Display Name</label> -->
                        <input class="form-control" type="text" name="create-display" placeholder="Display Name" value="<?php
                            if (isset($_POST['create-display'])) {
                                echo $_POST['create-display'];
                            }
                        ?>" data-toggle="tooltip" data-placement="right" title="Display name must not be a blank string.">
                        <p class="error" id="create-display-err"><?php if(isset($errors['create-display'])) {echo $errors['create-display'];}?></p>
                    </div>
                    <div class="form-group">
                        <!-- <label for="email">Email</label> -->
                        <input class="form-control" type="email" name="create-email" placeholder="Email" value="<?php
                            if (isset($_POST['create-email'])) {
                                echo $_POST['create-email'];
                            }
                        ?>">
                        <p class="error" id="create-email-err"><?php if(isset($errors['create-email'])) {echo $errors['create-email'];}?></p>
                    </div>
                    <div class="form-group">
                        <!-- <label for="username">Username</label> -->
                        <input class="form-control data-tooltip" type="text" name="create-username" placeholder="Username" 
                        value="<?php
                            if (isset($_POST['create-username'])) {
                                echo $_POST['create-username'];
                            }
                        ?>" data-toggle="tooltip" data-placement="right" title="Username must be at least 6 characters.">
                        <p class="error" id="create-username-err"><?php if(isset($errors['create-username'])) {echo $errors['create-username'];}?></p>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group mb-1">
                                <!-- <label for="password1">Password</label> -->
                                <input class="form-control" type="password" name="create-password1" placeholder="Password"
                                data-toggle="tooltip" data-placement="right" title="Password length must be at least 6 characters.">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group mb-1">
                                <!-- <label for="password2">Confirm Password</label> -->
                                <input class="form-control" type="password" name="create-password2" placeholder="Confirm Password">
                            </div>
                        </div>
                        <p class="error ml-3" id="create-password-err"></p>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input class="form-control" type="date" name="create-dob" value="<?php 
                            if (isset($_POST['create-dob'])) {
                                echo $_POST['create-dob'];
                            }
                        ?>">
                        <p class="error" id="create-dob-err"><?php if(isset($errors['dob'])) {echo $errors['dob'];}?></p>
                    </div>
                    <div class="form-group">
                        <label for="type">Gender</label>
                        <div class="form-check">
                            <input type="radio" name="create-gender" value="male" <?php 
                                if((isset($_POST['create-gender']) && $_POST['create-gender'] == 'male') || !isset($_POST['create-gender']))
                                    echo "checked";
                            ?>>
                            <label class="form-check-label">
                                Male
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="create-gender" value="female" <?php 
                                if(isset($_POST['create-gender']) && $_POST['create-gender'] == 'female')
                                    echo "checked";
                            ?>>
                            <label class="form-check-label">
                                Female
                            </label>
                        </div>
                        <p class="error" id="create-gender-err"><?php 
                            if(isset($errors['create-gender']))
                                echo $errors['create-gender'];
                        ?></p>
                    </div>
                    <p class="error mt-1" id="create-execute-err"></p>
                    <button class="btn btn-success btn-block" role="submit" name="submit">Create Account</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="includes/js/signIn.js"></script>
<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<?php
    //include "includes/footer.php";

    // echo "<script>
    //     document.querySelector('footer').classList.add('fixed-bottom');
    // </script>";

    if (isset($_POST['submit'])) {
        // echo "<script>
        //     formAnimationCheck();
        // </script>";
    }
?>