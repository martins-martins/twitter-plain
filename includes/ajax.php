<?php

//var_dump($_GET);
if($__user_id != 0) {
  // following
  if (isset($_GET['following']) && isset($_GET['user_id'])) {
    $following = intval($_GET['following']);
    $user_id = intval($_GET['user_id']);
    if(($following == 0 || $following == 1) && $user_id >0) { 
      if($following == 1) {
         $query = "DELETE FROM following WHERE user_id=".intval($user_id)." AND user_id_follower=".intval($__user_id); 
      } else {
         $query = "INSERT INTO following (user_id, user_id_follower) VALUES (".intval($user_id).", ".intval($__user_id).")";     
      }
      mysqli_query($mysqli, $query);
      if(mysqli_affected_rows($mysqli) > 0) {
        echo 1;
      } else {
        echo 0;
      }
    }
  }
  // retweeting
  if (isset($_GET['retweeted']) && isset($_GET['tweet_id'])) {
    $retweeted = intval($_GET['retweeted']);
    $tweet_id = intval($_GET['tweet_id']);
    if(($retweeted == 0 || $retweeted == 1) && $tweet_id >0) { 
      if($retweeted == 1) {
         $query = "DELETE FROM retweeted WHERE user_id=".intval($__user_id)." AND tweet_id=".intval($tweet_id); 
      } else {
         $query = "INSERT INTO retweeted (user_id, tweet_id) VALUES (".intval($__user_id).", ".intval($tweet_id).")";     
      }
      mysqli_query($mysqli, $query);
      
      if(mysqli_affected_rows($mysqli) > 0) {
        echo 1;
      } else {
        echo 0;  
      }
    }
  }
  // tweets
  if (isset($_GET['tweet_id']) && isset($_GET['recursive']) && isset($_GET['show'])) {     
    $tweets = array();
    $conversation = array();
    $tweet_id = intval($_GET['tweet_id']);
    $recursive = intval($_GET['recursive']);
    $show = $_GET['show'];
    header('Content-type: text/json');    
    echo json_encode(get_tweets($tweets, $conversation, $show, $tweet_id, $recursive, 1));
  }
  // users
  if (isset($_GET['show']) && isset($_GET['search_keyword'])) {     
    $show = $_GET['show'];
    $search_keyword = $_GET['search_keyword'];
    header('Content-type: text/json');   
    echo json_encode(get_users($show, $search_keyword, 1));
  }
}


?>