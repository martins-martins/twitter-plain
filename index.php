<?php

/**
*   Front controller
*   Includes common files and specific page files based on analyzed request
*/

include('includes/functions.php');

session_start();

if(isset($_GET['logout'])) {
  session_destroy(); 
  redirect_to("/");
  exit; 
}

$mysqli = mysqli_connect("localhost", "root", "", "twitter");

$__user_id = 0;
$__user_fullname = '';

if(isset($_SESSION['user_id'])) {
  $__user_id = intval($_SESSION['user_id']);
  $__user_fullname = $_SESSION['fullname'];
}

$__request_parts = explode('?', $_SERVER['REQUEST_URI']);
$__requested_path_parts = explode('/', $__request_parts[0]);
$__requested_page = '/'.$__requested_path_parts[1];
 
$__page_title = 'Login';

$__links = array('index' => '/',
                 'ajax' => '/ajax',
                 'signup' => '/signup',
                 'home' => '/home');
/**
* $show is used in home and in ajax
*/
$show_pages = array('all_users', 'all_tweets', 'my_tweets', 'following', 'followers', 'search');
$show = 'all_tweets';
if(isset($__requested_path_parts[2]) && in_array($__requested_path_parts[2], $show_pages)) {
  $show = $__requested_path_parts[2];  
}
                               
switch($__requested_page){
  case '/ajax':
    include('includes/pages/home_functions.php');
    include('includes/ajax.php');  
  break;
  case '/':
    include('includes/pages/login_handler.php');
    include('includes/header.php');    
    include('includes/pages/login.php');  
  break;
  case '/signup':
    $__page_title = 'Signup';
    include('includes/pages/signup_handler.php');
    include('includes/header.php');    
    include('includes/pages/signup.php');
  break;
  case '/home':
    $__page_title = 'Twitter - Home';
    include('includes/pages/home_functions.php');
    include('includes/pages/home_handler.php');
    include('includes/header.php');    
    include('includes/pages/home.php');
  break;
  default:
    $__page_title = 'Twitter - Page Not Found';
    include('includes/pages/404.php');
}
if($__request_parts[0] != '/ajax') {
  include('includes/footer.php');  
}

?>