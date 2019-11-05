<?php
declare(strict_types=1);

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

	$coil_status = get_post_meta( get_the_ID(), '_coil_monetize_post_status', true );

	if ( ! is_singular() && ! empty( $coil_status ) ) {
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
