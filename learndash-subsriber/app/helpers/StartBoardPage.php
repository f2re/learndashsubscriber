<?php
namespace Helpers;

/**
 * класс 
 */
class StartBoardPage {
  
  /**
   * registration fields output
   * @var array
   */
  private $_fields = [];

  private $_template = LDSUBSCRIBER_PATH.'/app/view/startboard.html';

  function __construct($noenqueue=false){
    // 
    // include validate and jquery scripts
    // 
    if ( !$noenqueue ) {
      $this->enqueue_scripts();
    }

  }

  /**
   * render register form
   * @return [type] [description]
   */
  public function renderform(){
    // 
    // if user logged in - then redirect
    // 
    // if (!is_user_logged_in()) {
    //   wp_redirect(get_permalink( get_page_by_path( 'login' ) )); 
    //   exit;
    // }

    $userid = 0;

    // проверяем вошел ли пользователь
    if (is_user_logged_in()) {
      $current_user       = wp_get_current_user();
      // 
      // get user from DB
      // 
      $groupsids = learndash_get_users_group_ids( $current_user->ID );
      $userid    = $current_user->ID;
      
    }


    // 
    //  star record
    // 
    ob_start();
    // 
    // start section with forms
    // 
    echo '<section class="leadersection-subscriber-startboard" id="leadersection-subscriber-startboard" postid="'.get_the_ID().'" userid="'.$userid.'">';

    $this->write_user_scores($user_data);
    // print_r(\uncanny_learndash_reporting\ReportingApi::get_labels());

    echo '</section>';

    // 
    // load template from file
    // 
    $doc = new \DOMDocument();
    $doc->loadHTMLFile($this->_template);

    echo '<div id="LDS-popup" class="overlay">
            <div class="popup">';
    echo $doc->saveHTML();
    echo '  </div>
          </div>';

    // 
    // return data
    // 
    return ob_get_clean();
  }


  /**
   * echo user fileds
   * @param  [type] $userdata [description]
   * @return [type]           [description]
   */
  function write_user_scores($userdata){
    
    // 
    // begin form
    // 
    $this->form_begin('user-form');
    // 
    // iterate over fields
    // 
    
    // 
    // finish form user
    // 
    $this->form_end();
   
  }



  /**
   * user form begin
   * @return [type] [description]
   */
  public function form_begin($id='user-form'){
    echo " <form class='".$id." ' id='".$id."' action='".admin_url('admin-ajax.php')."' method='post'>";

  }

  /**
   * user form end
   * @return [type] [description]
   */
  public function form_end($buttons=''){
    echo $buttons."
      </form>";
  }

  /**
   * [enqueue_scripts jquery and validate
   * @return [type] [description]
   */
  public function enqueue_scripts(){
    // echo 'enquerue';
    wp_enqueue_script('jquery'); 
    // wp_enqueue_script('ldsubscriber-require', LDSUBSCRIBER_URl.'assets/js/require.js',['jquery'],'1.0',true );
    // wp_enqueue_script('ldsubscriber-useradmin', LDSUBSCRIBER_URl.'assets/js/userboard.js',['jquery','ldsubscriber-require'],'1.0',true );
    wp_enqueue_script('ldsubscriber-useradmin', LDSUBSCRIBER_URl.'assets/js/userboard.js',['jquery'],'1.0',true );
  }
  
  
}