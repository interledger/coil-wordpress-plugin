<?php
declare(strict_types=1);
/**
 * Coil admin screens and options.
 */

namespace Coil\Admin;

use Coil;
use Coil\Gating;
use const Coil\PLUGIN_VERSION;

/**
 * Coil Customizer panel ID.
 *
 * @var string
 */
const CUSTOMIZER_PANEL_ID = 'coil_customizer_settings_panel';

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
 * @param int $term_id Term ID.
 * @return void
 */
function maybe_save_term_meta( int $term_id ) : void {

	if ( ! current_user_can( 'edit_post', $term_id ) || empty( $_REQUEST['term_gating_nonce'] ) ) {
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
		'coil_admin_params',
		$admin_params
	);

	// Enqueue localized script.
	wp_enqueue_script( 'coil_admin_notices' );
}

/**
 * Get a text field saved in the customizer. If no text is set for the field,
 * a default value is returned.
 *
 * @param string $field_id The id of the control_setting text field defined in the customizer.
 * @param bool $get_default If true, will output the default value instead of getting the customizer setting.
 * @return string
 */
function get_customizer_text_field( $field_id, $get_default = false ) : string {
	// Set up defaults.
	$defaults = [
		'coil_unsupported_message'             => __( 'Not using supported browser and extension, this is how to access / get COIL', 'coil-web-monetization' ),
		'coil_unable_to_verify_message'        => __( 'You need a valid Coil account in order to see content, here\'s how..', 'coil-web-monetization' ),
		'coil_voluntary_donation_message'      => __( 'This site is monetized using Coil.  We ask for your help to pay for our time in creating this content for you.  Here\'s how...', 'coil-web-monetization' ),
		'coil_verifying_status_message'        => __( 'Verifying Web Monetization status. Please wait...', 'coil-web-monetization' ),
		'coil_partial_gating_message'          => __( 'This content is for Coil members only. To access, join Coil and install the browser extension.', 'coil-web-monetization' ),
		'coil_fully_gated_excerpt_message'     => __( 'The content in this article is for members only!', 'coil-web-monetization' ),
		'coil_partially_gated_excerpt_message' => __( 'This article is monetized and some content is for members only.', 'coil-web-monetization' ),
		'coil_learn_more_button_text'          => __( 'Get Coil to access', 'coil-web-monetization' ),
		'coil_learn_more_button_link'          => 'https://coil.com/learn-more/',
	];

	// Get the field from the customizer.
	$customizer_setting = get_theme_mod( $field_id );

	/**
	 * If an empty string is saved in the customizer,
	 * get_theme_mod returns an empty string instead of the default
	 * setting whcih is defined as an optional second parameter.
	 * This is recognized as a bug (wontfix) in WordPress Core.
	 *
	 * @see https://core.trac.wordpress.org/ticket/28637
	 */
	if ( true === $get_default || empty( $customizer_setting ) || false === $customizer_setting ) {
		$customizer_setting = isset( $defaults[ $field_id ] ) ? $defaults[ $field_id ] : '';
	}

	return $customizer_setting;
}

/**
 * Add Coil messaging panel to the Customizer.
 *
 * @param \WP_Customize_Manager $wp_customize WordPress Customizer object.
 */
