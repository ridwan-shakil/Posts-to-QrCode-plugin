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

class main {
    function __construct() {

        add_action('admin_enqueue_scripts', array($this, 'pqrc_enque_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'posts_qrcode_enque_style'));
        add_filter('the_content', array($this, 'post_to_qrcode_genrate'), 9);
        add_action('admin_init', array($this, 'add_pqrc_dimenson'));
    }

    // Load textdomain 
    function post_qrcode_load_textdomain() {
        load_plugin_textdomain('posts-toQr', false, dirname(__FILE__) . '/languages');
    }

    // adding scripts for admin panel 
    function pqrc_enque_admin_scripts($screen) {
        if ($screen == 'options-general.php') {
            // css
            wp_enqueue_style('pqrc_minitoggle_css', plugin_dir_url(__FILE__) . "assets/css/minitoggle.css");


            // js 
            wp_enqueue_script('pqrc_minitoggle_js', plugin_dir_url(__FILE__) . "/assets/js/minitoggle.js", ['jquery'], '1.0', true);

            wp_enqueue_script('pqrc_main_js', plugin_dir_url(__FILE__) . '/assets/js/pqrc_main.js', ['jquery'], time(), true);
        }
    }

    // adding stylesheet for frontend
    function posts_qrcode_enque_style() {
        wp_enqueue_style('posts_to_qrcode_style', plugin_dir_url(__FILE__) . "style.css");
    }

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

    // ===================================
    // adding height & width from setting options 
    // ===================================
    function add_pqrc_dimenson() {
        add_settings_section('pqrc_section', 'Post to QrCode :', '', 'general',);
        add_settings_field('qrheight', 'QrCode height', array($this, 'clbc_pqrc_dimension'), 'general', 'pqrc_section', ['qrheight']);
        add_settings_field('qrwidth', 'QrCode width', array($this, 'clbc_pqrc_dimension'), 'general', 'pqrc_section', ['qrwidth']);
        add_settings_field('pqrc_country', 'Select country', array($this, 'clbc_pqrc_country'), 'general', 'pqrc_section');
        add_settings_field('pqrc_minitoggle', 'Mini toggle', array($this, 'clbc_pqrc_minitoggle'), 'general', 'pqrc_section');

        register_setting('general', 'qrheight', array(' sanitize_callback' => 'esc_attr'));
        register_setting('general', 'qrwidth', array(' sanitize_callback' => 'esc_attr'));
        register_setting('general', 'pqrc_country', array(' sanitize_callback' => 'esc_attr'));
        register_setting('general', 'pqrc_minitoggle');
    }
    // Add heitht and width field  
    function clbc_pqrc_dimension($args) {
        $value = get_option($args[0], 150);
        $name = $args[0];
        printf('<input type="text" name="%s" id="" value="%s">', $name, $value);
    }
    // Select country 
    function clbc_pqrc_country() {
        $option = get_option('pqrc_country');
        $countries = [
            'None', 'Bangladesh', 'india', 'Nepal', 'Vutan', 'Pakistan'
        ];

        $countries = apply_filters('pqrc_add_country', $countries);
        echo '<select name="pqrc_country" id="pqrc_country">';

        foreach ($countries as $country) {
            $selected = '';
            if ($option == $country) {
                $selected = 'selected';
            }
            printf('<option value="%s" %s >%s</option>', $country, $selected, $country);
        }



        echo ' </select>';
    }
    // Show Minitoggle
    function clbc_pqrc_minitoggle() {
        $value = get_option("pqrc_minitoggle");
        printf('<div id="toggle1"></div>');
        echo '<input type="hidden" id="toggle1_hidden" name="pqrc_minitoggle" value=' . "$value" . ' >';
    }
}

new main();
