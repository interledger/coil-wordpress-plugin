<?php
/**
 * Plugin Name: Coil for WordPress
 * Plugin URI: https://github.com/seb86/coil-for-wp
 * Description: Enables support for Coil in WordPress.
 * Author: Sébastien Dumont
 * Author URI: https://sebastiendumont.com
 * Version: 1.0.0-alpha.2
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Coil' ) ) {
	class Coil {

		/**
		 * Plugin Version
		 *
		 * @access public
		 * @static
		 */
		public static $version = '1.0.0-alpha.2';

		/**
		 * @var Coil - the single instance of the class.
		 *
		 * @access protected
		 * @static
		 */
		protected static $_instance = null;

		/**
		 * Main Coil Instance.
		 *
		 * Ensures only one instance of Coil is loaded or can be loaded.
		 *
		 * @access  public
		 * @static
		 * @see     Coil()
		 * @return  Coil - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @access public
		 * @return void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cloning this object is forbidden.', 'coil-for-wp' ), self::$version );
		} // END __clone()

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @access public
		 * @return void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'coil-for-wp' ), self::$version );
		} // END __wakeup()

		/**
		 * Load the plugin.
		 *
		 * @access public
		 */
		public function __construct() {
			// Setup Constants.
			$this->setup_constants();

			// Include admin classes to handle all back-end functions.
			$this->admin_includes();

			// Sets the monetization meta tag if set via the settings.
			add_action( 'wp_head', array( $this, 'meta_tag' ) );

			// Sets a body class if the singular post has enabled monetization.
			add_filter( 'body_class', array( $this, 'body_classes' ) );

			// Enqueue front-end asset to initialize monetization.
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

			// Include required files.
			add_action( 'init', array( $this, 'includes' ) );

			// Load translation files.
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ), 99 );
			add_action( 'enqueue_block_editor_assets', array( $this, 'block_localization' ) );
		} // END __construct()

		/**
		 * Setup Constants
		 *
		 * @access public
		 */
		public function setup_constants() {
			$this->define('COIL_VERSION', self::$version);
			$this->define('COIL_SLUG', 'coil-for-wp');

			$this->define('COIL_FILE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			$this->define('COIL_URL_PATH', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
			$this->define('COIL_PLUGIN_FILE', __FILE__);
			$this->define('COIL_PLUGIN_BASE', plugin_basename( __FILE__ ) );

			$this->define('COIL_WP_VERSION_REQUIRE', '4.9');

			$this->define('COIL_PLUGIN_URL', '#');
			$this->define('COIL_SUPPORT_URL', '#');
			$this->define('COIL_DOCUMENTATION_URL', '#');

			$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
			$this->define('COIL_ASSET_SUFFIX', $suffix);
		} // END setup_constants()

		/**
		 * Define constant if not already set.
		 *
		 * @access private
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		} // END define()

		/**
		 * Includes Coil support for WordPress.
		 *
		 * @access public
		 * @return void
		 */
		public function includes() {
			require_once( COIL_FILE_PATH . '/includes/class-coil-autoloader.php' );
			require_once( COIL_FILE_PATH . '/includes/class-coil-block-assets.php' );
			require_once( COIL_FILE_PATH . '/includes/class-coil-register-blocks.php' );
			require_once( COIL_FILE_PATH . '/includes/class-coil-meta-box.php' );
			require_once( COIL_FILE_PATH . '/includes/class-coil-post-meta.php' );
		} // END includes()

		/**
		 * Include admin classes to handle all back-end functions.
		 *
		 * @access public
		 * @return void
		 */
		public function admin_includes() {
			if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
				require_once( dirname( __FILE__ ) . '/includes/admin/class-coil-admin.php' );
				require_once( dirname( __FILE__ ) . '/includes/class-coil-install.php' ); // Install Coil.
			}
		} // END admin_includes()

		/**
		 * Adds the monetize tag to the header.
		 *
		 * 1. If a post specifies a pointer of it's own then that will override the global tag.
		 * 2. If the post does not specify a pointer then the global tag will be used if set.
		 *
		 * @access public
		 * @global object $post
		 * @return void
		 */
		public function meta_tag() {
			global $post;

			//$post_payout_pointer_id = get_post_meta( $post->ID, '_coil_payout_pointer_id', true );
			$monetize_status        = get_post_meta( $post->ID, '_coil_monetize_post_status', true );

			//if ( ! empty( $post_payout_pointer_id ) ) {
				//$payout_pointer_id = $post_payout_pointer_id;
			//} else {
				$payout_pointer_id = get_option( "coil_payout_pointer_id" );
			//}

			// If the post is not set for monetizing then just return.
			if ( ! empty( $monetize_status ) && $monetize_status == 'no' ) {
				return;
			}

			if ( ! empty( $payout_pointer_id ) ) {
				echo '<meta name="monetization" content="' . $payout_pointer_id . '" />' . "\n";
			}
		} // END meta_tag()

		/**
		 * If debug is on, serve unminified source assets.
		 *
		 * @access public
		 * @param string|string $type The type of resource.
		 * @param string|string $directory Any extra directories needed.
		 */
		public function asset_source( $type = 'js', $directory = null ) {
			if ( 'js' === $type ) {
				return SCRIPT_DEBUG ? COIL_URL_PATH . 'src/' . $type . '/' . $directory : COIL_URL_PATH . 'dist/' . $type . '/' . $directory;
			} else {
				return COIL_URL_PATH . 'dist/css/' . $directory;
			}
		} // END asset_source()

		/**
		 * Load the plugin translations if any ready.
		 *
		 * Translations should be added in the WordPress language directory:
		 *      - WP_LANG_DIR/plugins/coil-for-wp-LOCALE.mo
		 *
		 * @access public
		 * @return void
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'coil-for-wp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		} // END load_plugin_textdomain()

		/**
		 * Enqueue localization data for our blocks.
		 *
		 * @access public
		 */
		public function block_localization() {
			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'coil-editor', 'coil' );
			}
		} // END block_localization()

		/**
		 * Is an AMP endpoint.
		 *
		 * @return bool Whether the current response will be AMP.
		 */
		public function is_amp() {
			return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
		} // END is_amp()

		/**
		 * Sets a body class if the singular post has enabled monetization.
		 *
		 * @access public
		 * @global object WP_Post - The post object.
		 * @param  array $classes - List of body classes already applied.
		 * @return array $classes - List of new body classes.
		 */
		public function body_classes( $classes ) {
			global $post;

			$payout_pointer_id = get_option( "coil_payout_pointer_id" );
			$monetize_status   = get_post_meta( $post->ID, '_coil_monetize_post_status', true );

			if ( is_singular() && ! empty( $monetize_status ) && $monetize_status != 'no' ) {
				if ( ! empty( $payout_pointer_id ) ) {
					// JavaScript trigger class.
					$classes[] = 'monetization-not-initialized';

					// Monetize post status class
					$classes[] = 'coil-' . $monetize_status;
				} else {
					// Payment pointer ID is missing.
					$classes[] = 'coil-missing-id';

					// If the user logged in is admin then add a special class.
					if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
						$classes[] = 'coil-show-admin-notice';
					}
				}
			}

			return $classes;
		} // END body_classes()

		/**
		 * Enqueue front-end asset to initialize monetization.
		 *
		 * @access public
		 * @global object WP_Post - The post object.
		 */
		public function frontend_scripts() {
			global $post;

			// Custom scripts are not allowed in AMP, so short-circuit.
			if ( self::is_amp() ) {
				return;
			}

			$monetize_status = get_post_meta( $post->ID, '_coil_monetize_post_status', true );

			// If the post is not monetizing then don't load asset.
			if ( is_singular() && ! empty( $monetize_status ) && $monetize_status == 'no' ) {
				return;
			}

			wp_enqueue_script(
				'initialize-monetization',
				COIL_URL_PATH . '/assets/js/initialize-monetization' . COIL_ASSET_SUFFIX . '.js',
				array( 'jquery' ),
				COIL_VERSION
			);

			wp_localize_script( 'initialize-monetization', 'coil_params', array(
				'coil_for_wp_version'         => COIL_VERSION,
				'content_container'           => get_option( 'coil_content_container' ),
				'verifying_browser_extension' => __( 'This post is monetized. Please wait while we verify you are a subscriber...', 'coil-for-wp' ),
				'browser_extension_missing'   => sprintf( __( 'You need to %1$sinstall the Coil browser extension%2$s in order to view this posts content.', 'coil-for-wp' ), '<a target="_blank" href="https://help.coil.com/en/articles/2701494-supported-browsers">', '</a>' ),
				'verifying_coil_account'      => __( 'Verifying your Coil account. Please wait...', 'coil-for-wp' ),
				'loading_content'             => __( 'Loading content. Please wait...', 'coil-for-wp' )
			) );
		} // END frontend_scripts()

	} // END class

} // END if class exists

/**
 * The main function for that returns Coil
 *
 * The main function responsible for returning the one true Coil
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $coil = Coil(); ?>
 *
 * @return object|Coil The one true Coil Instance.
 */
function coil() {
	return Coil::instance();
}

// Get the plugin running. Load on plugins_loaded action to avoid issue on multisite.
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	add_action( 'plugins_loaded', 'coil', 90 );
} else {
	coil();
}