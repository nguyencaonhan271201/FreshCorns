<?php
    include "includes/header.php";  

    $errors = [];

    if (isset($_POST['submit']) && $_POST['csrf'] == $_SESSION['csrf_token']) {
        $this_user->checkSignIn($_POST, $errors);
    }

    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
?>

<div id="main-container" class="container mt-5">
    <h1 class="text-center">Sign In</h1>
    <?php if(!empty($errors)): ?>
        <div class="row">
            <div class="col-md-8 col-sm-12 col-12 offset-md-2 alert alert-danger mt-3" role="alert">
                Sign in information is not correct. Please try again.
            </div>
        </div>
    <?php endif; ?>
    <div class="row mb-5 p-1">
        <div class="col-md-8 col-sm-12 col-12 offset-md-2 grey-form">
            <form method="post" action="signin.php">
                <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input class="form-control" type="text" name="username" placeholder="Username" value="<?php
                        if (isset($_POST['username'])) {
                            echo $_POST['username'];
                        }
                    ?>">
                    <p class="error"><?php if(isset($errors['username'])) {echo $errors['username'];}?></p>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input class="form-control" type="password" name="password" placeholder="Password">
                    <p class="error"><?php if(isset($errors['password'])) {echo $errors['password'];}?></p>
                </div>
                <button class="btn btn-dark btn-block" role="submit" name="submit">Sign In</button>
            </form>
        </div>
    </div>
</div>

<!-- <script type="text/javascript" src="includes/js/formAnimation.js"></script> -->

<?php
    include "includes/footer.php";

    // echo "<script>
    //     document.querySelector('footer').classList.add('fixed-bottom');
    // </script>";

    if (isset($_POST['submit'])) {
        // echo "<script>
        //     formAnimationCheck();
        // </script>";
    }
?>