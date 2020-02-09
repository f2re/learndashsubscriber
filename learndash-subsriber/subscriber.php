<?php
/**
 * Plugin Name: LearnDashSubscriber
 * Plugin URI: https://github.com/f2re/subscriber
 * Description: Plugin for learndash subscribe
 * Version: 0.1
 * Author: f2re
 * Author URI: https://github.com/f2re
 * License: GPL2
 * Text Domain: subscriber
 * Domain Path: /language/
 */
 
/*  Copyright 2020  f2re  (email : lendingad@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// Start writing code after this line!


/**
 * defining contants
 */
if(! defined('ABSPATH')) exit;
if( ! defined('LDSUBSCRIBER_URl')){
  define('LDSUBSCRIBER_URl',plugin_dir_url(__FILE__));
}
if( ! defined('LDSUBSCRIBER_PATH')){
  define('LDSUBSCRIBER_PATH',plugin_dir_path(__FILE__));
}



/**
 * Регистрируем и описываем класс
 */
if(! class_exists('LDSubscriber_init')){
  class LDSubscriber_init {
    function __construct(){ 
      add_action('plugins_loaded',[$this,'include_dependencies']);
    }        
    function include_dependencies() {
      include LDSUBSCRIBER_PATH.'/core/init.php';
    }              
  }        
}
// 
// запускаем клиентов
// 
new LDSubscriber_init();



