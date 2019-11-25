<?php
declare(strict_types=1);
/**
 * Coil settings.
 */

namespace Coil\Settings;

use Coil\Gating;

/* ------------------------------------------------------------------------ *
 * Menu Registration
 * ------------------------------------------------------------------------ */

/**
 * Add Coil settings to the admin navigation menu.
 *
 * @return void
 */
function register_admin_menu() : void {
	add_menu_page(
		esc_attr__( 'Settings for Coil', 'coil-monetize-content' ),
		_x( 'Coil', 'admin menu name', 'coil-monetize-content' ),
		apply_filters( 'coil_settings_capability', 'manage_options' ),
		'coil',
		__NAMESPACE__ . '\render_coil_settings_screen'
	);

	add_submenu_page(
		'coil',
		_x( 'Content Settings', 'admin submenu page title', 'coil-monetize-content' ),
		_x( 'Content Settings', 'admin submenu title', 'coil-monetize-content' ),
		apply_filters( 'coil_settings_capability', 'manage_options' ),
		'coil_content_settings',
		__NAMESPACE__ . '\render_coil_submenu_settings_screen'
	);
}

/* ------------------------------------------------------------------------ *
 * Setting Registration
 * ------------------------------------------------------------------------ */

/**
 * Initialize the theme options page by registering the Sections,
 * Fields, and Settings.
 *
 * @return void
 */
function register_admin_content_settings() {

	// Posts.
	register_setting(
		'coil_content_settings_posts_group',
		'coil_content_settings_posts_group',
		__NAMESPACE__ . '\coil_content_settings_posts_validation'
	);

	add_settings_section(
		'coil_content_settings_posts_section',
		false,
		__NAMESPACE__ . '\coil_content_settings_posts_render_callback',
		'coil_content_settings_posts'
	);

	// Excerpt settings.
	register_setting(
		'coil_content_settings_excerpt_group',
		'coil_content_settings_excerpt_group',
		__NAMESPACE__ . '\coil_content_settings_excerpt_validation'
	);

	add_settings_section(
		'coil_content_settings_excerpts_section',
		false,
		__NAMESPACE__ . '\coil_content_settings_excerpts_render_callback',
		'coil_content_settings_excerpts'
	);

}

/* ------------------------------------------------------------------------ *
 * Section Validation
 * ------------------------------------------------------------------------ */

/**
 * Maybe save the Coil admin settings.
 *
 * @return void
 */
function maybe_save_coil_admin_settings() : void {

	if (
		! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ||
		empty( $_REQUEST['coil_for_wp_settings_nonce'] )
	) {
		return;
	}

	// Check the nonce.
	check_admin_referer( 'coil_for_wp_settings_action', 'coil_for_wp_settings_nonce' );

	$payment_pointer_id = ! empty( $_POST['coil_payment_pointer_id'] ) ? $_POST['coil_payment_pointer_id'] : '';
	$content_container  = ! empty( $_POST['coil_content_container'] ) ? $_POST['coil_content_container'] : '.content-area .entry-content';

	$coil_options = [
		'coil_payment_pointer_id' => sanitize_text_field( $payment_pointer_id ),
		'coil_content_container'  => sanitize_text_field( $content_container ),
	];

	foreach ( $coil_options as $key => $value ) {
		if ( ! empty( $value ) ) {
			update_option( $key, $value );
		} else {
			delete_option( $key );
		}
	}
}

/**
 * Allow the radio button options in the posts content section to
 * be properly validated
 *
 * @param array $post_content_settings The posted radio options from the content settings section.
 * @return array
 */
function coil_content_settings_posts_validation( $post_content_settings ) : array {
	return array_map(
		function( $radio_value ) {
			$valid_choices = array_keys( Gating\get_monetization_setting_types() );
			return ( in_array( $radio_value, $valid_choices, true ) ? sanitize_key( $radio_value ) : 'no' );
		},
		(array) $post_content_settings
	);
}

/**
 * Allow each "Display Excerpt" checkbox in the content setting table to be properly validated
 *
 * @param array $excerpt_content_settings The posted checkbox options from the content settings section.
 * @return array
 */
function coil_content_settings_excerpt_validation( $excerpt_content_settings ) : array {
	return array_map(
		function( $checkbox_value ) {
			return ( isset( $checkbox_value ) ) ? true : false;
		},
		(array) $excerpt_content_settings
	);
}

/**
 * Allow the radio button options in the taxonomies content section to
 * be properly validated
 *
 * @param array $taxonomy_content_settings The posted radio options from the content settings section.
 * @return array
 */
