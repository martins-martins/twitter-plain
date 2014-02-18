<?php

if($__user_id == 0) {
  redirect_to($__links['index']);   
}

$titles = array('all_tweets'=> 'Tweets',
                'followers'=> 'Followers',
                'all_users'=> 'All Users',
                'search'=> 'Search',
                'my_tweets'=> 'My Tweets',
                'following'=> 'Following');
                
$__page_title = $titles[$show];

// insert tweet
if(isset($_POST['tweet']) && strlen($_POST['tweet']) > 0 && strlen($_POST['tweet']) < 141) {

  $parent_id = isset($_POST['tweet_id'])?$_POST['tweet_id']:0;

  $res = mysqli_query($mysqli, "INSERT INTO tweets (user_id, parent_id, tweet) 
                                      VALUES (".intval($__user_id).",
                                      ".intval($parent_id).",
                                      '".mysqli_escape_string($mysqli, $_POST['tweet'])."')");

  $last_id = mysqli_insert_id($mysqli); 
  //for($i=0;$i<10;$i++){
  //                $res = mysqli_query($mysqli, "INSERT INTO tweets (user_id, parent_id, tweet) 
  //                                                VALUES (".intval($__user_id).",
  //                                                ".intval($last_id).",
  //                                                '".$i."')");
  //                                                
  //                $last_id = mysqli_insert_id($mysqli);
  //            }
  //for($i=0;$i<80000;$i++){
//                  $res = mysqli_query($mysqli, "INSERT INTO tweets (user_id, parent_id, tweet) 
//                                                  VALUES (".rand(1, 4).",
//                                                  ".intval(0).",
//                                                  '".$i."')");
//                                                  
//                  //$last_id = mysqli_insert_id($mysqli);
//              }
  redirect_to($__links['home']);                                        
}
//delete tweet
if(isset($_GET['tweet_id']) && intval($_GET['tweet_id']) > 0) {
  $query = "DELETE FROM tweets WHERE id=".intval($_GET['tweet_id'])." AND user_id=".$__user_id;
  mysqli_query($mysqli, $query);
  $query = "DELETE FROM retweeted WHERE tweet_id=".intval($_GET['tweet_id']);
  mysqli_query($mysqli, $query);
}        
$message = '';
$my_tweets_count = my_tweets_count();
$following_count = following_count();
$followers_count = followers_count();

$tweets = array();
$conversation = array();
$users = array();
$search_keyword = isset($_GET['search_keyword']) ? $_GET['search_keyword'] : '';
// show tweets else users
if($show == 'all_tweets' || $show == 'my_tweets') {
  get_tweets($tweets, $conversation, $show);

  if(count($tweets) < 1) {
    $message = 'There are no tweets.';  
  }        
} else {
  $users = get_users($show, $search_keyword, 0);

  if (count($users) <= 0) {
    switch($show){
      case 'all_users';
        $message = 'There are no users.';    
        break;
      case 'search';
        $message = 'Search returned no users.';
        break;
      case 'following';
        $message = 'You are not following anyone.';    
        break;
      case 'followers';
        $message = 'There are no followers.';
        break;
    }   
  }
}