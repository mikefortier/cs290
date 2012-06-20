<?php
/*
Plugin Name: jQuery Slider
Description: jQuery slider with lots of customization options
Author: Vijay Kumar
Version: 1.2
Author URI: http://www.wp-contents.com
Plugin URI: http://www.wp-contents.com/jquery-slider/

 Copyright 2011  Vijay Kumar  (email : bidla.vijay@gmail.com)

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
//This is a comment
define('JS_DIR', WP_PLUGIN_DIR.'/jquery-slider');
define('JS_URL', WP_PLUGIN_URL.'/jquery-slider');

include_once 'settings.php';

// Activating plugin
register_activation_hook(__FILE__, 'js_activate');
function js_activate(){
	add_option('js_width', '750');
	add_option('js_height', '345');
	add_option('js_pause', true);
	add_option('js_paging', true);
	add_option('js_nav', true);
}

/* Slider Post Types */
add_action('init', 'js_custom_init');
function js_custom_init() 
{
  $labels = array(
	'name' => _x('Slides', 'post type general name'),
    'singular_name' => _x('Slide', 'post type singular name'),
    'add_new' => _x('Add New', 'slide'),
    'add_new_item' => __('Add New Slide'),
    'edit_item' => __('Edit Slide'),
    'new_item' => __('New Slide'),
    'view_item' => __('View Slide'),
    'search_items' => __('Search Slides'),
    'not_found' =>  __('No slides found'),
    'not_found_in_trash' => __('No slides found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => 'Slides'
  );
  $args = array(
	'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => false, 
    'hierarchical' => false,
    'menu_position' => 20,
    'supports' => array('title','editor','custom-fields','thumbnail')
  ); 
  register_post_type('slide',$args);
}

// Load javascripts and css files
if(!is_admin()){
	add_action('wp_print_scripts', 'js_load_js');
	function js_load_js(){
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquerySliderJs', JS_URL.'/js/jquerySlider.min.js');
	}

	add_action('wp_print_styles', 'js_load_css');
	function js_load_css(){
		wp_enqueue_style('jquerySliderCss', JS_URL.'/css/jquery-slider.css');
	}

	add_action('wp_head', 'js_head_code');
	function js_head_code(){
		$out = "<script type='text/javascript'>
					jQuery(document).ready(function(){
						jQuery('.slider').jquerySlider({
							width:".get_option('js_width').", 
							height:".get_option('js_height').", pauseSlideshowOnHover:".get_option('js_pause').",
						});
					});
				</script>";

		echo $out;
	}
}

function jquery_slider(){
	global $post;
	
	$qry = new WP_Query('post_type=slide&showposts=-1');
	if($qry->have_posts()):
	  
	  $out = '<div class="slider">';
		while($qry->have_posts()) : $qry->the_post();
		$out .= '<div class="slider-item">';

		  $images = get_posts( 'post_parent='.$post->ID.'&post_type=attachment&post_mime_type=image' );

		  if ( empty($images) ) {
				// no attachments here
		  } else {
				$imgAttr = wp_get_attachment_image_src( $images[0]->ID );
				$out .= '<img src="'.$images[0]->guid.'" />';
				$out .= '<img class="thumbnail" src="'.$imgAttr[0].'" />';
		  }

		$out .= '<div class="caption">'.get_the_content($post->ID).'</div>';
	  $out .= '</div>';
	  endwhile;
	$out .= '</div>';
	endif;
	wp_reset_postdata();

	return $out;
}

add_shortcode('jQuery Slider', 'jquery_slider');
add_theme_support('post-thumbnails');