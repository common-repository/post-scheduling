<?php
/**
 * Plugin Name: Post Scheduler
 * Plugin URI: http://www.vijaywebsolutions.com/
 * Description: A plugin that helps to scheduling of posts as per users requirement depending upon the range of dates
 * Version: 1.0
 * Author: Vijay Web Solutions
 * Author URI: http://www.vijaywebsolutions.com/
 * License: GPL2
 */

include_once(plugin_dir_path(__FILE__) . 'post-scheduler-option.php');

add_action( 'admin_init', 'post_scheduler_scripts' );

function post_scheduler_scripts(){

	wp_enqueue_style('custom-wp-admin-css', plugins_url('/css/post-scheduler-css.css' , __FILE__));
	
	wp_enqueue_script('jquery-ui-datetimepicker', plugins_url('/js/jquery.datetimepicker.js', __FILE__));
	
	wp_enqueue_style('custom-ui-datetimepicker', plugins_url('/js/jquery.datetimepicker.css' , __FILE__));
	
	wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-form');
}

add_action('admin_footer', 'ps_datetimepicker_script');

function ps_datetimepicker_script(){ ?>
	<script type="text/javascript">
	    jQuery(document).ready(function(){
	    	var dt = new Date();
			var datetime = dt.getFullYear() + "/" + (dt.getMonth()+1) + "/"  + dt.getDate() + " " + dt.getHours() + ":" + dt.getSeconds();
            
	        jQuery('.example-datepicker').datetimepicker().datetimepicker({value:datetime,step:10});
	    });
    </script>
<?php
	wp_enqueue_script( 'jquery-form-validation', plugins_url('/js/jquery-form-validation.js', __FILE__));
}

add_action('wp_head', 'add_meta_desc_tag');

function add_meta_desc_tag() {
		global $post;	
		$text = strip_tags($post->post_excerpt);		
		if ( is_single() || is_page() && !empty($text) ) {	
			print "\n".'<meta name="description" content="'.$text.'" />'."\n";
		}
}
?>