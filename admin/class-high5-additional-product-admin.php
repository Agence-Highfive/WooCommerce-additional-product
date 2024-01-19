<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.highfive.fr
 * @since      1.0.0
 *
 * @package    high5_Additional_Product
 * @subpackage high5_Additional_Product/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    high5_Additional_Product
 * @subpackage high5_Additional_Product/admin
 * @author     Highfive <contact@highfive.fr>
 */
class high5_Additional_Product_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
	

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		
	}


	/**
	 * Create a new tabs in Woocommerce product parameters 
	 *
	 * @since    1.0.0
	 */

	public function high5_additional_product_tab($tabs) {
		$tabs['high5_additional_product_tab'] = array(
			'label'     => __( 'Additional products', 'h5-additional-product' ), //Navigation Label Name
			'target'    => 'high5_additional_product_content', //The HTML ID of the tab content wrapper
			'class' => array( 'show_if_simple', 'show_if_variable' ), //Show if the product type is simple
			'priority' => 99,
		);
		 
		return $tabs;
	}

	/**
	 * Add fields in the tab
	 *
	 * @since    1.0.0
	 */

	public function high5_add_custom_additional_fields() {
		
		echo '<div id="high5_additional_product_content" class="panel woocommerce_options_panel">';
		?>
		<div class="presentation_field">
			<?php $this->high5_add_label_product_field() ?>
		</div>
		<?php 
		$this->high5_select_product_field(); 
		$this->high5_checkbox_price_product();
		echo '</div>';
		}

	/**
	 * Select product field
	 *
	 * @since    1.0.0
	 */

	public function high5_select_product_field(){
		global $woocommerce, $post;
		?>
		<p class="form-field">
		<label for="related_ids"><?php _e( 'Additional product to the cart', 'h5-additional-product' ); ?></label>
		 <select class="wc-product-search" multiple="multiple" style="width: 50%;" id="related_ids" name="related_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval( $post->ID ); ?>">
		  <?php
			//check if there are values recorded to the database
			$saved_values = get_post_meta( $post->ID, 'related_ids', false );
			//debug($saved_values);
			foreach($saved_values[0] as $saved_product_id){
				$product = wc_get_product( $saved_product_id );
						if ( is_object( $product ) ) {
						echo '<option value="' . esc_attr( $saved_product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
						}
			}
	
			foreach ( $product_ids as $product_id ) {
			  $product = wc_get_product( $product_id );
			  if ( is_object( $product ) ) {
				echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
			  }
			}
			
		  ?>
		</select> <?php echo wc_help_tip( __( 'Search for a product', 'h5-additional-product' ) ); ?>
	  </p><?php
	}


	/**
	 * Title field
	 *
	 * @since    1.0.0
	 */

	public function high5_add_label_product_field(){
        // Custom Product Text Field
        woocommerce_wp_text_input(
            array(
                'id'          => 'high5_additional_product_presentation_field',
                'label'       => __( 'Title', 'h5-additional-product' ),
                'placeholder' => 'Information regarding the product(s)',
                'desc_tip'    => 'true'
            )
        );
	}

	/**
	 * Display price checkbox field
	 *
	 * @since    1.0.0
	 */

	public function high5_checkbox_price_product() {

		echo '<div class="options_group">'; 
		woocommerce_wp_checkbox( array(
			'id'          => '_high5_checkbox_price_product',
			'value'       => get_post_meta( get_the_ID(), '_high5_checkbox_price_product' )[0],
			'label'       => __( 'Show price', 'h5-additional-product' ),
			'description' => __( 'Show product price after additional product name', 'h5-additional-product' ),
		) );
		echo '</div>';
	}

	/**
	 * Save fields
	 *
	 * @since    1.0.0
	 */

	public function high5_additional_fields_save( $post_id ){
	
		// // Text Field présentation
		if(isset($_POST['high5_additional_product_presentation_field'])){
			$woocommerce_text_field = sanitize_text_field($_POST['high5_additional_product_presentation_field']);
			if( !empty( $woocommerce_text_field ) ){
				update_post_meta( $post_id, 'high5_additional_product_presentation_field', esc_attr( $woocommerce_text_field ) );
			}
		}

		// // Checkbox display price option
		if(isset($_POST['_high5_checkbox_price_product']))	{
			$price_checkbox = isset( $_POST['_high5_checkbox_price_product'] ) ? 'yes' : 'no';
			update_post_meta( $post_id, '_high5_checkbox_price_product', $price_checkbox );
		}
		
			
		// Product Field Type
		if(isset($_POST['related_ids']) && is_array($_POST['related_ids'])){
			$product_field_type = array_map( 'absint', (array) $_POST['related_ids'] ) ;
			update_post_meta( $post_id, 'related_ids', $product_field_type );
		}
		
	}
	

}
