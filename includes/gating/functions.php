<?php
declare(strict_types=1);
/**
 * Coil gating.
 */

namespace Coil\Gating;

use Coil\Admin;

/**
 * Register post/user meta.
 */
function register_content_meta() : void {

	register_meta(
		'post',
		'_coil_monetization_post_status',
		[
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			},
			'show_in_rest'  => true,
			'single'        => true,
			'type'          => 'string',
		]
	);

	register_meta(
		'post',
		'_coil_visibility_post_status',
		[
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			},
			'show_in_rest'  => true,
			'single'        => true,
			'type'          => 'string',
		]
	);
}

/**
 * Register term meta.
 *
 * @return void
 */
function register_term_meta() {

	register_meta(
		'term',
		'_coil_monetize_term_status',
		[
			'auth_callback' => function() {

				return current_user_can( 'edit_posts' );
			},
			'show_in_rest'  => true,
			'single'        => true,
			'type'          => 'string',
		]
	);
}

/**
 * Store the monetization options.
 *
 * @param bool $show_default Whether or not to show the default option.
 * @return array
 */
function get_monetization_setting_types( $show_default = false ) : array {

	$settings = [];

	if ( true === $show_default ) {
		$settings['default'] = esc_html__( 'Use Default', 'coil-web-monetization' );
	}

	$settings['no']        = esc_html__( 'Disabled', 'coil-web-monetization' );
	$settings['no-gating'] = esc_html__( 'Enabled & Public', 'coil-web-monetization' );
	$settings['gate-all']  = esc_html__( 'Enabled & Exclusive', 'coil-web-monetization' );

	return $settings;
}

/**
 * Declare all the valid monetization slugs used as a reference
 * before the particular option is saved in the database.
 *
 * @return array An array of valid monetization slug types.
 */
function get_valid_monetization_types() {

	$valid = [
		'monetized', // Monetization is enabled.
		'not-monetized', // Monetization is disabled.
		'default', // Whatever is set on the post to revert back.
	];
	return $valid;
}

/**
 * Declare all the valid visibility slugs used as a reference
 * before the particular option is saved in the database.
 *
 * @return array An array of valid visibility slug types.
 */
function get_valid_visibility_types() {

	$valid = [
		'public', // visable to everyone.
		'exclusive', // visable to Coil members only.
		'split', // split content.
		'default', // Whatever is set on the post to revert back.
	];
	return $valid;
}

/**
 * Maybe prefix a padlock emoji to the post title.
 *
 * Used on archive pages to represent if a post is gated.
 *
 * @param string $title The post title.
 * @param int    $id    The post ID. Optional.
 *
 * @return string The updated post title.
 */
function maybe_add_padlock_to_title( string $title, int $id = 0 ) : string {

	// If no explicit post ID passed, try to grab implicit one
	$id = ( empty( $id ) ? get_the_ID() : $id );

	// No post ID found. Assume no padlock.
	if ( empty( $id ) ) {
		return $title;
	}

	if ( ! Admin\get_exlusive_post_setting( 'coil_title_padlock' ) ) {
		return $title;
	}

	$status = get_content_gating( $id );
	if ( $status !== 'gate-all' ) {
		return $title;
	}

	$post_title = sprintf(
		/* translators: %s: Gated post title. */
		__( '🔒 %s', 'coil-web-monetization' ),
		$title
	);

	return apply_filters( 'coil_maybe_add_padlock_to_title', $post_title, $title, $id );
}

/**
 * Maybe restrict (gate) visibility of the post content on archive pages, home pages, and feeds.
 * If the post is gated then no excerpt will show unless one has been set explicitly.
 *
 * @param string $content Post content.
 *
 * @return string $content Updated post content.
 */
function maybe_restrict_content( string $content ) : string {

	// Plugins can call the `the_content` filter outside of the post loop.
	if ( is_singular() || ! get_the_ID() ) {
		return $content;
	}

	$coil_status     = get_content_gating( get_the_ID() );
	$post_obj        = get_post( get_the_ID() );
	$content_excerpt = $post_obj->post_excerpt;
	$public_content  = '';

	switch ( $coil_status ) {
		case 'gate-all':
		case 'gate-tagged-blocks':
			// Restrict all / some excerpt content based on gating settings.
			if ( get_excerpt_gating( get_queried_object_id() ) ) {
				$public_content .= sprintf(
					'<p>%s</p>',
					$content_excerpt
				);
			}
			break;

		/**
		 * case 'default': doesn't exist in this context because the last possible
		 * saved option will be 'no', after a post has fallen back to the taxonomy
		 * and then the default post options.
		 */
		case 'no':
		case 'no-gating':
		default:
			$public_content = $content;
			break;
	}

	return apply_filters( 'coil_maybe_restrict_content', $public_content, $content, $coil_status );
}

