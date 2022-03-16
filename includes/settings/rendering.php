<?php
declare(strict_types=1);
/**
 * Coil settings rendering helper functions.
 * Creates the basic components that can be used to render elements and tabs in the Coil settings panel
 */

namespace Coil\Rendering;

use Coil;
use const Coil\COIL__FILE__;

/**
 * Renders the heading for each section in the settings panel.
 * @return void
 * @param string $heading
 * @param string $description
*/
function render_settings_section_heading( $heading, $description = '' ) {
	printf(
		'<h3>%1$s</h3>',
		esc_html( $heading )
	);

	if ( $description !== '' ) {
		echo '<p>' . esc_html( $description ) . '</p>';
	}
}

/**
 * Renders the heading for input fields in the settings panel.
 * @return void
 * @param string $heading
 * @param string $id Provided if the heading needs an ID
*/
function render_input_field_heading( $heading, $id = '' ) {
	if ( $id !== '' ) {
		printf(
			'<h4 id="%s"><strong>%s</strong></h4>',
			esc_html( $id ),
			esc_html( $heading )
		);
	} else {
		printf(
			'<h4><strong>%s</strong></h4>',
			esc_html( $heading )
		);
	}
}

/**
 * Sets up a heading, with an explanation and a link to the appropriate tab in the settings panel.
 * @return void
 * @param string $heading
 * @param string $explanation
 * @param string $tab Used to create the link to the appropriate tab if there is one, otherwise a null string.
 * @param string $button_text Text appearing on button (if there is one) that links to a tab.
*/
function render_welcome_section( $heading, $explanation, $tab = '', $button_text = '' ) {
	?>
	<div style="padding-top: 10px;">
			<?php
			echo '<h2>' . esc_html( $heading ) . '</h2>';
			echo '<p>' . esc_html( $explanation ) . '</p>';

			if ( $link !== '' && $button_text !== '' ) {
				printf(
					'<a class="button button-primary" href="%s">%s</a>',
					esc_url( admin_url( 'admin.php?page=coil_settings&tab=' . $tab, COIL__FILE__ ) ),
					esc_html( $button_text )
				);
			}
			?>
		</div>
	<?php
}

/**
 * Creates a text input element.
 * @return void
 * @param string $id
 * @param string $name
 * @param string $value
 * @param string $placeholder
 * @param string $heading
 * @param string $description
*/
function render_text_input_field( $id, $name, $value, $placeholder, $heading = '', $description = '' ) {
	if ( $heading !== '' ) {
		render_input_field_heading( $heading );
	}

	printf(
		'<input class="%s" type="%s" name="%s" id="%s" value="%s" placeholder="%s" />',
		esc_attr( 'wide-input' ),
		esc_attr( 'text' ),
		esc_attr( $name ),
		esc_attr( $id ),
		esc_attr( $value ),
		esc_attr( $placeholder )
	);

	if ( $description !== '' ) {
		printf(
			'<p class="description">%s</p>',
			esc_html( $description )
		);
	}
}

/**
 * Creates a toggle element.
 * @return void
 * @param string $id
 * @param string $name
 * @param string $value
*/
function render_toggle( $id, $name, $value ) {
	if ( $value === true ) {
		$checked_input = 'checked="checked"';
	} else {
		$checked_input = '';
	}
	echo sprintf(
		'<label class="coil-checkbox" for="%1$s"><input type="%2$s" name="%3$s" id="%1$s" %4$s /><span></span><i></i></label>',
		esc_attr( $id ),
		esc_attr( 'checkbox' ),
		esc_attr( $name ),
		$checked_input
	);
}

/**
 * Creates a radio button element.
 * @return void
 * @param string $id
 * @param string $name
 * @param string $value
 * @param string $description
 * @param string $saved_value The value (if any) already stored in the database.
 * @param boolean $default Is this the radio option that should be selected by default?
*/
function render_radio_button_field( $id, $name, $value, $description, $saved_value, $default = false ) {
	$checked = ( ! empty( $saved_value ) && $saved_value === $value ) || ( empty( $saved_value ) && $default ) ? 'checked="checked"' : false;
	printf(
		'<label for="%1$s"><input type="radio" name="%2$s" id="%1$s" value="%3$s" %4$s /> %5$s</label>',
		esc_attr( $id ),
		esc_attr( $name ),
		esc_attr( $value ),
		esc_attr( $checked ),
		esc_html( $description )
	);
}

