<?php

@include 'config.php';

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = md5($_POST['password']);
   $cpass = md5($_POST['cpassword']);
   $user_type = $_POST['user_type'];

   $select = " SELECT * FROM user_form WHERE email = '$email' && password = '$pass' ";

   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){

      $error[] = 'user already exist!';

   }else{

      if($pass != $cpass){
         $error[] = 'password not matched!';
      }else{
         $insert = "INSERT INTO user_form(name, email, password, user_type) VALUES('$name','$email','$pass','$user_type')";
         mysqli_query($conn, $insert);
         header('location:login_form.php');
      }
   }

};
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register Form</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="style.css">
   <!-- Bootstrap Icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">

   <style>
      .password-container {
         position: relative;
      }
      .password-container input {
         width: 100%;
         padding-right: 30px; /* Add space for the icon */
      }
      .password-container .bi {
         position: absolute;
         right: 10px;
         top: 50%;
         right: 25px;
         transform: translateY(-50%);
         cursor: pointer;
      }
   </style>
</head>
<body>

<div class="form-container">

   <form action="" method="post">
      <h3>Register Now</h3>
      <?php
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
      ?>
      <input type="text" name="name" required placeholder="Enter your name">
      <input type="email" name="email" required placeholder="Enter your email">
      <div class="password-container">
         <input type="password" name="password" id="password" required placeholder="Enter your password">
         <i class="bi bi-eye" id="togglePassword1"></i>
      </div>
      <div class="password-container">
         <input type="password" name="cpassword" id="cpassword" required placeholder="Confirm your password">
         <i class="bi bi-eye" id="togglePassword2"></i>
      </div>
      <select name="user_type">
         <option value="user">User</option>
         <option value="admin">Admin</option>
      </select>
      <input type="submit" name="submit" value="Register Now" class="form-btn">
      <p>Already have an account? <a href="login_form.php">Login Now</a></p>
   </form>

</div>

<script>
   const togglePassword1 = document.querySelector('#togglePassword1');
   const password1 = document.querySelector('#password');

   togglePassword1.addEventListener('click', function () {
       // Toggle the type attribute
       const type = password1.getAttribute('type') === 'password' ? 'text' : 'password';
       password1.setAttribute('type', type);
       // Toggle the eye icon
       this.classList.toggle('bi-eye');
       this.classList.toggle('bi-eye-slash');
   });

   const togglePassword2 = document.querySelector('#togglePassword2');
   const password2 = document.querySelector('#cpassword');

   togglePassword2.addEventListener('click', function () {
       // Toggle the type attribute
       const type = password2.getAttribute('type') === 'password' ? 'text' : 'password';
       password2.setAttribute('type', type);
       // Toggle the eye icon
       this.classList.toggle('bi-eye');
       this.classList.toggle('bi-eye-slash');
   });
</script>

</body>
</html>
