<?php
/* 
* Plugin Name: Open Graph Headers for WP
* Description: Plugin adds meta tags to pages for sharing on social nets with OpenGraph standards (https://ogp.me/). There is no any features. Upload plugin folder to the `/wp-content/plugins/` directory. Activate the plugin through the 'Plugins' menu in WordPress. Enjoy!
* Plugin URI: https://github.com/alexlead/al_open_graph
* Version: 1.0.1
* Author: Alexander Lead
* Author URI: https://codepen.io/alexlead/
* License: GPL 
* License URI: https://www.gnu.org/licenses/gpl-3.0.html 
*
*/


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {	
	exit;
}


/*
* @return array: Open Graph properties
*/ 
if( !function_exists( 'al_og_properties_list' ) ) {
    function al_og_properties_list(){
        // return of array with OG properties
        return array(
            'title', 'url', 'description', 'image', 'type', 'video', 'audio' 
        );
    }
}

/* 
* getting title of page for OG property 
* @return string:title of page
*/
if( !function_exists( 'al_og_page_title' ) ){
    function al_og_page_title(){
        // return title of post or blog name for other pages
        return (is_single() || is_page())? get_the_title() : get_bloginfo('name'); 
    }
}
 
/* 
* getting url of page for OG property 
* @return string:url of page
*/
if( !function_exists( 'al_og_page_url' ) ){
    function al_og_page_url(){
        // return post link or link of current page
        return (is_single() || is_page())?get_permalink(): (( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); 
    }
}


/* 
* getting description of page for OG property 
* @return string:description of page
*/
if( !function_exists( 'al_og_page_description' ) ){
    function al_og_page_description(){
        // return excerpt from post or blog info if this is not post
        $str = (is_single() || is_page())?  get_the_excerpt() :  get_bloginfo('description');
        $str = str_replace( "'", '"' ,$str);
        $str = substr($str, 0, 200).'...';

        return $str; 
    }
}

/* 
* getting image of page for OG property 
* @return string:image url of page
*/
if( !function_exists( 'al_og_page_image' ) ){
    function al_og_page_image(){
        $image_url = '';
        // prepare image for posts & pages
        if((is_single() || is_page())){
            // if post has thumbnail image - use it
            $image_url = get_the_post_thumbnail_url();
                if ( strlen( $image_url ) > 0 ){
                    return $image_url;
                } 
            // if post contain any image inside - use it
            // get post id
            $id = get_queried_object_id();
            // get all images from post
            $attachment_image = get_attached_media('image', $id);

            // take first image from array
            $attachment_image = array_shift($attachment_image);
            
            // get image URL
            $image_url = wp_get_attachment_url( $attachment_image->ID );
            if ( strlen( $image_url ) > 0 ){
                return $image_url;
            } 

        }

        // take blog logo for other cases
        $image_url = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
        


        return $image_url; 
    }
}


/* 
* getting type of page for OG property 
* @return string:type of page
*/
if( !function_exists( 'al_og_page_type' ) ){
    function al_og_page_type(){
        // return type of link
        $type = 'website';
        if(is_single() || is_page()){
            $type = 'article';
        }
        if ( is_author() ) {
			$type = 'profile';
		}

        return $type;
    }
}

/* 
* getting audio from page for OG property 
* @return string: audio URL
*/
if( !function_exists( 'al_og_page_audio' ) ){
    function al_og_page_audio(){

        // get post id
        $id = get_queried_object_id();
        // get all audio from post
        $attachment_audio = get_attached_media('audio', $id);

        // take first audio from array
        $attachment_audio = array_shift($attachment_audio);

        // get audio URL
        $audio_url = (is_array( $attachment_audio ) ) ?  $attachment_audio->guid : '';

        return $audio_url;
    }
}

/* 
* getting video from page for OG property 
* @return string: video URL
*/
if( !function_exists( 'al_og_page_video' ) ){
    function al_og_page_video(){

        // get post id
        $id = get_queried_object_id();
        // get all video from post
        $attachment_video = get_attached_media('video', $id);

        // take first video from array
        $attachment_video = array_shift($attachment_video);

        // get video URL
        $video_url = (is_array( $attachment_video ) ) ?  $attachment_video->guid : '';

        return $video_url;
    }
}

/* 
* 
* @return string: with meta tags
*/

if ( !function_exists( 'al_og_prepare_meta' ) ){
    function al_og_prepare_meta(){
    // get all properties for OG Meta
    $properties = al_og_properties_list();
    // Prepare string of properties
    $meta_string = "<!--  Open Graph Meta tags  --> \n\r";
    // prepare all meta tags with the prperites array
    foreach ($properties as $key => $value) {
        // take function name for current property
        $func = 'al_og_page_'.$value;
        // check if the function exists
        if ( function_exists($func) ) {
            // get data from the function
            $str = $func();
            // prepare meta string if the data are not empty
            if(strlen($str)>0){
            $meta_string .="<meta property='og:$value' content='$str' /> \n\r";  
            }
        }
    }

    // close meta tags with comment string
    $meta_string .= "<!-- END: Open Graph Meta tags  --> \n\r";
    // return prepared string
    return $meta_string;
    }
}

/* 
* @output string of meta tags
*/
if ( !function_exists( 'al_og_meta_preparing' ) ){
    function al_og_meta_preparing() { 
        // output meta tags to header
        echo al_og_prepare_meta();

        
    }

}

// add action - function starts with head of page preparing
add_action('wp_head', 'al_og_meta_preparing');