<?php
/**
* Plugin Name: Sport Survey
* Plugin URI: https://johanalves.com
* Description: Helps you to decide which sport product to buy
* Version: 1.0.0
* Author: Johan Alves
* Author URI: https://johanalves.com
* Requires at least: 5.6
* Requires PHP: 7.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HTTPS_WP_PLUGIN_URL', str_replace('http:', 'https:',  WP_PLUGIN_URL));
define( 'HTTPS_SITE_URL', str_replace('http:', 'https:',  site_url()));

class SportsSurvey{
    function __construct(){
        require_once('sport_shortcode.php');
        add_action('wp_enqueue_scripts','surveyResources');
        add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');
        add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');
        
    }     
}

function surveyResources(){
    wp_enqueue_style('main_sports_survey_css', HTTPS_WP_PLUGIN_URL.'/sport-survey/sport-survey.css');   
}

function woocommerce_ajax_add_to_cart() {
    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = 1;
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    $product_status = get_post_status($product_id);

    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity) && 'publish' === $product_status) {

        do_action('woocommerce_ajax_added_to_cart', $product_id);

        if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
            wc_add_to_cart_message(array($product_id => $quantity), true);
        }

        WC_AJAX :: get_refreshed_fragments();
    } else {

        $data = array(
            'error' => true,
            'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));

        echo wp_send_json($data);
    }

    wp_die();
} 



$initSportsSurvey = new SportsSurvey();