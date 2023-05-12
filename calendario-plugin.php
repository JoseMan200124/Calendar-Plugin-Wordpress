<?php
/**
 * 
 * @package calendario
 * 
 */

 /*
 Plugin Name: Calendario Plugin
 Plugin URI: http://amorsintemor.com/plugin
 Description: This is a booking calendar plugin.
 Version: 1.0.0
 Author: José Daniel Man Castellanos
 Author URI: http://imdanielman.com
 License: GPLv2 or later
 Text Domain: calendario-plugin
 */


 defined('ABSPATH') or die('Oye!, ¿que estás haciendo aquí?'); 
 require_once plugin_dir_path(__FILE__) . 'calendar.php';

 class calendario{

    //public 
    //can be accessed everywhere 


    //protected
    //can be accessed only within the class itself or extensions that class

    //private
    // Can be access

    
    function __construct(){
    }
    function register(){
        add_action('admin_enqueue_scripts', array($this, 'enqueue')); 
       // Activate & Deactivate
       register_activation_hook(__FILE__, array($this, 'activate'));
       register_deactivation_hook(__FILE__, array($this, 'deactivate'));
       add_shortcode('calendario_plugin', array($this, 'output_calendar')); // Aquí se define el shortcode.

    }
    function output_calendar() {
        $calendar = new Calendar();
        return $calendar->show();
    }
    protected function create_post_type(){
        add_action('init', array($this,'custom_post_type')); 

    }
    function activate(){
        //generated a CPT
        $this->custom_post_type(); 
        //flush rewrite rules
        flush_rewrite_rules();
    }
    function deactivate(){
        //flush rewrite rules
        flush_rewrite_rules(); 
    }

     function custom_post_type(){
        register_post_type('book', ['public' => 'true', 'label' => 'Books']); 
    }
    function enqueue(){
        // enqueue all our scripts
        wp_enqueue_style('mypluginstyle', plugins_url('/assets/mystyle.css', __FILE__)); 
        wp_enqueue_style('fullcalendar-css', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css');
        wp_enqueue_script('moment-js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js', array(), false, true);
        wp_enqueue_script('fullcalendar-js', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js', array('jquery', 'moment-js'), false, true);
        wp_enqueue_script('mypluginscript', plugins_url('/assets/myscript.js', __FILE__), array('jquery', 'moment-js', 'fullcalendar-js'), false, true);
    }


 }

class SecondClass extends calendario{
    
    function register_post_type(){
        $this->create_post_type();     

    }
}
if(class_exists( 'calendario')){
    $calendarioPlugin = new calendario(); 
    $calendarioPlugin->register(); 
}

$SecondClassCalendario = new SecondClass(); 
$SecondClassCalendario->register_post_type(); 


//Activation
register_activation_hook(__FILE__, array($calendarioPlugin, 'activate')); 

// deactivation
register_deactivation_hook(__FILE__, array($calendarioPlugin, 'deactivate')); 
// uninstall
register_uninstall_hook(__FILE__, array($calendarioPlugin, 'uninstall')); 


?>