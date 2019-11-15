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
 * Store the monetization options.
 *
 * @return array
 */
function get_monetization_setting_types( $post_id = false ) : array {

	if ( ! empty( $post_id ) ) {
		$settings['default'] = esc_html__( 'Use Default', 'coil-monetize-content' );
	}

	$settings['no']        = esc_html__( 'No Monetization', 'coil-monetize-content' );
	$settings['no-gating'] = esc_html__( 'Monetized and Public', 'coil-monetize-content' );
	$settings['gate-all']  = esc_html__( 'Subscribers Only', 'coil-monetize-content' );

	return $settings;
}

function get_valid_gating_types() {
	$valid = [
		'gate-all',
		'gate-tagged-blocks',
		'no',
		'no-gating',
		'default',
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

	$coil_status    = get_post_gating( get_the_ID() );
	$public_content = '';

	switch ( $coil_status ) {
		case 'gate-all':
			// Restrict all content.
			$public_content = '<p>' . esc_html__( 'The contents of this article is for subscribers only!', 'coil-monetize-content' ) . '</p>';
			break;

		case 'gate-tagged-blocks':
			// Restrict some part of this content.
			$public_content  = '<p>' . esc_html__( 'This article is monetized and some content is for subscribers only.', 'coil-monetize-content' ) . '</p>';
			$public_content .= $content;
			break;

		case 'default':
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

function get_content_gating( int $post_id ) : string {

	$post_gating = get_post_gating( $post_id );

	if ( $post_gating === 'default' ) {

		// Check Taxonomy
		$tax_gating = get_taxonomy_gating();
		if ( $tax_gating === 'default' ) {

			// Check global plugin settings
			$global_gating = get_global_posts_gating();
			if ( $global_gating === 'no' ) {
				// do nothing
			} else {
				// adhere to the gating option for this post
			}
		}
	}

	// return a source of truth for this post.
	// 1 check post setting - from this ID get_post_gating()
	// 2 check taxonomy setting - get_taxonomy_gating()
	// 3. check global setting - get_global_posts_gating()

	// if return value of each function is default, move onto the next function
	// if the value is non default, return immediately.
}

// $val = get_content_gating(11);
// at this point val, doesn't need to know if the content gating is on the post or tax,
// it just returns one of the strings.

function get_taxonomy_gating() {
	// abstract the tax loop to this function and use in get_content_gating().

	// get all the taxonomies,
	// get the meta
	// check if set or not
}

// abstract the global loop to this function and use in get_content_gating().
function get_global_posts_gating() {
	return get_option( 'coil_content_settings_posts_group' );
}


/**
 * Set the gating type for the specified post.
 *
 * @param integer $post_id    The post to set gating for.
 * @param string $gating_type Either "no", "no-gating", "gate-all", "gate-tagged-blocks".
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
