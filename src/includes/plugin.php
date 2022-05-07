<?php /** @noinspection PhpIncludeInspection */

namespace LokusWP\Commerce;

if ( ! defined( 'WPTEST' ) ) {
	defined( 'ABSPATH' ) or die( "Direct access to files is prohibited" );
}

class Plugin {

	public function __construct() {

		// Activation and Deactivation
		register_activation_hook( LWC_BASE, [ $this, 'activation' ] );
		register_deactivation_hook( LWC_BASE, [ $this, 'uninstall' ] );
		require_once LWC_PATH . 'src/includes/common/class-i18n.php';

		// Shortcodes
		new Shortcodes\Product_Listing();
		new Shortcodes\Order_History();
		new Shortcodes\Cart_Icon();

		// Products
		new Modules\Product\Post_Type_Product;
		new Modules\Product\Metabox_Product;

		// System
		new Modules\Plugin\Updater;

		// Helper
		require_once LWC_PATH . 'src/includes/helper/func-order-meta.php';
		require_once LWC_PATH . 'src/includes/helper/func-price.php';
		require_once LWC_PATH . 'src/includes/helper/func-stock.php';
		require_once LWC_PATH . 'src/includes/helper/func-setter.php';
		require_once LWC_PATH . 'src/includes/helper/func-getter.php';
		require_once LWC_PATH . 'src/includes/helper/func-helper.php';

		// Hook
		require_once LWC_PATH . 'src/includes/hook/cart/func-cart-processing.php';
		require_once LWC_PATH . 'src/includes/hook/checkout/func-checkout-tab.php';
		require_once LWC_PATH . 'src/includes/hook/checkout/func-checkout-logic.php';
		require_once LWC_PATH . 'src/includes/hook/checkout/func-post-checkout.php';
		require_once LWC_PATH . 'src/includes/hook/notification/func-notification-scheduler.php';
		require_once LWC_PATH . 'src/includes/hook/order/func-order-create.php';

		// Order
		require_once LWC_PATH . 'src/includes/modules/order/class-order.php';

		// License
		require_once LWC_PATH . 'src/includes/modules/license/class-license.php';

		// Shipping Module
		// require_once LWC_PATH . 'src/includes/modules/shipping/abstract-shipping.php';
		// require_once LWC_PATH . 'src/includes/modules/shipping/class-manager.php';
		// require_once LWC_PATH . 'src/includes/modules/shipping/methods/class-shipping-processing.php';

		// API
		// require_once LWC_PATH . 'src/includes/modules/shipping/api/class-rajaongkir-api.php';
		// require_once LWC_PATH . 'src/includes/modules/shipping/api/class-get-shipping-list.php';

		// Shipping
		// require_once LWC_PATH . 'src/includes/modules/shipping/carriers/class-email-smtp.php';
		// require_once LWC_PATH . 'src/includes/modules/shipping/carriers/class-jne-rajaongkir.php';

		// Plugins Loaded
		add_action( 'plugins_loaded', [ $this, 'global_loaded' ] );

		// Administration / BackOffice
		$lwcommerce = array(
			'slug'    => 'lwcommerce',
			'name'    => 'LWCommerce',
			'version' => LWC_VERSION
		);

		if ( is_admin() ) {
			require_once LWC_PATH . 'src/admin/class-admin.php';
			Admin::register( $lwcommerce );
		} else {
			require_once LWC_PATH . 'src/public/class-public.php';
			Frontend::register( $lwcommerce );
		}

		// Register custom meta table
		$this->register_ordermeta();
	}

	/**
	 * Load Globally
	 * When you depend on third party function plugin
	 *
	 * @return void
	 * @since 0.1.0
	 */
	public function global_loaded() {

		if ( is_admin() ) {
			require_once LWC_PATH . 'src/includes/modules/order/class-followup-whatsapp.php';
		}

		// require_once LWC_PATH . 'src/includes/modules/shipping/methods/class-shipping-processing.php';
		// require_once LWC_PATH . 'src/includes/modules/order/methods/class-order-processing.php';
	}

	/**
	 * Register Order Meta Data to WordPress
	 *
	 * @return void
	 * @since 0.1.0
	 */
	private function register_ordermeta() {
		global $wpdb;
		$wpdb->lwcommerce_ordermeta = $wpdb->prefix . 'lwcommerce_ordermeta';
	}

	/**
	 * Load Class Activation on Plugin Active
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function activation() {
		require_once LWC_PATH . 'src/includes/common/class-activation.php';
		Activation::activate();
	}

	/**
	 * Load Class Deactivation on Plugin Deactivate
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function uninstall() {
		require_once LWC_PATH . 'src/includes/common/class-deactivation.php';
		Deactivation::deactivate();
	}

	/**
	 * Clone.
	 *
	 * Disable class cloning and throw an error on object clone.
	 *
	 * @since 0.1.0
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'lokuswp' ), LOKUSWP_VERSION );
	}

	/**
	 * Wakeup.
	 *
	 * Disable serializing of the class.
	 *
	 * @since 0.1.0
	 */
	public function __wakeup() {
		// Disable serializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'lokuswp' ), LOKUSWP_VERSION );
	}
}

