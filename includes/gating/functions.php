<?php
declare(strict_types=1);
/**
 * Coil gating.
 */

namespace Coil\Gating;

/**
 * Register post/user meta.
 */
function register_content_meta() : void {

	register_meta(
		'post',
		'_coil_monetize_post_status',
		[
			'single' => true,
			'type'   => 'string',
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
			'single' => true,
			'type'   => 'string',
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

	if ( true === $show_default ) {
		$settings['default'] = esc_html__( 'Use Default', 'coil-monetize-content' );
	}

	$settings['no']        = esc_html__( 'No Monetization', 'coil-monetize-content' );
	$settings['no-gating'] = esc_html__( 'Monetized and Public', 'coil-monetize-content' );
	$settings['gate-all']  = esc_html__( 'Subscribers Only', 'coil-monetize-content' );

	return $settings;
}

function get_valid_gating_types() {
	$valid = [
		'gate-all', // subscribers only.
		'gate-tagged-blocks', // split content.
		'no', // no monetization.
		'no-gating', // monetixed and public.
		'default', // whatever is set on the post to revert back.
	];
	return $valid;
}

/**
 * Maybe restrict (gate) visibility of the specified post content.
 *
 * @param string $content Post content to checl.
 *
 * @return string $content Updated post content.
 */
function maybe_restrict_content( string $content ) : string {

	if ( is_singular() ) {
		return $content;
	}

	$coil_status    = get_content_gating( get_the_ID() );
	$public_content = '';

	switch ( $coil_status ) {
		case 'gate-all':
			// Restrict all content (subscribers only).
			$public_content = '<p>' . esc_html__( 'The contents of this article is for subscribers only!', 'coil-monetize-content' ) . '</p>';
			break;

		case 'gate-tagged-blocks':
			// Restrict some part of this content. (split content).
			$public_content  = '<p>' . esc_html__( 'This article is monetized and some content is for subscribers only.', 'coil-monetize-content' ) . '</p>';
			$public_content .= $content;
			break;

		/**
		 * case 'default': doesn't exist in this context because the last possible
		 * saved option will be 'no', after a post has fallen back to the taxonomy
		 * and then the default post options.
		 */
		case 'no':
		case 'no-gate':
		default:
			$public_content = $content;
			break;
	}

	return apply_filters( 'coil_maybe_restrict_content', $public_content, $content, $coil_status );
}

/**
 * Get the gating type for the specified post.
 *
 * @param integer $post_id The post to check.
 *
 * @return string Either "no" (default), "no-gating", "gate-all", "gate-tagged-blocks".
 */
function get_post_gating( int $post_id ) : string {

	$gating = get_post_meta( $post_id, '_coil_monetize_post_status', true );

	if ( empty( $gating ) ) {
		$gating = 'default';
	}

	return $gating;
}

/**
 * Get the gating type for the specified term.
 *
 * @param integer $term_id The term_id to check.
 *
 * @return string Either "default" (default), "no", "no-gating", "gate-all".
 */
function get_term_gating( $term_id ) {

	$term_gating = get_term_meta( $term_id, '_coil_monetize_term_status', true );

	if ( empty( $term_gating ) ) {
		$term_gating = 'default';
	}
	return $term_gating;
}

/**
 * This function does the following:
 *
 * 1) Get any terms assigned to the post
 * 2) Do these terms have gating?
 * 3) If yes, rank them by order (see confluence) and return highest rank
 *
 * @return string Gating type.
 */
function get_taxonomy_term_gating( $post_id ) {

	// $post_terms = wp_get_post_terms(
	// 	$post_id,



	// );

	// Set to 'default' for now as this work is part of another ticket.
	return 'default';
}

/**
 * Return the single source of truth for post gating based on the fallback
 * options if the post gating selection is 'default'. E.g.
 * If return value of each function is default, move onto the next function,
 * otherwise return immediately.
 *
 * @param integer $post_id
 * @return void
 */
function get_content_gating( int $post_id ) : string {

	$post_gating = get_post_gating( $post_id );

	// Set a default monetization value.
	$content_gating = 'no';

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
			$global_gating_settings = get_global_posts_gating();

			if ( ! empty( $global_gating_settings ) && isset( $global_gating_settings[ $post->post_type ] ) ) {
				$content_gating = $global_gating_settings[ $post->post_type ];
			}
		}
	}

	return $content_gating;
}


/**
 * Get whatever settings are stored in the plugin as the default
 * content gating settings (post, page, cpt etc).
 *
 * @return array Setting stored in options, or blank array.
 */
function get_global_posts_gating() : array {
	$global_settings = get_option( 'coil_content_settings_posts_group' );
	if ( ! empty( $global_settings ) ) {
		return $global_settings;
	}

	return [];
}

/**
 * Set the gating type for the specified post.
 *
 * @param integer $post_id    The post to set gating for.
 * @param string $gating_type Either "default", "no", "no-gating", "gate-all", "gate-tagged-blocks".
 *
 * @return void
 */
function set_post_gating( int $post_id, string $gating_type ) : void {

	$valid_gating_types = get_valid_gating_types();
	if ( ! in_array( $gating_type, $valid_gating_types, true ) ) {
		return;
	}

	update_post_meta( $post_id, '_coil_monetize_post_status', $gating_type );
}

/**
 * Set the gating type for the specified term.
 *
 * @param integer $term_id    The term to set gating for.
 * @param string $gating_type Either "default", "no", "no-gating", "gate-all", "gate-tagged-blocks".
 *
 * @return void
 */
function set_term_gating( int $term_id, string $gating_type ) : void {

	$valid_gating_types = get_valid_gating_types();
	if ( ! in_array( $gating_type, $valid_gating_types, true ) ) {
		return;
	}

	update_term_meta( $term_id, '_coil_monetize_term_status', $gating_type );
}

/**
 * New function to determine if the content is monetized
 * based on the outout of get_content_gating.
 *
 * @param int $post_id
 * @return boolean
 */
function is_content_monetized( $post_id ) : bool {
	$coil_status = get_content_gating( $post_id );
	return ( $coil_status === 'no' ) ? false : true;
}
