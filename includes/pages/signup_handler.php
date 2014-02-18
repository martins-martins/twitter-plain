<?php

if($__user_id > 0) {
  redirect_to($__links['home']);    
}
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$password2 = isset($_POST['password2']) ? trim($_POST['password2']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';

$validated = false;
$errors = array('fullname' => '',
  'email' => '',
  'password' => '',
  'username' => '');

if (isset($_POST['username'])) {
  $validated = true;
  if(strlen($fullname) < 1) {
    $errors['fullname'] = 'Full name must be at least 1 character long.';
    $validated = false;    
  } elseif(strlen($fullname) > 50) {
    $errors['fullname'] = 'Full name must be shorter than 51 characters.';
    $validated = false;    
  }
  if($validated) {
    $res = mysqli_query($mysqli, "SELECT id FROM users WHERE full_name='".mysqli_escape_string($mysqli, $fullname)."'");
    if (mysqli_num_rows($res) > 0) {
      $errors['fullname'] = 'Account already exists with Full name : '.$fullname.'.';
      $validated = false;    
    }
  }
  if(strlen($email) > 50) {
    $errors['email'] = 'Email must be shorter than 51 characters.';
    $validated = false;    
  }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Email is invalid.';
    $validated = false;
  }
  if($errors['email'] == '') {
    $res = mysqli_query($mysqli, "SELECT id FROM users WHERE email='".mysqli_escape_string($mysqli, $email)."'");
    if (mysqli_num_rows($res) > 0) {
      $errors['email'] = 'Account already exists with email : '.$email.'. Please choose another email.';
      $validated = false;    
    }
  }
  if(strlen($password) < 6) {
    $errors['password'] = 'Password must be at least 6 characters long.';
    $validated = false;    
  } elseif ($password != $password2) {
    $errors['password'] = 'Passwords do not match.';
    $validated = false;    
  }
  if(strlen($username) < 1) {
    $errors['username'] = 'Username must be at least 1 character long.';
    $validated = false;    
  } elseif(strlen($username) > 50) {
    $errors['username'] = 'Username must be shorter than 51 characters.';
    $validated = false;    
  }
  if($errors['username'] == '') {
    $res = mysqli_query($mysqli, "SELECT id FROM users WHERE username='".mysqli_escape_string($mysqli, $username)."'");
    if (mysqli_num_rows($res) > 0) {
      $errors['username'] = 'Account already exists with username : '.$username.'.';
      $validated = false;    
    }
  }
}

$created_message = '';
if($validated) {
  $activation_hash = md5(time().$username);
  $res = mysqli_query($mysqli, "INSERT INTO users (username, password, full_name, email, hash) 
                                VALUES ('".mysqli_escape_string($mysqli, $username)."',
                                '".md5(mysqli_escape_string($mysqli, $password))."',
                                '".mysqli_escape_string($mysqli, $fullname)."',
                                '".mysqli_escape_string($mysqli, $email)."','".$activation_hash."')");
  
  for($i=10001;$i<90000;$i++){
      mysqli_query($mysqli, "INSERT INTO users (username, password, full_name, email, activated) 
                                VALUES ('".mysqli_escape_string($mysqli, $username.$i)."',
                                '".md5(mysqli_escape_string($mysqli, $password))."',
                                '".mysqli_escape_string($mysqli, $fullname.$i)."',
                                '".mysqli_escape_string($mysqli, $i.$email)."', '1')");
      $last_id = mysqli_insert_id($mysqli);                           
      mysqli_query($mysqli, "INSERT INTO following (user_id, user_id_follower) VALUES (".intval($last_id).", 1)");                               

  }
  
  $url = generate_url($__links['index'], array('activation_hash' => $activation_hash), 1);
  
  $message = "Hello ".$fullname." !
  Click on link to activate your Twitter account : <a href=".$url.">activate</a>";
  $mail_sent = mail($email, 'Activation of your new Twitter account', $message);
  if($mail_sent) {
    $created_message = 'Account created. Message with activation link sent to '.$email; 
  }
  $fullname = $email = $password = $password2 = $username = '';                                                
}

  $user =  array('fullname' => $fullname,
                 'email' => $email,
                 'password' => $password,
                 'password2' => $password2,
                 'username' => $username);