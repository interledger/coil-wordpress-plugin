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
		'_coil_monetization_term_status',
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
		'term',
		'_coil_visibility_term_status',
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
		'public', // visible to everyone.
		'exclusive', // visible to Coil members only.
		'gate-tagged-blocks', // split content.
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

	// Do not show the padlock on the menu items
	if ( ! in_the_loop() && ! is_singular() ) {
		return $title;
	}

	if ( ! Admin\get_exlusive_post_setting( 'coil_title_padlock' ) ) {
		return $title;
	}

	$status = get_content_status( $id, 'visibility' );
	if ( $status !== 'exclusive' || ! Admin\is_exclusive_content_enabled() ) {
		return $title;
	}

	$padlock_icon_styles = Admin\get_padlock_icon_styles();
	$padlock_icon        = Admin\get_exlusive_post_setting( 'coil_padlock_icon_style', 'lock' );
	$padlock_location    = Admin\get_exlusive_post_setting( 'coil_padlock_icon_position' );

	if ( $padlock_location === 'after' ) {
		$post_title = sprintf(
			/* translators: %s: Gated post title. */
			__( '%1$s %2$s', 'coil-web-monetization' ),
			$title,
			$padlock_icon_styles[ $padlock_icon ]
		);
	} else {
		$post_title = sprintf(
			/* translators: %s: Gated post title. */
			__( '%1$s %2$s', 'coil-web-monetization' ),
			$padlock_icon_styles[ $padlock_icon ],
			$title
		);
	}

	return apply_filters( 'coil_maybe_add_padlock_to_title', $post_title, $title, $id );
}

/**
 * Maybe restrict (gate) visibility of the post content on archive pages, home pages, and feeds.
 * If the post is exclusive then no excerpt will show unless one has been set explicitly.
 *
 * @param string $content Post content.
 *
 * @return string $content Updated post content.
 */
function maybe_restrict_content( string $content ) : string {

	// Plugins can call the `the_content` filter outside of the post loop.
	if ( ! get_the_ID() ) {
		return $content;
	}

	// If exclusive content has been disabled then nothing will be restricted
	if ( ! Admin\is_exclusive_content_enabled() ) {
		return $content;
	}

	$coil_read_more_string  = '<span class="wp-block-coil-exclusive-content-divider"></span>';
	$coil_visibility_status = get_content_status( get_the_ID(), 'visibility' );
	$post_obj               = get_post( get_the_ID() );
	$content_excerpt        = $post_obj->post_excerpt;
	$public_content         = '';

	// If it's a single post which doesn't have a read more block, just return the content
	if ( is_singular() && ! has_read_more_block( $content ) ) {
		return $content;
	}

	switch ( $coil_visibility_status ) {
		case 'exclusive':
		case 'gate-tagged-blocks':
			// Restrict content beneath the Coil Read More block
			if ( has_read_more_block( $content ) ) {

				$content        = str_replace( $coil_read_more_string, '<div class="coil-restricted-content">', $content );
				$content       .= '</div>';
				$public_content = $content;
				//die(htmlspecialchars($public_content));
			} elseif ( is_excerpt_visible( get_queried_object_id() ) ) { // Restrict all / some excerpt content based on visibility settings.
				$public_content .= sprintf(
					'<p>%s</p>',
					$content_excerpt
				);
			}
			break;

		/**
		 * case 'default': doesn't exist in this context because the last possible
		 * saved option will be 'public', after a post has fallen back to the taxonomy
		 * and then the default post options.
		 */
		case 'public':
		default:
			$public_content = $content;
			break;
	}

	return apply_filters( 'coil_maybe_restrict_content', $public_content, $content, $coil_visibility_status );
}

/**
 * Check whether or not the Coil read more block is present
 *
 * @param string $content Post content.
 *
 * @return bool true if the block is present
 */
function has_read_more_block( $content ) : bool {

	$coil_read_more_string = '<span class="wp-block-coil-exclusive-content-divider"></span>';
	if ( false !== strpos( $content, $coil_read_more_string ) ) {
		return true;
	}

	return false;
}

