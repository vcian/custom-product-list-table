<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Product List Page
 *
 * The html markup for the product list
 * 
 * @packageCategory List Table
 * @since 1.0.0
 */

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
	
class Ww_Clt_Product_List extends WP_List_Table {

	public $model;
	
	public function __construct(){
	
        global $ww_clt_model, $page;
                
        //Set parent defaults
        parent::__construct( array(
							            'singular'  => 'category',     //singular name of the listed records
							            'plural'    => 'categories',    //plural name of the listed records
							            'ajax'      => false        //does this table support ajax?
							        ) );   
		
		$this->model = $ww_clt_model;
		
    }
    
    /**
	 * Displaying Prodcuts
	 *
	 * Does prepare the data for displaying the products in the table.
	 * 
	 * @packageCategory List Table
	 * @since 1.0.0
	 */	
	public function display_products() {
	
		$prefix = WW_CLT_META_PREFIX;
		//if search is call then pass searching value to function for displaying searching values
		$args = array();
		//in case of search make parameter for retriving search data
		if(isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {
			$args['search']	= $_REQUEST['s'];
		}
		//in case of sort make parameter for retriving sort data
		if(isset($_GET['clt_status'])) {
			$args['meta_query']	= array(
											array(
														'key' 	=> $prefix.'product_status',
														'value'	=> $_GET['clt_status']
													)
										);
		}
				
		//call function to retrive data from table
		$data = $this->model->ww_clt_get_products( $args );
		$resultdata = array();
		
		foreach ($data as $key => $value) {
			
			$resultdata[$key]['ID'] = $value['ID'];
			$resultdata[$key]['post_title'] = $value['post_title'];
			$resultdata[$key]['post_content'] = $value['post_content'];
				
			$status = get_post_meta($value['ID'],$prefix.'product_status',true);
            $statustxt = $this->model->ww_clt_text_from_value_status($status);
            
            $product_terms = wp_get_object_terms($value['ID'], WW_CLT_TAXONOMY);
            $cat_name = !empty($product_terms[0]->name) ? $product_terms[0]->name : '';
            	
            $resultdata[$key]['clt_cat'] = $cat_name;
			$resultdata[$key]['clt_status'] = $statustxt;
			$resultdata[$key]['post_date'] = date_i18n( get_option('date_format'). ' '. get_option('time_format') ,strtotime($value['post_date']));
		}
	
		return $resultdata;
	}
	
	/**
	 * Mange column data
	 *
	 * Default Column for listing table
	 * 
	 * @packageCategory List Table
	 * @since 1.0.0
	 */
	public function column_default( $item, $column_name ){
	
        switch( $column_name ){
            case 'post_title':
            case 'post_content':
            case 'clt_cat':
            case 'clt_status':
            case 'post_date':
            	return $item[ $column_name ];
			default:
				return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }
	
   
    /**
     * Manage Edit/Delete Link
     * 
     * Does to show the edit and delete link below the column cell
     * function name should be column_{field name}
     * For ex. I want to put Edit/Delete link below the post title 
     * so i made its name is column_post_title
     * 
     * @packageCategory List Table
	 * @since 1.0.0
     */
    public function column_post_title($item){
    
    	$prefix = WW_CLT_META_PREFIX;
        //Build row actions
        $str_status = '';
        if(isset($_GET['clt_status'])) {
        	$str_status = '&clt_status='.$_GET['clt_status'];
        }
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&clt_id=%s">'.__('Edit', 'wwclt').'</a>','ww_clt_add_form','edit',$item['ID']),
            'delete'    => sprintf('<a href="?page=%s&action=%s'.$str_status.'&category[]=%s">'.__('Delete', 'wwclt').'</a>',$_REQUEST['page'],'delete',$item['ID']),
        );
        
        //add more link below to the title of table 
        	$status 	= get_post_meta($item['ID'],$prefix.'product_status',true);
        	$approve = __('Approve','wwclt');
        	$pending = __('Pending','wwclt');
        	$cancelled = __('Cancelled','wwclt');
        	
        if ($status == '1') { //is approved
         	$plactions = array(
	            'pending'    => sprintf('<a href="?page=%s&clt_status=%s&clt_id=%s">'.$pending.'</a>',$_REQUEST['page'],'0',$item['ID']),
	            'cancel'    => sprintf('<a href="?page=%s&clt_status=%s&clt_id=%s">'.$cancelled.'</a>',$_REQUEST['page'],'2',$item['ID']),
	        );
         } else if ($status == '2') { //is cancelled
         	
         	$plactions = array(
	            'sapprove'  => sprintf('<a href="?page=%s&clt_status=%s&clt_id=%s">'.$approve.'</a>',$_REQUEST['page'],'1',$item['ID']),
	            'pending'    => sprintf('<a href="?page=%s&clt_status=%s&clt_id=%s">'.$pending.'</a>',$_REQUEST['page'],'0',$item['ID']),
	        );
	        
         } else { //is pending
         	
         	$plactions = array(
	            'sapprove'  => sprintf('<a href="?page=%s&clt_status=%s&clt_id=%s">'.$approve.'</a>',$_REQUEST['page'],'1',$item['ID']),
	            'cancel'    => sprintf('<a href="?page=%s&clt_status=%s&clt_id=%s">'.$cancelled.'</a>',$_REQUEST['page'],'2',$item['ID']),
	        );
         }
         $actions = array_merge($actions,$plactions);
        
        //Return the title contents	        
        return sprintf('%1$s %2$s',
            /*$1%s*/ $item['post_title'],
            /*$2%s*/ $this->row_actions($actions)
        );
    }
    
    public function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }
    
