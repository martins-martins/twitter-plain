<?php
// my tweets count
function my_tweets_count() {
  global $mysqli, $__user_id;
  $res = mysqli_query($mysqli, "SELECT count(id) as created_tweet_count, 
                                    (SELECT count(tweet_id) 
                                     FROM retweeted 
                                     WHERE user_id=".intval($__user_id).") as retweeted_tweet_count  
                              FROM tweets  
                              WHERE user_id=".intval($__user_id));
                              
  if (mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    return $row['created_tweet_count']+$row['retweeted_tweet_count']; 
  }
  return 0;  
}

// following count
function following_count() {
  global $mysqli, $__user_id;
  $res = mysqli_query($mysqli, "SELECT count(user_id) as following_count 
                                FROM following 
                                WHERE user_id_follower=".intval($__user_id));
  if (mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    return $row['following_count']; 
  }
  return 0;  
}

// followers count
function followers_count() {
  global $mysqli, $__user_id;
  $res = mysqli_query($mysqli, "SELECT count(user_id) as followers_count 
                                FROM following 
                                WHERE user_id=".intval($__user_id));
  if (mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    return $row['followers_count']; 
  }
  return 0;  
}

function get_tweets(&$tweets, &$conversation, $show, $tweet_id = 0, $recursive = 0, $ajax = 0) {
  global $mysqli, $__user_id;      
  $query = "SELECT tweets.id, tweets.parent_id, tweets.user_id, tweets.tweet, tweets.time_created, users.full_name, users.username, 
                  (SELECT user_id 
                      FROM retweeted 
                      WHERE tweet_id=tweets.id AND user_id=".intval($__user_id).") as retweeted, 
                  (SELECT count(user_id) 
                      FROM retweeted 
                      WHERE tweet_id=tweets.id AND user_id IN (
                                                       SELECT user_id 
                                                       FROM following 
                                                       WHERE user_id_follower=".intval($__user_id).")) as retweeted_by,
                  (SELECT group_concat(username SEPARATOR ', ') as users 
                      FROM retweeted LEFT JOIN users ON (retweeted.user_id=users.id)  
                      WHERE tweet_id=tweets.id AND users.id != ".intval($__user_id)." AND user_id IN (
                                                       SELECT user_id 
                                                       FROM following 
                                                       WHERE user_id_follower=".intval($__user_id).")) as retweeted_by_users
          FROM tweets LEFT JOIN users ON (tweets.user_id=users.id)";
    // if recursive, parent for one of the tweets is fetched
    if($recursive) {
        $query .= " WHERE tweets.id=".intval($tweet_id);    
    } else if ($show == 'all_tweets') {
    // user's created tweets 
    // tweets from users to whom current user is following 
    // retweeted tweets by users to whom current user is following         
    $query .= " 
            WHERE (tweets.id IN (SELECT id 
                                FROM tweets 
                                WHERE tweets.user_id=".intval($__user_id).") OR 
                                      tweets.user_id IN (SELECT user_id 
                                                         FROM following 
                                                         WHERE user_id_follower=".intval($__user_id).") OR 
                 tweets.id IN (SELECT tweet_id 
                                FROM retweeted 
                                WHERE retweeted.user_id IN (SELECT user_id 
                                                         FROM following 
                                                         WHERE user_id_follower=".intval($__user_id).")))";
    } else {
    // user's created tweets 
    // user's retweeted tweets
        $query .= " 
            WHERE (tweets.id IN (SELECT id 
                                FROM tweets 
                                WHERE tweets.user_id=".intval($__user_id).") OR 
                  tweets.id IN (SELECT tweet_id 
                                 FROM retweeted 
                                 WHERE user_id=".intval($__user_id)."))";    
    }
    $query .= " AND tweets.tweet LIKE '%2%'";
    $query .= " ORDER BY tweets.time_created DESC";
    
    if ($recursive == 0) {
        if($ajax == 0) {
           $page = 1;  
        } else {
            $page = $_SESSION['tweets_page'];
        }
        
        $query .= " LIMIT 8 OFFSET ".($page-1)*8;
        
        $page += 1;
        $_SESSION['tweets_page'] = $page;
    }
    //$query = "SELECT * FROM tweets WHERE tweet = 'zzz'";
    //echo $query; 
    $res = mysqli_query($mysqli, $query);       
  if (mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
      $array = array('user_id' => $row['user_id'],
                    'tweet_id' => $row['id'],
                    'text' => $row['tweet'],
                    'fullname' => $row['full_name'],
                    'username' => $row['username'],
                    'time' => date('d-m-Y H:i', strtotime($row['time_created'])),
                    'retweeted_by' => $row['retweeted_by']!='0'?1:0,
                    'retweeted_by_users' => isset($row['retweeted_by_users']) ? $row['retweeted_by_users'] : '',
                    'conversation' => $tweet_id == $row['id'] ? 1 : 0,
                    'conversation_link' => $row['parent_id'] > 0 && $tweet_id != $row['id'] ? 1 : 0,
                    'retweeted' => isset($row['retweeted']) ? 1 : 0);
      // if recursive or parameter from URI is present, add to conversation
      if($recursive || $tweet_id == $row['id']) {
        $conversation[] = $array;    
      } else {
        $tweets[] = $array;     
      }
      // if current tweet belongs to conversation and parent exists, go fetch one
      if($tweet_id == $row['id'] && $row['parent_id'] > 0) {
        get_tweets($tweets, $conversation, $show, $row['parent_id'], 1);
      }
      // checks for conversation
      if(count($conversation) > 0) {
        // on top wee need oldest tweets, so array_reverse must be performed
        $conversation = array_reverse($conversation);
        // merging all tweets with conversation
        $tweets = array_merge($tweets, $conversation);
        // after recursion, when all parents are fetched and added to all tweets, converstion array is cleared
        $conversation = array(); 
      } 
    }
  }
    
  if($ajax == 1) {
    return $tweets;    
  }
}

function get_users($show, $search_keyword, $ajax = 1) {
  global $mysqli, $__user_id;
  
  $users = array();
  switch($show){
    case 'following';
      $query = "SELECT full_name, username, id, 'following' 
                FROM users 
                WHERE id!=".intval($__user_id)." AND 
                      id IN (SELECT user_id 
                      FROM following 
                      WHERE users.id=user_id AND user_id_follower=".intval($__user_id).")";
      break;
    case 'followers';
      $query = "SELECT full_name, username, id, 
                      (SELECT user_id 
                      FROM following 
                      WHERE users.id=user_id AND user_id_follower=".intval($__user_id).") as following 
                FROM users 
                WHERE id!=".intval($__user_id)." AND 
                      id IN (SELECT user_id_follower 
                      FROM following 
                      WHERE users.id=user_id_follower AND user_id=".intval($__user_id).")";    
      break;
    case 'all_users';
      $query = "SELECT full_name, username, id, 
                      (SELECT user_id 
                      FROM following 
                      WHERE users.id=user_id AND user_id_follower=".intval($__user_id).") as following 
                FROM users 
                WHERE id!=".intval($__user_id); 
      break;
    case 'search';
      $query = "SELECT full_name, username, id, 
                      (SELECT user_id 
                      FROM following 
                      WHERE users.id=user_id AND user_id_follower=".intval($__user_id).") as following 
                FROM users 
                WHERE (username LIKE '%".mysqli_escape_string($mysqli, $search_keyword)."%') AND id!=".intval($__user_id);
      break;    
  }
  if($ajax == 0) {
     $page = 1;  
  } else {
      $page = $_SESSION['users_page'];
  }
  $query .= " LIMIT 30 OFFSET ".($page-1)*30;
  $page += 1;
  $_SESSION['users_page'] = $page;
  
  $res = mysqli_query($mysqli, $query);
  if (mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {

      $users[] = array('fullname' => $row['full_name'],
        'username'=> $row['username'],
        'user_id' => $row['id'],
        'following' => isset($row['following']) ? 1 : 0);
    }
  }
  return $users;
}