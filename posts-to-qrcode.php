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

function posts_qrcode_enque_style() {
    wp_enqueue_style('posts_to_qrcode_style', plugin_dir_url(__FILE__) . "style.css");
}

add_action('wp_enqueue_scripts', 'posts_qrcode_enque_style');


function post_to_qrcode_genrate($content) {
    $current_post_id = get_the_ID();
    $current_post_url =  get_the_permalink($current_post_id);  //urlencode 
    $alt_text = get_the_title($current_post_id);
    $qrcode_size = '100x100';
    $qrcode_size = apply_filters('posts_qrcode_size', $qrcode_size);

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

add_filter('the_content', 'post_to_qrcode_genrate', 10);
