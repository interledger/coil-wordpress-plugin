<?php
declare(strict_types=1);
/**
 * Coil settings.
 */

namespace Coil\Settings;

use Coil;
use Coil\Admin;
use Coil\Gating;
use const Coil\COIL__FILE__;

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
		esc_html__( 'Coil', 'coil-web-monetization' ),
		esc_html( _x( 'Coil', 'admin menu name', 'coil-web-monetization' ) ),
		apply_filters( 'coil_settings_capability', 'manage_options' ),
		'coil_settings',
		__NAMESPACE__ . '\render_coil_settings_screen',
		'data:image/svg+xml;base64,' . base64_encode( '<svg height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m10 18c4.4183 0 8-3.5817 8-8 0-4.41828-3.5817-8-8-8-4.41828 0-8 3.58172-8 8 0 4.4183 3.58172 8 8 8zm3.1274-5.5636c-.1986-.4734-.4822-.5848-.6997-.5848-.0778 0-.1556.0156-.2045.0253-.0046.0009-.0089.0018-.0129.0026-.0785.0337-.1576.1036-.2553.19-.2791.2466-.7099.6274-1.7113.6824h-.1607c-1.03998 0-2.00434-.5383-2.49598-1.4014-.22691-.4084-.34036-.8632-.34036-1.318 0-.529.15127-1.06731.46327-1.53137.23636-.36197.69963-.94669 1.53163-1.19728.39709-.12066.74691-.16706 1.04944-.16706.9455 0 1.3804.4919 1.3804.85387 0 .1949-.1229.35268-.3593.38053-.0284.00928-.0473.00928-.0756.00928-.0851 0-.1797-.01856-.2553-.06497-.0284-.01856-.0662-.02784-.104-.02784-.2931 0-.6996.50118-.6996.92812 0 .31556.2269.594.8981.594.1121 0 .2309-.0133.3679-.02864.0249-.00279.0504-.00564.0765-.00849.7375-.10209 1.3709-.62184 1.56-1.29937.0284-.08353.0567-.22275.0567-.40837 0-.42694-.1702-1.08591-.9927-1.68919-.5862-.43621-1.2575-.56615-1.8625-.56615-.62404 0-1.1724.13922-1.4844.24131-1.22909.39909-1.92872 1.13231-2.288 1.68919-.46327.69609-.69963 1.50355-.69963 2.31103 0 .6961.17018 1.3829.52 2.0047.74691 1.318 2.19345 2.1347 3.75343 2.1347.0378 0 .078-.0023.1182-.0046s.0804-.0047.1182-.0047c1.0494-.0557 2.8552-.761 2.8552-1.5406 0-.065-.0189-.1393-.0472-.2042z" fill-rule="evenodd" fill="black"/></svg>' )
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
		__( 'Global Settings', 'coil-web-monetization' ),
		false,
		'coil_global_settings_global'
	);

	add_settings_field(
		'coil_payment_pointer_id',
		__( 'Payment Pointer', 'coil-web-monetization' ),
		__NAMESPACE__ . '\coil_global_settings_payment_pointer_render_callback',
		'coil_global_settings_global',
		'coil_global_settings_top_section'
	);

	// ==== Advanced Config.
	add_settings_section(
		'coil_global_settings_bottom_section',
		__( 'Advanced Config', 'coil-web-monetization' ),
		'\__return_empty_string',
		'coil_global_settings_advanced'
	);

	add_settings_field(
		'coil_content_container',
		__( 'CSS Selectors', 'coil-web-monetization' ),
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

	// Tab 5 - Messaging settings.
	add_settings_section(
		'coil_messaging_settings_section',
		false,
		__NAMESPACE__ . '\coil_messaging_settings_render_callback',
		'coil_messaging_settings'
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
	<h3><?php esc_html_e( 'How-to guides', 'coil-web-monetization' ); ?></h3>
<ul>
		<?php
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( 'https://help.coil.com/for-creators/wordpress-plugin' ),
			esc_html__( 'Configure and use the Coil WordPress plugin', 'coil-web-monetization' )
		);
		?>
		<?php
		printf(
			'<li><a target="_blank" href="%1$s">%2$s</a></li>',
			esc_url( 'https://help.coil.com/' ),
			esc_html__( 'Learn more about Coil and Web Monetization', 'coil-web-monetization' )
		);
		?>
		<?php
		printf(
			'<li><a target="_blank" href="%1$s">%2$s</a></li>',
			esc_url( 'https://help.coil.com/accounts/creator-accounts' ),
			esc_html__( 'Get a free Coil creator account', 'coil-web-monetization' )
		);
		?>
	</ul>

	<a href="<?php echo esc_url( '?page=coil_settings&tab=global_settings' ); ?>" class="button button-primary button-large"><?php esc_html_e( 'Configure the plugin', 'coil-web-monetization' ); ?></a>
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
		esc_attr( '$wallet.example.com/alice' ),
		esc_attr( 'min-width: 440px' )
	);

	echo '<p class="' . esc_attr( 'description' ) . '">';

	$payment_pointer_description = esc_html__( 'Enter the payment pointer assigned by your digital wallet provider. Don\'t have a digital wallet or know your payment pointer? Click the button below.', 'coil-web-monetization' );
	echo $payment_pointer_description . '</p>'; // phpcs:ignore. Output already escaped.

	printf(
		'<br><a href="%s" target="%s" class="%s">%s</a>',
		esc_url( 'https://webmonetization.org/docs/ilp-wallets' ),
		esc_attr( '_blank' ),
		esc_attr( 'button button-large' ),
		esc_html__( 'Learn more about digital wallets and payment pointers', 'coil-web-monetization' )
	);

}