function coil_content_settings_taxonomies_validation( $taxonomy_content_settings ) : array {
	return array_map(
		function( $radio_value ) {
			$valid_choices = array_keys( Gating\get_monetization_setting_types() );
			return ( in_array( $radio_value, $valid_choices, true ) ? sanitize_key( $radio_value ) : 'no' );
		},
		(array) $taxonomy_content_settings
	);
}

/* ------------------------------------------------------------------------ *
 * Settings Rendering
 * ------------------------------------------------------------------------ */

/**
 * Render the Coil setting screen.
 *
 * @return void
 */
function render_coil_settings_screen() : void {
	include_once( dirname( __FILE__ ) . '/temp-html-settings.php' );
}

/**
 * Renders the output of the radio buttons based on the post
 * types available in WordPress.
 *
 * @return void
 */
function coil_content_settings_posts_render_callback() {

	$post_types = get_post_types(
		[],
		'objects'
	);

	// Set up options to exclude certain post types.
	$post_types_exclude = [
		'attachment',
		'revision',
		'nav_menu_item',
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		'user_request',
		'wp_block',
	];

	$exclude = apply_filters( 'coil_settings_content_type_exclude', $post_types_exclude );

	// Store the post type options using the above exclusion options.
	$post_type_options = [];
	foreach ( $post_types as $post_type ) {

		if ( ! empty( $exclude ) && in_array( $post_type->name, $exclude, true ) ) {
			continue;
		}
		$post_type_options[] = $post_type;
	}

	// If there are post types available, output them:
	if ( ! empty( $post_type_options ) ) {

		$form_gating_settings           = Gating\get_monetization_setting_types();
		$content_settings_posts_options = Gating\get_global_posts_gating();

		?>
		<table class="widefat">
			<thead>
				<th><?php esc_html_e( 'Post Type', 'coil-monetize-content' ); ?></th>
				<?php foreach ( $form_gating_settings as $setting_key => $setting_value ) : ?>
					<th class="posts_table_header">
						<?php echo esc_html( $setting_value ); ?>
					</th>
				<?php endforeach; ?>
			</thead>
			<tbody>
				<?php foreach ( $post_type_options as $post_type ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $post_type->label ); ?></th>
						<?php
						foreach ( $form_gating_settings as $setting_key => $setting_value ) :
							$input_id   = $post_type->name . '_' . $setting_key;
							$input_name = 'coil_content_settings_posts_group[' . $post_type->name . ']';

							/**
							 * Specify the default checked state on the input from
							 * any settings stored in the database. If the individual
							 * input status is not set, default to the first radio
							 * option (No Monetization)
							 */
							$checked_input = false;
							if ( $setting_key === 'no' ) {
								$checked_input = 'checked="true"';
							} elseif ( isset( $content_settings_posts_options[ $post_type->name ] ) ) {
								$checked_input = checked( $setting_key, $content_settings_posts_options[ $post_type->name ], false );
							}
							?>
							<td>
								<?php
								printf(
									'<input type="radio" name="%s" id="%s" value="%s"%s />',
									esc_attr( $input_name ),
									esc_attr( $input_id ),
									esc_attr( $setting_key ),
									$checked_input
								);
								?>

							</td>
							<?php
						endforeach;
						?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}

/**
 * Renders the output of the checkbox inputs based on the post
 * types available in WordPress.
 *
 * @return void
 */
function coil_content_settings_excerpts_render_callback() {

	$post_types = get_post_types(
		[],
		'objects'
	);

	// Set up options to exclude certain post types.
	$post_types_exclude = [
		'attachment',
		'revision',
		'nav_menu_item',
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		'user_request',
		'wp_block',
	];

	$exclude = apply_filters( 'coil_settings_content_type_exclude', $post_types_exclude );

	// Store the post type options using the above exclusion options.
	$post_type_options = [];
	foreach ( $post_types as $post_type ) {

		if ( ! empty( $exclude ) && in_array( $post_type->name, $exclude, true ) ) {
			continue;
		}
		$post_type_options[] = $post_type;
	}

	// If there are post types available, output them:
	if ( ! empty( $post_type_options ) ) {

		$content_settings_excerpt_options = Gating\get_global_excerpt_settings();

		?>
		<table class="widefat">
			<thead>
				<th><?php esc_html_e( 'Post Type', 'coil-monetize-content' ); ?></th>
				<th><?php esc_html_e( 'Display Excerpt', 'coil-monetize-content' ); ?></th>
			</thead>
			<tbody>
				<?php foreach ( $post_type_options as $post_type ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $post_type->label ); ?></th>
						<td>
						<?php
						$excerpt_name = 'coil_content_settings_excerpt_group[' . $post_type->name . ']';
						$excerpt_id   = $post_type->name . '_display_excerpt';

						$checked_excerpt = false;
						if ( isset( $content_settings_excerpt_options[ $post_type->name ] ) ) {
							$checked_excerpt = checked( 1, $content_settings_excerpt_options[ $post_type->name ], false );
						}
						printf(
							'<input type="checkbox" name="%s" id="%s" %s />',
							esc_attr( $excerpt_name ),
							esc_attr( $excerpt_id ),
							$checked_excerpt
						);
						?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}

/**
 * Render the Coil submenu setting screen to display options to gate posts
 * and taxonomy content types.
 *
 * @return void
 */
function render_coil_submenu_settings_screen() : void {
	?>
	<div class="wrap coil plugin-settings">

		<h1><?php esc_html_e( 'Default Content Settings', 'coil-monetize-content' ); ?></h1>
		<br>

		<?php settings_errors(); ?>

		<?php
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'posts_settings';
		?>

		<h2 class="nav-tab-wrapper">
			<a href="<?php echo esc_url( '?page=coil_content_settings&tab=posts_settings' ); ?>" class="nav-tab <?php echo $active_tab === 'posts_settings' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Posts', 'coil-monetize-content' ); ?></a>
			<a href="<?php echo esc_url( '?page=coil_content_settings&tab=excerpt_settings' ); ?>" class="nav-tab <?php echo $active_tab === 'excerpt_settings' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Excerpts', 'coil-monetize-content' ); ?></a>
		</h2>

		<form action="options.php" method="post">
			<?php
			if ( 'posts_settings' === $active_tab ) {
				settings_fields( 'coil_content_settings_posts_group' );
				do_settings_sections( 'coil_content_settings_posts' );
			} else {
				settings_fields( 'coil_content_settings_excerpt_group' );
				do_settings_sections( 'coil_content_settings_excerpts' );
			}
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Add a set of gating controls to the "Add Term" screen i.e.
 * when creating a brand new term.
 *
 * @param WP_Term_Object $term
 * @return void
 */
function coil_add_term_custom_meta( $term ) {

	// Get gating options.
	$gating_options = Gating\get_monetization_setting_types( true );
	if ( empty( $gating_options ) || ! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ) {
		return;
	}

	// Retrieve the gating saved on the term.
	$gating = Gating\get_term_gating( $term->term_id );

	?>
	<tr class="form-field">
		<th scope="row">
			<label><?php esc_html_e( 'Web Monetization - Coil', 'coil-monetize-content' ); ?></label>
		</th>
		<td>
			<fieldset>
			<?php
			foreach ( $gating_options as $setting_key => $setting_value ) {

				$checked_input = false;
				if ( $setting_key === 'default' ) {
					$checked_input = 'checked="true"';
				} elseif ( ! empty( $gating ) ) {
					$checked_input = checked( $setting_key, $gating, false );
				}
				?>
				<label for="<?php echo esc_attr( $setting_key ); ?>">
				<?php
				printf(
					'<input type="radio" name="%s" id="%s" value="%s"%s />%s',
					esc_attr( 'coil_monetize_term_status' ),
					esc_attr( $setting_key ),
					esc_attr( $setting_key ),
					$checked_input,
					esc_attr( $setting_value )
				);
				?>
				</label><br>
				<?php
			}
			?>
			</fieldset>
		</td>
	</tr>

	<?php
	wp_nonce_field( 'coil_term_gating_nonce_action', 'term_gating_nonce' );
}

/**
 * Add a set of gating controls to the "Edit Term" screen, i.e.
 * when editing an existing term.
 *
 * @return void
 */
function coil_edit_term_custom_meta() {

	// Get gating options.
	$gating_options = Gating\get_monetization_setting_types( true );
	if ( empty( $gating_options ) || ! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ) {
		return;
	}
	?>
	<div class="form-field">
		<h2><?php esc_html_e( 'Web Monetization - Coil', 'coil-monetize-content' ); ?></h2>
		<fieldset>
		<?php
		foreach ( $gating_options as $setting_key => $setting_value ) {
			$checked_input = false;
			if ( $setting_key === 'default' ) {
				$checked_input = 'checked="true"';
			}
			?>
			<label for="<?php echo esc_attr( $setting_key ); ?>">
			<?php
			printf(
				'<input type="radio" name="%s" id="%s" value="%s"%s />%s',
				esc_attr( 'coil_monetize_term_status' ),
				esc_attr( $setting_key ),
				esc_attr( $setting_key ),
				$checked_input,
				esc_attr( $setting_value )
			);
			?>
			</label>
			<?php
		}
		?>
		<br>
		</fieldset>
	</div>

	<?php
	wp_nonce_field( 'coil_term_gating_nonce_action', 'term_gating_nonce' );
}