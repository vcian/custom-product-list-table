<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Admin Pages Class
 * 
 * Handles all admin functinalities
 *
 * @packageCategory List Table 
 * @since 1.0.0
 */
class Ww_Clt_Admin_Pages{
	
	public $model, $scripts;
	
	public function __construct(){
		
		global $ww_clt_model, $ww_clt_scripts;
		$this->model = $ww_clt_model;
		$this->scripts = $ww_clt_scripts;
		
	}
	
	/**
	 * Add Top Level Menu Page
	 *
	 * Runs when the admin_menu hook fires and adds a new
	 * top level admin page and menu item
	 * 
	 * @packageCategory List Table
	 * @since 1.0.0
	 */
	public function ww_clt_admin_menu() {

		//main menu page	
		add_menu_page( __('Custom Product List Table','wwclt'), __('Custom Product List Table','wwclt'), wwcltlevel, 'ww_clt_products', '');

		add_submenu_page( 'ww_clt_products', __('Products','wwclt'), __('Products','wwclt'), wwcltlevel, 'ww_clt_products', array($this,'ww_clt_add_submenu_list_table_page') );
		
		$ww_clt_admin_add_page = add_submenu_page( 'ww_clt_products', __('Products','wwclt'), __('Add New','wwclt'), wwcltlevel, 'ww_clt_add_form', array($this,'ww_clt_add_submenu_page') );
		
		add_submenu_page( 'ww_clt_products', __('Categories','wwclt'), __('Categories','wwclt'), wwcltlevel, 'edit-tags.php?taxonomy=' . WW_CLT_TAXONOMY . '&post_type=' . WW_CLT_POST_TYPE );
		
		//loads javascript needed for add page for toggle metaboxes
		add_action( "admin_head-$ww_clt_admin_add_page", array( $this->scripts, 'ww_clt_add_product_page_load_scripts' ) );
	}
	
	/**
	 * List of all Product
	 *
	 * Handles Function to listing all product
	 * 
	 * @packageCategory List Table
	 * @since 1.0.0
	 */
	public function ww_clt_add_submenu_list_table_page() {
		
		include_once( WW_CLT_ADMIN . '/forms/ww-clt-product-list.php');
		
	}
	
	/**
	 * Adding Admin Sub Menu Page
	 *
	 * Handles Function to adding add data form
	 * 
	 * @packageCategory List Table
	 * @since 1.0.0
	 */
	public function ww_clt_add_submenu_page() {
		
		include_once( WW_CLT_ADMIN . '/forms/ww-clt-add-edit-product.php');
		
	}
	
	
	/**
	 * Add action admin init
	 * 
	 * Handles add and edit functionality of product
	 * 
	 * @packageCategory List Table
	 * @since 1.0.0
	 */
	public function ww_clt_admin_init() {
		include_once( WW_CLT_ADMIN . '/forms/ww-clt-product-save.php');
	}
	
	/**
	 * Bulk Delete
	 * 
	 * Handles bulk delete functinalities of product
	 * 
	 * @packageCategory List Table
	 * @since 1.0.0
	 */
	public function ww_clt_admin_bulk_delete() {
		
		if(((isset( $_GET['action'] ) && $_GET['action'] == 'delete') || (isset( $_GET['action2'] ) && $_GET['action2'] == 'delete')) && isset($_GET['page']) && $_GET['page'] == 'ww_clt_products' ) { //check action and page
		
			// get redirect url
			$redirect_url = add_query_arg( array( 'page' => 'ww_clt_products' ), admin_url( 'admin.php' ) );	
			
			//get bulk product array from $_GET
			$action_on_id = $_GET['category'];
			
			if( count( $action_on_id ) > 0 ) { //check there is some checkboxes are checked or not 
				
				//if there is multiple checkboxes are checked then call delete in loop
				foreach ( $action_on_id as $ww_clt_id ) {
					
					//parameters for delete function
					$args = array (
									'clt_id' => $ww_clt_id
								);
								
					//call delete function from model class to delete records
					$this->model->ww_clt_bulk_delete( $args );
				}
				$redirect_url = add_query_arg( array( 'message' => '3' ), $redirect_url );
				
				//if bulk delete is performed successfully then redirect 
				wp_redirect( $redirect_url ); 
				exit;
			} else {
				//if there is no checboxes are checked then redirect to listing page
				wp_redirect( $redirect_url ); 
				exit;
			}			
		}
	}
	
	/**
	 * Status Change
	 * 
	 * Handles changing status of product
	 * 
	 * @packageCategory List Table
	 * @since 1.0.0
	 */
	public function ww_clt_admin_change_status() {
		$prefix = WW_CLT_META_PREFIX;
		if (isset($_GET['clt_status']) && isset($_GET['clt_id']) && !empty($_GET['clt_id'])) {
			
			// get redirect url
			$redirect_url = add_query_arg( array( 'page' => 'ww_clt_products', 'message' => '4' ), admin_url( 'admin.php' ) );
			
			$postid = $_GET['clt_id'];
			update_post_meta( $postid, $prefix.'product_status',$_GET['clt_status']);
			
			wp_redirect( $redirect_url ); 
			exit;
			
		} 
		
	}
		
	/**
	 * Display products using category
	 * 
	 * Handles to display products using category
	 * 
	 * @packageCategory List Table
	 * @since 1.0.0
	 */
	public function ww_clt_category_search( $where ) {
	
		if( is_admin() ) {
			global $wpdb;
			
			if ( isset( $_GET['clt_category'] ) && !empty( $_GET['clt_category'] ) && intval( $_GET['clt_category'] ) != 0 ) {
				
				$product_category = intval( $_GET['clt_category'] );
			
				$where .= " AND ID IN ( SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id=$product_category )";
			}
		}
		return $where;
	}

	/**
	 * Adding Hooks
	 *
	 * @packageCategory List Table
	 * @since 1.0.0
	 */
	public function add_hooks() {
		
		//add new admin menu page
		add_action('admin_menu',array($this,'ww_clt_admin_menu'));
		
		//add admin init for saving data
		add_action( 'admin_init' , array($this,'ww_clt_admin_init'));
		
		//add admin init for bult delete functionality
		add_action( 'admin_init' , array($this,'ww_clt_admin_bulk_delete'));
		
		//add admin init for changing status
		add_action( 'admin_init' , array($this,'ww_clt_admin_change_status') );

		// Add filter for display products using category
		add_filter( 'posts_where' , array($this, 'ww_clt_category_search' ) );

	}
}
?>