    /**
     * Display Columns
     *
     * Handles which columns to show in table
     * 
	 * @packageCategory List Table
	 * @since 1.0.0
     */
	public function get_columns(){
	
        $columns = array(
	        					'cb'      		=> '<input type="checkbox" />', //Render a checkbox instead of text
					            'post_title'	=> __( 'Title', 'wwclt' ),
					            'post_content'	=> __( 'Description', 'wwclt' ),
					            'clt_cat'		=> __( 'Category', 'wwclt' ),
					            'clt_status'	=> __( 'Status', 'wwclt' ),
								'post_date'		=> __( 'Date', 'wwclt' )
					        );
        return $columns;
    }
	
    /**
     * Sortable Columns
     *
     * Handles soratable columns of the table
     * 
	 * @packageCategory List Table
	 * @since 1.0.0
     */
	public function get_sortable_columns() {
		
		
        $sortable_columns = array(
							            'post_title'		=> array( 'post_title', true ),     //true means its already sorted
							            'post_content'		=> array( 'post_content', true ),
							            'post_date'			=> array( 'post_date', true ),
							            'clt_status'		=> array( 'clt_status', true ),
							        );
        return $sortable_columns;
    }
	
	public function no_items() {
		//message to show when no records in database table
		_e( 'No products found.', 'wwclt' );
	}
	
	/**
     * Bulk actions field
     *
     * Handles Bulk Action combo box values
     * 
	 * @packageCategory List Table
	 * @since 1.0.0
     */
	public function get_bulk_actions() {
		//bulk action combo box parameter
		//if you want to add some more value to bulk action parameter then push key value set in below array
        $actions = array(
					            'delete'    => __('Delete','wwclt')
					        );
        return $actions;
    }
    
