<?php
/*
 * Plugin Name:       Posts to QrCode
 * Plugin URI:        https://github.com/ridwan-shakil/Posts-to-QrCode-plugin
 * Description:       Shows QrCode in the bottom of posts , users can go to the post directly jst by scanning the qrcode. You can decide where you want to shwow the qrcode and where you don't . 
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            MD.Ridwan
 * Author URI:        
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       posts-toQr
 * Domain Path:       /languages
 */
if (!defined('ABSPATH')) {
    exit; // exits if try to access directly
}

function post_qrcode_load_textdomain() {
    load_plugin_textdomain('posts-toQr', false, dirname(__FILE__) . '/languages');
}

function post_to_qrcode_genrate($content) {
    $current_post_id = get_the_ID();
    $current_post_url =  get_the_permalink($current_post_id);  //urlencode 
    $alt_text = get_the_title($current_post_id);
    $qrcode_size = '150*200';

    $Qrcode =  sprintf('<img src="https://api.qrserver.com/v1/create-qr-code/?size=%s&ecc=L&qzone=1&data=%s" alt="%s" srcset="">', $qrcode_size, $current_post_url, $alt_text);
    $content .= $Qrcode;
    return $content;
}

add_filter('the_content', 'post_to_qrcode_genrate', 10);
