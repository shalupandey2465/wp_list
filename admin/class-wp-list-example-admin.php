<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://gh
 * @since      1.0.0
 *
 * @package    Wp_List_Example
 * @subpackage Wp_List_Example/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_List_Example
 * @subpackage Wp_List_Example/admin
 * @author     Shalu Pandey <shalupandey998445@gmail.com>
 */
class Wp_List_Example_Admin {

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
	public function __construct( $plugin_name, $version ) {
// 		ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_List_Example_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_List_Example_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'bootstrap_style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-list-example-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_List_Example_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_List_Example_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( 'bootstrap-cdn','https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );


		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-list-example-admin.js', array( 'jquery' ), $this->version, false );
		
	}

	public function wpdocs_register_my_custom_menu_page()
	{
		add_menu_page( 
			__( 'Custom Menu Title', 'textdomain' ),
			'custom menu',
			'manage_options',
			'custompage',
			array($this,'my_custom_menu_page'),
			'',
			6
		); 
	}

	public function my_custom_menu_page()
	{
		global $wpdb;
		$table = $wpdb->prefix . 'posts';
		$all_categoriess =  $wpdb->get_results(
			"SELECT * FROM {$table} GROUP BY post_status"
		);

		echo '
		<div id="overlayone"></div>
		<div class="show_notice"></div>
		<div class="progress" style="display:none;">
		<div class="progress-bar progress-bar-striped progress-bar-animated bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
		</div>
		<div class="card">
		<div class="card-body">
		<form action="" method="post">
		<div class="mb-3">
		<label for="exampleInputEmail1" class="form-label">Consumer key</label>
		<input type="text" class="form-control" id="consumer_key" placeholder="Enter Consumer key" value="'.get_option('consumer_key').'">
		</div>
		<div class="mb-3">
		<label for="exampleInputEmail1" class="form-label">Consumer Secret</label>
		<input type="text" class="form-control" id="consumer_secret" placeholder="Enter Consumer secret" value="'.get_option('consumer_secret').'">
		</div>
		<div class="mb-3" id="productId">
		<label for="exampleInputEmail1" class="form-label">ID</label>
		<input type="text" class="form-control" id="product_id" placeholder="Enter Product Id">
		</div>
		<div class="mb-3"><select name="post_status" id="product_statuss">';
		foreach ($all_categoriess as $all_categoriess_key => $all_categoriess_value) {

			echo '<option value="'.$all_categoriess_value->post_status.'">'.$all_categoriess_value->post_status.'</option>';
			
		}
		
		echo '</select></div><div class="mb-3 form-check">
		<input type="checkbox"  id="exampleCheck1" >
		<label for="exampleCheck1">Import multiple Products</label>
		</div>
		<button type="button" class="btn btn-primary" id="wp_List_Example">Submit</button>
		</form>
		</div>
		</div>
		';

		require_once('includes/wp_list.php');
	}


	public function isProductExist($productID) {
		global $wpdb;
		$table = $wpdb->prefix . 'postmeta';
		// print_r("SELECT meta_value FROM {$table} WHERE meta_key='product_customid' AND meta_value='".$productID."'");
		$data =  $wpdb->get_results(
			"SELECT meta_value FROM {$table} WHERE meta_key='product_customid' AND meta_value='".$productID."'"
		);
		if( isset($data[0]) && !empty($data[0]) ) {
			return true;
		}else{
			return false;
		}
	}


