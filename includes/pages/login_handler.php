<?php
 
if($__user_id > 0) {
  redirect_to($__links['home']);    
}

$success_message = '';
$fail_message = '';

$activation_hash = isset($_GET['activation_hash'])?$_GET['activation_hash']:'';

if($activation_hash != '') {

  $res = mysqli_query($mysqli, "UPDATE users 
    SET activated='1', hash='' 
    WHERE hash='".mysqli_escape_string($mysqli, $activation_hash)."'");
  if(mysqli_affected_rows($mysqli)==1) {
    $success_message = 'Account activated. You can now log in.'; 
  } else {
    $fail_message = 'Invalid activation code'; 
  }   
} 
if(isset($_POST['username'])){
  $user = $_POST['username']; 
  $pass = $_POST['password'];
  $valid = false;
  if(strlen($user) > 0 && strlen($pass) > 0) {

    $res = mysqli_query($mysqli, "SELECT id, username, full_name, activated 
      FROM users 
      WHERE username='".mysqli_escape_string($mysqli, $user)."' AND 
      password='".md5($pass)."'");
    if (mysqli_num_rows($res) > 0) {
      $row = mysqli_fetch_assoc($res);
      if($row['activated'] == '1') {                  
        $valid = true;

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['fullname'] = $row['full_name'];
      } else {
        $fail_message = 'Account not activated';  
      }
    } else {
      $fail_message = 'Wrong username/password';    
    }    
  } else {
    $fail_message = 'Wrong username/password';  
  }
  if($valid) {
    redirect_to($__links['home']);  
  }
}


?>