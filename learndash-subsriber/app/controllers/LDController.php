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
    // print_r($user);
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

    register_rest_route('ldsubscriber/v1', '/reset_course/', array(
      'methods'  => 'POST',
      'callback' => array($this, 'reset_course'),
    ));

    register_rest_route('ldsubscriber/v1', '/complete_lesson/', array(
      'methods'  => 'POST',
      'callback' => array($this, 'complete_lesson'),
    ));


  }

  /**
   * Complete lesson
   *
   * @return void
   */
  public function complete_lesson()
  {
    $postid = $_POST['postid']; 
    $userid = $_POST['userid'];
    $scores = $_POST['scores'];
    global $post;
    $post      =  get_post($postid);
    $course_id = learndash_get_course_id($postid);

    // $scores = (int)$scores +  (int)get_user_meta($userid,"course_scores_".$course_id,true);
    update_user_meta($userid,"course_scores_".$course_id,$scores);

    // reset usermeta lesson
    update_user_meta($userid,"lessonid_".$postid,true);

    return;
  }

  /**
   * API to reset course data for users
   * users points and course status
   *
   * @return void
   */
  public function reset_course()
  {
    $postid = $_POST['postid']; 
    $userid = $_POST['userid'];
    global $post;
    $post      =  get_post($postid);
    $course_id = learndash_get_course_id($postid);


    // reset usermeta lesson
    $_courselist = learndash_get_lesson_list( $course_id, array( 'num' => 0 ) );
    foreach ($_courselist as $_c) {
      $ret = [];
      if ( $_c->post_status=='publish' ){
        // set lesson id
        delete_user_meta($userid, "lessonid_". $_c->ID);
      }
    }
    
    // reset usermeta course points $course_score
    update_user_meta($userid,"course_scores_".$course_id,0);
    


    // reset lesson activity
    $args     = array(
      'course_id'     => $course_id,
      'user_id'       => $userid,
      // 'post_id'       => $postid,
      'activity_type' => 'lesson',
    );
    // $lesson_activity = \learndash_get_user_activity( $args );
    // print_r($lesson_activity);
    global $wpdb;

    $complete_key = "course_completed_{$course_id}";
		$sql_string   = $wpdb->prepare("SELECT user_id, meta_key, meta_value FROM {$wpdb->usermeta} WHERE ( meta_key = '_sfwd-course_progress' OR meta_key = '{$complete_key}' ) AND user_id=$userid ");

    // $sql_string = $wpdb->prepare("SELECT user_id, meta_key, meta_value FROM $wpdb->usermeta WHERE meta_key LIKE '%' AND user_id=$userid ");
    $status = $wpdb->get_results( $sql_string );	
    
    foreach ( $status as $data ){
      $meta_key   = $data->meta_key;
      $meta_value = $data->meta_value;
      if ( $complete_key === $meta_key ) {				
        delete_user_meta( $userid, $meta_key );
			} elseif ( '_sfwd-course_progress' === $meta_key ) {
        $progress = unserialize( $meta_value );
        // print_r($progress);
				if ( ! empty( $progress ) && ! empty( $progress[ $course_id ] ) )  {
          $progress[ $course_id ] = array();
          // print_r(serialize($progress));
          update_user_meta( $userid, $meta_key, serialize($progress) );
				}
			}
    }


    // 
    // 
    //  DELETING ORIGINAL LEARNDASH DATA OF COURSE
    // 
    // 
    $sql_str = $wpdb->prepare("SELECT * FROM " . \LDLMS_DB::get_table_name( 'user_activity' ) . " WHERE user_id=%d AND course_id=%d  AND activity_type=%s ", 
                              $args['user_id'], 
                              $args['course_id'], 
                              $args['activity_type'] );

    $activity = $wpdb->get_results( $sql_str );	
    // count of deleted activities
    $deleted = 0;
    foreach ($activity as $_act){
      \learndash_delete_user_activity( $_act->activity_id );
      $deleted++;
    }
    return ["deleted"=>$deleted];
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
    $lesson_activity       = \learndash_get_user_activity( $lesson_args );
    $current_status_course = $lesson_activity->activity_status;
    // print_r($lesson_activity);
    
    // 
    // current user
    // 
    $current_user = get_userdata($userid);
    
    // 
    // get users score from post_meta 
    // 
    //$users                       = [];
    //$users['marta']['point']     = get_field('player_marta', $postid);
    //$users['marta']['name']      = get_field('group_names_4', $postid);
    //$users['dominique']['point'] = get_field('player_dominique', $postid);
    //$users['dominique']['name']  = get_field('group_names_3', $postid);
    //$users['kaleb']['point']     = get_field('player_kaleb', $postid);
    //$users['kaleb']['name']      = get_field('group_names_2', $postid);
    //$users['luke']['point']      = get_field('player_luke', $postid);
    //$users['luke']['name']       = get_field('group_names_1', $postid);

    $users                       = [];
    $users['marta']['point']     = get_field('player_marta', $postid);
    $users['marta']['name']      = get_field('group_names_1', $postid);
    $users['dominique']['point'] = get_field('player_dominique', $postid);
    $users['dominique']['name']  = get_field('group_names_2', $postid);
    $users['kaleb']['point']     = get_field('player_kaleb', $postid);
    $users['kaleb']['name']      = get_field('group_names_3', $postid);
    $users['luke']['point']      = get_field('player_luke', $postid);
    $users['luke']['name']       = get_field('group_names_4', $postid);

    //
    // audio data; since this is get_course_list() do we want to extend this function and rename it?
    //
    $audio            = [];
    $audio['5']       = get_field('5_points', $postid)['url'];
    $audio['6']       = get_field('6_points', $postid)['url'];
    $audio['7']       = get_field('7_points', $postid)['url'];
    $audio['8']       = get_field('8_points', $postid)['url'];
    $audio['9']       = get_field('9_points', $postid)['url'];

    // questions scores
    $scores = [];
    for ($k=1; $k < 10; $k++) { 
      $scores["$k"] = get_field( "question_$k",$postid );
    }

    // first uncompleted link
    // this link is first link of all courses, whick not completed
    $first_uncompleted_link = false;
    // tmp variable which show is prev lesson completed?
    $_completed_before = false;
    $courselist        = [];
    // fill lesson list
    $_courselist = learndash_get_lesson_list( $course_id, array( 'num' => 0 ) );
    foreach ($_courselist as $_c) {
      $ret = [];
      if ( $_c->post_status=='publish' ){
        // set lesson id
        $lesson_args['post_id'] = $_c->ID;
        // fill ret value
        $ret['title']     = $_c->post_title;
        $ret['link']      = get_permalink($_c->ID);
        $ret['id']        = $_c->ID;
        // completed
        // $ret['largs']     = \learndash_get_user_activity( $lesson_args );
        // $ret['completed'] =  $ret['largs']->activity_status;
        $ret['completed'] =  get_user_meta($userid,"lessonid_".$_c->ID,true);
        // $ret['args']      = $lesson_args;

        // check if prev course completed and this - not completed
        // then current link - thing, what we search
        if ( !$ret['completed'] ){
          if ( $_completed_before && $first_uncompleted_link==false ){
            $first_uncompleted_link = $ret['link'];
          }
        }else{
          $_completed_before = true;
        }
      }

      $courselist[] = $ret;
    }

    // course score
    $course_score = get_user_meta($userid,"course_scores_".$course_id,true);

    return [
      "next_post"          => learndash_next_post_link('', true, $post),
      "prev_post"          => learndash_previous_post_link('', true),
      "all_course_content" => do_shortcode('[course_content course_id="' . $course_id . '"]'),
      "users"              => $users,
      "course_score"       => (int)$course_score,
      "you"                => $current_user->first_name.' '.$current_user->last_name,
      "audio"              => $audio,
      "scores"             => $scores,
      "course_completed"   => get_user_meta($userid,"lessonid_$postid",true),
      "course_completed_user"   => $current_status_course,
      "first_uncompleted_link"  => $first_uncompleted_link,
      'list'               => $courselist,
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
