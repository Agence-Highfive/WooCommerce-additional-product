<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.highfive.fr
 * @since      1.0.0
 *
 * @package    high5_Additional_Product
 * @subpackage high5_Additional_Product/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    high5_Additional_Product
 * @subpackage high5_Additional_Product/public
 * @author     Highfive <contact@highfive.fr>
 */
class high5_Additional_Product_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * 
		 * Add style
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/main.css', array(), $this->version, 'all' );

	}


	/**
	 * Format the price correctly.
	 *
	 * @since    1.0.4
	 */
	// public function display_price($prix){
		

	// }

	/**
	 * Display the product option on the main product page.
	 *
	 * @since    1.0.0
	 */

	public function high5_display_additional_product_to_cart($nonce){
    
		if(is_product()){    
		global $product;
		$additional_products = [];
		if(is_array(get_post_meta( $product->get_id(), 'related_ids', false )) && count(get_post_meta( $product->get_id(), 'related_ids', false )) > 0){
			$additional_products = get_post_meta( $product->get_id(), 'related_ids', false )[0];
		}
		
		
		$presentation_products = get_post_meta( $product->get_id(), 'high5_additional_product_presentation_field', true );

		if(is_array(get_post_meta( $product->get_id(), 'high5_checkbox_price_product', false )) && count(get_post_meta( $product->get_id(), 'high5_checkbox_price_product', false )) > 0){
			$checkbox_price_products = (get_post_meta( $product->get_id(), 'high5_checkbox_price_product', false )[0] == 'yes') ? true : false;
		}
		if(is_array(get_post_meta( $product->get_id(), 'high5_checkbox_display_thumb', false )) && count(get_post_meta( $product->get_id(), 'high5_checkbox_display_thumb', false )) > 0){
			$checkbox_display_thumb = (get_post_meta( $product->get_id(), 'high5_checkbox_display_thumb', false )[0] == 'yes') ? true : false;
		}
		
		//debug($additional_products);
		
		if(is_array($additional_products)){ ?>
			<div class="high5_block_additionnal_product">
			<div class="high5_presentation_produit"><?php echo esc_html($presentation_products) ?></div>
			<?php $count = 0;
			
			if(!empty($additional_products)){
			foreach($additional_products as $additional_product_id){
				//debug($additional_product_id);
				$add_product = wc_get_product( $additional_product_id );
				
				if($add_product != false){
					$add_product_data = $add_product->get_data();
					$image_id  = $add_product->get_image_id();	
					//Whether the product is simple or is a variation
					if($add_product->get_type() == "simple" ): ?>
					<div class="high5_product_block">
						<?php $attributes = $add_product->get_data()['attributes'] ?>
						<?php // debug($attributes) ?>
						<input type="checkbox" id="produit_<?php echo esc_attr($count) ?>" name="high5_produits[]" value="<?php echo  esc_attr($additional_product_id)  ?>">
						
						<label class="h5_label" for="produit_<?php echo  esc_attr($count) ?>">
						<?php if($checkbox_display_thumb): ?> <span class="h5_product_thumb"><img src="<?php echo wp_get_attachment_image_url( $image_id, 'woocommerce_gallery_thumbnail' ) ?>"  ></span><?php endif ?>
							<div>
								<span class="h5_product_name"><?php echo esc_attr($add_product->get_name()) ?></span>
								<span class="h5_price"><?php if($checkbox_price_products): ?> +<?php echo ($add_product->is_on_sale()) ? wp_kses_post(wc_price($add_product->get_sale_price())) : wp_kses_post(wc_price($add_product->get_regular_price()))  ?><?php endif ?></span>
							</div>
						
						
								</label><br>
							
					</div>
					<?php elseif ($add_product->get_type() == "variable"): ?>
					<?php
							$variations_add = $add_product->get_available_variations();
						?>
						<div class="high5_select_container">
							<div class="high5_add_product_name"><?php echo esc_attr($add_product->get_name()); ?></div>
							
							<select name="high5_produits[]" class="" data-attribute="">
								<option value=""><?php esc_html_e('Choose an option', 'woocommerce') ?></option>
								<?php foreach($variations_add as $add_variation): ?>
									<?php $add_product = wc_get_product($add_variation['variation_id']) ?>
									<?php $attributes = $add_product->get_data()['attributes'] ?>
									
									<option data-attributes="<?php foreach(array_keys($add_variation['attributes']) as $dataAttrib) {
										echo esc_html($dataAttrib);
										}   ?>" value="<?php echo esc_attr($add_variation['variation_id']) ?>">
										<?php $countAttrib = 1; ?>
										<?php foreach ($attributes as $key => $attr): 
											echo esc_attr(str_replace('-', ' ', $attr));
											if($countAttrib == count($attributes)):
												echo ' ';
											else:
												echo ' - ';
											endif;
											$countAttrib++;
										endforeach ?> <?php if($checkbox_price_products): ?> (+ <?php echo ($add_product->is_on_sale()) ? wp_kses_post(wc_price($add_product->get_sale_price())) : wp_kses_post(wc_price($add_product->get_regular_price())) ?>)<?php endif ?></option>
								<?php endforeach ?>
								
							</select>
						</div>
						
					<?php endif;
					}
					$count++;
				} 
			} ?>
			</div>
		<?php }
	   
	}
	}

	/**
	 * Add the selected product to the cart on clicking to the "add to cart" button.
	 *
	 * @since    1.0.0
	 */

	public function high5_additional_add_to_cart($product_key,$variation_id, $quantity, $variation, $cart_item_data, $nonce) {

		
		$product = wc_get_product($variation);
		
		if(array_key_exists('high5_produits', $_POST)){
			$tableau_prod = array_map( 'absint', (array) $_POST['high5_produits'] ) ;
			$_SESSION['added_products'] = $tableau_prod;
			//debug($_POST['high5_produits']);
			unset($_POST['high5_produits']);
			foreach( $tableau_prod as $add_prod_id){
				$product = wc_get_product(absint($add_prod_id));

				if($product){
					if($product->get_type() == 'simple'){
						WC()->cart->add_to_cart($add_prod_id, $quantity);	
					}
					elseif($product->get_type() == 'variation'){
						$product_data = $product->get_data();
						$parent_id = $product_data['parent_id'];

						WC()->cart->add_to_cart($parent_id, $quantity, $add_prod_id);
					}
				}
				
			}
		}
	}

}