	public function get_product_by_id()
	{

		global $wpdb;
		$table = $wpdb->prefix . 'postmeta';
		$progress_count=array();
		$consumer_key     = $_POST['consumer_key'];
		$consumer_secret  = $_POST['consumer_secret'];
		$product_id       = $_POST['product_id'];
		if($this->isProductExist($product_id)) {
			wp_send_json_error('product already exists');
			die();
		}
		$post_status =$_POST['post_status'];
		update_option('consumer_key',$consumer_key );
		update_option('consumer_secret',$consumer_secret );
		$all_categoriess =  $wpdb->get_results(
			"SELECT meta_value FROM {$table} WHERE meta_key='product_customid'"
		);

		// 122991

		// foreach($all_categoriess as $key11=>$value11)
		// {
		
		//         if($value11->meta_value == $product_id )
		//        {
		
		//        	  wp_send_json_error('product already exists');
		//        }
		//        else
		{

			$data              = $this->get_product_list($consumer_key,$consumer_secret,$product_id);
			$file_content      = json_decode($data,true);

			$images            = $file_content['images'];
			$imgs=array();
			
			foreach($images as $img=>$im)
			{


				$imgs['attachement']=$this->get_image($im['src']);		


			}    

			if($file_content['variations'] == [])
			{

				$product = new WC_Product_Simple();
				$product->set_name($file_content['name']);
				$product->set_slug($file_content['slug']);
				$product->set_status($post_status );
				$product->set_regular_price($file_content['regular_price']);
				$product->set_short_description($file_content['description'].''.$file_content['short_description']);
				$product->set_sale_price($file_content['sale_price']);
				               // $product->set_sku($file_content['sku']); 
				$product->update_meta_data( 'my_custom_meta_key', 'my data' );
				$product->update_meta_data( 'product_customid', $file_content['id']);
				$product->set_image_id($imgs['attachement']);
				$product->set_stock_status( 'instock' );
				$product->set_manage_stock( true );
				$product->set_stock_quantity( 5 );
				$attributes = array();
				foreach($file_content['attributes'] as $attr_key => $attr_value)
				{

					$attribute = new WC_Product_Attribute();
					$attribute->set_name( $attr_value['name']);
					$attribute->set_options($attr_value['options']);
					$attribute->set_position( 0 );
					$attribute->set_visible( true );
					$attribute->set_variation( true );
					$attributes[] = $attribute;
					$product->set_attributes( $attributes );
				}
				$cat = array();
				foreach($file_content['categories']  as $file_key=>$file_value)
				{

					$term_id=get_term_by('name',$file_value['name'], 'product_cat');
					$cat[] = $term_id->term_id;
					$product->set_category_ids($cat);

				}

				$tag = array();

				foreach($file_content['tags']  as $tags_key=>$tags_value)
				{

					$tags_term_id=get_term_by('name',$tags_value['name'], 'product_tag');
					$tag[] = $tags_term_id->term_id;
					$product->set_tag_ids($tag);

				}
				$product->save();
				if($product->get_id() !='')
				{
					wp_send_json_success('single product inserted');
					
					
				}
				

			}
			else
			{


				$product = new WC_Product_Variable();
				$product->set_name($file_content['name']);
				$product->set_slug($file_content['slug']);
				$product->set_short_description($file_content['description'].''.$file_content['short_description']);
			        // $product->set_sku($file_content['sku']); 
				$product->set_status($post_status );
				$product->update_meta_data( 'my_custom_meta_key', 'my data' );
				$product->update_meta_data( 'product_customid', $file_content['id']);
				$product->set_image_id($imgs['attachement']);
				$product->set_stock_status( 'instock' );
				$product->set_manage_stock( true );
				$product->set_stock_quantity( 5 );
				$attributes = array();
				// $attributes = array();
				foreach($file_content['attributes'] as $attr_key => $attr_value)
				{
					 // print_r($attr_key);
                     //  die();
					$attribute = new WC_Product_Attribute();
					$attribute->set_name( $attr_value['name']);
					$attribute->set_options($attr_value['options']);
					$attribute->set_position( 0 );
					$attribute->set_visible( true );
					$attribute->set_variation( true );
					$attributes[] = $attribute;
					$product->set_attributes( $attributes );
				}

				$cat = array();
				foreach($file_content['categories']  as $file_key=>$file_value)
				{

					$term_id=get_term_by('name',$file_value['name'], 'product_cat');
					$cat[] = $term_id->term_id;
					$product->set_category_ids($cat);

				}

				$tag = array();

				foreach($file_content['tags']  as $tags_key=>$tags_value)
				{

					$tags_term_id=get_term_by('name',$tags_value['name'], 'product_tag');
					$tag[] = $tags_term_id->term_id;
					$product->set_tag_ids($tag);

				}

				$product->save();

				$variations_bulk=$file_content['variations'];
				foreach ($variations_bulk as $variation_key => $variation_value){

					$variation_details      = $this->get_product_list( $consumer_key,$consumer_secret,$variation_value);
					$variation_content      = json_decode($data,true);
					$variation              = new WC_Product_Variation();


					$variation->set_parent_id($product->get_id() );	
					$variation->set_name($variation_content['name']);  
					$variation->set_status($post_status );            
					$variation->set_slug($variation_content['slug']);
					$variation->set_short_description($variation_content['description'].''.$variation_content['short_description']);
					$variation->set_sale_price($variation_content['sale_price']);
			        // $variation->set_sku($variation_content['sku']); 
					foreach($variation_content['attributes'] as $attr_variationkeys => $attr_variationvalues)
					{

						$vardatas= strtolower($attr_variationvalues['name']);
                         // print_r($vardata);
                         // // die();
						$variation->set_attributes(array($vardatas => $attr_variationvalues['option']));

					} 
					$variation->set_regular_price($variation_content['regular_price']);	
					$variation->update_meta_data( 'my_custom_meta_key', 'my data' );
					$variation->save();


				}

				if($product->get_id() !='')
				{
					wp_send_json_success('single product inserted');
					
					
				}



			}
		}
		
		// }
		
		
	}


