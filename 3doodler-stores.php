<?php
/**
 * Plugin Name: The 3Doodler Store Finder Plugin
 * Plugin URI: http://the3doodler.com
 * Description: A plugin to automate the store finding.
 * Version: 2.0
 * Author: Ryan McManimie
 * Author URI: http://the3doodler.com
 * License: GPL2
 */

define( 'DPSTO_ROOT', plugins_url( '', __FILE__ ) );
define( 'DPSTO_IMAGES', DPSTO_ROOT . '/img/' );
define( 'DPSTO_STYLES', DPSTO_ROOT . '/css/' );
define( 'DPSTO_SCRIPTS', DPSTO_ROOT . '/js/' );
define( 'DPSTO_TEMPLATES', DPSTO_ROOT . '/templates/' );
define( 'DPSTO_INC', DPSTO_ROOT . '/inc/' );
define( 'DPSTO_ADMIN', DPSTO_ROOT . '/admin/' );

require_once( 'inc/3doodler-stores.php' );

function dpsto_enqueue_script_style() {

    /*wp_enqueue_script( "dpsto-style", SG_SCRIPTS. "quickview.js", array('jquery'), "", true );*/
    wp_enqueue_style( 'dpsto-style', DPSTO_STYLES . 'style.css');

}
add_action( 'wp_enqueue_scripts', 'sg_enqueue_script_style' );
