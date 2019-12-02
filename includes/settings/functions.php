<?php
declare(strict_types=1);
/**
 * Coil settings.
 */

namespace Coil\Settings;

use Coil\Admin;
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
		esc_attr__( 'Coil Settings', 'coil-monetize-content' ),
		_x( 'Coil Settings', 'admin menu name', 'coil-monetize-content' ),
		apply_filters( 'coil_settings_capability', 'manage_options' ),
		'coil_settings',
		__NAMESPACE__ . '\render_coil_settings_screen'
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

	// Tab 1 - Getting Started.
	add_settings_section(
		'coil_getting_started_settings_section',
		false,
		__NAMESPACE__ . '\coil_getting_started_settings_render_callback',
		'coil_getting_started_settings'
	);

	// Tab 2 - Global Settings.
	register_setting(
		'coil_global_settings_group',
		'coil_global_settings_group',
		__NAMESPACE__ . '\coil_global_settings_group_validation'
	);

	// ==== Global Settings.
	add_settings_section(
		'coil_global_settings_top_section',
		__( 'Global Settings', 'coil-monetize-content' ),
		false,
		'coil_global_settings_global'
	);

	add_settings_field(
		'coil_payment_pointer_id',
		__( 'Payment Pointer', 'coil-monetize-content' ),
		__NAMESPACE__ . '\coil_global_settings_payment_pointer_render_callback',
		'coil_global_settings_global',
		'coil_global_settings_top_section'
	);

	// ==== Advanced Config.
	add_settings_section(
		'coil_global_settings_bottom_section',
		__( 'Advanced Config', 'coil-monetize-content' ),
		__NAMESPACE__ . '\coil_global_settings_advanced_config_description_callback',
		'coil_global_settings_advanced'
	);

	add_settings_field(
		'coil_content_container',
		__( 'Post Container ID', 'coil-monetize-content' ),
		__NAMESPACE__ . '\coil_global_settings_advanced_config_render_callback',
		'coil_global_settings_advanced',
		'coil_global_settings_bottom_section'
	);

	// Tab 3 - Content Settings.
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

	// Tab 4 - Excerpt settings.
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

	// Tab 5 - Message settings.
	add_settings_section(
		'coil_message_settings_section',
		false,
		__NAMESPACE__ . '\coil_message_settings_render_callback',
		'coil_message_settings'
	);

}

/* ------------------------------------------------------------------------ *
 * Section Validation
 * ------------------------------------------------------------------------ */

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
 * Allow the text inputs in the global settings section to
 * be properly validated. These allow the payment pointer
 * to be saved.
 *
 * @param array $global_settings The posted text input fields.
 * @return array
 */
