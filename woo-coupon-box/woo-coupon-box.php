<?php
/**
 * Plugin Name: Coupon Box for WooCommerce
 * Plugin URI: https://villatheme.com/extensions/woo-coupon-box/
 * Description: Engage customers while collecting their email address with a well-designed popup. Optionally reward them with coupons.
 * Version: 2.1.5
 * Author: VillaTheme
 * Author URI: http://villatheme.com
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0
 * Text Domain: woo-coupon-box
 * Domain Path: /languages
 * Copyright 2017-2024 VillaTheme.com. All rights reserved.
 * Requires Plugins: woocommerce
 * Requires at least: 5.0
 * Tested up to: 6.6.2
 * WC requires at least: 7.0
 * WC tested up to: 9.3.1
 * Requires PHP: 7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

//Compatible with High-Performance order storage (COT)
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

if ( is_plugin_active( 'woocommerce-coupon-box/woocommerce-coupon-box.php' ) ) {
	return;
}

define( 'VI_WOO_COUPON_BOX_VERSION', '2.1.5' );

class VI_WOO_COUPON_BOX_FREE {
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	function init() {
        
		if ( ! class_exists( 'VillaTheme_Require_Environment' ) ) {
			require_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woo-coupon-box/includes/support.php";
		}

		$environment = new VillaTheme_Require_Environment( [
				'plugin_name'     => 'Coupon Box for WooCommerce',
				'php_version'     => '7.0',
				'wp_version'      => '5.0',
				'wc_version'      => '7.0',
				'require_plugins' => [
					[
						'slug' => 'woocommerce',
						'name' => 'WooCommerce',
					],
				]
			]
		);

		if ( $environment->has_error() ) {
			return;
		}

		$init_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woo-coupon-box" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "define.php";
		require_once $init_file;
	}
}

new VI_WOO_COUPON_BOX_FREE;