// TODO: remove
/**
 * Get the gating type for the specified post.
 *
 * @param integer $post_id The post to check.
 *
 * @return string Either "no-gating" (default), "no-gating", "gate-all", "gate-tagged-blocks".
 */
function get_post_gating( $post_id ) : string {

	$post_id = (int) $post_id;
	$gating  = get_post_meta( $post_id, '_coil_monetize_post_status', true );

	if ( empty( $gating ) ) {
		$gating = 'default';
	}

	return $gating;
}

/**
 * Get the value of the "Display Excerpt" setting for this post .
 *
 * @param integer $post_id The post to check.
 * @return bool true show excerpt, false hide excerpt.
 */
function get_excerpt_gating( $post_id ) : bool {

	$post_id   = (int) $post_id;
	$post_type = get_post_type( $post_id );

	$display_excerpt   = false;
	$exclusive_options = Admin\get_exclusive_settings();
	if ( ! empty( $exclusive_options ) && isset( $exclusive_options[ $post_type . '_excerpt' ] ) ) {
		$display_excerpt = $exclusive_options[ $post_type . '_excerpt' ];
	}
	return $display_excerpt;
}


/**
 * Get the gating type for the specified term.
 *
 * @param integer $term_id The term_id to check.
 *
 * @return string Either "default" (default), "no", "no-gating", "gate-all".
 */
function get_term_gating( $term_id ) {

	$term_id     = (int) $term_id;
	$term_gating = get_term_meta( $term_id, '_coil_monetize_term_status', true );

	if ( empty( $term_gating ) ) {
		$term_gating = 'default';
	}
	return $term_gating;
}

/**
 * Get any terms attached to the post and return their gating status,
 * ranked by priority order.
 *
 * @return string Gating type.
 */
function get_taxonomy_term_gating( $post_id ) {

	$post_id      = (int) $post_id;
	$term_default = 'default';

	$valid_taxonomies = Admin\get_valid_taxonomies();

	// 1) Get any terms assigned to the post.
	$post_terms = wp_get_post_terms(
		$post_id,
		$valid_taxonomies,
		[
			'fields' => 'ids',
		]
	);

	// 2) Do these terms have gating?
	$term_gating_options = [];
	if ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) {

		foreach ( $post_terms as $term_id ) {

			$post_term_gating = get_term_gating( $term_id );
			if ( ! in_array( $post_term_gating, $term_gating_options, true ) ) {
				$term_gating_options[] = $post_term_gating;
			}
		}
	}

	if ( empty( $term_gating_options ) ) {
		return $term_default;
	}

	// 3) If terms have gating, rank by priority.
	if ( in_array( 'gate-all', $term_gating_options, true ) ) {

		// Priority 1 - Monetization is enabled and visable to Coil members only.
		return 'gate-all';

	} elseif ( in_array( 'no-gating', $term_gating_options, true ) ) {

		// Priority 2 - Monetization is enabled and visable to everyone.
		return 'no-gating';

	} elseif ( in_array( 'no', $term_gating_options, true ) ) {

		// Priority 3 - Monetization is disabled.
		return 'no';

	} else {
		return $term_default;
	}
}

/**
 * Return the single source of truth for post gating based on the fallback
 * options if the post gating selection is 'default'. E.g.
 * If return value of each function is default, move onto the next function,
 * otherwise return immediately.
 *
 * @param integer $post_id
 * @return string $content_gating Gating slug type.
 */
function get_content_gating( $post_id ) : string {

	$post_id     = (int) $post_id;
	$post_gating = get_post_gating( $post_id );

	// Set a default monetization value.
	$content_gating = 'no-gating';

	// Hierarchy 1 - Check what is set on the post.
	if ( 'default' !== $post_gating ) {

		$content_gating = $post_gating; // Honour what is set on the post.

	} else {

		// Hierarchy 2 - Check what is set on the taxonomy.
		$taxonomy_gating = get_taxonomy_term_gating( $post_id );
		if ( 'default' !== $taxonomy_gating ) {

			$content_gating = $taxonomy_gating; // Honour what is set on the taxonomy.

		} else {

			// Hierarchy 3 - Check what is set in the global default.
			// Get the post type for this post to check against what is set for default.
			$post = get_post( $post_id );

			// Get the post type from what is saved in global options
			$global_gating_settings = Admin\get_exclusive_settings();

			if ( ! empty( $global_gating_settings ) && ! empty( $post ) && isset( $global_gating_settings[ $post->post_type ] ) ) {
				$content_gating = $global_gating_settings[ $post->post_type ];
			}
		}
	}

	return $content_gating;
}

// TODO: remove
/**
 * Set the gating type for the specified post.
 *
 * @param integer $post_id    The post to set gating for.
 * @param string $gating_type Either "default", "no", "no-gating", "gate-all", "gate-tagged-blocks".
 *
 * @return void
 */
