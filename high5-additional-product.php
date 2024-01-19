<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.highfive.fr
 * @since             1.0.0
 * @package           high5_Additional_Product
 *
 * @wordpress-plugin
 * Plugin Name:       h5 additional product to cart
 * Plugin URI:        https://www.highfive.fr
 * Description:       Add a simple or variable additionnal product to the cart for woocommerce
 * Version:           1.0.0
 * Author:            Highfive
 * Author URI:        https://www.highfive.fr/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       h5-additional-product
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'high5_ADDITIONAL_PRODUCT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-h5-additional-product-activator.php
 */
function activate_high5_additional_product() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-high5-additional-product-activator.php';
	high5_Additional_Product_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-h5-additional-product-deactivator.php
 */
function deactivate_high5_additional_product() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-high5-additional-product-deactivator.php';
	high5_Additional_Product_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_high5_additional_product' );
register_deactivation_hook( __FILE__, 'deactivate_high5_additional_product' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-high5-additional-product.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function run_high5_additional_product() {

	$plugin = new high5_Additional_Product();
	$plugin->run();

}
run_high5_additional_product();




/**
 * Add a tab to the WooCommerce product parameters
 *
 * @since    1.0.0
 */

function high5_additional_product_admin_tab($tabs){
	require_once plugin_dir_path( __FILE__ ) . 'admin/class-high5-additional-product-admin.php';
    $high5_APA = new high5_Additional_Product_Admin($plugin_name, $version);
	$tabs = $high5_APA->high5_additional_product_tab($tabs);
    return $tabs;
}

add_filter( 'woocommerce_product_data_tabs', 'high5_additional_product_admin_tab');

/**
 * Add fields to the tab
 *
 * @since    1.0.0
 */

function high5_additional_product_admin_field(){
	require_once plugin_dir_path( __FILE__ ) . 'admin/class-high5-additional-product-admin.php';
    $high5_APA = new high5_Additional_Product_Admin($plugin_name, $version);
	$high5_APA->high5_add_custom_additional_fields(); 
}
add_action( 'woocommerce_product_data_panels', 'high5_additional_product_admin_field' );

/**
 * Save fields with the product metadata
 *
 * @since    1.0.0
 */

function high5_additional_product_admin_field_save($post_id){
	require_once plugin_dir_path( __FILE__ ) . 'admin/class-high5-additional-product-admin.php';
    $high5_APA = new high5_Additional_Product_Admin($plugin_name, $version);
	return $high5_APA->high5_additional_fields_save($post_id);
    
}
add_action( 'woocommerce_process_product_meta', 'high5_additional_product_admin_field_save' );


/** FRONT
 * 
 * Display option on the product page
 * Add to cart selected product
 * 
 */

/**
    * Displays selected products on the product page, in the add to cart form
    * @since    1.0.0
*/

function high5_display_additional_product_to_cart_front(){
	require_once plugin_dir_path( __FILE__ ) . 'public/class-high5-additional-product-public.php';
    $high5_APA = new high5_Additional_Product_Public($plugin_name, $version);
	$high5_APA->high5_display_additional_product_to_cart(); 
}
add_action( 'woocommerce_before_add_to_cart_quantity', 'high5_display_additional_product_to_cart_front', 1000, 0);


/**
    * Add product to the cart
    * @since    1.0.0
*/

function high5_additional_add_to_cart_action($product_key,$variation_id, $quantity, $variation, $cart_item_data) {

    require_once plugin_dir_path( __FILE__ ) . 'public/class-high5-additional-product-public.php';
    $high5_APA = new high5_Additional_Product_Public($plugin_name, $version);
	$high5_APA->high5_additional_add_to_cart($product_key,$variation_id, $quantity, $variation, $cart_item_data); 
    
}
add_action('woocommerce_add_to_cart', 'high5_additional_add_to_cart_action', 10, 6 );
