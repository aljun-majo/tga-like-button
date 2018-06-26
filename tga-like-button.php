<?php
/*
Plugin Name:  TGA Like Button and FB emojis
Plugin URI:   https://thegoodartisan.com
Description:  Video series demo Only, with FB emojis
Version:      0.2
Author:       baymax
Author URI:   https://thegoodartisan.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  thegoodartisan
Domain Path:  /languages


This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 
2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
with this program. If not, visit: https://www.gnu.org/licenses/

*/

// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {	
	exit;	
}


// enqueue scripts
function ajax_public_enqueue_scripts( $hook ) {

	// define script url
	$script_url = plugins_url( 'public/js/tga-like-button.js', __FILE__ );

	//Set Your Nonce
	$ajax_nonce = wp_create_nonce( "thegoodartisan-security-nonce" ); 

	// define ajax url http:// or https://
	$ajax_url = admin_url( 'admin-ajax.php' );

	// define script
	$script = array( 
		'ajax_nonce'=> $ajax_nonce,
		'ajaxurl' => $ajax_url 
		);


	if( is_single() || is_home() ) {

		wp_enqueue_style( 'tga-like-button', plugins_url( 'public/css/tga-like-button.css', __FILE__ ) );

		// enqueue script
		wp_enqueue_script( 'tga-script-public', $script_url, array( 'jquery' ) );	

		//must be added after wp_enqueue_script 'tga-script-public'
		// localize script
		wp_localize_script( 'tga-script-public', 'ajax_public_handle', $script );			

	}



}
add_action( 'wp_enqueue_scripts', 'ajax_public_enqueue_scripts' );


function tga_like_button_append_content( $content ) {

	//if( is_single() ) { 
	if( is_single() || is_home() ) { 
	  $append_likebutton_content = '';  


		// table = wp_postmeta   
		// rows = post_id = ### (unique value) || meta_key = 'like_count' || meta_value = (start 1) or (incremented value)
		$like = get_post_meta( get_the_ID(), 'like_count', true );//ex: meta_value = 5
		$like = ( empty( $like ) ) ? 0 : $like;// set value to zero if empty

		// echo "<pre>";
		// echo "<h1>strval($like)</h1>";
		// print_r( strval($like) );
		// echo "</pre>";
		// echo "<pre>";
		// echo "<h1>strval(intval</h1>";
		// print_r( strval(intval($like)) ); 
		// echo "</pre>"; 

		//make sure it is a number
		//ex strval(intval($like) =  1a => 1
		//ex strval(intval($like) =  bbbb => 0
		// check php site http://php.net/manual/en/function.strval.php
		// check php site http://php.net/manual/en/function.intval.php
		if(  strval($like) != strval(intval($like)) ) { 
			//if not a number set to '0' 
			$like = 0;
		} 

		$like_text = ($like != 0) ? 'Liked' : 'Like';     
		
		$append_likebutton_content = '<div class="like-wrapper py-3">
											<a class="btn btn-danger" href="' . admin_url( 'admin-ajax.php?action=tga_ajax_public_handler&post_id=' . get_the_ID() ) . '" data-id="' . get_the_ID() . '">
												' . $like_text . '   
												<span class="' . get_the_ID() . ' like-count-ui ml-3 badge badge-light btn-lg">' . number_format_i18n( (int)$like ) . 
												'</span>  
											</a> 
										</div>';   
	 
		$content = $content . $append_likebutton_content;   

	 }//if is_single() 

  return $content; 
}
add_filter( 'the_content', 'tga_like_button_append_content' );

 
// process ajax request 
function tga_ajax_public_handler() {  
 
	//check the $ajax_nonce value 
	check_ajax_referer( 'thegoodartisan-security-nonce', 'security' );    
  
	//find the post meta curret value  
	// wp_postmeta table AND rows 
	// post_id = ### (unique value) || meta_key = 'like_count' || meta_value = (start 1) or (incremented value)
	$like = get_post_meta( $_REQUEST['post_id'], 'like_count', true );//ex meta_value = 5

		//force $like to be a number
		//ex strval(intval($like) =  1a => 1
		//ex strval(intval($like) =  bbbb => 0
		// check php site http://php.net/manual/en/function.strval.php
		// check php site http://php.net/manual/en/function.intval.php
		if(  strval($like) != strval(intval($like))  ) {

			if ( (int)$like == '0' ) {
				$like = 0;
			} else {
				$like = strval(intval($like));
			}

		}
		//increment current value = 6
		$like++;//5 + 1 = 6


	//send/update to database the new value = 6
	update_post_meta( $_REQUEST['post_id'], 'like_count', $like );

	//char char
	if ( wp_doing_ajax() ) {
	    echo $like;
		die();
	} else {
		wp_redirect( get_permalink( $_REQUEST['post_id'] ) );
		exit();
	}

	// end processing
	wp_die(); 

}

// ajax hook for logged-in users: wp_ajax_{action}
add_action( 'wp_ajax_tga_ajax_public_handler', 'tga_ajax_public_handler' );

// ajax hook for non-logged-in users: wp_ajax_nopriv_{action}
add_action( 'wp_ajax_nopriv_tga_ajax_public_handler', 'tga_ajax_public_handler' );







