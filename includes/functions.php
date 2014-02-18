<?php

function redirect_to($location = NULL, $params = array()) {
  
  if($location != NULL) {
    
    $location .= count($params)>0 ? '?'.http_build_query($params) : '';
    header("Location: {$location}");
    exit;
  }
}

function generate_url($page = '/', $params = array(), $absolute = 0, $schema = 'http') {
  $url = '';
  if ($absolute) {
    $url .= $schema.'://'.$_SERVER['HTTP_HOST'];  
  }
  $url .= $page;
  
  if (count($params) > 0 ) {
    $url .= '?'.http_build_query($params);  
  }
  return $url;    
}