function coil_global_settings_group_validation( $global_settings ) : array {
	if ( ! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ) {
		return [];
	}

	if ( isset( $global_settings['coil_content_container'] ) && empty( $global_settings['coil_content_container'] ) ) {
		$global_settings['coil_content_container'] = '.content-area .entry-content';
	}

	return array_map(
		function( $global_settings_input ) {

			return sanitize_text_field( $global_settings_input );
		},
		(array) $global_settings
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
 * Renders the output of the Getting Started tab.
 *
 * @return void
 */
function coil_getting_started_settings_render_callback() {
	?>
	<p>
		<?php esc_html_e( 'Please watch the video below first to understand more about Web Monetization, Coil, and how to get the most out of this plugin on your site', 'coil-monetize-content' ); ?>
	</p>
	<iframe width="560" height="315" src="https://www.youtube.com/embed/D-_L8qR78vU" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

	<h3><?php esc_html_e( 'How to guides', 'coil-monetize-content' ); ?></h3>
	<ol>
		<li><?php esc_html_e( 'What is Web Monetization, and who are Coil?  [Find out more]', 'coil-monetize-content' ); ?></li>
		<li><?php esc_html_e( 'Should I monetize my content?  [Read the best practices]', 'coil-monetize-content' ); ?></li>
		<li><?php esc_html_e( 'How do I configure and use the Coil WordPress plugin [Find out more]', 'coil-monetize-content' ); ?></li>
		<li><?php esc_html_e( 'Coil WordPress Plugin FAQ [Find out more]', 'coil-monetize-content' ); ?></li>
	</ol>

	<a href="<?php echo esc_url( '?page=coil_settings&tab=global_settings' ); ?>" class="button button-primary button-large"><?php esc_html_e( 'Start configuring the plugin', 'coil-monetize-content' ); ?></a>
	<?php
}

// Render the text field for the payment point in global settings.
function coil_global_settings_payment_pointer_render_callback() {
	printf(
		'<input class="%s" type="%s" name="%s" id="%s" value="%s" placeholder="%s" style="%s" />',
		esc_attr( 'wide-input' ),
		esc_attr( 'text' ),
		esc_attr( 'coil_global_settings_group[coil_payment_pointer_id]' ),
		esc_attr( 'coil_payment_pointer_id' ),
		esc_attr( Admin\get_global_settings( 'coil_payment_pointer_id' ) ),
		esc_attr( '$pay.stronghold.co/0000000000000000000000000000000000000' ),
		esc_attr( 'min-width: 440px' )
	);

	echo '<p class="' . esc_attr( 'description' ) . '">';
	printf(
		'Don\'t have a payment pointer yet? <a href="%s">%s</a>. You can also click the button below to quickly set up a payment pointer with Coil.',
		esc_url( '#' ),
		esc_html( 'Find out more about payment pointers' )
	);
	echo '</p>';
	printf(
		'<br><a href="%s" target="%s" class="%s">%s</a>',
		esc_url( 'https://coil.com/signup' ),
		esc_attr( '_blank' ),
		esc_attr( 'button button-large' ),
		esc_html( 'Create a payment pointer with Coil' )
	);

}

/**
 * Render a short description before the output of the advanced
 * config settings fields.
 *
 * @return void
 */
function coil_global_settings_advanced_config_description_callback() {
	echo '<p class="' . esc_attr( 'description' ) . '">';
	printf(
		'In most themes, you won’t need to use this field and can leave it blank. If the content gating is not working correctly though (<a href="%s">%s</a>) then you may need to find your post content container Id and enter it here (see the <a href="%s">%s</a> to see how to do this. )',
		esc_url( '#' ),
		esc_html( 'see the How To Guides' ),
		esc_url( '#' ),
		esc_html( 'Advanced config guide' )
	);
	echo '</p>';
}

/**
 * Render the advanced config settings fields.
 *
 * @return void
 */
function coil_global_settings_advanced_config_render_callback() {
	printf(
		'<input class="%s" type="%s" name="%s" id="%s" value="%s" placeholder="%s" style="%s" />',
		esc_attr( 'wide-input' ),
		esc_attr( 'text' ),
		esc_attr( 'coil_global_settings_group[coil_content_container]' ),
		esc_attr( 'coil_content_container' ),
		esc_attr( Admin\get_global_settings( 'coil_content_container' ) ),
		esc_attr( '.content-area .entry-content' ),
		esc_attr( 'min-width: 440px' )
	);
}

/**
 * Renders the output of the post settings showing radio buttons
 * based on the post types available in WordPress.
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
		<p><?php esc_html_e( 'You can use the settings below to control how your content is monetized and gated across your whole site easily. These settings are your defaults, that you can then override by configuring monetization against your categories and taxonomies. You can also override the settings against individual pages and posts. Read all about how to configure monetization and gating in the', 'coil-monetize-content' ); ?>
		<a href="#" target="_blank"><?php esc_html_e( 'How To guides', 'coil-monetize-content' ); ?></a>
		</p>
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
 * Renders the output of the excerpt settings showing checkbox
 * inputs based on the post types available in WordPress.
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
		<p><?php esc_html_e( 'You can choose whether a short excerpt shows for any pages or posts you choose to gate access to. You can control this for each of your content types on your website using the options below. Support for displaying an excerpt may depend on your particular theme and setup of WordPress.', 'coil-monetize-content' ); ?></p>
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
 * Renders the output of the message settings.
 *
 * @return void
 */
function coil_message_settings_render_callback() {
	?>
	<p><?php esc_html_e( 'The Coil plugin allows you to edit the different messages your site visitors are shown when trying to access content, depending on how you’ve configured monetization and gating on your pages and posts, and depending on whether they’re using a supported browser and the Coil browser extension.', 'coil-monetize-content' ); ?></p>
	<p><?php esc_html_e( 'Read the How To guides and watch the video to better understand what your visitors need in order to be able to access your content and stream payment to your payment pointer.  Also in the guides is further information on what the different Message Settings are for.', 'coil-monetize-content' ); ?></p>
	<p><?php esc_html_e( 'You can leave these as the default messages, or if you want to set your own messages -  click the button below to be taken to the WordPress Customizer.', 'coil-monetize-content' ); ?></p>
	<a href="#" class="button button-primary button-large"><?php esc_html_e( 'Edit web monetization messages', 'coil-monetize-content' ); ?></a>
	<?php
}

/**
 * Render the Coil submenu setting screen to display options to gate posts
 * and taxonomy content types.
 *
 * @return void
 */
function render_coil_settings_screen() : void {
	?>
	<div class="wrap coil plugin-settings">

		<h1><?php esc_html_e( 'Welcome to the Coil Web Monetization Plugin', 'coil-monetize-content' ); ?></h1>
		<br>

		<?php settings_errors(); ?>

		<?php
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'getting_started';
		?>

		<h2 class="nav-tab-wrapper">
			<a href="<?php echo esc_url( '?page=coil_settings&tab=getting_started' ); ?>" class="nav-tab <?php echo $active_tab === 'getting_started' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Getting Started', 'coil-monetize-content' ); ?></a>
			<a href="<?php echo esc_url( '?page=coil_settings&tab=global_settings' ); ?>" class="nav-tab <?php echo $active_tab === 'global_settings' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Global Settings', 'coil-monetize-content' ); ?></a>
			<a href="<?php echo esc_url( '?page=coil_settings&tab=content_settings' ); ?>" class="nav-tab <?php echo $active_tab === 'content_settings' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Content Settings', 'coil-monetize-content' ); ?></a>
			<a href="<?php echo esc_url( '?page=coil_settings&tab=excerpt_settings' ); ?>" class="nav-tab <?php echo $active_tab === 'excerpt_settings' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Excerpt Settings', 'coil-monetize-content' ); ?></a>
			<a href="<?php echo esc_url( '?page=coil_settings&tab=message_settings' ); ?>" class="nav-tab <?php echo $active_tab === 'message_settings' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Message Settings', 'coil-monetize-content' ); ?></a>
		</h2>
		<br>

		<form action="options.php" method="post">
			<?php
			switch ( $active_tab ) {
				case 'getting_started':
					do_settings_sections( 'coil_getting_started_settings' );
					break;
				case 'global_settings':
					settings_fields( 'coil_global_settings_group' );
					do_settings_sections( 'coil_global_settings_global' );
					do_settings_sections( 'coil_global_settings_advanced' );
					submit_button();
					break;
				case 'content_settings':
					settings_fields( 'coil_content_settings_posts_group' );
					do_settings_sections( 'coil_content_settings_posts' );
					submit_button();
					break;
				case 'excerpt_settings':
					settings_fields( 'coil_content_settings_excerpt_group' );
					do_settings_sections( 'coil_content_settings_excerpts' );
					submit_button();
					break;
				case 'message_settings':
					do_settings_sections( 'coil_message_settings' );
					break;
			}
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