/**
 * Get the post content until the read more block
 * This is necessary if a user is restricting content up to the Read More block, it will override th excerpt
 *
 * @param string $content Post content.
 *
 * @return string $excerpt Post content until the read more block
 */
function coil_get_excerpt( $content ) : string {

	$coil_read_more_string = '<span class="wp-block-coil-exclusive-content-divider"></span>';

	$content_end = strpos( $content, $coil_read_more_string );

	return substr( $content, 0, $content_end );
}

/**
 * Get the value of the "Display Excerpt" setting for this post .
 *
 * @param integer $post_id The post to check.
 * @return bool true show excerpt, false hide excerpt.
 */
function is_excerpt_visible( $post_id ) : bool {

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
 * Get the Coil status for the specified term.
 *
 * @param integer $term_id The term_id to check.
 * @param string $meta_key { '_coil_monetization_term_status' | '_coil_visibility_term_status' }
 *
 * @return string Either "default" (default), or the applicable status.
 */
function get_term_status( $term_id, $meta_key ) {

	$term_id     = (int) $term_id;
	$term_status = get_term_meta( $term_id, $meta_key, true );

	if ( empty( $term_status ) ) {
		$term_status = 'default';
	}
	return $term_status;
}

/**
 * Get any terms attached to the post and return their highest priority Coil status.
 *
 * @param string $meta_key { '_coil_monetization_term_status' | '_coil_visibility_term_status' }
 * @return string Coil status.
 */
function get_taxonomy_term_status( $post_id, $meta_key ) {

	$post_id           = (int) $post_id;
	$final_term_status = 'default';

	$valid_taxonomies = Admin\get_valid_taxonomies();

	// 1) Get any terms assigned to the post.
	$post_terms = wp_get_post_terms(
		$post_id,
		$valid_taxonomies,
		[
			'fields' => 'ids',
		]
	);

	// 2) Has a Coil status been attached to this taxonomy?
	if ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) {

		// Specifies the highest status possible
		if ( $meta_key === '_coil_monetization_term_status' ) {
			$priority_state = 'monetized';
		} elseif ( $meta_key === '_coil_visibility_term_status' ) {
			$priority_state = 'exclusive';
		} else {
			// Invalid meta key was used.
			// Returns default because then the term status won't be considered in determining the content status.
			return $final_term_status;
		}

		foreach ( $post_terms as $term_id ) {

			$post_term_status = get_term_status( $term_id, $meta_key );
			// If a term is assigned the highest status simply break out of loop and return.
			if ( $post_term_status === $priority_state ) {
				$final_term_status = $post_term_status;
				break;
				// If a term's status has been set then save it - in contrast to it being 'default'.
				// Don't break yet, keep checking for a higher state.
			} elseif ( $post_term_status !== 'default' ) {
				$final_term_status = $post_term_status;
			}
		}
	}

	return $final_term_status;
}

/**
 * Return the post monetization & visibility status
 * based on the global defaults, taxonomies, and post metafields.
 * If return value of each function is default, move onto the next function,
 * otherwise return immediately.
 * Note: even if the content's visibility status is 'exclusive' this can still be disabled on a gloabl level.
 *
 * @param integer $post_id
 * @param string $status_type {'monetization' | 'visibility'}
 * @return string $content_status Coil status slug.
 */
