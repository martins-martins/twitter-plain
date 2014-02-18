$(document).ready(function($){

  function tweet_limit() {
    var length = $(this).val().length; 
    if(length>0) {
      $(this).parent().parent().find("input[name=post_tweet]").attr('disabled',false);    
    } else {
      $(this).parent().parent().find("input[name=post_tweet]").attr('disabled','disabled');    
    }
    var remaining = 140-length; 
    $(this).parent().parent().find(".counter").html(remaining);
  }
  $(".profile-box textarea[name=tweet], div.tweet textarea[name=tweet]").keyup(tweet_limit);

  function reply(e) {
    e.preventDefault();
    $(this).parent().find('form').toggle(0,function(){
      if($(this).css('display')=='none'){
        $(this).parent().find('.reply').html('Reply');    
      } else {
        $(this).parent().find('.reply').html('Hide');  
      }    
    });
  }
  $('.reply').click(reply) ;
  // follow, unfollow
  function follow_unfollow(e) {
    e.preventDefault();
    var following = $(this).attr('following');
    $.ajax({
      url: $(this).attr('href'),
      context: this                 
    }).done(function(data) {
      // 1 success
      if(data==1) {
        var following_count =  $('#following_count').html();
        if(following==1) {
          following = 0;
          $(this).html('Follow');
          $('#following_count').html(parseInt(following_count)-1);    
        } else {
          following = 1; 
          $(this).html('Unfollow');
          $('#following_count').html(parseInt(following_count)+1);     
        }
        $(this).attr('following', following);
        $(this).attr('href', __links['ajax'] + '?user_id=' + user_id + '&following=' + following);   
      }
    });
  }
  $('a.follow').click(follow_unfollow);
  // retweet, unretweet
  function retweet_unretweet(e) {
    e.preventDefault();
    var retweeted = $(this).attr('retweeted');
    var tweet_id = $(this).attr('tweet_id');
    $.ajax({
      url: $(this).attr('href'),
      context: this                 
    }).done(function(data) {
      // 1 success
      if(data==1) {
        var my_tweets_count =  $('#my_tweets_count').html();
        if(retweeted==1) {
          retweeted = 0;
          $(this).html('Retweet');
          $('#my_tweets_count').html(parseInt(my_tweets_count)-1);   
        } else {
          retweeted = 1;
          $(this).html('Retweeted');
          $('#my_tweets_count').html(parseInt(my_tweets_count)+1);    
        }
        $(this).attr('retweeted', retweeted);
        $(this).attr('href', __links['ajax'] + '?tweet_id=' + tweet_id + '&retweeted=' + retweeted);   
      }
    });
  } 
  $('a.retweet').click(retweet_unretweet);

  function parse_tweet_template(tweet, ajax_loader){ 
    var tweet_template = '<div class="tweet" id="">' +
      '<div class="tweet-user">' +
      '<span class="tweet-user-fullname"></span>' +
      '<span class="light-grey"></span>' +
      '<span class="tweet-time"></span>' +
      '</div>' +
      '<p></p>' +
      '<a href="#" class="reply">Reply</a>' +
      '<form method="post" action="" role="form" style="display: none;">' +
      '<input type="hidden" name="tweet_id" value="">' +
      '<div class="form-group">' +
      '<textarea name="tweet" cols="40" rows="4" class="form-control" maxlength="140"></textarea>' +
      '</div>' +
      '<input name="post_tweet" disabled="disabled" type="submit" class="btn btn-default" value="Reply">' +
      '<span class="counter">140</span>' +
      '</form>' +
      '</div>';

    var tweet_div = $(tweet_template);
    tweet_div.attr('id', tweet["tweet_id"]);
    if(tweet["conversation"] == 1) {
      tweet_div.addClass("tweet-conversation");    
    }
    tweet_div.find('.tweet-user span:first').html(tweet["fullname"]);
    tweet_div.find('.tweet-user span.light-grey').html(tweet["username"]);
    tweet_div.find('.tweet-user span.tweet-time').html(tweet["time"]);
    tweet_div.find('p').html(tweet["text"]);
    if(tweet["retweeted_by"] == 1) {
      tweet_div.find('p').after('<p class="light-grey">Retweeted by ' + tweet["retweeted_by_users"] + '</p>');    
    }
    // conversation
    if(tweet["conversation_link"] == 1) {
      tweet_div.find('p:last').after('<a href="' + __links['ajax'] + '?tweet_id=' + tweet["tweet_id"] + '&recursive=1' + '" class="show-conversation">Show conversation</a>');        
      tweet_div.find('a.show-conversation').click(show_conversation);  
    }
    // reply, textarea
    tweet_div.find('a.reply').click(reply);
    tweet_div.find('input[name=tweet_id]').val(tweet["tweet_id"]);
    tweet_div.find('textarea[name=tweet]').keyup(tweet_limit);
    // retweeting
    if (user_id != tweet["user_id"]) {
      var link_title = 'Retweet';
      if(tweet["retweeted"]) {
        link_title = 'Retweeted';    
      } 
      tweet_div.find('a.reply').before('<a href="' + __links['ajax'] + '?tweet_id=' + tweet["tweet_id"] + '&retweeted' + tweet["retweeted"] + '" retweeted="' + tweet["retweeted"] + '" class="retweet">' + link_title + '</a>');         
      tweet_div.find('a.retweet').click(retweet_unretweet);
    }
    if (user_id == tweet["user_id"]) {
      tweet_div.find('a.reply').after('<a href="' + __links['home'] + '?tweet_id=' + tweet["tweet_id"] + '" class="delete">Delete</a>'); 
    }               
    return tweet_div;    
  }
  function parse_user_template(user, ajax_loader) { 
    var user_template = '<div class="user">' +
      '<span></span>' +
      '<span class="light-grey"></span>' +
      '<a href="" user_id="" following="" class="follow btn btn-default">' +
      '</a></div>';
      
    var user_div = $(user_template);
    user_div.find('span:first').html(user["fullname"]); 
    user_div.find('span.light-grey').html(user["username"]); 
    user_div.find('a').attr('href', __links['ajax'] + '?user_id=' + user["user_id"] + '&following=' + user["following"]);
    user_div.find('a').attr('user_id', user["user_id"]);
    user_div.find('a').attr('following', user["following"]);
    
    if (user["following"]) {
      user_div.find('a').html('Unfollow');  
    } else {
      user_div.find('a').html('Follow');
    }
    user_div.find('a').click(follow_unfollow);
    
    return user_div; 
  }
  // show conversation function for assigning to links
  function show_conversation(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('href'),
      context: this                 
    }).done(function(tweets) {
      // 1 success
      if(tweets.length > 0)
      { 
        var ajax_loader = $('div#loadmoreajaxloader');
        //var conversation = $('');
        for (index in tweets) {
          if (tweets.hasOwnProperty(index)) {
            var parsed_tweet_template = parse_tweet_template(tweets[index], ajax_loader);
            
            $(this).parent().before(parsed_tweet_template);
          }
        }
      }
    });
  }
  // clicking on "Show conversation"  
  $('a.show-conversation').click(show_conversation);
  // infinite scroll
  var miliseconds_interval = 51;
  var miliseconds;
  var miliseconds2;
  var more_data = true; 
  var ajax_url = '';
  var div_class = 'tweet';
  var data_per_page = 8;
  if(__show == 'all_tweets' || __show == 'my_tweets') {
    ajax_url = __links['ajax'] + '?tweet_id=0&recursive=0&show=' + __show;
  } else {
    div_class = 'user';
    data_per_page = 30;
    ajax_url = __links['ajax'] + '?show=' + __show + '&search_keyword=' + __search_keyword;
  } 
  $(window).scroll(function()
    {      
      miliseconds = new Date().getTime();
      var ajax_loader = $('div#loadmoreajaxloader'); 
      if(($(document).height() - $(window).height()) == $(window).scrollTop() && $('div.'+div_class).length > 0 && more_data && miliseconds_interval > 50)
      {
        miliseconds2 = new Date().getTime();
        miliseconds_interval = miliseconds2 - miliseconds;
        ajax_loader.show();
        $.ajax({
          url: ajax_url,
          success: function(data)
          {   
            if(data.length > 0)
            { 
              for (index in data) {
                if (data.hasOwnProperty(index)) {
                  if (div_class == 'tweet') {
                    var parsed_template = parse_tweet_template(data[index], ajax_loader);
                  } else {
                    var parsed_template = parse_user_template(data[index], ajax_loader);
                  }
                  ajax_loader.before(parsed_template);
                }
              }
              ajax_loader.hide();

              miliseconds2 = new Date().getTime();
              miliseconds_interval = miliseconds2 - miliseconds;

              if (data.length < data_per_page) {
                more_data = false;
              }
            } else {
              more_data = false;
              ajax_loader.hide();
            }
          },
          error: function(){
            more_data = false;
            ajax_loader.hide();
          },
        });
      }
  });  
});