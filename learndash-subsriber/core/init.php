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
// Pull post IDs to inject our leaderboard conditionally
//
global $post;

// 
// Shortcode for leaderboard scores players
// 
add_shortcode('ldsubscriber_board','ldsubscriber_board_shortcode');
function ldsubscriber_board_shortcode(){
  $board = new Helpers\BoardPage();
  return $board->renderform();
}



if( $post->ID == 174) {
  add_action('wp_footer', 'ldsubscriber_board');
  echo 'test';
}

// 
// Startboard shortcode for start page
// 
add_shortcode('ldsubscriber_startboard','ldsubscriber_startboard_shortcode');
function ldsubscriber_startboard_shortcode(){
  $board = new Helpers\StartBoardPage();
  return $board->renderform();
}


