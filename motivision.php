<?php
/**
 * @package Motivision
 */
/*
Plugin Name: Motivision
Plugin URI: http://estadosbinarios.com
Description: Motivision is a Wordpress plugin that helps to increase the journalists or writers motivation. It shows a panel that gives them an overview about how many articles they wrote the last weeks and how many articles wrote the rest of writers. It also shows them the impact in terms of views of their articles for the website.
Author: Carlos Pérez
Version: 1.1.2
*/

define( 'MOTIVISION_VERSION', '1.1.1' );
define( 'MOTIVISION_PATH', dirname( __FILE__ ) );
define( 'MOTIVISION_PATH_INCLUDES', dirname( __FILE__ ) . '/inc' );
define( 'MOTIVISION_FOLDER', basename( MOTIVISION_PATH ) );
define( 'MOTIVISION_URL', plugins_url() . '/' . MOTIVISION_FOLDER );
define( 'MOTIVISION_URL_INCLUDES', MOTIVISION_URL . '/inc' );
define( 'MOTIVISION_PER_PAGE', 10 );


/**
 *
 * The plugin base class - the root of all WP goods!
 *
 * @author Carlos Pérez
 *
 */
class Motivision_Plugin {

	/**
	 *
	 * Assign everything as a call from within the constructor
	 */
	public function __construct() {

		add_action( 'the_content', array( $this, 'setPostViews' ) );

		// add scripts and styles only available in admin
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_JS' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_CSS' ) );

		// register admin pages for the plugin
		add_action( 'admin_menu', array( $this, 'admin_pages_callback' ) );

		// register meta boxes for Pages (could be replicated for posts and custom post types)
		add_action( 'add_meta_boxes', array( $this, 'meta_boxes_callback' ) );

		// register save_post hooks for saving the custom fields
		add_action( 'save_post', array( $this, 'save_sample_field' ) );

		// Register custom post types and taxonomies
		add_action( 'init', array( $this, 'custom_post_types_callback' ), 5 );
		add_action( 'init', array( $this, 'custom_taxonomies_callback' ), 6 );

		// Register activation and deactivation hooks
		register_activation_hook( __FILE__, 'on_activate_callback' );
		register_deactivation_hook( __FILE__, 'on_deactivate_callback' );

		// Translation-ready
		add_action( 'plugins_loaded', array( $this, 'add_textdomain' ) );

		// Add earlier execution as it needs to occur before admin page display
		add_action( 'admin_init', array( $this, 'register_settings' ), 5 );
		add_action( 'admin_init', array( $this, 'motivision_controller' ), 5 );

		// Add a sample shortcode
		//add_action( 'init', array( $this, 'sample_shortcode' ) );

		/*
		 * TODO:
		 * 		template_redirect
		 */

		// Add actions for storing value and fetching URL
		// use the wp_ajax_nopriv_ hook for non-logged users (handle guest actions)
		//add_action( 'wp_ajax_store_ajax_value', array( $this, 'store_ajax_value' ) );
		//add_action( 'wp_ajax_fetch_ajax_url_http', array( $this, 'fetch_ajax_url_http' ) );


		//remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);


	}

	public function setPostViews($content) {

		if(get_post_type() == 'post' && is_singular()) {
			$post = get_post();

			$count_key = 'post_views_count';
			$count = get_post_meta($post->ID, $count_key, true);
			if($count==''){
				$count = 0;
				delete_post_meta($post->ID, $count_key);
				add_post_meta($post->ID, $count_key, '0');
			}else{
				$count++;
				update_post_meta($post->ID, $count_key, $count);
			}

		}

		return $content;
	}


	/**
	 *
	 * Adding JavaScript scripts for the admin pages only
	 *
	 * Loading existing scripts from wp-includes or adding custom ones
	 *
	 */
	public function add_admin_JS( $hook ) {
		wp_enqueue_script( 'jquery' );

		wp_register_script( 'bootstrapscript-admin', plugins_url( '/js/bootstrap.min.js' , __FILE__ ), array('jquery'), '1.0', true );
		wp_enqueue_script( 'bootstrapscript-admin' );
	}

	/**
	 *
	 * Add admin CSS styles - available only on admin
	 *
	 */
	public function add_admin_CSS( $hook ) {
		wp_register_style( 'bootstrapstyle-admin', plugins_url( '/css/bootstrap.min.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_enqueue_style( 'bootstrapstyle-admin' );

		wp_register_style('help_page',  plugins_url( '/help-page.css', __FILE__ ) );
		wp_enqueue_style('help_page');

	}

	/**
	 *
	 * Callback for registering pages
	 *
	 * This demo registers a custom page for the plugin and a subpage
	 *
	 */
	public function admin_pages_callback() {
		add_menu_page(__( "Motivision", 'mtvision' ), __( "Motivision", 'mtvision' ), 'publish_posts', 'motivision', array( $this, 'panel_page' ) );
		add_submenu_page( 'motivision', __( "Settings", 'mtvision' ), __( "Settings", 'mtvision' ), 'edit_themes', 'motivision-setting', array( $this, 'settings_page' ) );
	}

	/**
	 *
	 * The content of the base page
	 *
	 */
	public function panel_page() {
		include_once( MOTIVISION_PATH . '/panel-page.php' );
	}

	/**
	 *
	 * The content of the sub base page
	 *
	 */
	public function settings_page() {
		include_once( MOTIVISION_PATH . '/settings-page.php' );
	}


	/**
	 *
	 *  Adding right and bottom meta boxes to Pages
	 *
	 */
	public function meta_boxes_callback() {
		// register side box
		add_meta_box(
			'side_meta_box',
			__( "DX Side Box", 'mtvision' ),
			array( $this, 'side_meta_box' ),
			'pluginbase', // leave empty quotes as '' if you want it on all custom post add/edit screens
			'side',
			'high'
		);

		// register bottom box
		add_meta_box(
			'bottom_meta_box',
			__( "DX Bottom Box", 'mtvision' ),
			array( $this, 'bottom_meta_box' ),
			'' // leave empty quotes as '' if you want it on all custom post add/edit screens or add a post type slug
		);
	}

	/**
	 *
	 * Init right side meta box here
	 * @param post $post the post object of the given page
	 * @param metabox $metabox metabox data
	 */
	public function side_meta_box( $post, $metabox) {
		_e("<p>Side meta content here</p>", 'mtvision');

		// Add some test data here - a custom field, that is
		$test_input = '';
		if ( ! empty ( $post ) ) {
			// Read the database record if we've saved that before
			$test_input = get_post_meta( $post->ID, 'test_input', true );
		}
		?>
		<label for="dx-test-input"><?php _e( 'Test Custom Field', 'mtvision' ); ?></label>
		<input type="text" id="dx-test-input" name="test_input" value="<?php echo $test_input; ?>" />
		<?php
	}

	/**
	 * Save the custom field from the side metabox
	 * @param $post_id the current post ID
	 * @return post_id the post ID from the input arguments
	 *
	 */
	public function save_sample_field( $post_id ) {
		// Avoid autosaves
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$slug = 'pluginbase';
		// If this isn't a 'book' post, don't update it.
		if ( ! isset( $_POST['post_type'] ) || $slug != $_POST['post_type'] ) {
			return;
		}

		// If the custom field is found, update the postmeta record
		// Also, filter the HTML just to be safe
		if ( isset( $_POST['test_input']  ) ) {
			update_post_meta( $post_id, 'test_input',  esc_html( $_POST['test_input'] ) );
		}
	}

	/**
	 *
	 * Init bottom meta box here
	 * @param post $post the post object of the given page
	 * @param metabox $metabox metabox data
	 */
	public function bottom_meta_box( $post, $metabox) {
		_e( "<p>Bottom meta content here</p>", 'mtvision' );
	}

	/**
	 * Register custom post types
	 *
	 */
	public function custom_post_types_callback() {
		register_post_type( 'pluginbase', array(
			'labels' => array(
				'name' => __("Base Items", 'mtvision'),
				'singular_name' => __("Base Item", 'mtvision'),
				'add_new' => _x("Add New", 'pluginbase', 'mtvision' ),
				'add_new_item' => __("Add New Base Item", 'mtvision' ),
				'edit_item' => __("Edit Base Item", 'mtvision' ),
				'new_item' => __("New Base Item", 'mtvision' ),
				'view_item' => __("View Base Item", 'mtvision' ),
				'search_items' => __("Search Base Items", 'mtvision' ),
				'not_found' =>  __("No base items found", 'mtvision' ),
				'not_found_in_trash' => __("No base items found in Trash", 'mtvision' ),
			),
			'description' => __("Base Items for the demo", 'mtvision'),
			'public' => true,
			'publicly_queryable' => true,
			'query_var' => true,
			'rewrite' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 40, // probably have to change, many plugins use this
			'supports' => array(
				'title',
				'editor',
				'thumbnail',
				'custom-fields',
				'page-attributes',
			),
			'taxonomies' => array( 'post_tag' )
		));
	}


	/**
	 * Register custom taxonomies
	 *
	 */
	public function custom_taxonomies_callback() {
		register_taxonomy( 'pluginbase_taxonomy', 'pluginbase', array(
			'hierarchical' => true,
			'labels' => array(
				'name' => _x( "Base Item Taxonomies", 'taxonomy general name', 'mtvision' ),
				'singular_name' => _x( "Base Item Taxonomy", 'taxonomy singular name', 'mtvision' ),
				'search_items' =>  __( "Search Taxonomies", 'mtvision' ),
				'popular_items' => __( "Popular Taxonomies", 'mtvision' ),
				'all_items' => __( "All Taxonomies", 'mtvision' ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __( "Edit Base Item Taxonomy", 'mtvision' ),
				'update_item' => __( "Update Base Item Taxonomy", 'mtvision' ),
				'add_new_item' => __( "Add New Base Item Taxonomy", 'mtvision' ),
				'new_item_name' => __( "New Base Item Taxonomy Name", 'mtvision' ),
				'separate_items_with_commas' => __( "Separate Base Item taxonomies with commas", 'mtvision' ),
				'add_or_remove_items' => __( "Add or remove Base Item taxonomy", 'mtvision' ),
				'choose_from_most_used' => __( "Choose from the most used Base Item taxonomies", 'mtvision' )
			),
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => true,
		));

		register_taxonomy_for_object_type( 'pluginbase_taxonomy', 'pluginbase' );
	}

	/**
	 * Initialize the Settings class
	 *
	 * Register a settings section with a field for a secure WordPress admin option creation.
	 *
	 */
	public function register_settings() {
		require_once( MOTIVISION_PATH . '/plugin-settings.class.php' );
		new Plugin_Settings();
	}

	/**
	 * Hook for including a sample widget with options
	 */
	public function motivision_controller() {
		include_once MOTIVISION_PATH_INCLUDES . '/motivision.class.php';
	}

	/**
	 * Add textdomain for plugin
	 */
	public function add_textdomain() {
		load_plugin_textdomain( 'mtvision', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Callback for saving a simple AJAX option with no page reload
	 */
	public function store_ajax_value() {
		if( isset( $_POST['data'] ) && isset( $_POST['data']['option_from_ajax'] ) ) {
			update_option( 'option_from_ajax' , $_POST['data']['option_from_ajax'] );
		}
		die();
	}

	/**
	 * Callback for getting a URL and fetching it's content in the admin page
	 */
	public function fetch_ajax_url_http() {
		if( isset( $_POST['data'] ) && isset( $_POST['data']['url_for_ajax'] ) ) {
			$ajax_url = $_POST['data']['url_for_ajax'];

			$response = wp_remote_get( $ajax_url );

			if( is_wp_error( $response ) ) {
				echo json_encode( __( 'Invalid HTTP resource', 'mtvision' ) );
				die();
			}

			if( isset( $response['body'] ) ) {
				if( preg_match( '/<title>(.*)<\/title>/', $response['body'], $matches ) ) {
					echo json_encode( $matches[1] );
					die();
				}
			}
		}
		echo json_encode( __( 'No title found or site was not fetched properly', 'mtvision' ) );
		die();
	}

}


/**
 * Register activation hook
 *
 */
function on_activate_callback() {
	// do something on activation
}

/**
 * Register deactivation hook
 *
 */
function on_deactivate_callback() {
	// do something when deactivated
}

// Initialize everything
$plugin_base = new Motivision_Plugin();
