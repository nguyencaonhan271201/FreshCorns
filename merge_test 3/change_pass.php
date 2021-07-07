<?php
    include "includes/header.php";  

    $errors = [];

    if (isset($_POST['submit']) && $_POST['csrf'] == $_SESSION['csrf_token']) {
        $this_user->changePassword($_POST, $errors, $db->conn, $_SESSION['username']);
    }

    if ($_SESSION['signed_in']) {
        $name = $_SESSION['name'];
        $profile_image = $_SESSION['profile_img'];
    }

    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
?>

<div id="main-container" class="container mt-5">
    <h1 class="text-center">Change password</h1>
    <?php if(!empty($errors)): ?>
        <div class="row">
            <div class="col-md-8 col-sm-12 col-12 offset-md-2 alert alert-danger mt-3" role="alert">
                Error occured. Please try again.
            </div>
        </div>
    <?php endif; ?>
    <div class="row mb-5 p-1">
        <div class="col-md-8 col-sm-12 col-12 offset-md-2 grey-form">
            <div class="col-6 offset-3 text-center">
                <img src=<?php echo $profile_image; ?> alt="" class="rounded-circle change-pass-profile-img mb-1"></img>
                <h5><?php echo $name; ?></h5>
            </div>
            <form method="post" action="change_pass.php">
                <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
                <div class="form-group">
                    <label for="new-password">New Password</label>
                    <input class="form-control" type="password" name="new-password" placeholder="New Password">
                    <p class="error"><?php if(isset($errors['new-password'])) {echo $errors['new-password'];}?></p>
                </div>
                <div class="form-group">
                    <label for="new-password-confirm">Confirmation</label>
                    <input class="form-control" type="password" name="new-password-confirm" placeholder="Confirmation">
                    <p class="error"><?php if(isset($errors['new-password-confirm'])) {echo $errors['new-password-confirm'];}?></p>
                </div>
                <button class="btn btn-dark btn-block" role="submit" name="submit">Change Password</button>
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