/**
 * Render the advanced config settings fields.
 *
 * @return void
 */
function coil_global_settings_advanced_config_render_callback() {
	printf(
		'<input class="%s" type="%s" name="%s" id="%s" value="%s" placeholder="%s" style="%s" required="required"/>',
		esc_attr( 'wide-input' ),
		esc_attr( 'text' ),
		esc_attr( 'coil_global_settings_group[coil_content_container]' ),
		esc_attr( 'coil_content_container' ),
		esc_attr( Admin\get_global_settings( 'coil_content_container' ) ),
		esc_attr( '.content-area .entry-content' ),
		esc_attr( 'min-width: 440px' )
	);

	echo '<p class="description">';

	printf(
		/* translators: 1) HTML link open tag, 2) HTML link close tag, 3) HTML link open tag, 4) HTML link close tag. */
		esc_html__( 'Enter the CSS selectors used in your theme that could include gated content. Most themes use the pre-filled CSS selectors. (%1$sLearn more%2$s)', 'coil-web-monetization' ),
		sprintf( '<a href="%s" target="_blank">', esc_url( 'https://help.coil.com/for-creators/wordpress-plugin#everyone-no-one-can-see-my-monetized-content-why' ) ),
		'</a>'
	);

	echo '</p>';
}

/**
 * Renders the output of the post settings showing radio buttons
 * based on the post types available in WordPress.
 *
 * @return void
 */
