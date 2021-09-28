<?php
declare(strict_types=1);
/**
 * Coil admin screens and options.
 */

namespace Coil\Admin;

use Coil;
use Coil\Gating;
use const Coil\COIL__FILE__;
use const Coil\PLUGIN_VERSION;

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

	if ( ! function_exists( '\use_block_editor_for_post' ) ) {
		// Show if Gutenberg not active.
		$show_metabox = true;
	} elseif ( ! \use_block_editor_for_post( $GLOBALS['post'] ) ) {
		// Show if post is NOT using Gutenberg.
		$show_metabox = true;
	} elseif ( version_compare( $GLOBALS['wp_version'], '5.3', '<' ) ) {
		// Show if using incompatible version of Gutenberg (`wp.editPost.PluginDocumentSettingPanel`).
		$show_metabox = true;
	}

	if ( ! $show_metabox ) {
		return;
	}

	add_meta_box(
		'coil',
		__( 'Coil Web Monetization', 'coil-web-monetization' ),
		__NAMESPACE__ . '\render_coil_metabox',
		Coil\get_supported_post_types(),
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

	// Explicitly use the post gating option to render whatever is saved on this post,
	// instead of what is saved globally. This is used to output the correct meta box
	// option.
	$post_gating   = Gating\get_post_gating( absint( $post->ID ) );
	$use_gutenberg = function_exists( '\use_block_editor_for_post' ) && use_block_editor_for_post( $post );
	$settings      = Gating\get_monetization_setting_types( true );

	if ( $use_gutenberg ) {
		// This is used if WP < 5.3 (in some cases, without the Gutenberg plugin).
		$settings['gate-tagged-blocks'] = esc_html__( 'Split Content', 'coil-web-monetization' );
	}

	do_action( 'coil_before_render_metabox', $settings );
	?>

	<fieldset>
		<legend>
			<?php
			if ( $use_gutenberg ) {
				esc_html_e( 'Set the type of monetization for the article. Note: If "Split Content" selected, you will need to save the article and reload the editor to view the options at block level.', 'coil-web-monetization' );
			} else {
				esc_html_e( 'Set the type of monetization for the article.', 'coil-web-monetization' );
			}
			?>
		</legend>

		<?php foreach ( $settings as $option => $name ) : ?>
			<label for="<?php echo esc_attr( $option ); ?>">
				<input type="radio" name="coil_monetize_post_status" id="<?php echo esc_attr( $option ); ?>" value="<?php echo esc_attr( $option ); ?>" <?php checked( $post_gating, $option ); ?>/>
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

	$post_gating = sanitize_text_field( $_REQUEST['coil_monetize_post_status'] ?? '' );

	if ( $post_gating ) {
		Gating\set_post_gating( $post_id, $post_gating );
	} else {
		delete_post_meta( $post_id, '_coil_monetize_post_status' );
	}
}

/**
 * Fires after a term has been updated, but before the term cache has been cleaned.
 *
 * @param int    $term_id  Term ID.
 * @param int    $tt_id    Term taxonomy ID.
 * @param string $taxonomy Taxonomy slug.
 *
 * @return void
 */
function maybe_save_term_meta( int $term_id, int $tt_id, $taxonomy ) : void {

	$tax_obj = get_taxonomy( $taxonomy );
	if ( ! current_user_can( $tax_obj->cap->manage_terms ) || empty( $_REQUEST['term_gating_nonce'] ) ) {
		return;
	}

	// Check the nonce.
	check_admin_referer( 'coil_term_gating_nonce_action', 'term_gating_nonce' );

	$term_gating = sanitize_text_field( $_REQUEST['coil_monetize_term_status'] ?? '' );

	if ( $term_gating ) {
		Gating\set_term_gating( $term_id, $term_gating );
	} else {
		delete_term_monetization_meta( $term_id );
	}

}

/**
 * Deletes any term meta when a term is deleted.
 *
 * @param int $term The term id.
 * @return void
 */