function set_post_gating( $post_id, string $gating_type ) : void {

	$post_id = (int) $post_id;

	$valid_gating_types = get_valid_monetization_types();
	if ( ! in_array( $gating_type, $valid_gating_types, true ) ) {
		return;
	}

	update_post_meta( $post_id, '_coil_monetize_post_status', $gating_type );
}

/**
 * Set the monetization type for the specified term.
 *
 * @param integer $term_id    The term to set monetization for.
 * @param string $monetization_setting Either "default", "not-monetized", or "monetized".
 *
 * @return void
 */
function set_term_monetization( $term_id, string $monetization_setting ) : void {

	$term_id = (int) $term_id;

	$valid_monetization_types = get_valid_monetization_types();
	if ( ! in_array( $monetization_setting, $valid_monetization_types, true ) ) {
		return;
	}

	update_term_meta( $term_id, '_coil_monetization_term_status', $monetization_setting );
}

/**
 * Set the visibility type for the specified term.
 *
 * @param integer $term_id    The term to set visibility for.
 * @param string $visibility_setting Either "default", "public", or "exclusive".
 *
 * @return void
 */
function set_term_visibility( $term_id, string $visibility_setting ) : void {

	$term_id = (int) $term_id;

	$valid_visibility_types = get_valid_visibility_types();
	if ( ! in_array( $visibility_setting, $valid_visibility_types, true ) ) {
		return;
	}

	update_term_meta( $term_id, '_coil_visibility_term_status', $visibility_setting );
}

/**
 * New function to determine if the content is monetized
 * based on the output of get_content_gating.
 *
 * @param int $post_id
 * @return boolean
 */
function is_content_monetized( $post_id ) : bool {

	$coil_status = get_content_gating( $post_id );
	return ( $coil_status === 'no' ) ? false : true;
}

function is_monetization_and_visibility_compatible() {

	if ( get_monetization_setting() === 'not-monetized' && get_visibility_setting() === 'exclusive' ) {
		return false;
	}
	return true;

}

/**
 * Get the monetization type for the specified post.
 *
 * @param integer $post_id The post to check.
 *
 * @return string Either "monetized" (default) or "not-monetized".
 */
function get_post_monetization( $post_id ) : string {

	$post_id      = (int) $post_id;
	$monetization = get_post_meta( $post_id, '_coil_monetization_post_status', true );

	if ( empty( $monetization ) ) {
		$monetization = 'default';
	}

	return $monetization;
}

/**
 * Get the visibility type for the specified post.
 *
 * @param integer $post_id The post to check.
 *
 * @return string Either "public" (default) or "exclusive".
 */
function get_post_visibility( $post_id ) : string {

	$post_id    = (int) $post_id;
	$visibility = get_post_meta( $post_id, '_coil_visibility_post_status', true );

	if ( empty( $visibility ) ) {
		$visibility = 'default';
	}

	return $visibility;
}

/**
 * Set the monetization status for the specified post.
 *
 * @param integer $post_id    The post to set gating for.
 * @param string $post_monetization Either "default", "not-monetized", or "monetized".
 *
 * @return void
 */
function set_post_monetization( $post_id, string $post_monetization ) : void {

	$post_id = (int) $post_id;

	$valid_monetization_options = Admin\get_monetization_types();
	if ( ! in_array( $post_monetization, $valid_monetization_options, true ) ) {
		$post_monetization = 'default';
	}

	update_post_meta( $post_id, '_coil_monetization_post_status', $post_monetization );
}

/**
 * Set the visibility status for the specified post.
 *
 * @param integer $post_id    The post to set gating for.
 * @param string $post_visibility Either "default", "public", or "exclusive".
 *
 * @return void
 */
function set_post_visibility( $post_id, string $post_visibility ) : void {

	$post_id = (int) $post_id;

	$valid_visibility_options = array_keys( Admin\get_visibility_types() );
	if ( ! in_array( $post_visibility, $valid_visibility_options, true ) ) {
		$post_visibility = 'default';
	}

	update_post_meta( $post_id, '_coil_visibility_status', $post_visibility );
}

// TODO finish these functions and place in correct files
// function have_padlock() {

// }

// function add_monetization_and_visibility_classes() {

// }

// function restrict_exclusive_content() {

// }

// function render_metaboxes() {
// 	if ( is_monetization_and_visibility_compatible() ) {
// 		set_monetization();
// 		set_visibility();
// 	} else {
// 		// set_monetization to true
// 		// set_visibility to public
// 	}
// }

// function get_monetization_and_visibility_setting_wording( $show_default = false ) : array {

// 	$settings = [ 'Use Default', 'Disabled', 'Enabled & Public', 'Enabled & Exclusive' ];

// 	return $settings;
// }
