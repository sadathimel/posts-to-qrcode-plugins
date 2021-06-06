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

function posts_to_qrcode_load_textdomain(){
    load_plugin_textdomain('posts-to-qrcode', false, dirname(__FILE__) . "/languages");
}
add_action( 'plugins_loaded', 'posts_to_qrcode_load_textdomain' );

function pqrc_display_qr_code($contant){
    $current_post_id = get_the_ID();
    $cuttent_post_url = get_the_permalink($current_post_id);
    $image_src = urlencode(sprintf('https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=%s'));
}
add_filter( 'the_content', 'pqrc_display_qr_code');