function delete_term_monetization_meta( $term_id ) {

	if ( empty( $term_id ) ) {
		return;
	}
	delete_term_meta( $term_id, '_coil_monetize_term_status' );
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
		'settings' => '<a href="' . add_query_arg( [ 'page' => 'coil_settings' ], admin_url( 'admin.php' ) ) . '" aria-label="' . esc_attr__( 'Settings for Coil', 'coil-web-monetization' ) . '">' . esc_attr__( 'Settings', 'coil-web-monetization' ) . '</a>',
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

	if ( $file !== 'coil-web-monetization/plugin.php' ) {
		return $metadata;
	}

	$row_meta = [
		'community' => '<a href="' . esc_url( 'https://wordpress.org/support/plugin/coil-web-monetization/' ) . '">' . esc_html__( 'Support forum', 'coil-web-monetization' ) . '</a>',
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
		'toplevel_page_coil_settings',
	];

	if ( ! in_array( $screen->id, $load_on_screens, true ) ) {
		return;
	}

	$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$admin_params = apply_filters(
		'coil_admin_js_params',
		[
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		]
	);

	wp_enqueue_style(
		'coil_admin',
		esc_url_raw( plugin_dir_url( dirname( __DIR__ ) ) . 'assets/css/admin/coil' . $suffix . '.css' ),
		[],
		PLUGIN_VERSION
	);

	wp_register_script(
		'coil_admin_notices',
		esc_url_raw( plugin_dir_url( dirname( __DIR__ ) ) . 'assets/js/admin/admin-notices' . $suffix . '.js' ),
		[ 'jquery' ],
		time(),
		true
	);

	wp_localize_script(
		'coil_admin_notices',
		'coilAdminParams',
		$admin_params
	);

	// Enqueue localized script.
	wp_enqueue_script( 'coil_admin_notices' );
}

/**
 * Adds a Coil Web Monetization section to the Customizer that redirects users from the customizer
 * (where these customization settings were found previously)
 * to the Coil Settings panel.
 *
 * @param \WP_Customize_Manager $wp_customize WordPress Customizer object.
 * @return void
 */
function add_redirect_customizer_section( $wp_customize ) : void {

	// Message and Monetization customization options relocation section.
	$redirect_section = 'coil_customizer_section';

	$wp_customize->add_section(
		$redirect_section,
		[
			'title'      => __( 'Coil Web Monetization', 'coil-web-monetization' ),
			'capability' => apply_filters( 'coil_settings_capability', 'manage_options' ),
		]
	);

	$redirect_setting_id = 'customization-redirect-id';

	$wp_customize->add_setting(
		$redirect_setting_id,
		[
			'capability' => apply_filters( 'coil_settings_capability', 'manage_options' ),
			'default'    => true,
		]
	);

	$description  = '<p>' . sprintf(
		__( 'Message customization settings have moved to the ', 'coil-web-monetization' ) . '<a href="%s">' . __( 'Messaging Settings tab', 'coil-web-monetization' ) . '</a>',
		esc_url( admin_url( 'admin.php?page=coil_settings&tab=messaging_settings', COIL__FILE__ ) )
	) . '</p>';
	$description .= '<p>' . sprintf(
		__( 'Monetization options have been moved to the ', 'coil-web-monetization' ) . '<a href="%s">' . __( 'Monetization Settings tab.', 'coil-web-monetization' ) . '</a>',
		esc_url( admin_url( 'admin.php?page=coil_settings&tab=monetization_settings', COIL__FILE__ ) )
	) . '</p>';

	$wp_customize->add_control(
		$redirect_setting_id,
		[
			'type'        => 'hidden',
			'section'     => $redirect_section,
			'label'       => __( 'Customization options have all been moved to the Coil settings panel.', 'coil-web-monetization' ),
			'description' => $description,
		]
	);
}

/**
 * Gets the taxonomies and allows the output to be filtered.
 *
 * @return array Taxonomies or empty array
 */
