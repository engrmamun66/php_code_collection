<?php

/**
* Plugin Name: Rentmy CDN
* Description: Enables your customers to make rental reservations from your website by Connecting Wordpress to the RentMy Reservation Software.
* Plugin URI: 
* Author: 
* Version: 1.0.0
* Author URI: https://rentmy.co
* Copyright: 
* Text Domain: rentmybusiness 
* Domain Path: /languages/
*/

defined( 'ABSPATH' ) || exit;

if(!defined('RNTM_CDN_PLUGIN_FILE')) define('RNTM_CDN_PLUGIN_FILE', __FILE__);
if(!defined('RNTM_CDN_PLUGIN_DIR')) define('RNTM_CDN_PLUGIN_DIR', rtrim(plugin_dir_path(__FILE__), '/') . '/');
if(!defined('RNTM_CDN_PLUGIN_URL')) define('RNTM_CDN_PLUGIN_URL', rtrim(plugin_dir_url(__FILE__), '/') . '/');
if(!defined('RNTM_CDN_THEME_DIR')) define('RNTM_CDN_THEME_DIR', rtrim(get_template_directory(), '/') . '/');
if(!defined('RNTM_CDN_THEME_DIR_URI')) define('RNTM_CDN_THEME_DIR_URI', rtrim(get_template_directory_uri(),'/') . '/');
if(!defined('RNTM_TEXT_DOMAIN')) define('RNTM_TEXT_DOMAIN', 'rentmybusiness');
if(!defined('RNTM_CDN_VERSION')) define('RNTM_CDN_VERSION', '1.0');

/* ------------------------------------------------------- */
/*                  Plugin Activation Hook                 */
/* ------------------------------------------------------- */
require_once( __DIR__ . '/inc/activation-hook.php' );

/* ------------------------------------------------------- */
/*                   Register Ajax Request                 */
/* ------------------------------------------------------- */
require_once( __DIR__ . '/inc/ajax-apis/http.php' );


function rentmycdn_enqueue_my_scripts($hook) {
    wp_enqueue_style('rentmy-style', '//localhost:4444/assets/index.css', array(), '1.0', 'all');     
    wp_enqueue_script('rentmy-script', '//localhost:4444/assets/script.js', [], '1.0', array('in_footer' => true,'strategy'  => 'defer'));
    
    // wp_enqueue_style('rentmy-live-style', plugins_url('cdn/index.css', RNTM_CDN_PLUGIN_FILE), array(), '1.0', 'all');    
    // wp_enqueue_script('rentmy-live-script', plugins_url('cdn/script.js', RNTM_CDN_PLUGIN_FILE), [], '1.0',array('in_footer' => true,'strategy'  => 'defer'));

    $data_to_pass = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'store_uid' => 'b4cf5ec466c011ea82610212d7dcece2',        
    );
    // $hook == 'toplevel_page_rentmy'    
    $data_to_pass = array(
        'ajax_url' => admin_url('admin-ajax.php'),    
        'home_url' => home_url(),    
        'is_administrator' => false,
        'store_uid' => '',
        'api_key' => '',
        'secret_key' => '',
        'ids' => [
            'products_list' => get_option('rentmy.page_url.products_list'),
            'product_details' => get_option('rentmy.page_url.product_details'),
            'package_details' => get_option('rentmy.page_url.package_details'),
            'checkout' => get_option('rentmy.page_url.checkout'),
            'cart' => get_option('rentmy.page_url.cart'),
            'reset_password' => get_option('rentmy.page_url.reset_password'),
            'profile' => get_option('rentmy.page_url.profile'),
        ],
        'parmalinks' => [
            'products_list' => get_permalink(get_option('rentmy.page_url.products_list')),
            'product_details' => get_permalink(get_option('rentmy.page_url.product_details')),
            'package_details' => get_permalink(get_option('rentmy.page_url.package_details')),
            'checkout' => get_permalink(get_option('rentmy.page_url.checkout')),
            'cart' => get_permalink(get_option('rentmy.page_url.cart')),
            'reset_password' => get_permalink(get_option('rentmy.page_url.reset_password')),
            'profile' => get_permalink(get_option('rentmy.page_url.profile')),
        ],

    ); 
    wp_localize_script('rentmy-script', 'RENTMY_GLOBAL', $data_to_pass);
    
    
}
add_action('wp_enqueue_scripts', 'rentmycdn_enqueue_my_scripts');
add_action('admin_enqueue_scripts', 'rentmycdn_enqueue_my_scripts');
add_action('load-post.php', 'rentmycdn_enqueue_my_scripts');

function rentmy_admin()
{
    include('admin/rentmy_admin.php');
}

function rentmy_admin_actions()
{
    add_options_page("RentMy", "RentMy", 'administrator', "rentmy", "rentmy_admin");
    add_menu_page("RentMy", "RentMy", 'administrator', "rentmy", "rentmy_admin" , RNTM_CDN_PLUGIN_URL . "/admin/assets/icon.png");
}
add_action('admin_menu', 'rentmy_admin_actions');


/* -------------------------------------------------------------------------- */
/*                           Add Elementor Controlls                          */
/* -------------------------------------------------------------------------- */
function elementor_test_addon() {
	require_once( __DIR__ . '/elementor-addons/plugin.php' );
	\Elementor_RentMy_Addons\Plugin::instance();
}
add_action( 'plugins_loaded', 'elementor_test_addon' );

?>