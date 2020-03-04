<?php

require LDSUBSCRIBER_PATH.'/vendor/autoload.php';

// enqueue styles and scripts with class-enqueue.php
// helper functions helper.php
include_once LDSUBSCRIBER_PATH.'/core/helpers.php';
//  *  Включаем все скрипты и все что нам надо в нашу загрузку
include_once LDSUBSCRIBER_PATH.'/core/class-enqueue.php';

// 
// add ajax actions
// 
$ldcontoller = new Controllers\LDController();
// add_action('wp_ajax_delete_client',[$actions,'delete']);


// 
// Shortcode for leaderboard scores players
// 
add_shortcode('ldsubscriber_board','ldsubscriber_board_shortcode');
function ldsubscriber_board_shortcode(){
  $board = new Helpers\BoardPage();
  return $board->renderform();
}

//
// Initiate leaderboard for lesson custom post type (sfwd-lessons)
//

global $post;
$current_post_id = $post->ID;
echo $current_post_id;

$modules = array(172);

if (in_array($current_post_id, $modules)) {
  $board = new Helpers\BoardPage();
  return $board->renderform();

  echo 'we\'re looking at a module';
}


function init_leaderboard() {
  if( is_singular('sfwd-lessons') ) {
    $board = new Helpers\BoardPage();
    return $board->renderform();
  }
}
add_action('wp', 'init_leaderboard');

// 
// Startboard shortcode for start page
// 
add_shortcode('ldsubscriber_startboard','ldsubscriber_startboard_shortcode');
function ldsubscriber_startboard_shortcode(){
  $board = new Helpers\StartBoardPage();
  return $board->renderform();
}


