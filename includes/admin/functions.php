<?php
declare(strict_types=1);
/**
 * Coil admin screens and options.
 */

namespace Coil\Admin;

use const \Coil\PLUGIN_VERSION;

/**
 * Customise the environment where we want to show the Coil metabox.
 *
 * @return void
 */
function load_metaboxes() : void {
	add_action( 'add_meta_boxes', __NAMESPACE__ . '\add_metabox' );
}

/**
 * Add metabox to the content editing screen.
 *
 * @return void
 */
function add_metabox() : void {
	$show_metabox = false;

	if ( ! function_exists( '\is_gutenberg_page' ) ) {
		// Show meta box if Gutenberg not installed.
		$show_metabox = true;
	} elseif ( ! \use_block_editor_for_post( $GLOBALS['post'] ) ) {
		// Show meta box if post is NOT using Gutenberg.
		$show_metabox = true;
	}

	if ( ! $show_metabox ) {
		return;
	}

	add_meta_box(
		'coil',
		__( 'Web Monetization - Coil', 'coil-monetize-content' ),
		__NAMESPACE__ . '\render_coil_metabox',
		[ 'page', 'post' ],
		'side',
		'high'
	);
}

/**
 * Render the Coil metabox.
 *
 * @return void
 */
function render_coil_metabox() : void {
	global $post;

	$coil_status   = get_post_meta( $post->ID, '_coil_monetize_post_status', true );
	$use_gutenberg = function_exists( '\use_block_editor_for_post' ) && use_block_editor_for_post( $post );
	$settings      = [
		'no'        => esc_html__( 'No Monetization', 'coil-monetize-content' ),
		'no-gating' => esc_html__( 'Monetized and Public', 'coil-monetize-content' ),
		'gate-all'  => esc_html__( 'Subscribers Only', 'coil-monetize-content' ),
	];

	if ( $use_gutenberg ) {
		$settings['gate-tagged-blocks'] = esc_html__( 'Split Content', 'coil-monetize-content' );
	}

	do_action( 'coil_before_render_metabox', $settings );
	?>

	<fieldset>
		<legend>
			<?php
			if ( $use_gutenberg ) {
				esc_html_e( 'Set the type of monetization for the article. Note: If "Split Content" selected, you will need to save the article and reload the editor to view the options at block level.', 'coil-monetize-content' );
			} else {
				esc_html_e( 'Set the type of monetization for the article.', 'coil-monetize-content' );
			}
			?>
		</legend>

		<?php foreach ( $settings as $option => $name ) : ?>
			<?php
			if ( $option === 'no' && empty( $coil_status ) ) {
				$is_selected = 'checked="checked"';
			} else {
				$is_selected = checked( $coil_status, $option, false );
			}
			?>
			<label for="track">
				<input type="radio" name="coil_monetize_post_status" id="<?php echo esc_attr( $option ); ?>" value="<?php echo esc_attr( $option ); ?>" <?php echo $is_selected; ?>/>
				<?php echo esc_html( $name ); ?>
				<br />
			</label>
		<?php endforeach; ?>
	</fieldset>

	<?php
	wp_nonce_field( 'coil_metabox_nonce_action', 'coil_metabox_nonce' );

	do_action( 'coil_after_render_metabox' );
}

/**
 * Maybe save the Coil metabox data on content save.
 *
 * @param int $post_id The ID of the post being saved.
 *
 * @return void
 */
function maybe_save_post_metabox( int $post_id ) : void {

	if ( ! current_user_can( 'edit_post', $post_id ) || empty( $_REQUEST['coil_metabox_nonce'] ) ) {
		return;
	}

	// Check the nonce.
	check_admin_referer( 'coil_metabox_nonce_action', 'coil_metabox_nonce' );

	$do_autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
	if ( $do_autosave || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	$coil_meta = [
		'_coil_monetize_post_status' => sanitize_text_field( $_POST['coil_monetize_post_status'] ),
	];

	foreach ( $coil_meta as $key => $value ) {
		if ( ! empty( $value ) ) {
			// For coil_monetize_post_status.
			if ( ! in_array( $value, [ 'gate-all', 'gate-tagged-blocks', 'no', 'no-gating' ], true ) ) {
				continue;
			}

			update_post_meta( $post_id, $key, $value );
		} else {
			delete_post_meta( $post_id, $key );
		}
	}
}

/**
 * Add action links to the list on the plugins screen.
 *
 * @param array $links An array of action links.
 *
 * @return array $links Updated array of action links.
 */
function add_plugin_action_links( array $links ) : array {
	if ( ! current_user_can( 'manage_options' ) ) {
		return $links;
	}

	$action_links = [
		'settings' => '<a href="' . add_query_arg( [ 'page' => 'coil' ], admin_url( 'admin.php' ) ) . '" aria-label="' . esc_attr__( 'Settings for Coil', 'coil-monetize-content' ) . '">' . esc_attr__( 'Settings', 'coil-monetize-content' ) . '</a>',
	];

	return array_merge( $action_links, $links );
}

/**
 * Add extra information to the meta section on the list on the plugins screen.
 *
 * @param string[] $metadata Plugin metadata.
 * @param string   $file     Path to this plugin's main file. Used to identify which row we're in.
 *
 * @return array $metadata Updated array of plugin meta.
 */
function add_plugin_meta_link( array $metadata, string $file ) : array {

	if ( $file !== 'coil-monetize-content/plugin.php' ) {
		return $metadata;
	}

	$row_meta = [
		'community' => '<a href="' . esc_url( 'https://wordpress.org/support/plugin/coil-monetize-content/' ) . '">' . esc_html__( 'Support forum', 'coil-monetize-content' ) . '</a>',
	];

	return array_merge( $metadata, $row_meta );
}

/**
 * Adds admin body class for the Coil settings screen.
 *
 * @param string $classes CSS classes.
 *
 * @return string $classes Updated CSS classes.
 */
function add_admin_body_class( string $classes ) : string {

	$screen = get_current_screen();
	if ( ! $screen ) {
		return $classes;
	}

	if ( $screen->id === 'toplevel_page_coil' ) {
		$classes = ' coil ';
	}

	return $classes;
}

/**
 * Load admin-only CSS/JS.
 *
 * @return void
 */
function load_admin_assets() : void {

	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$load_on_screens = [
		'toplevel_page_coil',
	];

	if ( ! in_array( $screen->id, $load_on_screens, true ) ) {
		return;
	}

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_style(
		'coil_admin',
		esc_url_raw( plugin_dir_url( dirname( __DIR__ ) ) . 'assets/css/admin/coil' . $suffix . '.css' ),
		[],
		PLUGIN_VERSION
	);
}
