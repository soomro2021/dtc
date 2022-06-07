<?php
/**
 * Plugin Name: Contact form 7 Ticket Booking add-on
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
class CF7_Ticket_Booking{
	public function __construct(){
		add_action( 'plugins_loaded', array( $this, 'tb_load_plugin_textdomain' ) );
		if ( class_exists( 'WPCF7' ) ) {
			$this->tb_constants();
			register_activation_hook(  __FILE__, array( &$this, 'tbCreateTable') );
	
			require_once CF_TB_PATH . 'includes/autoload.php';
		}else{
			
			add_action( 'admin_notices', array( $this, 'tb_admin_error_notice' ) );
		}
		
	}

	public function tb_load_plugin_textdomain() {

		load_plugin_textdomain( 'tb-cf7', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
	}

	public function tb_admin_error_notice(){
		$message = sprintf( esc_html__( 'The %1$s Ticket booking add-on Contact Form 7%2$s plugin requires %1$sContact form 7%2$s plugin active to run properly. Please install %1$scontact form 7%2$s and activate', 'tb-cf7' ),'<strong>', '</strong>');

		printf( '<div class="notice notice-error"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}
  public function tb_constants(){
		
		if ( ! defined( 'CF_TB_PATH' ) ) {
			define( 'CF_TB_PATH', plugin_dir_path( __FILE__ ) );
		}
		if ( ! defined( 'CF_TB_URL' ) ) {
			define( 'CF_TB_URL', plugin_dir_url( __FILE__ ) );
		}
		
	}

	public	function tbCreateTable() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'cf7booking';
		$sql = "CREATE TABLE `$table_name` (
		`id` int(11) NOT NULL,
		`status` int(11) DEFAULT '0',
		`user` varchar(220) DEFAULT NULL,
		`booking_time` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  
		PRIMARY KEY(id)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		  dbDelta($sql);

		}
		$array = range(1, 10);

        foreach ($array as $ticket) {
         $wpdb->insert(	$table_name , array(
            'id' => $ticket,
         ));
        }


	  }
}





$CF7_Ticket_Booking = new CF7_Ticket_Booking();