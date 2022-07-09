<?php
/* 
* Plugin Name: Open Graph Headers for WP
* Description: Plugin adds meta tags to pages for sharing on social nets with OpenGraph standards (https://ogp.me/). There is no any features. Upload plugin folder to the `/wp-content/plugins/` directory. Activate the plugin through the 'Plugins' menu in WordPress. Enjoy!
* Plugin URI: https://github.com/alexlead/al_open_graph
* Version: 1.0.2
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


if( !class_exists( 'al_og_meta_properties') ) {

    class AL_OG_META_PROPERTIES
    {
        private $meta_tags;
        private $id;

        public function __construct()
        {
            // meta tags properties
            $this->meta_tags = array(
                'title', 'url', 'description', 'image', 'type', 'video', 'audio' 
            );

            $this->id = get_queried_object_id();
            $this->template();
        }

        
        
        /**
         * Service function
         * GET PAGES ATTACHED MEDIA
         * @get String
         * @return String
         * **/ 
        private function attached( $media )
        {
            // get all audio from post
            $attachment_media = get_attached_media( $media, $this->id);
    
            // take first audio from array
            $attachment_media = array_shift($attachment_media);
    
            // get audio URL
            $media_url = (is_array( $attachment_media ) ) ?  $attachment_media->guid : '';
    
            return $media_url;
        }
        
        
        /**
         * Prepare template properties
         * **/
        public function template ()
        {
            $data = array();
            foreach ( $this->meta_tags as $key ) {

                $data[$key] = $this->$key();

            }
            include( dirname( __FILE__ ) . "/templates/meta_tags.php" );
        }

        /**
         * Page title
         * **/ 
        private function title()
        {
            return (is_single() || is_page())? get_the_title() : get_bloginfo('name'); 
        }

        /**
         * Page URL 
         * **/ 
        private function url()
        {
            return (is_single() || is_page())?get_permalink(): (( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); 
        }

        /**
         * Page description
         * **/ 
        private function description()
        {
            // return excerpt from post or blog info if this is not post
            $str = (is_single() || is_page())?  get_the_excerpt() :  get_bloginfo('description');
            $str = str_replace( "'", '"' ,$str);
            $str = substr($str, 0, 200).'...';

            return $str; 
        }

        /**
         * Page Image
         * **/ 
        private function image()
        {
            $image_url = '';
            // prepare image for posts & pages
            if((is_single() || is_page())){
                // if post has thumbnail image - use it
                $image_url = get_the_post_thumbnail_url();
                    if ( strlen( $image_url ) > 0 ){
                        return $image_url;
                    } 
                // if post contain any image inside - use it
                // get image URL
                $image_url = $this->attached('image');
                if ( strlen( $image_url ) > 0 ){
                    return $image_url;
                } 
    
            }
    
            // take blog logo for other cases
            $image_url = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
            
    
            return $image_url; 
        }

        /***
         * Page type
         * **/ 
        private function type()
        {
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

        /**
         * Page video
         * **/ 
        private function video()
        {    
            return $this->attached('video');
        }

        /**
         * Page audio
         * **/ 
        private function audio()
        {
            return $this->attached('audio');
        }
        
    }
}


/* 
* @output string of meta tags
*/
if ( !function_exists( 'al_og_meta_preparing' ) ){
    function al_og_meta_preparing() {
        if ( is_admin() ) {
            return;
        } 
        // output meta tags to header
        $meta_tags = new \AL_OG_META_PROPERTIES();

        
    }

}

// add action - function starts with head of page preparing
add_action('wp_head', 'al_og_meta_preparing');
