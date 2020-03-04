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
// Initiate leaderboard
//
add_action( 'elementor/frontend/the_content', function( $content ) {
	
	// $modules = array(172, 174, 411, 617);
  if('sfwd-lessons' == get_post_type() && get_the_ID()==176 ) { // if our post is a learndash lesson that is not post 176 (intro post)
    $board = new Helpers\StartBoardPage();
    $content= $content.$board->renderform();   
  }
  return $content;

});


// 
// Shortcode for leaderboard scores players
// 
add_shortcode('ldsubscriber_board','ldsubscriber_board_shortcode');
function ldsubscriber_board_shortcode(){
  $board = new Helpers\BoardPage();
  return $board->renderform();
}


// 
// Startboard shortcode for start page
// 
add_shortcode('ldsubscriber_startboard','ldsubscriber_startboard_shortcode');
function ldsubscriber_startboard_shortcode(){
  $board = new Helpers\StartBoardPage();
  return $board->renderform();
}