	public function process_bulk_action() {
    
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            
        	wp_die(__( 'Items deleted (or they would be if we had items to delete)!', 'wwclt' ));
        } 
        
    }

    public function extra_tablenav( $which ) {
    	if( $which == 'top' ) {
    		
			$html = '';
			
    		$html .= '<div class="alignleft actions">';
    			//$this->months_dropdown( WW_CLT_POST_TYPE );
    			
				$terms = get_terms( WW_CLT_TAXONOMY );
				
				if ( $terms ) {
		
					$html .= '<select name="clt_category" id="clt_category">';
					
					$html .= '<option value="" ' .  selected( isset( $_GET['clt_category'] ) ? $_GET['clt_category'] : '', '', false ) . '>'.__( 'Select a category', 'wwclt' ).'</option>';
			
					foreach ( $terms as $term ) {
						
						if( isset($_GET[WW_CLT_TAXONOMY]) && $_GET[WW_CLT_TAXONOMY] == $term->slug ) {
							
							$_GET['clt_category'] = $term->term_taxonomy_id;
						}
						
						$term_count = ' (' . $term->count . ')';
						$html .= '<option value="' . $term->term_taxonomy_id . '" ' . selected( isset( $_GET['clt_category'] ) ? $_GET['clt_category'] : '', $term->term_taxonomy_id, false ) . '>' . $term->name . $term_count . '</option>';
					}
				
					$html .= '</select>';
					
				}
				
    		$html .= '	<input type="submit" value="'.__('Filter','wwclt').'" class="button" id="post-query-submit" name="">';
    		$html .= '</div>';
    		
			echo $html;
    	}
    }
    
    /**
	 * To make linked all links for sorting on top of the table
	 * 
	 * Get an associative array ( id => link ) with the list
	 * of views available on this table.
	 *
	 *
	 * @return array
	 */
	public function get_views() {
		
		$allcount = count($this->model->ww_clt_get_products());
		$pendingcount = count($this->model->ww_clt_get_products( array( 'clt_status' => '0')));
		$approvedcount = count($this->model->ww_clt_get_products( array( 'clt_status' => '1')));
		$cancelledcount = count($this->model->ww_clt_get_products( array( 'clt_status' => '2')));
		
		//makr proper class to show this link is viewing currently
		$class_pending = ''; 
		$class_approved = ''; 
		$class_cancelled = '';
		$class_all = '';
		
		if( ( isset( $_GET['clt_status'] ) && $_GET['clt_status'] == '0' ) ) { // pending status list
	
			$class_pending = ' class="current" ';
			
		} elseif( isset( $_GET['clt_status'] ) && $_GET['clt_status'] == '1' ) { // approved status list
			
			$class_approved = ' class="current" ';
			
		} elseif( isset( $_GET['clt_status'] ) && $_GET['clt_status'] == '2' ) { // cancelled list
			
			$class_cancelled = ' class="current" ';
			
		} else { // all status list
			
			$class_all = ' class="current" ';
		}
		
		//make array to show links for sorting
		$views = array( 
							'all' 		=> sprintf('<a %s href="admin.php?page=%s">'.__('All','wwclt').'<span class="count">(%s)</span></a>',$class_all,$_REQUEST['page'],$allcount),
							'pending' 	=> sprintf('<a %s href="admin.php?page=%s&clt_status=%s">'.__('Pending','wwclt').'<span class="count">(%s)</span></a>',$class_pending,$_REQUEST['page'],'0',$pendingcount),
							'approved' 	=> sprintf('<a %s href="admin.php?page=%s&clt_status=%s">'.__('Approved','wwclt').'<span class="count">(%s)</span></a>',$class_approved,$_REQUEST['page'],'1',$approvedcount),
							'cancelled' => sprintf('<a %s href="admin.php?page=%s&clt_status=%s">'.__('Cancelled','wwclt').'<span class="count">(%s)</span></a>',$class_cancelled,$_REQUEST['page'],'2',$cancelledcount)
						);
		return $views;
	}
	
	/**
	 * Generates content for a single row of the table
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param object $item The current item
	 */
  	public function single_row( $item ) {
  		
		static $row_class = '';
		
  		if( isset( $item['clt_status'] ) ) {
  			
  			switch( strtolower($item['clt_status']) ) {
  				case 'pending' :
  								$row_class = ' class="ww-clt-pending"';
  								break;
		  		case 'approved' :
  								$row_class = ' class="ww-clt-approved"';
  								break;
		  		case 'cancelled' :
  								$row_class = ' class="ww-clt-cancelled"';
  								break;
		  		default :
	  				  			$row_class = ( $row_class == '' ? ' class="alternate"' : '' );
  			}
  			
		} else {
			
			$row_class = ( $row_class == '' ? ' class="alternate"' : '' );
		}
  		
		echo '<tr' . $row_class . '>';
		$this->single_row_columns( $item );
		echo '</tr>';
	}
	
	public function prepare_items() {
        
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 5;
        
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
         /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
		$data = $this->display_products();
		
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby 	= (!empty($_REQUEST['orderby'])) 	? $_REQUEST['orderby'] 	: 'ID'; 	// If no sort, default to title
            $order 		= (!empty($_REQUEST['order'])) 		? $_REQUEST['order'] 	: 'desc'; 	// If no order, default to asc
            
            // If field value is integer
            $clt_int_sort = array( 'ID' );
            
            // Integer value sorting else string sorting
            if( in_array($orderby, $clt_int_sort) ) {
			    
            	if ($a[$orderby] == $b[$orderby]) {
			        return 0;
			    }
			    $result = ($a[$orderby] < $b[$orderby]) ? -1 : 1;
			    
            } else {
            	$result = strcmp($a[$orderby], $b[$orderby]); // Determine sort order
            }
            
            return ($order==='asc') ? $result : -$result; // Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
       
                
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
									            'total_items' => $total_items,                  //WE have to calculate the total number of items
									            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
									            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
									        ) );
    }
    
}

//Create an instance of our package class...
$ProductListTable = new Ww_Clt_Product_List();
	
//Fetch, prepare, sort, and filter our data...
$ProductListTable->prepare_items();

?>

<div class="wrap">
    
    <?php echo screen_icon('options-general'); ?>
	
    <h2>
    	<?php _e( 'Products', 'wwclt' ); ?>
    	<a class="add-new-h2" href="admin.php?page=ww_clt_add_form"><?php _e( 'Add New','wwclt' ); ?></a>
    </h2>
   	<?php 
   		$html = '';
		if(isset($_GET['message']) && !empty($_GET['message']) ) { //check message
			
			if( $_GET['message'] == '1' ) { //check insert message
				$html .= '<div class="updated settings-error" id="setting-error-settings_updated">
							<p><strong>'.__("Product Inserted Successfully.",'wwclt').'</strong></p>
						</div>'; 
			} else if($_GET['message'] == '2') {//check update message
				$html .= '<div class="updated" id="message">
							<p><strong>'.__("Product Updated Successfully.",'wwclt').'</strong></p>
						</div>'; 
			} else if($_GET['message'] == '3') {//check delete message
				$html .= '<div class="updated" id="message">
							<p><strong>'.__("Product deleted Successfully.",'wwclt').'</strong></p>
						</div>'; 
			} else if($_GET['message'] == '4') {//check delete message
				$html .= '<div class="updated" id="message">
							<p><strong>'.__("Product Status Changed Successfully.",'wwclt').'</strong></p>
						</div>'; 
			}
		}
		echo $html;
		
		//showing links for sorting for calling this function must override the function get_views() as made in this class file
		$ProductListTable->views();
		
	?>
	
    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="product-filter" method="get">
        
    	<!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		
        <!-- Search Title -->
        <?php $ProductListTable->search_box( __( 'Search', 'wwclt' ), 'ww_clt_search' ); ?>
        
        <!-- Now we can render the completed list table -->
        <?php $ProductListTable->display() ?>
        
    </form>
	        
</div>