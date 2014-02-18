<div class="global-nav">
  <div class="global-nav-inner">
    <a href="<?php echo $__links['home']; ?>">Home</a>
    <form action="<?php echo generate_url($__links['home'].'/search'); ?>" role="form">
      <div class="form-group">
        <input name="search_keyword" type="text" class="form-control" value="<?php echo $search_keyword; ?>">
      </div>                    
      <input type="submit" class="btn btn-default" value="Search">
    </form>
    <a href="<?php echo generate_url($__links['home'].'/all_users'); ?>">All Users</a>
    <a href="<?php echo $__links['index']; ?>?logout=1">Log Out</a>
  </div>    
</div>
<div class="main">
  <div id="content">
    <div class="profile-box">
      <p class="name"><?php echo $__user_fullname; ?></p>
      <ul>
        <li><a href="<?php echo generate_url($__links['home'].'/my_tweets'); ?>"><span id="my_tweets_count"><?php echo $my_tweets_count; ?></span> MY TWEETS</a></li>
        <li><a href="<?php echo generate_url($__links['home'].'/following'); ?>"><span id="following_count"><?php echo $following_count; ?></span> FOLLOWING</a></li>
        <li><a href="<?php echo generate_url($__links['home'].'/followers'); ?>"><?php echo $followers_count; ?> FOLLOWERS</a></li>
      </ul>
      <form method="post" action="" role="form">
        <div class="form-group">
          <textarea name="tweet" cols="40" rows="5" class="form-control" maxlength="140" placeholder="Compose new Tweet..."></textarea>
        </div>
        <input name="post_tweet" disabled="disabled" type="submit" class="btn btn-default" value="Tweet">
        <span class="counter">140</span>
      </form>  
    </div>
    <div class="main-content" id="tweet-column">
      <h4><?php echo $__page_title; ?></h4>
      <?php
      if(count($users) > 0) { 
        foreach($users as $user) {
          ?>
          <div class="user">
            <span><?php echo $user['fullname']; ?></span>
            <span class="light-grey"><?php echo $user['username']; ?></span>
            <a href="<?php echo generate_url($__links['ajax'], array('user_id'=>$user['user_id'], 'following'=>$user['following'])); ?>" user_id="<?php echo $user['user_id']; ?>" following="<?php echo $user['following']; ?>" class="follow btn btn-default">
              <?php
              if($user['following']) {
                echo 'Unfollow';
              } else {
                echo 'Follow';
              }  
              ?>
            </a>
          </div>
          <?php 
        }
      } 
      ?>    

      <?php 
      if(count($tweets) > 0) { 
        foreach($tweets as $tweet) {

          if($tweet['conversation']) {
            echo '<div class="tweet tweet-conversation" id="'.$tweet['tweet_id'].'">';
          } else { 
            echo '<div class="tweet" id="'.$tweet['tweet_id'].'">';
          } 
          echo '<div class="tweet-user">
          <span class="tweet-user-fullname">'.$tweet['fullname'].'</span><span class="light-grey">'.$tweet['username'].'</span>
          <span class="tweet-time">'.$tweet['time'].'</span>
          </div>
          <p>'.$tweet['text'].'</p>';

          if($tweet['retweeted_by']) {
            echo '<p class="light-grey">Retweeted by '.$tweet['retweeted_by_users'].'</p>';
          }
          if($tweet['conversation_link']) {
            echo '<a href="'.generate_url($__links['ajax'], array('tweet_id'=>$tweet['tweet_id'], 'recursive'=>1)).'" class="show-conversation">Show conversation</a>';
          } 
          if($tweet['user_id'] != $__user_id) {       
            echo '<a href="'.generate_url($__links['ajax'], array('tweet_id'=>$tweet['tweet_id'], 'retweeted'=>$tweet['retweeted'])).'" tweet_id="'.$tweet['tweet_id'].'" retweeted="'.$tweet['retweeted'].'" class="retweet">';
            if($tweet['retweeted']) {
              echo 'Retweeted';
            } else {
              echo 'Retweet';
            }  
            echo '</a>';
          }
          echo '<a href="#" class="reply">Reply</a>';
          if($tweet['user_id'] == $__user_id) { 
            echo '<a href="'.generate_url($__links['home'], array('tweet_id'=>$tweet['tweet_id'])).'" class="delete">Delete</a>'; 
          }
          ?>
          <form method="post" action="" role="form" style="display: none;">
            <input type="hidden" name="tweet_id" value="<?php echo $tweet['tweet_id']; ?>">
            <div class="form-group">
              <textarea name="tweet" cols="40" rows="4" class="form-control" maxlength="140"></textarea>
            </div>
            <input name="post_tweet" disabled="disabled" type="submit" class="btn btn-default" value="Reply">
            <span class="counter">140</span>
          </form>
        </div>
        <?php
      }  
    }
    echo '
      <div id="loadmoreajaxloader" style="display:none;"><center><img src="/assets/images/ajax-loader.gif" /></center></div>';
    if($message != '') {
      echo '
      <div class="text-danger home-message">'.$message.'</div>';
    } 
    ?>
    <script>
      var __links = <?php echo json_encode($__links); ?>;
      var user_id = <?php echo $__user_id; ?>;
      var __show = '<?php echo $show; ?>'; 
      var __search_keyword = '<?php echo $search_keyword; ?>'; 
    </script>
  </div>
</div>