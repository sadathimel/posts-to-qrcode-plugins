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

$pqrc_countries = [
    __('None','posts-to-qrcode'),
    __('Afghanistan','posts-to-qrcode'),
    __('Bangladesh','posts-to-qrcode'),
    __('Bhutan','posts-to-qrcode'),
    __('India','posts-to-qrcode'),
    __('Maldives','posts-to-qrcode'),
    __('Nepal','posts-to-qrcode'),
    __('Pakistan','posts-to-qrcode'),
    __('Sri Lanka','posts-to-qrcode')
];

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
    $height    = get_option( 'pqrc_height' );
    $width     = get_option( 'pqrc_width' );
    $height    = $height ? $height : 180;
    $width     = $width ? $width : 180;
    $dimension = apply_filters( 'pqrc_qrcode_dimension', "{$width}x{$height}" );

    /**
     * Image Attributes
     */
    $image_attributes = apply_filters( 'pqrc_image_attributes', '' );

    $image_src = sprintf( 'https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s', $dimension, $current_post_url );
    $content .= sprintf( "<div class ='qrcode'><img %s src='%s' alt='%s' /> </div>", $image_attributes, $image_src, $current_post_title );
    return $content;
}
add_filter( 'the_content', 'pqrc_display_qr_code' );

function pqrc_settings_init() {
    add_settings_section('pqrc_section',__('Post to QR Code Plugins','posts-to-qrcode'),'pqrc_section_callback','general');

    add_settings_field( "pqrc_height", __( "Qr Code Height", "posts-to-qrcode" ), "pqrc_display_field", "general",'pqrc_section',['pqrc_height'] );
    add_settings_field( 'pqrc_width', __( 'Qr Code width', 'posts-to-qrcode' ), 'pqrc_display_field', 'general','pqrc_section',['pqrc_width'] );
    // add_settings_field( 'pqrc_extra', __( 'Extra Field', 'posts-to-qrcode' ), 'pqrc_display_field', 'general','pqrc_section',['pqrc_extra'] );
    add_settings_field( 'pqrc_select', __( 'Dropdown', 'posts-to-qrcode' ), 'pqrc_display_select_field', 'general','pqrc_section');
    add_settings_field( 'pqrc_checkbox', __( 'Checkbox', 'posts-to-qrcode' ), 'pqrc_display_checkbox_field', 'general','pqrc_section');
    // add_settings_field( 'pqrc_radio', __( 'Radio', 'posts-to-qrcode' ), 'pqrc_display_radio_field', 'general','pqrc_section');


    register_setting( 'general', 'pqrc_height', [ 'sanitize_callback' => 'esc_attr' ] );
    register_setting( 'general', 'pqrc_width', [ 'sanitize_callback' => 'esc_attr' ] );
    // register_setting( 'general', 'pqrc_extra', [ 'sanitize_callback' => 'esc_attr' ] );
    register_setting( 'general', 'pqrc_select', [ 'sanitize_callback' => 'esc_attr' ] );
    register_setting( 'general', 'pqrc_checkbox');
    // register_setting( 'general', 'pqrc_radio');
}

// function pqrc_display_radio_field(){
//     $option = get_option('pqrc_radio');
//     foreach($pqrc_countries as $country){
//         $selected = '';
//         if(is_array($option) && in_array($country,$option)){
//             $selected = 'checked';
//         };
//         printf("<input type='radio' name ='%s' value='%s' %s /> %s",$country,$country,$selected,$country);
//     }
// }

function pqrc_display_checkbox_field(){
    global $pqrc_countries;
    $option = get_option('pqrc_checkbox');
    $pqrc_countries = apply_filters('pqrc_countries',$pqrc_countries);
    foreach($pqrc_countries as $country){
        $selected = '';
        if(is_array($option) && in_array($country,$option)){
            $selected = 'checked';
        };
        printf("<input type='checkbox' name ='pqrc_checkbox[]' value='%s' %s /> %s",$country,$selected,$country);
    }
}

function pqrc_display_select_field(){
    global $pqrc_countries;
    $option = get_option('pqrc_select');
    $pqrc_countries = apply_filters('pqrc_countries',$pqrc_countries);
    printf("<select id='%s' name='%s'>",'pqrc_select','pqrc_select');
    foreach($pqrc_countries as $country){
        $selected = '';
        if($option == $country) $selected ='selected';
        printf("<option value='%s' %s >%s</option>",$country,$selected,$country);
    }
    echo "</select>";
}

function pqrc_section_callback(){
    echo "<p>".__('Setting to Post to QR Code Plugins','posts-to-qrcode')."</p>";   
}

function pqrc_display_field($args){
    $option = get_option($args[0]);
    printf( "<input type='text' id='%s' name='%s' value='%s'/>", $args[0], $args[0], $option );
}

function pqrc_display_height() {
    $height = get_option( 'pqrc_height' );
    printf( "<input type='text' id='%s' name='%s' value='%s'/>", 'pqrc_height', 'pqrc_height', $height );
}
function pqrc_display_width() {
    $width = get_option( 'pqrc_width' );
    printf( "<input type='text' id='%s' name='%s' value='%s'/>", 'pqrc_width', 'pqrc_width', $width );
}

add_action( 'admin_init', 'pqrc_settings_init' );