function get_valid_taxonomies() : array {

	$all_taxonomies = get_taxonomies(
		[],
		'objects'
	);

	// Set up options to exclude certain taxonomies.
	$taxonomies_exclude = [
		'nav_menu',
		'link_category',
		'post_format',
	];

	$taxonomies_exclude = apply_filters( 'coil_settings_taxonomy_exclude', $taxonomies_exclude );

	// Store the available taxonomies using the above exclusion options.
	$taxonomy_options = [];
	foreach ( $all_taxonomies as $taxonomy ) {

		if ( ! empty( $taxonomies_exclude ) && in_array( $taxonomy->name, $taxonomies_exclude, true ) ) {
			continue;
		}
		if ( ! in_array( $taxonomy->name, $taxonomy_options, true ) ) {
			$taxonomy_options[] = $taxonomy->name;
		}
	}

	return $taxonomy_options;
}

/**
 * Retrieve the Welcome settings using a key from the welcome
 * settings group (serialized).
 *
 * @param string $setting_id The named key in the wp_options serialized array.
 * @return string
 */
function get_welcome_setting( $setting_id ) {

	$options = get_option( 'coil_welcome_settings_group', [] );

	if ( $setting_id === 'coil_payment_pointer_id' ) {
		return ( ! empty( $options[ $setting_id ] ) ) ? $options[ $setting_id ] : '';
	}

	return '';
}

/**
 * Get whatever settings are stored in the plugin as the default
 * content monetization settings (post, page, cpt etc).
 *
 * @return array Setting stored in options, or blank array.
 */
function get_general_settings() : array {

	$general_settings = get_option( 'coil_general_settings_group', [] );
	if ( ! empty( $general_settings ) ) {
		return $general_settings;
	}

	return [];
}

/**
 * @return array Valid monetization states - Monetized or Not Monetized.
 */
function get_monetization_types() {

	$monetization_types = [
		'monetized'     => 'Monetized',
		'not-monetized' => 'Not Monetized',
	];

	return $monetization_types;
}

/**
 * Returns the single default 'monetized'.
 *
 * @return string
 */
function get_monetization_default() {
	return 'monetized';
}

/**
 * Retrieve the Exclusive Content settings.
 * @return array Setting stored in options.
 */
function get_exclusive_settings(): array {

	// Set up defaults.
	$defaults = [ 'coil_content_container' => '.content-area .entry-content' ];

	$exclusive_options = get_option( 'coil_exclusive_settings_group', [] );
	if ( ! isset( $exclusive_options['coil_content_container'] ) ) {
		$exclusive_options['coil_content_container'] = $defaults['coil_content_container'];
	}

	return $exclusive_options;
}

/**
 * Retrieve the paywall text field defaults
 * This includes the title, message, button text and button link
 *
 * @return array Text field default values
 */
function get_paywall_apprearance_text_defaults() {

	// Set up defaults.
	return [
		'coil_paywall_title'       => __( 'Keep Reading with Coil', 'coil-web-monetization' ),
		'coil_paywall_message'     => __( 'We partnered with Coil to offer exclusive content. Access this and other great content with a Coil membership.', 'coil-web-monetization' ),
		'coil_paywall_button_text' => __( 'Become a Coil Member', 'coil-web-monetization' ),
		'coil_paywall_button_link' => __( 'https://coil.com/', 'coil-web-monetization' ),
	];
}

/**
 * Retrieve the paywall appearance settings
 * using a key from coil_exclusive_settings_group (serialized).
 *
 * @param string $field_id The named key in the wp_options serialized array.
 * @param bool $default set to either return the text field default if the text field item is empty or to return an empty string.
 * @return string
 */
function get_paywall_appearance_setting( $field_id, $use_text_default = false ) {

	$exclusive_options = get_exclusive_settings();

	$text_defaults  = get_paywall_apprearance_text_defaults();
	$style_defaults = get_paywall_appearance_defaults();

	$text_fields = [ 'coil_paywall_title', 'coil_paywall_message', 'coil_paywall_button_text', 'coil_paywall_button_link' ];

	// Text inputs can be empty strings, in which the placeholder text will display or the default text will be returned.
	if ( in_array( $field_id, $text_fields, true ) ) {

		// The default is returned as a placeholder or as a coil_js_ui_messages field when no custom input has been provided
		if ( $use_text_default ) {
			return $text_defaults[ $field_id ];
		} else {
			return ( ! empty( $exclusive_options[ $field_id ] ) ) ? $exclusive_options[ $field_id ] : '';
		}
	} elseif ( $field_id === 'coil_message_color_theme' ) {
		// Default is the light theme
		if ( isset( $exclusive_options[ $field_id ] ) ) {
			$setting_value = $exclusive_options[ $field_id ];
		} else {
			$setting_value = style_defaults[ $field_id ];
		}
		return $setting_value;
	} elseif ( $field_id === 'coil_message_branding' ) {
		// Default is Coil logo
		if ( isset( $exclusive_options[ $field_id ] ) ) {
			$setting_value = $exclusive_options[ $field_id ];
		} else {
			$setting_value = style_defaults[ $field_id ];
		}
		return $setting_value;
	}
	return null;
}

