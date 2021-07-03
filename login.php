<?php
include 'include/header.php';
include 'user.php';



if (isset($_POST['create-account'])){
  $user_name = $_POST['username'];
  $user_email = $_POST['email'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm-password'];

  $user = new User($conn);
  $user->checkNewUser($user_name,$user_email,$password,$confirm_password );
  $errors = $user->errors;
}

if(isset($_POST['login'])) {
  $user_name = $_POST['username'];
  $user_password = $_POST['password'];
  $user = new User($conn);
  $user->checkLogin($user_name, $user_password);
  $errors = $user->errors;
}

 ?>

 <div class="container mt-5">
   <?php if (isset($errors) && !empty($errors)): ?>
     <div class="alert alert-danger" role="alert">
       <?php foreach ($errors as $error): ?>
         <?php echo $error . "</br>"; ?>
       <?php endforeach; ?>
     </div>
   <?php endif; ?>
   <div class="row">
     <div class="col-md-6">
       <h3>  Create Account</h3>
        <form class="" action="login.php" method="post">
          <label for="username">Username</label>
          <input type="text" name="username" class="form-control" placeholder="Your Name...">
          <p class="error error-username"></p>
          <label for="email">Email</label>
          <input type="email" name="email" class="form-control" placeholder="Your Email">
          <p class="error error-email"></p>
          <label for="password">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Your Password">
          <label for="confirm-password">Confrim Password</label>
          <input type="password" name="confirm-password" class="form-control" placeholder="Password again">
          <p class="error error-password"></p>
         <button type="submit" name="create-account" style="color:white; background-image: linear-gradient(to bottom right,#1B9CFC,#55E6C1);" class="btn btn-block"><i class="fa fa-plus"></i> Create Account</button>  </form>

     </div>
     <div class="col-md-6">
       <h3>  Login</h3>
       <form class="" action="login.php" method="post">
         <label for="username">Username</label>
         <input type="text" name="username" class="form-control" placeholder="Enter your name...">
         <p class="error error-username"></p>
         <label for="password">Password</label>
         <input type="password" name="password" class="form-control" placeholder="...">
         <p class="error error-password"></p>
          <button type="submit" name="login" style="color:white; background-image: linear-gradient(to bottom right,#1B9CFC,#55E6C1);" class="btn btn-block"><i class="fa fa-user"></i> Login</button>
       </form>
     </div>
   </div>
 </div>

 <?php
 include 'include/footer.php';
  ?>