function coil_content_settings_posts_render_callback() {

	$post_type_options = Coil\get_supported_post_types( 'objects' );

	// If there are post types available, output them:
	if ( ! empty( $post_type_options ) ) {

		$form_gating_settings           = Gating\get_monetization_setting_types();
		$content_settings_posts_options = Gating\get_global_posts_gating();

		?>
		<p><?php esc_html_e( 'Use the settings below to control the defaults for how your content is monetized and gated across your whole site. You can override the defaults by configuring monetization against your categories and taxonomies. You can also override the defaults against individual pages and posts.', 'coil-web-monetization' ); ?>
		</p>
		<table class="widefat">
			<thead>
				<th><?php esc_html_e( 'Post Type', 'coil-web-monetization' ); ?></th>
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

	$post_type_options = Coil\get_supported_post_types( 'objects' );

	// If there are post types available, output them:
	if ( ! empty( $post_type_options ) ) {

		$content_settings_excerpt_options = Gating\get_global_excerpt_settings();
		?>
		<p><?php esc_html_e( 'Use the settings below to select whether to show a short excerpt for any pages, posts, or other content types you choose to gate access to. Support for displaying an excerpt may depend on your particular theme and setup of WordPress.', 'coil-web-monetization' ); ?></p>
		<table class="widefat">
			<thead>
				<th><?php esc_html_e( 'Post Type', 'coil-web-monetization' ); ?></th>
				<th><?php esc_html_e( 'Display Excerpt', 'coil-web-monetization' ); ?></th>
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
 * Renders the output of the messaging settings.
 *
 * @return void
 */
function coil_messaging_settings_render_callback() {

	$customizer_link = add_query_arg(
		[
			'autofocus[panel]' => Admin\CUSTOMIZER_PANEL_ID,
			'return'           => add_query_arg(
				[
					'page' => 'coil_settings',
				],
				admin_url( 'admin.php' )
			),
		],
		admin_url( 'customize.php' )
	);
	?>

	<p><?php esc_html_e( 'The Coil plugin allows you to edit the messages your visitors might see when accessing your content. Messages can appear depending on how you’ve monetized and gated your content, whether your visitors are using a supported browser, and whether they have an active Coil account.', 'coil-web-monetization' ); ?></p>
	<p><?php esc_html_e( 'Click the button below to access the message customizer. Each message includes a description of what causes the message to appear.', 'coil-web-monetization' ); ?></p>

	<?php
	printf(
		'<a href="%s" class="%s">%s</a>',
		esc_url( $customizer_link ),
		esc_attr( 'button button-primary button-large' ),
		esc_html__( 'Edit messages', 'coil-web-monetization' )
	);
}

function admin_welcome_notice() {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	if ( $screen->id !== 'toplevel_page_coil_settings' ) {
		return;
	}

	$current_user       = wp_get_current_user();
	$payment_pointer_id = Admin\get_global_settings( 'coil_payment_pointer_id' );
	$notice_dismissed   = get_user_meta( $current_user->ID, 'coil-welcome-notice-dismissed', true );

	if ( $payment_pointer_id || $notice_dismissed === 'true' ) {
		return;
	}

	$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'getting_started';

	if ( $active_tab !== 'getting_started' ) {
		return;
	}
	?>

	<div class="notice is-dismissible coil-welcome-notice">
		<img width="48" height="48" class="coil-welcome-notice__icon" src="<?php echo esc_url( plugins_url( 'assets/images/web-mon-icon.svg', COIL__FILE__ ) ); ?>" alt="<?php esc_attr_e( 'Coil', 'coil-web-monetization' ); ?>" />
		<div class="coil-welcome-notice__content">
			<h3><?php esc_html_e( 'Welcome to Coil Web Monetization for WordPress', 'coil-web-monetization' ); ?></h3>
			<p>
			<?php
			printf(
				/* translators: 1) HTML link open tag, 2) HTML link close tag */
				esc_html__( 'To start using Web Monetization please set up your %1$spayment pointer%2$s.', 'coil-web-monetization' ),
				sprintf( '<a href="%1$s">', esc_url( '?page=coil_settings&tab=global_settings' ) ),
				'</a>'
			);
			?>
			</p>
		</div>
	</div>
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

		<div class="plugin-branding">
			<img width="60" height="25" class="coil-logo" src="<?php echo esc_url( plugins_url( 'assets/images/coil-logo.svg', COIL__FILE__ ) ); ?>" alt="<?php esc_attr_e( 'Coil', 'coil-web-monetization' ); ?>" />
		</div>

		<?php settings_errors(); ?>

		<?php
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'getting_started';
		?>

		<h2 class="nav-tab-wrapper">
			<a href="<?php echo esc_url( '?page=coil_settings&tab=getting_started' ); ?>" id="coil-getting-started" class="nav-tab <?php echo $active_tab === 'getting_started' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Getting Started', 'coil-web-monetization' ); ?></a>
			<a href="<?php echo esc_url( '?page=coil_settings&tab=global_settings' ); ?>" id="coil-global-settings" class="nav-tab <?php echo $active_tab === 'global_settings' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Global Settings', 'coil-web-monetization' ); ?></a>
			<a href="<?php echo esc_url( '?page=coil_settings&tab=content_settings' ); ?>" id="coil-content-settings" class="nav-tab <?php echo $active_tab === 'content_settings' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Content Settings', 'coil-web-monetization' ); ?></a>
			<a href="<?php echo esc_url( '?page=coil_settings&tab=excerpt_settings' ); ?>" id="coil-excerpt-settings" class="nav-tab <?php echo $active_tab === 'excerpt_settings' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Excerpt Settings', 'coil-web-monetization' ); ?></a>
			<a href="<?php echo esc_url( '?page=coil_settings&tab=messaging_settings' ); ?>" id="coil-messaging-settings" class="nav-tab <?php echo $active_tab === 'messaging_settings' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Messaging Settings', 'coil-web-monetization' ); ?></a>
		</h2>

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
				case 'messaging_settings':
					do_settings_sections( 'coil_messaging_settings' );
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
			<label><?php esc_html_e( 'Coil Web Monetization', 'coil-web-monetization' ); ?></label>
		</th>
		<td>
			<fieldset id="coil-category-settings">
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
		<h2><?php esc_html_e( 'Coil Web Monetization', 'coil-web-monetization' ); ?></h2>
		<fieldset id="coil-category-settings">
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

function dismiss_welcome_notice() {
	$current_user = wp_get_current_user();

	// Bail early - no user set (somehow).
	if ( empty( $current_user ) ) {
		return;
	}

	// User meta stored as strings, so use 'true' to avoid data type issues.
	update_user_meta( $current_user->ID, 'coil-welcome-notice-dismissed', 'true' );
}