function get_content_status( $post_id, $status_type ) : string {

	$post_id = (int) $post_id;

	// Set a metakey and a default value in case nothing has been set on the post or in the database.
	if ( $status_type === 'monetization' ) {
		$content_status = Admin\get_monetization_default();
		$meta_key       = '_coil_monetization_term_status';
	} elseif ( $status_type === 'visibility' ) {
		$content_status = Admin\get_visibility_default();
		$meta_key       = '_coil_visibility_term_status';
	} else {
		// Return if an unrecognised status type is being used
		return '';
	}

	// Hierarchy 1 - Check what is set on the post.
	$post_status = get_post_status( $post_id, $status_type );

	if ( 'default' !== $post_status ) {

		$content_status = $post_status; // Honour what is set on the post.

	} else {

		// Hierarchy 2 - Check what is set on the taxonomy.
		$term_status = get_taxonomy_term_status( $post_id, $meta_key );

		if ( 'default' !== $term_status ) {

			$content_status = $term_status; // Honour what is set on the taxonomy.

		} else {

			// Hierarchy 3 - Check what is set in the global default.
			// Get the post type for this post to check against what is set for default.
			$post = get_post( $post_id );

			if ( $status_type === 'monetization' ) {
				// Get the post type from what is saved in the general options
				$settings = Admin\get_general_settings();
			} elseif ( $status_type === 'visibility' ) {
				// Get the post type from what is saved in the exclusive options
				$settings = Admin\get_exclusive_settings();
			}
			if ( ! empty( $settings ) && ! empty( $post ) && isset( $settings[ $post->post_type . '_' . $status_type ] ) ) {
				$content_status = $settings[ $post->post_type . '_' . $status_type ];
			}
		}
	}

	return $content_status;
}

/**
 * Set the Coil status for the specified term.
 * If the status that is passed in is invalid a default will be used in its place.
 * It's better to pass in a safe default than risk an incompatible state.
 *
 * @param integer $term_id  The term to set the status for.
 * @param string $meta_key  { '_coil_monetization_term_status' | '_coil_visibility_term_status' }
 * @param string $status    Monetization or visibility state.
 *
 * @return void
 */
function set_term_status( $term_id, string $meta_key, string $status ) : void {

	$term_id = (int) $term_id;

	if ( $meta_key === '_coil_monetization_term_status' ) {
		$valid_status_options = get_valid_monetization_types();
		$status               = in_array( $status, $valid_status_options, true ) ? $status : Admin\get_monetization_default();
	} elseif ( $meta_key === '_coil_visibility_term_status' ) {
		$valid_status_options = get_valid_visibility_types();
		$status               = in_array( $status, $valid_status_options, true ) ? $status : Admin\get_visibility_default();
	} else {
		// Invalid meta key.
		return;
	}
	update_term_meta( $term_id, $meta_key, $status );
}

/**
 * New function to determine if the content is monetized
 * based on the output of get_content_status.
 *
 * @param int $post_id
 * @return boolean
 */
function is_content_monetized( $post_id ) : bool {

	$monetization_status = get_content_status( $post_id, 'monetization' );
	return ( $monetization_status === 'monetized' ) ? true : false;
}

/**
 * Get the Coil status for the specified post.
 *
 * @param integer $post_id The post to check.
 * @param string $status_type
 *
 * @return string Coil status.
 */
function get_post_status( $post_id, $status_type ) : string {

	$post_id = (int) $post_id;
	if ( $status_type === 'monetization' ) {
		$status = get_post_meta( $post_id, '_coil_monetization_post_status', true );
	} elseif ( $status_type === 'visibility' ) {
		$status = get_post_meta( $post_id, '_coil_visibility_post_status', true );
	}

	if ( empty( $status ) ) {
		// Returns default because then the post status won't be considered in determining the content status.
		$status = 'default';
	}

	return $status;
}

/**
 * Set the Coil status for the specified post.
 *
 * @param integer $post_id The post to set status for.
 * @param string $meta_key { '_coil_monetization_post_status' | '_coil_visibility_post_status' }
 * @param string $post_status Coil status (monetization and visibility settings).
 *
 * @return void
 */
function set_post_status( $post_id, string $meta_key, string $post_status ) : void {

	$post_id = (int) $post_id;

	if ( $meta_key === '_coil_monetization_post_status' ) {
		$valid_status_options = get_valid_monetization_types();
	} elseif ( $meta_key === '_coil_visibility_post_status' ) {
		$valid_status_options = get_valid_visibility_types();
	} else {
		// Unrecognised meta key.
		return;
	}

	if ( ! in_array( $post_status, $valid_status_options, true ) ) {
		$post_status = 'default';
	}

	update_post_meta( $post_id, $meta_key, $post_status );
}