/**
 * Creates a checkbox element.
 * @return void
 * @param string $id
 * @param string $name
 * @param string $description
 * @param string $saved_value The value (if any) already stored in the database.
*/
function render_checkbox_field( $id, $name, $description, $saved_value ) {
	$checked = ( isset( $saved_value ) && $saved_value === true ) ? 'checked="checked"' : false;
	printf(
		'<label class="%1$s" for="%2$s"><input type="%3$s" name="%4$s" id="%2$s" %5$s /> <strong>%6$s</strong></label>',
		esc_attr( 'coil-clear-left' ),
		esc_attr( $id ),
		esc_attr( 'checkbox' ),
		esc_attr( $name ),
		esc_attr( $checked ),
		esc_html( $description )
	);
}

/**
 * Creates a checkbox element that can be used to hide other elements when it is unchecked.
 * @return void
 * @param string $id
 * @param string $name
 * @param string $description
 * @param string $saved_value The value (if any) already stored in the database.
*/
function render_checkbox_that_toggles_content( $id, $name, $description, $saved_value ) {
	$checked = ( isset( $saved_value ) && $saved_value === true ) ? 'checked="checked"' : false;

	printf(
		'<br><input type="%1$s" name="%2$s" id="%3$s" %4$s>',
		esc_attr( 'checkbox' ),
		esc_attr( $name ),
		esc_attr( $id ),
		esc_attr( $checked )
	);

	printf(
		'<label for="%1$s"> <strong>%2$s</strong></label>',
		esc_attr( $id ),
		esc_html( $description )
	);
}

/**
 * Sets up a table to create radio button / checkbox options for the different post types available.
 * @return void
 * @param string $settings_group Name of options group in the wp_options table
 * @param array $column_names
 * @param string $input_type checkbox or radio.
 * @param array $value_id_suffix The suffix that goes after the post type name to create an id for it.
 * @param array $current_options Settings currently stored in the database
*/
function render_generic_post_type_table( $settings_group, $column_names, $input_type, $value_id_suffix, $current_options ) {
	$post_type_options = Coil\get_supported_post_types( 'objects' );

	// If there are post types available, output them:
	if ( ! empty( $post_type_options ) ) {
		// Get the values behind the column names
		$keys = array_keys( $column_names );

		?>
		<table class="widefat" style="border-radius: 4px;">
			<thead>
				<th><?php esc_html_e( 'Post Type', 'coil-web-monetization' ); ?></th>
				<?php foreach ( $column_names as $setting_key => $setting_value ) : ?>
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
						foreach ( $column_names as $setting_key => $setting_value ) :
							if ( $input_type === 'checkbox' ) {
								$input_id = $post_type->name . '_' . $value_id_suffix;
							} else {
								$input_id = $post_type->name . '_' . $value_id_suffix . '_' . $setting_key;
							}
							$input_name = $settings_group . '[' . $post_type->name . '_' . $value_id_suffix . ']';

							/**
							 * The default checked state is the first option on the input from.
							 */
							$checked_input = false;
							if ( $input_type === 'radio' && $setting_key === $keys[0] ) {
								$checked_input = 'checked="true"';
							} elseif ( $input_type === 'radio' && isset( $current_options[ $post_type->name . '_' . $value_id_suffix ] ) ) {
								$checked_input = checked( $setting_key, $current_options[ $post_type->name . '_' . $value_id_suffix ], false );
							} elseif ( $input_type === 'checkbox' ) {
								if ( isset( $current_options[ $post_type->name . '_' . $value_id_suffix ] ) && $current_options[ $post_type->name . '_' . $value_id_suffix ] === true ) {
									$checked_input = 'checked="true"';
								} else {
									$checked_input = '';
								}
							}
							?>
							<td>
								<?php
								if ( $input_type === 'checkbox' ) {
									printf(
										'<input type="%s" name="%s" id="%s" %s />',
										esc_attr( $input_type ),
										esc_attr( $input_name ),
										esc_attr( $input_id ),
										$checked_input
									);
								} else {
									printf(
										'<input type="%s" name="%s" id="%s" value="%s"%s />',
										esc_attr( $input_type ),
										esc_attr( $input_name ),
										esc_attr( $input_id ),
										esc_attr( $setting_key ),
										$checked_input
									);
								}
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
