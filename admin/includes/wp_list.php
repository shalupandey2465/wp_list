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
		$perPage = $this->count_product_qnty();
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
			'cb'            =>  '<input type="checkbox">',
			'image'         => __('Image', 'supporthost-cookie-consent'),
			'name'          => __('Name', 'supporthost-cookie-consent'),
			'price'         => __('Price', 'supporthost-cookie-consent'),
			'category'      => __('Category', 'supporthost-cookie-consent'),
			'tag'           => __('Tag', 'supporthost-cookie-consent'),
			'stock'         => __('Stock', 'supporthost-cookie-consent')

		);
		return $columns;
	}



	private function get_table_data($search = '')
	{

		global $wpdb;
		$table = $wpdb->prefix . 'postmeta';
		$postdta = $wpdb->prefix . 'posts';
		$product_searched_data = array();
		if (!empty($search)) {


			// $post_id = $wpdb->get_results(
			// 			"SELECT post_id FROM {$table} WHERE meta_key='my_custom_meta_key'",

			// 		);
			// foreach($post_id as $idsss=>$post_idsss)
			// {
			$post_info = $wpdb->get_results(
				"SELECT * FROM wp_posts INNER JOIN wp_postmeta ON wp_posts.post_title 
				LIKE CONCAT('%', '$search', '%') 
				AND (wp_posts.ID = wp_postmeta.post_id) 
				AND wp_postmeta.meta_key = 'my_custom_meta_key'"
			);
			return $post_info;
		} else {

			$product_searched_data['search_data'] = $wpdb->get_results(
				"SELECT * FROM {$table} WHERE meta_key='my_custom_meta_key'",
				ARRAY_A
			);

			return  $product_searched_data['search_data'];
		}
	}



	public function get_sortable_columns()
	{
		return array(
			'image'      => array('image', false),
			'name'       => array('name', false),
			'category'   => array('category', false),
			'tag'        => array('tag', false),
			'price'      => array('price', false),
			'stock'      => array('stock', false)
		);
	}






	public function table_data()
	{

		global $wpdb;
		$table = $wpdb->prefix . 'posts';
		$data = array();
		$category = array();
		$tag = array();
		$post_type = array();
		$stock_status = array();
		$variation = [];
		if (isset($_POST['s'])) {
			$post_meta_info  = $this->get_table_data($_POST['s']);
			foreach ($post_meta_info as $post_key_info => $post_info_value) {

				$attachment_id = get_post_thumbnail_id($post_info_value->ID);
				// print_r($post_info_value->post_title);
				$url = wp_get_attachment_image_src($attachment_id, 'desired-size');
				if ($url != '') {
					$urlsss = $url[0];
				} else {

					$urlsss = 'https://wabisabiproject.com/wp-content/uploads/woocommerce-placeholder.png';
				}

				$cat_term = get_the_terms($post_info_value->ID, 'product_cat');
				foreach ($cat_term as $cat_key => $cat_value) {

					$cat_name = $cat_value->name;
				}

				$product_tag_term = get_the_terms($post_info_value->ID, 'product_tag');
				foreach ($product_tag_term as $tag_key => $tag_value) {
					$tag_name = $tag_value->name;
				}
				$stock = get_post_meta($post_info_value->ID, '_stock_status', true);
				$main_price = get_post_meta($post_info_value->ID, '_price', true);
				$data[] = array(
					'image'      => '<a href="http://localhost/web/practice/?product=' . "$post_info_value->post_name" . '"><img src="' . $urlsss . '" height="50" width="50"></a>',
					'name'       => '<a href="http://localhost/web/practice/wp-admin/post.php?post=' . "$post_info_value->ID" . '&action=edit">' . $post_info_value->post_title . '</a>',
					'price'      => ($main_price != '') ? $main_price : "_",
					'category'   => ($cat_name != '') ? $cat_name : "_",
					'tag'        => ($tag_name != '') ? $tag_name : "_",
					'stock'      => ($stock != '') ? $stock : "_"
				);
			}
		} else {
			$post_meta_info  = $this->get_table_data();

			foreach ($post_meta_info as $key => $value) {
				$data_id = $value['post_id'];
				$post_info = $wpdb->get_results(
					"SELECT * FROM {$table} WHERE ID='" . $data_id . "'"
				);
				if (isset($post_info[0]) && !empty($post_info[0])) {
					$post_info = $post_info[0];
					$attachment_id = get_post_thumbnail_id($post_info->ID);
					$url = wp_get_attachment_image_src($attachment_id, 'desired-size');
					if ($url != '') {
						$urlsss = $url[0];
					} else {

						$urlsss = 'https://wabisabiproject.com/wp-content/uploads/woocommerce-placeholder.png';
					}

					$cat_term = get_the_terms($post_info->ID, 'product_cat');
					foreach ($cat_term as $cat_key => $cat_value) {

						$cat_name = $cat_value->name;
					}

					$product_tag_term = get_the_terms($post_info->ID, 'product_tag');
					foreach ($product_tag_term as $tag_key => $tag_value) {
						$tag_name = $tag_value->name;
					}
					$stock = get_post_meta($post_info->ID, '_stock_status', true);
					$main_price = get_post_meta($data_id, '_price', true);
					$product = wc_get_product($post_info->ID);
					$product_parent = $product->get_parent_id();
					$args = array(
						'category_name' => $_POST['option_value'],
						'posts_per_page' => 3
					);



					$data[] = array(
						'image'      => '<a href="http://localhost/web/practice/?product=' . "$post_info->post_name" . '"><img src="' . $urlsss . '" height="50" width="50"></a>',
						'name'       => '<a href="http://localhost/web/practice/wp-admin/post.php?post=' . "$post_info->ID" . '&action=edit">' . $post_info->post_title.'</a>',
						'price'      => ($main_price != '') ? $main_price : "_",
						'category'   => ($cat_name != '') ? $cat_name : "_",
						'tag'        => ($tag_name != '') ? $tag_name : "_",
						'stock'      => ($stock != '') ? $stock : "_"
					);
				}
			}
		}


		if (isset($_REQUEST['option_value']) && $_REQUEST['option_value'] != '' && $_POST['filter-type'] == 'product') {
			$argsss = array(
				'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash') ,
				'meta_key'   => 'my_custom_meta_key',
				'meta_value' => 'my data',
				'tax_query' => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'term_id',
						'terms'     =>  $_POST['option_value'],
						'operator'  => 'IN'
					)
				)
			);
			$the_queryss = new wp_query($argsss);
			if ($the_queryss->posts[0]->ID == '') {
				return array();
			}
			foreach ($the_queryss->posts as $key => $cate_val) {

				$terms = get_the_terms($cate_val->ID, 'product_cat');
				foreach ($terms as $term_key => $term_val) {

					$term_name = $term_val->name;
				}

				$tag_terms = get_the_terms($cate_val->ID, 'product_tag');
				foreach ($tag_terms as $tag_key => $tag_val) {

					$tag_term_name = $tag_val->name;
				}
				$pro_price = get_post_meta($cate_val->ID, '_price', true);
				$pro_stock = get_post_meta($cate_val->ID, '_stock_status', true);
				$attachment_ids = get_post_thumbnail_id($cate_val->ID);
				$urls = wp_get_attachment_image_src($attachment_ids, 'desired-size');
				if ($urls != '') {
					$urlsss = $urls[0];
				} else {
					$urlsss = 'https://wabisabiproject.com/wp-content/uploads/woocommerce-placeholder.png';
				}
				$post_type[] = array(
					'image'      => '<a href="http://localhost/web/practice/?product=' . "$cate_val->post_name" . '"><img src="' . $urlsss . '" height="50" width="50"></a>',
					'name'       => '<a href="http://localhost/web/practice/wp-admin/post.php?post=' . "$cate_val->ID" . '&action=edit">'.$cate_val->post_title.'</a>',
					'price'      => ($pro_price != '') ? $pro_price : "_",
					'category'   => ($term_name != '') ? $term_name : "_",
					'tag'        => ($tag_term_name != '') ? $tag_term_name : "_",
					'stock'      => ($pro_stock != '') ? $pro_stock : "_"
				);
			}
		}

		if ($_POST['option_value'] != '' && $_POST['filter-type'] == 'categories') 
		{
			$args = array(
				'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash') ,
				'meta_key'   => 'my_custom_meta_key',
				'meta_value' => 'my data',
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
			if ($the_query->posts[0]->ID == '') 
			{
				return array();
			}
			foreach ($the_query->posts as $key => $cate_val) {

				$term = get_term_by('term_id', $_POST['option_value'], 'product_cat');
				$name = $term->name;
				$tag_terms = get_the_terms($cate_val->ID, 'product_tag');
				foreach ($tag_terms as $tag_key => $tag_val) {

					$tag_term_name = $tag_val->name;
				}
				$price = get_post_meta($cate_val->ID, '_price', true);
				$stock = get_post_meta($cate_val->ID, '_stock_status', true);
				$attachment_ids = get_post_thumbnail_id($cate_val->ID);
				$urls = wp_get_attachment_image_src($attachment_ids, 'desired-size');
				if ($urls != '') {
					$urlsss = $urls[0];
				} else {
					$urlsss = 'https://wabisabiproject.com/wp-content/uploads/woocommerce-placeholder.png';
				}

				$category[] = array(
					'image'      => '<a href="http://localhost/web/practice/?product=' . "$cate_val->post_name" . '"><img src="' . $urlsss . '" height="50" width="50"></a>',
					'name'       => '<a href="http://localhost/web/practice/wp-admin/post.php?post=' . "$cate_val->ID" . '&action=edit">'.$cate_val->post_title. '</a>',
					'price'      => ($price != '') ? $price : "_",
					'category'   => ($name != '') ? $name : "_",
					'tag'        => ($tag_term_name != '') ? $tag_term_name : "_",
					'stock'      => ($stock != '') ? $stock : "_"
				);
			}
		} else if ($_POST['option_value'] != '' && $_POST['filter-type'] == 'tags') {

			$args = array(
				'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash') ,
				'meta_key'   => 'my_custom_meta_key',
				'meta_value' => 'my data',
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
			if ($the_query->posts[0]->ID == '') {
				return array();
			}
			foreach ($the_query->posts as $key => $value) {

				$term = get_term_by('term_id', $_POST['option_value'], 'product_tag');
				$name = $term->name;
				$tag_price = get_post_meta($value->ID, '_price', true);
				$stock_status = get_post_meta($value->ID, '_stock_status', true);
				$cat = get_the_terms($value->ID, 'product_cat');
				foreach ($cat as $cat_key => $cat_val) {

					$term_name = $cat_val->name;
				}
				$attachment_ids = get_post_thumbnail_id($value->ID);
				$urls = wp_get_attachment_image_src($attachment_ids, 'desired-size');
				if ($urls != '') {
					$urlsss = $urls[0];
				} else {
					$urlsss = 'https://wabisabiproject.com/wp-content/uploads/woocommerce-placeholder.png';
				}

				$tag[] = array(
					'image'      => '<a href="http://localhost/web/practice/?product=' . "$value->post_name" . '"><img src="' . $urlsss . '" height="50" width="50"></a>',
					'name'       => '<a href="http://localhost/web/practice/wp-admin/post.php?post=' . "$value->ID" . '&action=edit">'.$value->post_title. '</a>',
					'price'      => ($tag_price != '') ? $tag_price : "_",
					'category'   => ($term_name != '') ? $term_name : "_",
					'tag'        => ($name != '') ? $name : "_",
					'stock'      => ($stock_status != '') ? $stock_status : "_"
				);
			}
		}

		if (isset($_REQUEST['option_value']) && $_REQUEST['option_value'] != '' && $_POST['filter-type'] == 'stock') {
			$myquery = array(
				'post_type'  => 'product',
				'meta_key'   => '_stock_status',
				'meta_value' => $_POST['option_value'],
				'order'      => 'ASC'
			);
			$the_query = get_posts($myquery);
			if ($the_query[0]->ID == '') {
				return array();
			}
			foreach ($the_query as $key => $cate_val) {

				$attachment_idss = get_post_thumbnail_id($cate_val->ID);
				$urls = wp_get_attachment_image_src($attachment_idss, 'desired-size');
				if ($urls != '') {
					$urlsss = $urls[0];
				} else {
					$urlsss = 'https://wabisabiproject.com/wp-content/uploads/woocommerce-placeholder.png';
				}

				$stock_status1 = get_post_meta($cate_val->ID, '_stock_status', true);
				$stock_price = get_post_meta($cate_val->ID, '_price', true);
				$cat = get_the_terms($cate_val->ID, 'product_cat');
				foreach ($cat as $cat_key => $cat_val) {

					$term_name = $cat_val->name;
				}


				$tag = get_the_terms($cate_val->ID, 'product_tag');
				foreach ($tag as $tag_key => $tag_val) {

					$tag_name = $tag_val->name;
				}



				$stock_status[] = array(
					'image'      => '<a href="http://localhost/web/practice/?product=' . "$cate_val->post_name" . '"><img src="' . $urlsss . '" height="50" width="50"></a>',
					'name'       => '<a href="http://localhost/web/practice/wp-admin/post.php?post=' . "$cate_val->ID" . '&action=edit">'.$cate_val->post_title. '</a>',
					'price'      => ($stock_price != '') ? $stock_price : "_",
					'category'   => ($term_name != '') ? $term_name : "_",
					'tag'        => ($tag_name != '') ? $tag_name : "_",
					'stock'      => ($stock_status1 != '') ? $stock_status1 : "_"
				);
			}
		}

		if ($_POST['option_value'] != '' && $_POST['filter-type'] == 'categories') {
			return  $category;
		} else if ($_POST['option_value'] != '' && $_POST['filter-type'] == 'tags') {
			return $tag;
		}

		if (isset($_REQUEST['option_value']) && $_REQUEST['option_value'] != '' && $_POST['filter-type'] == 'stock') {
			return $stock_status;
		}

		if (isset($_REQUEST['option_value']) && $_REQUEST['option_value'] != '' && $_POST['filter-type'] == 'product') {
			return $post_type;
		}

		return $data;
	}



	public function column_default($item, $column_name)
	{
		switch ($column_name) {
			case 'cb':
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



	public function column_cb($item)
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

	public function count_product_qnty()
	{

		$quantityss = array(5, 10, 15, 20, 25);
		update_option('product_quantity_option_one', serialize($quantityss));
		$option1 = unserialize(get_option('product_quantity_option_one'));
		$qty = $_POST['product_qnty'];
		update_option('product_quant', serialize($qty));
		$show_quant = unserialize(get_option('product_quant'));
		if ($show_quant == '') {
			return 10;
		} else {

			return $show_quant;
		}
	}


	public function extra_tablenav($which)
	{

		$filter_type = $_POST['filter-type'];
		if ($filter_type == 'categories') {
			$args = array(
				'taxonomy'     => 'product_cat',
				'orderby'      => 'name',
				'show_count'   => 0,
				'pad_counts'   => 0,
				'hierarchical' => 1,
				'title_li'     => '',
				'hide_empty'   => 0
			);
			$all_categories = get_categories($args);

			// print_r($all_categories[0]->term_id);



		} else if ($filter_type == 'tags') {

			$args = array(
				'taxonomy'     => 'product_tag',
				'orderby'      => 'name',
				'show_count'   => 0,
				'pad_counts'   => 0,
				'hierarchical' => 1,
				'title_li'     => '',
				'hide_empty'   => 0
			);
			$all_categories = get_categories($args);
		} else if ($filter_type == 'stock') {

			global $wpdb;
			$table = $wpdb->prefix . 'postmeta';
			$all_categoriess =  $wpdb->get_results(
				"SELECT * FROM {$table} WHERE meta_key='_stock_status' LIMIT 1"
			);
		} else if ($filter_type == 'product') {

			$product_types = get_terms('product_type', array('hide_empty' => false));
		}

		if ($which == "top") {
			?>

			<div class="alignleft actions bulkactions">
				<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
					<select name="product_qnty">
						<?php
						$option = unserialize(get_option('product_quantity_option_one'));
						foreach ($option as $option_key) {
							?>
							<option <?= selected($_REQUEST['product_qnty'], $option_key, false) ?> value="<?php echo $option_key ?>"><?php echo $option_key ?></option>

							<?php
						}

						?>

					</select>
					<select name="filter-type" class="filter-type">
						<option <?= selected($_REQUEST['filter-type'], 'all', false) ?> value="all">All</option>
						<option <?= selected($_REQUEST['filter-type'], 'categories', false) ?> value="categories">Categories</option>
						<option <?= selected($_REQUEST['filter-type'], 'tags', false) ?> value="tags">Tags</option>
						<option <?= selected($_REQUEST['filter-type'], 'product', false) ?> value="product">Product Type</option>
						<option <?= selected($_REQUEST['filter-type'], 'stock', false) ?> value="stock">Stock Status</option>
					</select>
					<select class="perform_onchange" name="option_value">

						<?php
						if (term_exists($all_categories[0]->term_id)) {
							foreach ($all_categories as $cat_data) {
								?>
								<option value="<?php echo $cat_data->term_id ?>"><?php echo $cat_data->cat_name ?></option>
								<?php
							}
						} else if (isset($all_categoriess[0]->meta_value)) {

							foreach ($all_categoriess as $stock_status) {
								?>
								<option value="outofstock">Out Of Stock</option>
								<option value="<?php echo $stock_status->meta_value ?>"><?php echo $stock_status->meta_value ?></option>
								<?php

							}
						} else {
							foreach ($product_types as $product_types_name) {
								?>
								<option value="<?php echo $product_types_name->term_id ?>"><?php echo $product_types_name->name ?></option>
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


	?>
	<div class="wrap">
		<h2>My List Table Test</h2>
		<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
			<?php
			$myListTable->prepare_items();
			$myListTable->search_box(__('Search'), 'search-box-id');
			$myListTable->display();
			?>
			<input type="hidden" name="page" value="" />
		</form>
	</div>