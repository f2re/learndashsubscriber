<?php
/**
 *
 *  Включаем все скрипты и все что нам надо в нашу загрузку
 * 
 */
if(! class_exists('LDSubscriber_Enqueue')){
  
  class LDSubscriber_Enqueue {
    
    function __construct(){
      add_action('wp_enqueue_scripts',array($this,'enqueue'));
    }   
    
    function enqueue() {
      wp_enqueue_style('dogbooking',LDSUBSCRIBER_URl.'assets/css/main.css');
      wp_enqueue_script('jquery');  
    }
          
  }
  
  new LDSubscriber_Enqueue();        
}