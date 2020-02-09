<?php

// require DOGBOOKING_PATH.'/vendor/autoload.php';

// enqueue styles and scripts with class-enqueue.php
// helper functions helper.php
include_once DOGBOOKING_PATH.'/core/helpers.php';
//  *  Включаем все скрипты и все что нам надо в нашу загрузку
include_once DOGBOOKING_PATH.'/core/class-enqueue.php';

// 
// add ajax actions
// 
// $actions = new Controllers\ClientController();
// add_action('wp_ajax_delete_client',[$actions,'delete']);


// 
// Шорткод для контактной формы
// 
// add_shortcode('dogbooking_contactform','dogbooking_contactform_shortcode');
// function dogbooking_contactform_shortcode(){
//   $contactform = new Helpers\ContactForm();
//   return $contactform->renderform();
// }
