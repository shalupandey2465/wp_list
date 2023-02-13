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
			     	     	$post_info = $wpdb->get_results(
				            "SELECT * FROM {$table} WHERE ID='".$data_id ."'"
				            );


				            foreach($post_info as $keyss=>$valuess)
				            {
				            	  $data[] = array(
				                   
				                    'name'       =>$valuess->ID ,
				                    'price'      => $valuess->post_title,
				                    'category'   => '1994',
				                    'tag'        => 'Frank Darabont',
				                    'stock'      => '9.3'
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
		}


  $myListTable = new Supporthost_List_Table();
  echo '<div class="wrap"><h2>My List Table Test</h2>'; 
  $myListTable->prepare_items(); 
  $myListTable->display(); 
  echo '</div>';
?>