<?php
// echo "abcdefhjnk";

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class Supporthost_List_Table extends WP_List_Table
{

	function prepare_items()
	{
		$data = $this->table_data();
		usort($data, array(&$this, 'sort_data'));
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$perPage = 10;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);
		$this->set_pagination_args(array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		));
		$data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->items = $data;
	}


	function get_columns()
	{
		$columns = array(
			'image'         => __('Image', 'supporthost-cookie-consent'),
			'name'          => __('Name', 'supporthost-cookie-consent'),
			'price'         => __('Price', 'supporthost-cookie-consent'),
			'category'      => __('Category', 'supporthost-cookie-consent'),
			'tag'           => __('Tag', 'supporthost-cookie-consent'),
			'stock'         => __('Stock', 'supporthost-cookie-consent')

		);
		return $columns;
	}



	private function get_table_data()
	{

		global $wpdb;
		$table = $wpdb->prefix . 'postmeta';
		return $wpdb->get_results(
			"SELECT * FROM {$table} WHERE meta_key='my_custom_meta_key'",
			ARRAY_A
		);
		// print_r('adf');
		// die();
	}



	public function get_sortable_columns()
	{
		return array(
			'image'      => array('image', false),
			'name'       => array('name', false),
			'category'   => array('category', false),
			'tag'        => array('tag', false),
			'price'      => array('price', false),
		);
	}



	


	function table_data()
	{

		global $wpdb;
		$table = $wpdb->prefix . 'posts';
		$data = array();
		$category=array();
		$tag=array();
		$variation = [];
		$post_meta_info = $this->get_table_data();
		foreach ($post_meta_info as $key => $value) {
			$data_id = $value['post_id'];
			$post_info = $wpdb->get_results(
				"SELECT * FROM {$table} WHERE ID='" . $data_id . "'"
			);
			if (isset($post_info[0]) && !empty($post_info[0])) {
				$post_info = $post_info[0];
				$attachment_id = get_post_thumbnail_id($post_info->ID);
				$url = wp_get_attachment_image_src($attachment_id, 'desired-size');

				$cat_term = get_the_terms($post_info->ID, 'product_cat');
				foreach ($cat_term as $cat_key => $cat_value) {

					$cat_name = $cat_value->name;
				}

				$product_tag_term = get_the_terms($post_info->ID, 'product_tag');
				foreach ($product_tag_term as $tag_key => $tag_value) {
					$tag_name = $tag_value->name;
				}

				$stock = get_post_meta($post_info->ID, '_stock', true);
				$product = wc_get_product($post_info->ID);
				$product_parent = $product->get_parent_id();
				$args = array(
					'category_name' => $_POST['option_value'], 
					'posts_per_page' => 3  
				); 

				if($_POST['option_value'] != '' && $_POST['filter-type']=='categories')
				{
					$args = array(
						'post_status' => 'publish',
						'tax_query' => array(
							array(
								'taxonomy' => 'product_cat',
								'field'    => 'term_id',
								'terms'     =>  $_POST['option_value'],
								'operator'  => 'IN'
							)
						)
					);
					$the_query = new wp_query($args);
					$term = get_term_by('term_id',$_POST['option_value'], 'product_cat'); 
					$name = $term->name;
					$attachment_ids = get_post_thumbnail_id($the_query->posts[0]->ID);
					$urls = wp_get_attachment_image_src($attachment_ids, 'desired-size');

					$category[]=array(
						'image'      => '<img src="' . $urls[0] . '" height="50" width="50">',
						'name'       => $the_query->posts[0]->post_title,
						'price'      => get_post_meta($the_query->posts[0]->ID, '_price', true),
						'category'   => $name,
						'tag'        => '',
						'stock'      => get_post_meta($the_query->posts[0]->ID, '_stock', true)
					);
				}
				else if($_POST['option_value'] != '' && $_POST['filter-type']=='tags')
				{
					$args = array(
						'post_status' => 'publish',
						'tax_query' => array(
							array(
								'taxonomy' => 'product_tag',
								'field'    => 'term_id',
								'terms'     =>  $_POST['option_value'],
								'operator'  => 'IN'
							)
						)
					);
					$the_query = new wp_query($args);
					$term = get_term_by('term_id',$_POST['option_value'], 'product_tag'); 
					$name = $term->name;
					$attachment_ids = get_post_thumbnail_id($the_query->posts[0]->ID);
					$urls = wp_get_attachment_image_src($attachment_ids, 'desired-size');

					$tag[]=array(
						'image'      => '<img src="' . $urls[0] . '" height="50" width="50">',
						'name'       => $the_query->posts[0]->post_title,
						'price'      => get_post_meta($the_query->posts[0]->ID, '_price', true),
						'category'   => '',
						'tag'        => $name,
						'stock'      => get_post_meta($the_query->posts[0]->ID, '_stock', true)
					);
					
				}
				
				
				$data[]=array(
					'image'      => '<img src="' . $url[0] . '" height="50" width="50">',
					'name'       => $post_info->post_title,
					'price'      => get_post_meta($data_id, '_price', true),
					'category'   => $cat_name,
					'tag'        => $tag_name,
					'stock'      => $stock
				);
			}
		}


		// if (isset($_POST['product-type']) && $_POST['product-type'] != 'all') {
		// 	return $variation;
		// }
		// return array_merge($variation, $simple);
		if($_POST['option_value'] != '' && $_POST['filter-type']=='categories')
		{
			return  $category;
		}
		else if($_POST['option_value'] != '' && $_POST['filter-type']=='tags')
		{
			return $tag;
		}
		return $data;
	}



	function column_default($item, $column_name)
	{
		switch ($column_name) {
			case 'image':
			case 'name':
			case 'price':
			case 'category':
			case 'tag':
			case 'stock':
			default:
			return $item[$column_name];
		}
	}



	function column_cb($item)
	{
		return sprintf(
			'<input type="checkbox" name="element[]" value="%s" />',
			$item['id']
		);
	}

	private function sort_data($a, $b)
	{
		// Set defaults
		$orderby = 'name';
		$order = 'asc';

		// If orderby is set, use this as the sort column
		if (!empty($_GET['orderby'])) {
			$orderby = $_GET['orderby'];
		}

		// If order is set use this as the order
		if (!empty($_GET['order'])) {
			$order = $_GET['order'];
		}


		$result = strcmp($a[$orderby], $b[$orderby]);

		if ($order === 'asc') {
			return $result;
		}

		return $result;
	}
	function extra_tablenav($which)
	{
		
		$filter_type=$_POST['filter-type'];

		if($filter_type=='categories')
		{
			$args = array(
				'taxonomy'     => 'product_cat',
				'orderby'      => 'name',
				'show_count'   => 0,
				'pad_counts'   => 0,
				'hierarchical' => 1,
				'title_li'     => '',
				'hide_empty'   => 0
			);
			$all_categories = get_categories( $args );
			// print_r($all_categories[0]->term_id);

	        

		}
		else if($filter_type=='tags')
		{

			$args = array(
				'taxonomy'     => 'product_tag',
				'orderby'      => 'name',
				'show_count'   => 0,
				'pad_counts'   => 0,
				'hierarchical' => 1,
				'title_li'     => '',
				'hide_empty'   => 0
			);
			$all_categories = get_categories( $args );

		}
		else if($filter_type=='stock status')
		{
			
			global $wpdb;
		    $table = $wpdb->prefix . 'postmeta';
		    $all_categoriess =  $wpdb->get_results(
			       "SELECT * FROM {$table} WHERE meta_key='_stock_status' LIMIT 1"
		     );

			
				
			
			
		}

		if ($which == "top") {
			?>
			
			<div class="alignleft actions bulkactions">
				<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
					<select name="filter-type" class="filter-type">
						<option <?= selected( $_REQUEST['filter-type'], 'all', false ) ?> value="all">All</option>
						<option <?= selected( $_REQUEST['filter-type'], 'categories', false ) ?> value="categories">Categories</option>
						<option <?= selected( $_REQUEST['filter-type'], 'tags', false ) ?> value="tags">Tags</option>
						<option <?= selected( $_REQUEST['filter-type'], 'product_type', false ) ?> value="product type">Product Type</option>
						<option <?= selected( $_REQUEST['filter-type'], 'stock_status', false ) ?> value="stock status">Stock Status</option>
					</select>
					<select class="perform_onchange" name="option_value">

						<?php
						if(term_exists($all_categories[0]->term_id))
	                     {
							foreach($all_categories as $cat_data)
							{
								?>
								<option  value="<?php echo $cat_data->term_id ?>"><?php echo $cat_data->cat_name?></option>
								<?php
							}
						 }
						 else{
                                foreach($all_categoriess as $stock_status)
							    {
						 	?>
                                      <option  value="<?php echo $stock_status->meta_value ?>"><?php echo $stock_status->meta_value ?></option>
                                      <option value="outofstock">Out of Stock</option>
						 	<?php

						        }
						 }
						?>
					</select>
					<input type="button" name="filter_data" id="filter_data" value="Apply">
					<div id="render_filter_type">
					</div>
				</form>
				<form>
				</div>
				<?php
			}
		}
	}


	$myListTable = new Supporthost_List_Table();
	echo '<div class="wrap"><h2>My List Table Test</h2>';
	$myListTable->prepare_items();
	$myListTable->display();
	echo '</div>';
?>