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
 * Maybe restrict (gate) visibility of the specified post content.
 *
 * @param string $content Post content to checl.
 *
 * @return string $content Updated post content.
 */
function maybe_restrict_content( string $content ) : string {

	$coil_status    = get_post_gating( get_the_ID() );
	$public_content = '';

	if ( ! is_singular() ) {
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

			case 'no':
			case 'no-gate':
			default:
				$public_content = $content;
				break;
		}
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

	if ( $gating === '' ) {
		$gating = 'no';
	}

	return $gating;
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

	$valid_gating_types = [
		'gate-all',
		'gate-tagged-blocks',
		'no',
		'no-gating',
	];

	if ( ! in_array( $gating_type, $valid_gating_types, true ) ) {
		return;
	}

	update_post_meta( $post_id, '_coil_monetize_post_status', $gating_type );
}
