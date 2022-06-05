<?php
/**
 * Plugin Name: Contact form 7 ticket booking addon
 * Plugin URI: https://example.com/
 * Description: Plugin Description
 * Version: 1.0.0
 * Author: zafar iqbal
 * Author URI: https://example.com/
 * Requires at least: 5.5.1
 * Tested up to: 5.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Act on plugin activation
register_activation_hook( __FILE__, 'activate_myplugin' );

// Act on plugin de-activation
register_deactivation_hook( __FILE__, 'deactivate_myplugin' );

// Activate Plugin
function activate_myplugin() {

	// Execute tasks on Plugin activation

	// Insert DB Tables
	init_db_myplugin();
}

// De-activate Plugin
function deactivate_myplugin() {

	// Execute tasks on Plugin de-activation
}

// Initialize DB Tables
function init_db_myplugin() {

	// Code to create DB Tables
}
