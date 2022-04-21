<?php

use LokusWP\Commerce\Onboarding;
use LSD\Migration\DB_LWCommerce_Order_Meta;

if ( ! defined( 'WPTEST' ) ) {
	defined( 'ABSPATH' ) or die( "Direct access to files is prohibited" );
}

/**
 * Firs Boot the plugin
 *
 * @since 0.1.0
 */
class LWCommerce_Boot {

	public function __construct() {

		// Checking The Flag
		$lokuswp_was_installed    = get_option( "lokuswp_was_installed" );
		$lwcommerce_was_installed = get_option( "lwcommerce_was_installed" );
		$is_backbone_active       = in_array( 'lokuswp/lokuswp.php', get_option( 'active_plugins' ) );
		$is_backbone_exist        = file_exists( WP_PLUGIN_DIR . '/lokuswp/lokuswp.php' );

		// LokusWP Not Found -> Onboard
		if ( ! $is_backbone_exist && ! $lwcommerce_was_installed ) {
			$this->on_board_screen();
		}

		// LokusWP Exist and `was installed` but `not activated` -> Activate
		if ( $is_backbone_exist && ! $is_backbone_active && $lokuswp_was_installed ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			activate_plugins( 'lokuswp/lokuswp.php' );
		}

		// LokusWP Exist and Active
		if ( $is_backbone_exist && $is_backbone_active && $lwcommerce_was_installed && $lokuswp_was_installed ) {
			$this->run();
		} else {
			// Reactive LokusWP on Disable
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			activate_plugins( 'lokuswp/lokuswp.php' );
			$this->on_board_screen();
		}

		// TODO :: Checking Version Compatibility
	}

	/**
	 * Start Onboard Screen
	 * Only on Once
	 *
	 * @return void
	 * @version 0.1.0
	 */
	public function on_board_screen() {

		// Only Run On-boarding Screen, Not Entire System
		include_once LWC_PATH . 'src/admin/class-on-boarding.php';
		Onboarding::register( array( 'slug' => 'lwcommerce', 'name' => 'LWCommerce', 'version' => LWC_VERSION ) );

		// Create Table :: Orders
		require LWC_PATH . 'src/includes/modules/database/class-db-orders.php';
		$db_orders_meta = new DB_LWCommerce_Order_Meta();
		$db_orders_meta->create_table();

	}

	/**
	 * Run Plugin After Everything Setup and OK
	 *
	 * @return void
	 * @version 0.1.0
	 */
	public function run() {

		/**
		 * Registers the autoloader for classes
		 * Thanks to Michiel Tramper 🙏
		 *
		 * @author Michiel Tramper
		 * @link https://www.makeitworkpress.com
		 */
		spl_autoload_register( function ( $classname ) {

			// Getting Path based on Class Name
			$class     = str_replace( '\\', DIRECTORY_SEPARATOR, strtolower( $classname ) );
			$classpath = LWC_PATH . 'src/includes' . DIRECTORY_SEPARATOR . $class . '.php'; // only load inside folder includes
			$classpath = str_replace( "lokuswp/commerce/", "", $classpath );
			$classpath = str_replace( "lokuswp\\commerce\\", "", $classpath ); // fix path for windows

			// Windows Environment
			if ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' ) {
				$classpath = explode( "plugins\lwcommerce/", $classpath )[1];
			} else {
				$classpath = explode( "plugins/lwcommerce/", $classpath )[1];
			}

			$classpath = str_replace( "_", "-", $classpath ); // prevent replacing public_html
			$classpath = LWC_PATH . $classpath; // Add Root Path

			// Load File Based on Namespace
			if ( file_exists( $classpath ) ) {
				include_once $classpath;
			}
		} );

		// Check if LokusWP is installed and Active
		if ( in_array( 'lokuswp/lokuswp.php', get_option( 'active_plugins' ) ) ) {
			new LokusWP\Commerce\Plugin(); // Run LWCommerce, Run !!! 🏃🏃🏃
		}
	}
}

// Booting ...
if ( defined( 'WPTEST' ) ) { // Skip on-boarding when run in Testing Mode
	new LokusWP\Commerce\Plugin();
} else {
	new LWCommerce_Boot();
}
