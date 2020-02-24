<?php

namespace Controllers;

/**
 * rest api controller
 */
class LDController
{
  /**
   * Class constructor
   */
  public function __construct()
  {
    //register api class
    add_action('rest_api_init', array($this, 'api'));
    // add action to mark post as completed
    add_action("learndash_lesson_completed", array($this,'mark_as_completed'), 5, 1);
  }

  /**
   * mark users meta that lesson is completed
   */
  public function mark_as_completed( $user,    $course=0,    $post=0,    $progress=0  ){
    print_r($user);
    $meta_key = "postid_$post->ID";
    update_user_meta( $user->ID, $meta_key, 1 );
  }

  /*
   * Register rest api endpoints
   *
   */
  public function api()
  {

    register_rest_route('ldsubscriber/v1', '/get_course_data/', array(
      'methods'  => 'POST',
      'callback' => array($this, 'get_course_data'),
    ));

    register_rest_route('ldsubscriber/v1', '/get_course_list/', array(
      'methods'  => 'POST',
      'callback' => array($this, 'get_course_list'),
    ));
  }

  /**
   * return user data summary
   * @return [type] [description]
   */
  public function get_course_list()
  {
    // 
    // if plugin installed - ok
    // 
    $postid = $_POST['postid']; 
    $userid = $_POST['userid'];
    // return [ "res"=>do_shortcode('[ld_course_list]') ];
    // return [ "res"=>do_shortcode('[course_content course_id="81"]') ];
    global $post;
    $post      =  get_post($postid);
    $course_id = learndash_get_course_id($postid);

    // get lesson activity
    $lesson_args     = array(
      'course_id'     => $course_id,
      'user_id'       => $userid,
      'post_id'       => $postid,
      'activity_type' => 'lesson',
    );
    $lesson_activity = \learndash_get_user_activity( $lesson_args );
    // print_r($lesson_activity);
    
    // 
    // current user
    // 
    $current_user = get_userdata($userid);
    
    // 
    // get users score from post_meta 
    // 
    $users              = [];
    $users['marta']     = get_field('player_marta', $postid);
    $users['dominique'] = get_field('player_dominique', $postid);
    $users['kaleb']     = get_field('player_kaleb', $postid);
    $users['luke']      = get_field('player_luke', $postid);

    //
    // audio data; since this is get_course_list() do we want to extend this function and rename it?
    //
    $audio            = [];
    $audio['5']       = get_field('5_points', $postid)['url'];
    $audio['6']       = get_field('6_points', $postid)['url'];
    $audio['7']       = get_field('7_points', $postid)['url'];
    $audio['8']       = get_field('8_points', $postid)['url'];
    $audio['9']       = get_field('9_points', $postid)['url'];

    return [
      "next_post"          => learndash_next_post_link('', true, $post),
      "prev_post"          => learndash_previous_post_link('', true),
      "all_course_content" => do_shortcode('[course_content course_id="' . $course_id . '"]'),
      "users"              => $users,
      "you"                => $current_user->first_name.' '.$current_user->last_name,
      "audio"              => $audio,
      "course_completed_user"   => get_user_meta($userid,"postid_$postid",true),
      "course_completed"   => $lesson_activity->activity_status
    ];
    // return [ "res"=>learndash_get_next_lesson_redirect($post) ];

    
  }

  /**
   * return user data summary
   * @return [type] [description]
   */
  public function get_course_data()
  {
    // 
    // if plugin installed - ok
    // 
    if (class_exists('\uncanny_learndash_reporting\ReportingApi')) {
      // 
      // getting user
      // 
      $current_user       = wp_get_current_user();

      // 
      // set up POST params
      // 
      $_POST['tableType']        = 'userSingleCoursesOverviewTable';
      $_POST['userId']           = $current_user->ID;

      // 
      // fill courses rows
      // 
      $courses                   = \uncanny_learndash_reporting\ReportingApi::get_course_list();
      if (count($courses) > 0) {
        $i = 0;
        foreach ($courses as $key => $value) {
          $_POST['rows'][$i] = [
            'rowId' => $value->ID,
            'ID'    => $current_user->ID
          ];
          $i++;
        }
      }

      $data = \uncanny_learndash_reporting\ReportingApi::get_table_data();

      return ["status" => "ok", "data" => $data, "post" => $_POST, "course" => $courses];
    } else {
      // 
      // else retur error
      // 
      $return_object['message']  = __('Not found activated plugin "tin-canny-learndash-reporting" class. Please install and activate plugin', 'ldsubscriber');
      return $return_object;
    }
  }
}
