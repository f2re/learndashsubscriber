<?php
namespace Controllers;

/**
 * rest api controller
 */
class LDController {
  /**
   * Class constructor
   */
  public function __construct() {
    //register api class
    add_action( 'rest_api_init', array( $this, 'api' ) );
  }

  /*
   * Register rest api endpoints
   *
   */
  public function api() {

    register_rest_route( 'ldsubscriber/v1', '/get_course_data/', array(
      'methods'  => 'POST',
      'callback' => array( $this, 'get_course_data' ),
    ) );
  }

  /**
   * return user data summary
   * @return [type] [description]
   */
  public function get_course_data(){
    // 
    // if plugin installed - ok
    // 
    if ( class_exists('\uncanny_learndash_reporting\ReportingApi') ){
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
      if ( count($courses)>0 ){
        $i=0;
        foreach ($courses as $key => $value) {
          $_POST['rows'][$i] = [ 'rowId' => $value->ID,
                                 'ID'    => $current_user->ID ];
          $i++;
        }
      }      

      $data = \uncanny_learndash_reporting\ReportingApi::get_table_data();

      return ["status"=>"ok", "data"=>$data, "post"=>$_POST, "course"=>$courses];
    }else{
      // 
      // else retur error
      // 
      $return_object['message']  = __( 'Not found activated plugin "tin-canny-learndash-reporting" class. Please install and activate plugin', 'ldsubscriber' );
      return $return_object;
    }

  }

}
