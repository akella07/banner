<?php # -*- coding: utf-8 -*-
declare(strict_types=1);

/**
 * Plugin Name: Ads Banner by Nixwood
 * Plugin URI:  nixwood.com
 * Description: Ads Banner by Nixwood allows you to improve ways of monetizing your blog or popularize your(or your vendors) products. Create banners and choose places to display them on your pages.
 * Version:     1.0
 * Author:      Nixwood team
 * License:     GPL-2.0
 * Text Domain: nixwood
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
define( 'AB_NIXWOOD', plugin_dir_url( __FILE__ ) );
define( 'AB_NIXWOOD_MAINFILE', __FILE__ );

if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
    function ab_nixwood_compat_issue() {
        echo '<div class="error"><p>' . __( 'Ads Banner by Nixwood requires PHP 5.6 (or higher) to function properly. Please upgrade PHP. The Plugin has been auto-deactivated.', 'nixwood' ) . '</p></div>';
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
    function deactivate_ab_nixwood() {
        deactivate_plugins( plugin_basename( AB_NIXWOOD_MAINFILE ) );
    }
    add_action( 'admin_notices', 'ab_nixwood_compat_issue' );
    add_action( 'admin_init', 'deactivate_ab_nixwood' );
    return;
}

require_once('inc/cpt.php');
require_once('inc/metabox.php');
require_once('inc/frontend.php');

function add_admin_scripts( $hook ) {

    global $post;

    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'abn_banners' === $post->post_type ) {
            wp_enqueue_style(  'custom_banner_style', plugin_dir_url( __FILE__ ) . 'assets/css/banner-admin.css' );
        }
    }
}
add_action( 'admin_enqueue_scripts', 'add_admin_scripts', 10, 1 );

add_action('init', function () {
	wp_enqueue_style('abn-styles', plugin_dir_url( __FILE__ ).'assets/css/abn.css');
});

$handle = 'jquery.js';
$list = 'enqueued';
$handle_min = 'jquery.min.js';
if (wp_script_is( $handle, $list ) || wp_script_is( $handle_min, $list )) {
    return;
} else {
    wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', array(), null, true);
}