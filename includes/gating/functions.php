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
		'public', // visable to everyone.
		'exclusive', // visable to Coil members only.
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

	if ( ! Admin\get_exlusive_post_setting( 'coil_title_padlock' ) ) {
		return $title;
	}

	$status = get_content_visibility( $id );
	if ( $status !== 'exclusive' ) {
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
 * If the post is exclusive then no excerpt will show unless one has been set explicitly.
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

	$coil_visibility_status = get_content_visibility( get_the_ID() );
	$post_obj               = get_post( get_the_ID() );
	$content_excerpt        = $post_obj->post_excerpt;
	$public_content         = '';

	switch ( $coil_visibility_status ) {
		case 'exclusive':
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
 * Get the monetization type for the specified term.
 *
 * @param integer $term_id The term_id to check.
 *
 * @return string Either "default" (default), "not-monetized", or "monetized".
 */
function get_term_monetization( $term_id ) {

	$term_id           = (int) $term_id;
	$term_monetization = get_term_meta( $term_id, '_coil_monetization_term_status', true );

	if ( empty( $term_monetization ) ) {
		$term_monetization = 'default';
	}
	return $term_monetization;
}

/**
 * Get the visibility type for the specified term.
 *
 * @param integer $term_id The term_id to check.
 *
 * @return string Either "default" (default), "not-monetized", or "monetized".
 */
function get_term_visibility( $term_id ) {

	$term_id         = (int) $term_id;
	$term_visibility = get_term_meta( $term_id, '_coil_visibility_term_status', true );

	if ( empty( $term_visibility ) ) {
		$term_visibility = 'default';
	}
	return $term_visibility;
}

/**
 * Get any terms attached to the post and return their highest monetization status.
 *
 * @return string Monetization status.
 */
function get_taxonomy_term_monetization( $post_id ) {

	$post_id                 = (int) $post_id;
	$final_term_monetization = 'default';

	$valid_taxonomies = Admin\get_valid_taxonomies();

	// 1) Get any terms assigned to the post.
	$post_terms = wp_get_post_terms(
		$post_id,
		$valid_taxonomies,
		[
			'fields' => 'ids',
		]
	);

	// 2) Has a monetization status been attached to this taxonomy?
	if ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) {

		foreach ( $post_terms as $term_id ) {

			$post_term_monetization = get_term_monetization( $term_id );
			// Monetized is the highest form, if this occurs simply break out of loop and return.
			if ( $post_term_monetization === 'monetized' ) {
				$final_term_monetization = $post_term_monetization;
				break;
				// If a term's monetization has been set then save it - in contrast to it being 'default'.
				// Don't break yet, keep checking for a monetized state.
			} elseif ( $post_term_monetization === 'not-monetized' ) {
				$final_term_monetization = $post_term_monetization;
			}
		}
	}

	// $final_term_monetization will be 'default' if no term had a set monetization meta field.
	return $final_term_monetization;
}

/**
 * Get any terms attached to the post and return their strictness visibility status.
 *
 * @return string Visibility status.
 */
function get_taxonomy_term_visibility( $post_id ) {

	$post_id               = (int) $post_id;
	$final_term_visibility = 'default';

	$valid_taxonomies = Admin\get_valid_taxonomies();

	// 1) Get any terms assigned to the post.
	$post_terms = wp_get_post_terms(
		$post_id,
		$valid_taxonomies,
		[
			'fields' => 'ids',
		]
	);

	// 2) Has a monetization status been attached to this taxonomy?
	if ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) {

		foreach ( $post_terms as $term_id ) {

			$post_term_visibility = get_term_visibility( $term_id );
			// Exclusive is the strictest form, if this occurs simply break out of loop and return.
			if ( $post_term_visibility === 'exclusive' ) {
				$final_term_visibility = $post_term_visibility;
				break;
				// If a term's visibility has been set then save it - in contrast to it being 'default'.
				// Don't break yet, keep checking for an exclusive state.
			} elseif ( $post_term_visibility === 'public' || $post_term_visibility === 'gate-tagged-blocks' ) {
				$final_term_visibility = $post_term_visibility;
			}
		}
	}

	// $final_term_visibility will be 'default' if no term had a set visibility meta field.
	return $final_term_visibility;
}

/**
 * Return the single source of truth for post monetization based on the fallback
 * options if the post monetization selection is 'default'. E.g.
 * If return value of each function is default, move onto the next function,
 * otherwise return immediately.
 *
 * @param integer $post_id
 * @return string $content_monetization Monetization slug type.
 */
function get_content_monetization( $post_id ) : string {

	$post_id           = (int) $post_id;
	$post_monetization = get_post_monetization( $post_id );

	// Set a default monetization value.
	$content_monetization = Admin\get_monetization_default();

	// Hierarchy 1 - Check what is set on the post.
	if ( 'default' !== $post_monetization ) {

		$content_monetization = $post_monetization; // Honour what is set on the post.

	} else {

		// Hierarchy 2 - Check what is set on the taxonomy.
		$taxonomy_monetization = get_taxonomy_term_monetization( $post_id );
		if ( 'default' !== $taxonomy_monetization ) {

			$content_monetization = $taxonomy_monetization; // Honour what is set on the taxonomy.

		} else {

			// Hierarchy 3 - Check what is set in the global default.
			// Get the post type for this post to check against what is set for default.
			$post = get_post( $post_id );

			// Get the post type from what is saved in global options
			$general_settings = Admin\get_exclusive_settings();

			if ( ! empty( $general_settings ) && ! empty( $post ) && isset( $general_settings[ $post->post_type ] ) ) {
				$content_monetization = $general_settings[ $post->post_type ];
			}
		}
	}

	return $content_monetization;
}

/**
 * Return the single source of truth for post visibility based on the fallback
 * options if the post visibility selection is 'default'. E.g.
 * If return value of each function is default, move onto the next function,
 * otherwise return immediately.
 *
 * @param integer $post_id
 * @return string $content_visibility Visibility slug type.
 */
function get_content_visibility( $post_id ) : string {

	$post_id         = (int) $post_id;
	$post_visibility = get_post_visibility( $post_id );

	// Set a default visibility value.
	$content_visibility = Admin\get_post_visibility_default();

	// Hierarchy 1 - Check what is set on the post.
	if ( 'default' !== $post_visibility ) {

		$content_visibility = $post_visibility; // Honour what is set on the post.

	} else {

		// Hierarchy 2 - Check what is set on the taxonomy.
		$taxonomy_visibility = get_taxonomy_term_visibility( $post_id );
		if ( 'default' !== $taxonomy_visibility ) {

			$content_visibility = $taxonomy_visibility; // Honour what is set on the taxonomy.

		} else {

			// Hierarchy 3 - Check what is set in the global default.
			// Get the post type for this post to check against what is set for default.
			$post = get_post( $post_id );

			// Get the post type from what is saved in global options
			$exclusive_settings = Admin\get_exclusive_settings();

			if ( ! empty( $exclusive_settings ) && ! empty( $post ) && isset( $exclusive_settings[ $post->post_type ] ) ) {
				$content_visibility = $exclusive_settings[ $post->post_type ];
			}
		}
	}

	return $content_visibility;
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
 * based on the output of get_content_monetization.
 *
 * @param int $post_id
 * @return boolean
 */
function is_content_monetized( $post_id ) : bool {

	$monetization_status = get_content_monetization( $post_id );
	return ( $monetization_status === 'not-monetized' ) ? false : true;
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

	update_post_meta( $post_id, '_coil_visibility_post_status', $post_visibility );
}