function add_customizer_messaging_panel( $wp_customize ) : void {

	$wp_customize->add_panel(
		CUSTOMIZER_PANEL_ID,
		[
			'title'      => __( 'Coil Web Monetization', 'coil-web-monetization' ),
			'capability' => apply_filters( 'coil_settings_capability', 'manage_options' ),
		]
	);

	// Messaging section.
	$messaging_section_id = 'coil_customizer_section_messaging';

	$wp_customize->add_section(
		$messaging_section_id,
		[
			'title' => __( 'Messages', 'coil-web-monetization' ),
			'panel' => CUSTOMIZER_PANEL_ID,
		]
	);

	// Incorrect browser setup message (textarea 1).
	$incorrect_browser_setup_message_id = 'coil_unsupported_message';

	$wp_customize->add_setting(
		$incorrect_browser_setup_message_id,
		[
			'capability'        => apply_filters( 'coil_settings_capability', 'manage_options' ),
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		]
	);

	$wp_customize->add_control(
		$incorrect_browser_setup_message_id,
		[
			'type'        => 'textarea',
			'label'       => __( 'Incorrect browser setup message', 'coil-web-monetization' ),
			'section'     => $messaging_section_id,
			'description' => __( 'This message is shown when content is set to be members-only, and visitor either isn\'t using a supported browser, or doesn\'t have the browser extension installed correctly.', 'coil-web-monetization' ),
			'input_attrs' => [
				'placeholder' => get_customizer_text_field( $incorrect_browser_setup_message_id, true ),
			],
		]
	);

	// Invalid Web Monetization message (textarea 2).
	$invalid_web_monetization_message_id = 'coil_unable_to_verify_message';

	$wp_customize->add_setting(
		$invalid_web_monetization_message_id,
		[
			'capability'        => apply_filters( 'coil_settings_capability', 'manage_options' ),
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		]
	);

	$wp_customize->add_control(
		$invalid_web_monetization_message_id,
		[
			'type'        => 'textarea',
			'label'       => __( 'Invalid Web Monetization message', 'coil-web-monetization' ),
			'section'     => $messaging_section_id,
			'description' => __( 'This message is shown when content is set to be members-only, browser setup is correct, but Web Monetization doesn\'t start.  It might be due to several reasons, including not having an active Coil account.', 'coil-web-monetization' ),
			'input_attrs' => [
				'placeholder' => get_customizer_text_field( $invalid_web_monetization_message_id, true ),
			],
		]
	);

	// Voluntary donation message (textarea 3).
	$voluntary_donation_message_id = 'coil_voluntary_donation_message';

	$wp_customize->add_setting(
		$voluntary_donation_message_id,
		[
			'capability'        => apply_filters( 'coil_settings_capability', 'manage_options' ),
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		]
	);

	$wp_customize->add_control(
		$voluntary_donation_message_id,
		[
			'type'        => 'textarea',
			'label'       => __( 'Voluntary donation message', 'coil-web-monetization' ),
			'section'     => $messaging_section_id,
			'description' => __( 'This message is shown when content is set to "Monetized and Public" and visitor does not have Web Monetization in place and active in their browser.', 'coil-web-monetization' ),
			'input_attrs' => [
				'placeholder' => get_customizer_text_field( $voluntary_donation_message_id, true ),
			],
		]
	);

	// Pending message (textarea 4).
	$pending_message_id = 'coil_verifying_status_message';

	$wp_customize->add_setting(
		$pending_message_id,
		[
			'capability'        => apply_filters( 'coil_settings_capability', 'manage_options' ),
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		]
	);

	$wp_customize->add_control(
		$pending_message_id,
		[
			'type'        => 'textarea',
			'label'       => __( 'Pending message', 'coil-web-monetization' ),
			'section'     => $messaging_section_id,
			'description' => __( 'This message is shown for a short time time only while check is made on browser setup and that an active Web Monetization account is in place.', 'coil-web-monetization' ),
			'input_attrs' => [
				'placeholder' => get_customizer_text_field( $pending_message_id, true ),
			],
		]
	);

	// Partial gating message (textarea 5).
	$partial_message_id = 'coil_partial_gating_message';

	$wp_customize->add_setting(
		$partial_message_id,
		[
			'capability'        => apply_filters( 'coil_settings_capability', 'manage_options' ),
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		]
	);

	$wp_customize->add_control(
		$partial_message_id,
		[
			'type'        => 'textarea',
			'label'       => __( 'Partial content gating message', 'coil-web-monetization' ),
			'section'     => $messaging_section_id,
			'description' => __( 'This message is shown in footer bar on pages where only some of the content blocks have been set as Members-Only.', 'coil-web-monetization' ),
			'input_attrs' => [
				'placeholder' => get_customizer_text_field( $partial_message_id, true ),
			],
		]
	);

	// Fully gated excerpt message (textarea 6).
	$fully_gated_excerpt_message_id = 'coil_fully_gated_excerpt_message';

	$wp_customize->add_setting(
		$fully_gated_excerpt_message_id,
		[
			'capability'        => apply_filters( 'coil_settings_capability', 'manage_options' ),
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		]
	);

	$wp_customize->add_control(
		$fully_gated_excerpt_message_id,
		[
			'type'        => 'textarea',
			'label'       => __( 'Fully gated excerpt message', 'coil-web-monetization' ),
			'section'     => $messaging_section_id,
			'description' => __( 'This message replaces the article excerpt on archive pages and blog feeds where the whole article has been set as Members-Only.', 'coil-web-monetization' ),
			'input_attrs' => [
				'placeholder' => get_customizer_text_field( $fully_gated_excerpt_message_id, true ),
			],
		]
	);

	// Partially gated excerpt message (textarea 6).
	$partially_gated_excerpt_message_id = 'coil_partially_gated_excerpt_message';

	$wp_customize->add_setting(
		$partially_gated_excerpt_message_id,
		[
			'capability'        => apply_filters( 'coil_settings_capability', 'manage_options' ),
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		]
	);

	$wp_customize->add_control(
		$partially_gated_excerpt_message_id,
		[
			'type'        => 'textarea',
			'label'       => __( 'Partially gated excerpt message', 'coil-web-monetization' ),
			'section'     => $messaging_section_id,
			'description' => __( 'This message replaces the article excerpt on archive pages and blog feeds where parts of the article have been set as Members-Only.', 'coil-web-monetization' ),
			'input_attrs' => [
				'placeholder' => get_customizer_text_field( $partially_gated_excerpt_message_id, true ),
			],
		]
	);
}

