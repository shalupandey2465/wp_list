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
				    	usort( $data, array( &$this, 'sort_data' ) );
				        $columns = $this->get_columns();
				        $hidden = array();  
				        $sortable = $this->get_sortable_columns();
				        $perPage = 10;
				        $currentPage = $this->get_pagenum();
				        $totalItems = count($data);
				        $this->set_pagination_args( array(
				            'total_items' => $totalItems,
				            'per_page'    => $perPage
				        ) );
				        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
				        $this->_column_headers = array($columns, $hidden, $sortable);
				        
				        $this->items = $data;
				    }


		      function get_columns()
			    {
			        $columns = array(
			                // 'cb'            =>  '<input type="checkbox" />',
			                'name'          => __('Name', 'supporthost-cookie-consent'),
			                'price'         => __('Price', 'supporthost-cookie-consent'),
			                'category'      => __('Category', 'supporthost-cookie-consent'),
			                'tag'           => __('Tag', 'supporthost-cookie-consent'),
			                'stock'         => __('Stock', 'supporthost-cookie-consent'),
			        );
			        return $columns;
			    }



			     private function get_table_data() {

				        global $wpdb;
				        $table = $wpdb->prefix .'postmeta';
			     	     return $wpdb->get_results(
				            "SELECT * FROM {$table} WHERE meta_key='my_custom_meta_key'",
				            ARRAY_A
				        );
			     	    
				      
				    }



				        public function get_sortable_columns()
					    {
					        return array('name'       => array('name', false),
					                     'category'   => array('category', false),
					                     'tag'        => array('tag', false),
					                     'price'      => array('price', false),
					                    );
					    }


				    function table_data()
				    {
				    	
				    	global $wpdb;
				        $table = $wpdb->prefix .'posts';
				        $data = array();
				    	$post_meta_info = $this->get_table_data();
				    	 foreach($post_meta_info as $key=>$value)
			     	     {
			     	     	$data_id = $value['post_id'];
					    	// print_r($price);
					    	// die();
			     	     	$post_info = $wpdb->get_results(
				            "SELECT * FROM {$table} WHERE ID='".$data_id ."'"
				            );


				            foreach($post_info as $keyss=>$valuess)
				            {
				            	$cat_term=get_the_terms($valuess->ID,'product_cat');
				            	foreach($cat_term as $cat_key=>$cat_value)
				            	{

					            	$cat_name=$cat_value->name;
					            	
				            	}

				            	$product_tag_term=get_the_terms($valuess->ID,'product_tag');
				            	foreach($product_tag_term as $tag_key=>$tag_value)
				            	{
                                   
                                    	$tag_name=$tag_value->name;
                                   
				            	}
				            	$stock=get_post_meta($valuess->ID,'_stock',true);

				            	  $data[] = array(
				                   
				                    'name'       =>$valuess->post_title ,
				                    'price'      => get_post_meta($data_id,'_price',true),
				                    'category'   => $cat_name,
				                    'tag'        => $tag_name,
				                    'stock'      => $stock
				                    );
				            }

				            

			     	     	

			     	     }
				    	


				      

				                    return $data;
				    }



				    function column_default($item, $column_name)
					    {
					          switch ($column_name) {
					     
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

						    private function sort_data( $a, $b )
						    {
						        // Set defaults
						        $orderby = 'name';
						        $order = 'asc';

						        // If orderby is set, use this as the sort column
						        if(!empty($_GET['orderby']))
						        {
						            $orderby = $_GET['orderby'];
						        }

						        // If order is set use this as the order
						        if(!empty($_GET['order']))
						        {
						            $order = $_GET['order'];
						        }


						        $result = strcmp( $a[$orderby], $b[$orderby] );

						        if($order === 'asc')
						        {
						            return $result;
						        }

						        return -$result;
						    }
		}


  $myListTable = new Supporthost_List_Table();
  echo '<div class="wrap"><h2>My List Table Test</h2>'; 
  $myListTable->prepare_items(); 
  $myListTable->display(); 
  echo '</div>';
?>