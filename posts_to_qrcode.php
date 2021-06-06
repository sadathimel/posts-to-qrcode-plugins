<?php
/*
Plugin Name: Posts To QR Code
Plugin URI: http://github.com/sadathimel
Description: This plugins Display QR code Under every posts.
Version: 1.0
Author: sadat himel
Author URI: http://github.com/sadathimel
License: GPLv2 or later
Text Domain: posts-to-qrcode
Domain Path: /languages
 */

// function qrcode_activation_hook(){

// }
// register_activation_hook(__FILE__,"qrcode_activation_hook");

// function qrcode_deactivation_hook(){

// }
// register_deactivation_hook(__FILE__,"qrcode_deactivation_hook");

function posts_to_qrcode_load_textdomain() {
    load_plugin_textdomain( 'posts-to-qrcode', false, dirname( __FILE__ ) . "/languages" );
}
add_action( 'plugins_loaded', 'posts_to_qrcode_load_textdomain' );

function pqrc_display_qr_code( $content ) {
    $current_post_id    = get_the_ID();
    $current_post_title = get_the_title( $current_post_id );
    $current_post_url   = urlencode( get_the_permalink( $current_post_id ) );
    $current_post_type  = get_post_type( $current_post_id );
    /**
     * post type chack
     */
    $excluded_post_type = apply_filters( "pqrc_excluded_post_type", [] );
    if ( in_array( $current_post_type, $excluded_post_type ) ) {
        return $content;
    }

    /**
     * Dimension Hook
     */
    $dimension = apply_filters('pqrc_qrcode_dimension', '150x150');

    /**
     * Image Attributes
     */
    $image_attributes = apply_filters('pqrc_image_attributes', '');

    $image_src = sprintf( 'https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s', $dimension,$current_post_url );
    $content .= sprintf( "<div class ='qrcode'><img %s src='%s' alt='%s' /> </div>", $image_attributes, $image_src, $current_post_title );
    return $content;
}
add_filter( 'the_content', 'pqrc_display_qr_code' );