/**
 * Add Coil options panel to the Customizer.
 *
 * @param \WP_Customize_Manager $wp_customize WordPress Customizer object.
 */
function add_customizer_options_panel( $wp_customize ) : void {

	// Options section.
	$options_section_id = 'coil_customizer_section_options';

	$wp_customize->add_section(
		$options_section_id,
		[
			'title' => _x( 'Options', 'page title', 'coil-web-monetization' ),
			'panel' => CUSTOMIZER_PANEL_ID,
		]
	);

	// Post title padlock.
	$padlock_setting_id = 'coil_title_padlock';

	$wp_customize->add_setting(
		$padlock_setting_id,
		[
			'capability' => apply_filters( 'coil_settings_capability', 'manage_options' ),
			'default'    => true,
		]
	);

	$wp_customize->add_control(
		$padlock_setting_id,
		[
			'label'   => __( 'Show padlock next to post title if the post is monetized and gated.', 'coil-web-monetization' ),
			'section' => $options_section_id,
			'type'    => 'checkbox',
		]
	);

	// Show donation bar.
	$donation_bar_setting_id = 'coil_show_donation_bar';

	$wp_customize->add_setting(
		$donation_bar_setting_id,
		[
			'capability' => apply_filters( 'coil_settings_capability', 'manage_options' ),
			'default'    => true,
		]
	);

	$wp_customize->add_control(
		$donation_bar_setting_id,
		[
			'label'   => __( 'Show a donation bar on posts that are monetized and public.', 'coil-web-monetization' ),
			'section' => $options_section_id,
			'type'    => 'checkbox',
		]
	);
}


/**
 * Add "Learn More" button settings panel to the Customizer.
 *
 * @param \WP_Customize_Manager $wp_customize WordPress Customizer object.
 */
function add_customizer_learn_more_button_settings_panel( $wp_customize ) : void {

	// Options section.
	$button_settings_section_id = 'coil_customizer_section_button_settings';

	$wp_customize->add_section(
		$button_settings_section_id,
		[
			'title' => __( 'Learn more button', 'coil-web-monetization' ),
			'panel' => CUSTOMIZER_PANEL_ID,
		]
	);

	// Post title padlock.
	$button_text_setting_id = 'coil_learn_more_button_text';

	$wp_customize->add_setting(
		$button_text_setting_id,
		[
			'capability'        => apply_filters( 'coil_settings_capability', 'manage_options' ),
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		]
	);

	$wp_customize->add_control(
		$button_text_setting_id,
		[
			'label'       => __( 'Text used for the "Learn more" button, which is shown to non-members on members-only content.', 'coil-web-monetization' ),
			'section'     => $button_settings_section_id,
			'type'        => 'text',
			'input_attrs' => [
				'placeholder' => get_customizer_text_field( $button_text_setting_id, true ),
			],
		]
	);

	// Show donation bar.
	$button_link_setting_id = 'coil_learn_more_button_link';

	$wp_customize->add_setting(
		$button_link_setting_id,
		[
			'capability'        => apply_filters( 'coil_settings_capability', 'manage_options' ),
			'sanitize_callback' => 'esc_url_raw',
		]
	);

	$wp_customize->add_control(
		$button_link_setting_id,
		[
			'label'       => __( 'Link/URL used for the "Learn more" button, which is shown to non-members on members-only content.', 'coil-web-monetization' ),
			'section'     => $button_settings_section_id,
			'type'        => 'url',
			'input_attrs' => [
				'placeholder' => get_customizer_text_field( $button_link_setting_id, true ),
			],
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
 * Retrieve the global content settings using a key from the global
 * settings group (serialized).
 *
 * @param string $setting_id The named key in the wp_options serialized array.
 * @return string
 */
function get_global_settings( $setting_id ) {
	$coil_global_settings_group_options = get_option( 'coil_global_settings_group' );
	if ( empty( $coil_global_settings_group_options ) ) {
		return '';
	}

	switch ( $setting_id ) {
		case 'coil_payment_pointer_id':
			return ( isset( $coil_global_settings_group_options['coil_payment_pointer_id'] ) )
			? $coil_global_settings_group_options['coil_payment_pointer_id']
			: '';
			break;
		case 'coil_content_container':
			return ( isset( $coil_global_settings_group_options['coil_content_container'] ) )
			? $coil_global_settings_group_options['coil_content_container']
			: '';
	}
}