	public function get_product_list($consumer_key,$consumer_secret,$id)
	{
		$curl_user= $consumer_key. ':' . $consumer_secret;
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://proshopukstaging.wpcomstaging.com/wp-json/wc/v3/products/'.$id);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

		curl_setopt($ch, CURLOPT_USERPWD, $curl_user);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		return $result;
	}



	public function get_image($images)
	{

		$temp_file = download_url($images);
		$file_da = array(
			'name'     => basename($images),
			'type'     => mime_content_type( $temp_file ),
			'tmp_name' => $temp_file,
			'size'     => filesize( $temp_file ),
		);
		$image_name = $file_da['name'];
		$tmp_name = $file_da['tmp_name'];
		$sideload = wp_handle_sideload(
			$file_da,
			array(
				'test_form'   => false
			)
		);

		$attachment_id = wp_insert_attachment(
			array(
				'guid'           => $sideload[ 'url' ],
				'post_mime_type' => $sideload[ 'type' ],
				'post_title'     => basename( $sideload[ 'file' ] ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			),
			$sideload[ 'file' ]
		);

		$att=wp_update_attachment_metadata(
			$attachment_id,
			wp_generate_attachment_metadata( $attachment_id, $sideload[ 'file' ] )
		);

		return $attachment_id;

	}



	public function get_all_product()
	{
		// ini_set('display_errors','1');
		// ini_set('display_startup_errors','1');
		// error_reporting(E_ALL);
		$consumer_key      = $_POST['consumer_key'];
		$consumer_secret   = $_POST['consumer_secret'];
		$post_status =$_POST['post_status'];
		$fetch_all_product = $this->fetch_all_products($consumer_key,$consumer_secret);
		$file_contentss    = json_decode($fetch_all_product,true);
		$response = [];
		$response['existProduct'] = 0;
		$response['simpleProduct'] = 0;
		$response['variableProduct'] = 0;
		foreach($file_contentss as $all_contentKey=>$all_contentValue)
		{
			// if($this->isProductExist($product_id)) {
			
			// }
			if( $this->isProductExist($all_contentValue['id']) ){
				$response['existProduct'] = $response['existProduct']+1;
				continue;
			}
			
			$imagess  = $all_contentValue['images'];
			$var_imges=array();

			foreach($imagess as $var_imge=>$var_imgs)
			{

				$var_imges['attachement']=$this->get_image($var_imgs['src']);		


			}  


			if($all_contentValue['variations'] == [])
			{

				$sample_product = new WC_Product_Simple();
				$sample_product->set_name($all_contentValue['name']);
				$sample_product->set_slug($all_contentValue['slug']);
				$sample_product->set_regular_price($all_contentValue['regular_price']);
				$sample_product->set_short_description($all_contentValue['description'].''.$all_contentValue['short_description']);
				$sample_product->set_sale_price($all_contentValue['sale_price']);
				$sample_product->set_status($post_status );
				// $product->set_sku($all_contentValue['sku']); 
				$sample_product->update_meta_data( 'my_custom_meta_key', 'my data' );
				$sample_product->set_stock_status( 'outofstock' );
				$sample_product->update_meta_data( 'product_customid', $all_contentValue['id']);
				$sample_product->set_manage_stock( true );
				$sample_product->set_stock_quantity( 5 );
				$attributes = array();
				$sample_product->set_image_id($var_imges['attachement']);
				$sample_attributes = array();
				foreach($all_contentValue['attributes'] as $attr_samplekey => $attr_samplevalue)
				{

					$sample_attribute = new WC_Product_Attribute();
					$sample_attribute->set_name( $attr_samplevalue['name']);
					$sample_attribute->set_options($attr_samplevalue['options']);
					$sample_attribute->set_position( 0 );
					$sample_attribute->set_visible( true );
					$sample_attribute->set_variation( true );
					$sample_attributes[] = $sample_attribute;
					$sample_product->set_attributes( $sample_attributes );
				}

				$cats = array();

				foreach($all_contentValue['categories']  as $cats_key=>$cats_value)
				{

					if(term_exists($cats_value['name'], 'product_cat'))
					{
						$term_id=get_term_by('name',$cats_value['name'], 'product_cat');
						$cats[]=$term_id->term_id;

					}else{

						$nameb = wp_insert_term($cats_value['name'], 'product_cat');
						$cats[]= $nameb['term_id'];
					}

					$sample_product->set_tag_ids($cats);

				}

				$tags = array();

				foreach($all_contentValue['tags']  as $tagss_key=>$tagss_value)
				{

					
					if(term_exists($tagss_value['name'], 'product_tag'))
					{
						$tag_term_id=get_term_by('name',$tagss_value['name'], 'product_tag');
						$tags[] = $tag_term_id->term_id;

					}else{

						$nameb = wp_insert_term($tagss_value['name'], 'product_tag');
						$tags[]= $nameb['term_id'];


					}
					
					$sample_product->set_tag_ids($tags);

				}

				$sample_product->save();
				if($sample_product->get_id() !='')
				{
					$response['simpleProduct'] = $response['simpleProduct'] + 1;
				}

			}
			else
			{

				$variable_product = new WC_Product_Variable();
				$variable_product->set_name($all_contentValue['name']);
				$variable_product->set_slug($all_contentValue['slug']);
				$variable_product->set_short_description($all_contentValue['description'].''.$all_contentValue['short_description']);
			    // $variable_product->set_sku($all_contentValue['sku']); 
				$variable_product->set_status($post_status );
				$variable_product->update_meta_data( 'my_custom_meta_key', 'my data' );
				$variable_product->set_stock_status( 'instock' );
				$variable_product->set_manage_stock( true );
				$variable_product->update_meta_data( 'product_customid', $all_contentValue['id']);
				$variable_product->set_stock_quantity( 5 );


				$variable_product->set_image_id($var_imges['attachement']);

				$variable_attributes = array();
				foreach($all_contentValue['attributes'] as $attr_variablekey => $attr_variablevalue)
				{ 

					// print_r($attr_variablevalue['options']);
                    // die();
					$variable_attribute = new WC_Product_Attribute();
					$variable_attribute->set_name( $attr_variablevalue['name']);
					$variable_attribute->set_options($attr_variablevalue['options']);
					$variable_attribute->set_position( 0 );
					$variable_attribute->set_visible( true );
					$variable_attribute->set_variation( true );
					$variable_attributes[] = $variable_attribute;
					$variable_product->set_attributes($variable_attributes);
				}
				

				$cats = array();
				foreach($all_contentValue['categories']  as $cats_key=>$cats_value)
				{

					if(term_exists($cats_value['name'], 'product_cat'))
					{
						$term_id=get_term_by('name',$cats_value['name'], 'product_cat');
						$cats[]=$term_id->term_id;
						
					}else{

						$nameb = wp_insert_term($cats_value['name'], 'product_cat');
						$cats[]= $nameb['term_id'];
						
						
					}
					$variable_product->set_category_ids($cats);

				}

				$tags = array();

				foreach($all_contentValue['tags']  as $tagss_key=>$tagss_value)
				{


					if(term_exists($tagss_value['name'], 'product_tag'))
					{
						$tag_term_id=get_term_by('name',$tagss_value['name'], 'product_tag');
						$tags[] = $tag_term_id->term_id;

					}else{

						$nameb = wp_insert_term($tagss_value['name'], 'product_tag');
						$tags[]= $nameb['term_id'];


					}
					$variable_product->set_tag_ids($tags);

				}

				$variable_product->save();
				$variations_bulkss=$all_contentValue['variations'];
				foreach ($variations_bulkss as $variation_keyss => $variation_valuess){

					$variation_detailsss   = $this->get_product_list( $consumer_key,$consumer_secret,$variation_valuess);
					$variation_contentss      = json_decode($variation_detailsss,true);
					$variationss              = new WC_Product_Variation();


					$variationss->set_parent_id($variable_product->get_id() );	
					$variationss->set_name($variation_contentss['name']);              
					$variationss->set_slug($variation_contentss['slug']);
					$variationss->set_short_description($variation_contentss['description'].''.$variation_contentss['short_description']);
					$variationss->set_sale_price($variation_contentss['sale_price']);
			        // $variation->set_sku($variation_contentss['sku']); 
					$variationss->set_regular_price($variation_contentss['regular_price']);	
					$setattributes=array();
					foreach($variation_contentss['attributes'] as $attr_variationkey => $attr_variationvalue)
					{
						$arr_key=strtolower($attr_variationvalue['name']);

						$setattributes[$arr_key]=$attr_variationvalue['option'];                  
                        
					

					} 
					 $variationss->set_attributes($setattributes);
					$variationss->update_meta_data( 'my_custom_meta_key', 'my data' );
					$variationss->save();

				}

				if($variable_product->get_id() !='')
				{
					$response['variableProduct'] = $response['variableProduct'] + 1;
				}
				
			}
		}
		$message = 'Total product imported '.($response['simpleProduct']+$response['variableProduct']).'. Product already Exists '.$response['existProduct']; 
		echo json_encode(array('success' => true, 'message' => $message));
		wp_die();
	}



	public function fetch_all_products($consumer_key,$consumer_secret)
	{

		$curl_user= $consumer_key. ':' . $consumer_secret;
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://proshopukstaging.wpcomstaging.com/wp-json/wc/v3/products');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

		curl_setopt($ch, CURLOPT_USERPWD,$curl_user);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		return $result;
	}




	// public function wpb_admin_notice_warn_this()
	// {
	// 	echo '<div class="notice notice-warning is-dismissible">product Already Exist !!</div>'; 
	// }





}
