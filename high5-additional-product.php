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
 * Version:           1.0.4
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
if (!session_id()) {
    session_start();
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'high5_ADDITIONAL_PRODUCT_VERSION', '1.0.4' );

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
 * Debug Function to debug website in prod (Only if user -> adminstrator roles)
 *
 * @param [type] $datas
 * @param string $title
 * @return void
 */
function debug($datas, $title="Debug") {
	if ($title == "") $title = "Debug";
	
	  echo '<pre style="background:black;color:white;text-align:left;padding:15px;">';
		  print_r("<span style='color:orange;'>%s : </span>", strtoupper($title));
		  var_dump($datas);
	  echo '</pre>';
	  echo "<br />";
	
  }

  /**
 * Change Added to cart message.
 */
function h5_add_to_cart_message_html( $message, $products ) {

	$count = 0;
	$titles = array();
	//debug($_SESSION['added_products']);
	foreach ( $products as $product_id => $qty ) {
		$titles[] = ( $qty > 1 ? absint( $qty ) . ' &times; ' : '' ) . sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), strip_tags( get_the_title( $product_id ) ) );
		$count += $qty;
	}
	if(array_key_exists('added_products', $_SESSION)){
		foreach($_SESSION['added_products'] as $added_prod){
			//debug(strip_tags( get_the_title( $added_prod ) ));
			$titles[] .= sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in cart', 'woocommerce' ), strip_tags( get_the_title( $added_prod ) ) );
			//sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), strip_tags( get_the_title( $added_prod ) ) );
			$count += $qty;
		}
	}

	$titles     = array_filter( $titles );
	$added_text = sprintf( _n(
		'%s '. esc_html__('has been added to your cart', 'h5-additional-product'), // Singular
		'%s '. esc_html__('have been added to your cart', 'h5-additional-product'), // Plural
		$count, // Number of products added
		'woocommerce' // Textdomain
	), wc_format_list_of_items( $titles ) );
	$message    = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', esc_url( wc_get_page_permalink( 'cart' ) ), esc_html__( 'View cart', 'woocommerce' ), esc_html( $added_text ) );


	return $message;
}
add_filter( 'wc_add_to_cart_message_html', 'h5_add_to_cart_message_html', 10, 2 );


/**
 * Add a tab to the WooCommerce product parameters
 *
 * @since    1.0.0
 */

function high5_additional_product_admin_tab($tabs){
	require_once plugin_dir_path( __FILE__ ) . 'admin/class-high5-additional-product-admin.php';
	$plugin_name = "h5-additional-product";
	$version = high5_ADDITIONAL_PRODUCT_VERSION;
    $high5_APA = new high5_Additional_Product_Admin($plugin_name , $version);
	$nonce = wp_create_nonce('admin-tab');
	$tabs = $high5_APA->high5_additional_product_tab($tabs, $nonce);
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
	$nonce = wp_create_nonce('admin-fields');
	$plugin_name = "h5-additional-product";
	$version = high5_ADDITIONAL_PRODUCT_VERSION;
    $high5_APA = new high5_Additional_Product_Admin($plugin_name, $version);
	$high5_APA->high5_add_custom_additional_fields($nonce); 
}
add_action( 'woocommerce_product_data_panels', 'high5_additional_product_admin_field' );

/**
 * Save fields with the product metadata
 *
 * @since    1.0.0
 */

function high5_additional_product_admin_field_save($post_id){
	require_once plugin_dir_path( __FILE__ ) . 'admin/class-high5-additional-product-admin.php';
	$nonce = wp_create_nonce('save-products');
	$plugin_name = "h5-additional-product";
	$version = high5_ADDITIONAL_PRODUCT_VERSION;
    $high5_APA = new high5_Additional_Product_Admin($plugin_name, $version);
	return $high5_APA->high5_additional_fields_save($post_id, $nonce);
    
}
add_action( 'woocommerce_process_product_meta', 'high5_additional_product_admin_field_save', 20,1 );


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
	$nonce = wp_create_nonce('cart-front');
	$plugin_name = "h5-additional-product";
	$version = high5_ADDITIONAL_PRODUCT_VERSION;
    $high5_APA = new high5_Additional_Product_Public($plugin_name, $version);
	$high5_APA->high5_display_additional_product_to_cart($nonce); 
}
add_action( 'woocommerce_before_add_to_cart_quantity', 'high5_display_additional_product_to_cart_front', 1000, 0);


/**
    * Add product to the cart
    * @since    1.0.0
*/

function high5_additional_add_to_cart_action($product_key,$variation_id, $quantity, $variation, $cart_item_data) {

    require_once plugin_dir_path( __FILE__ ) . 'public/class-high5-additional-product-public.php';
	$nonce = wp_create_nonce('add_to_cart');
	$plugin_name = "h5-additional-product";
	$version = high5_ADDITIONAL_PRODUCT_VERSION;
    $high5_APA = new high5_Additional_Product_Public($plugin_name, $version);
	$high5_APA->high5_additional_add_to_cart($product_key,$variation_id, $quantity, $variation, $cart_item_data, $nonce); 
    
}
add_action('woocommerce_add_to_cart', 'high5_additional_add_to_cart_action', 10, 6 );


