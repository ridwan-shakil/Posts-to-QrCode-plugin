<?php
/*
 * Plugin Name:       Posts to QrCode
 * Plugin URI:        https://github.com/ridwan-shakil/Posts-to-QrCode-plugin
 * Description:       Shows QrCode in the bottom of posts , users can go to the post directly jst by scanning the qrcode. You can decide where you want to shwow the qrcode and where you don't . 
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            MD.Ridwan
 * Author URI:        https://github.com/ridwan-shakil/P
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        
 * Text Domain:       posts-toQr
 * Domain Path:       /languages
 */
if (!defined('ABSPATH')) {
    exit; // exits if try to access directly
}

function post_qrcode_load_textdomain() {
    load_plugin_textdomain('posts-toQr', false, dirname(__FILE__) . '/languages');
}

// adding stylesheet 
function posts_qrcode_enque_style() {
    wp_enqueue_style('posts_to_qrcode_style', plugin_dir_url(__FILE__) . "style.css");
}

add_action('wp_enqueue_scripts', 'posts_qrcode_enque_style');

// Showing QrCode 
function post_to_qrcode_genrate($content) {
    $current_post_id = get_the_ID();
    $current_post_url =  get_the_permalink($current_post_id);  //urlencode 
    $alt_text = get_the_title($current_post_id);
    $qrheight = get_option('qrheight');
    $qrwidth = get_option('qrwidth');
    $qrheight = $qrheight ? $qrheight : 150;
    $qrwidth = $qrwidth ? $qrwidth : 150;
    $qrdimension = "{$qrheight}x{$qrwidth}";
    $qrcode_size = apply_filters('posts_qrcode_size', $qrdimension);

    // excluded post types 
    $current_post_type = get_post_type($current_post_id);
    $excluded_post_types = apply_filters('qrcode_excluded_post_tppes', []);
    if (in_array($current_post_type, $excluded_post_types)) {
        return $content;
    };

    $Qrcode =  sprintf('<div class="posts_qrcode"> <img src="https://api.qrserver.com/v1/create-qr-code/?size=%s&ecc=L&qzone=1&data=%s" alt="%s" srcset=""> </div>', $qrcode_size, $current_post_url, $alt_text);
    $content .= $Qrcode;
    return $content;
}

add_filter('the_content', 'post_to_qrcode_genrate', 9);


// adding height & width from setting options 

function add_pqrc_dimenson() {
    add_settings_section('pqrc_section', 'Post to QrCode :', '', 'general',);
    add_settings_field('height', 'QrCode height', 'clbc_pqrc_dimension', 'general', 'pqrc_section', ['height']);
    add_settings_field('width', 'QrCode width', 'clbc_pqrc_dimension', 'general', 'pqrc_section', ['width']);
    register_setting('general', 'height');
    register_setting('general', 'width');
}

function clbc_pqrc_dimension($args) {
    $value = get_option($args[0], 150);
    $name = $args[0];
    printf('<input type="text" name="%s" id="" value="%s">', $name, $value);
}

add_action('admin_init', 'add_pqrc_dimenson');