/**
 * @return array Valid colour theme states
 */
function get_theme_color_types() {

	$theme_colors = [ 'light', 'dark' ];

	return $theme_colors;
}

/**
 * @return array Valid branding options.
 */
function get_branding_options() {

	$branding_choices = [ 'site_logo', 'coil_logo', 'no_logo' ];

	return $branding_choices;
}

/**
 * Retrieve the inherited font paywall appearance setting
 * using its key from coil_exclusive_settings_group (serialized).
 * This is a separate function from the rest of the paywall appearance settings because it returns a boolean.
 *
 * @param string $field_id The named key in the wp_options serialized array.
 * @return string
 */
function get_inherited_font_setting( $field_id ) {

	$exclusive_options = get_exclusive_settings();
	if ( $field_id === 'coil_message_font' ) {
		if ( isset( $exclusive_options[ $field_id ] ) ) {
			$setting_value = $exclusive_options[ $field_id ];
		} else {
			$setting_value = false;
		}
		return $setting_value;
	}

	return false;
}

/**
 * Returns the paywall appearance settings for all fields that are not text based.
 * This includes the color theme, branding choice, and font style.
 *
 * @return array
 */
function get_paywall_appearance_defaults(): array {
	$paywall_appearance_defaults = [
		'coil_message_color_theme' => 'light';
		'coil_message_branding' => 'coil_logo';
		'coil_message_font' => false;,
	];

	return $paywall_appearance_defaults;
}

/**
 * Retrieve the exclusive post appearance settings
 * using a key from coil_exclusive_settings_group (serialized).
 *
 * @param string $field_id The named key in the wp_options serialized array.
 * @return bool
 */
function get_exlusive_post_appearance_setting( $field_id ): bool {

	$exclusive_options = get_exclusive_settings();

	if ( $field_id === 'coil_title_padlock' ) {
		// Default is checked
		if ( ! isset( $exclusive_options[ $field_id ] ) ) {
			$setting_value = true;
		} else {
			$setting_value = $exclusive_options[ $field_id ];
		}
		return $setting_value;
	}
	return false;
}

/**
 * Returns the padlock default as true.
 *
 * @return array
 */
function get_exclusive_post_defaults(): array {
	$exclusive_post_defaults[ 'coil_title_padlock' => true ];

	return $exclusive_post_defaults;
}

/**
 * @return array Valid visibility states - Public or Exclusive.
 */
function get_visibility_types() : array {

	$visibility_types = [
		'public'    => 'Public',
		'exclusive' => 'Exclusive',
	];

	return $visibility_types;
}

/**
 * Returns the visibility default which is 'public' for all post types.
 *
 * @return string
 */
function get_post_visibility_default() {

	return 'public';
}

/**
* Returns the default excerpt dialay for all post types which is to not diplay.
*
* @return boolean
*/
function get_excerpt_visibility_default() {

	return false;
}

/**
 * Retrieve the CSS selector setting settings.
 * @param string $field_name
 * @return string Setting stored in options.
 */
function get_css_selector_setting( $field_name ) {

	if ( $field_name === 'coil_content_container' ) {
		$exclusive_options = get_exclusive_settings();
		if ( empty( $exclusive_options ['coil_content_container'] ) ) {
			return '.content-area .entry-content';
		} else {
			return $exclusive_options ['coil_content_container'];
		}
	} else {
		return '';
	}
}
