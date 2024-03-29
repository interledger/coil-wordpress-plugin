const { __ } = window.wp.i18n;
import { Tooltip } from '@wordpress/components';
const { withSelect } = wp.data;

// Checks whether exclusivity has been globally enabled or disabled
const exclusiveContentStatus = coilEditorParams.exclusiveContentStatus;/* eslint-disable-line no-undef */
// Provides the actual default visibility status as it is stored in the wp_options table.
const postVisibilityDefault = coilEditorParams.visibilityDefault; // eslint-disable-line no-undef

/**
 * Returns the black or greyed out Coil logo.
 *
 * @param {String} visibilityStatus the visibility status of the post (either public or exclusive).
 *
 * @return {object} logo svg.
*/
function getCoilLogo( visibilityStatus ) {
	if ( exclusiveContentStatus === 'off' || visibilityStatus !== 'exclusive' ) {
		return (
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<g clipPath="url(#clip0_6_2)">
					<path d="M19.5 0H4.5C2.01472 0 0 2.01472 0 4.5V19.5C0 21.9853 2.01472 24 4.5 24H19.5C21.9853 24 24 21.9853 24 19.5V4.5C24 2.01472 21.9853 0 19.5 0Z" fill="#AAAAAA" />
					<path d="M15.8845 14.7619C16.2092 14.7619 16.6329 14.928 16.9294 15.634C16.9718 15.7309 17 15.8416 17 15.9385C17 17.1013 14.303 18.1533 12.7356 18.2364C12.6226 18.2364 12.4955 18.2502 12.3825 18.2502C10.0526 18.2502 7.89217 17.0321 6.77664 15.0664C6.25417 14.1389 6 13.1146 6 12.0764C6 10.8721 6.35302 9.66774 7.04493 8.62953C7.58151 7.79897 8.62644 6.70539 10.4621 6.11016C10.9281 5.95789 11.7471 5.75024 12.6791 5.75024C13.5828 5.75024 14.5854 5.94404 15.4608 6.59465C16.6893 7.49443 16.9435 8.47727 16.9435 9.11403C16.9435 9.39089 16.9012 9.59853 16.8588 9.72311C16.5764 10.7336 15.6303 11.5088 14.5289 11.6611C14.2747 11.6888 14.0629 11.7165 13.8652 11.7165C12.8626 11.7165 12.5237 11.3012 12.5237 10.8305C12.5237 10.1938 13.1309 9.44626 13.5687 9.44626C13.6252 9.44626 13.6816 9.4601 13.724 9.48779C13.837 9.557 13.9782 9.58468 14.1053 9.58468C14.1476 9.58468 14.1759 9.58468 14.2182 9.57084C14.5712 9.52931 14.7548 9.29399 14.7548 9.00329C14.7548 8.46342 14.1053 7.72976 12.6932 7.72976C12.2413 7.72976 11.7189 7.79897 11.1258 7.97893C9.88318 8.35268 9.19127 9.22477 8.83825 9.76464C8.37227 10.4568 8.14634 11.2597 8.14634 12.0487C8.14634 12.727 8.31579 13.4053 8.65469 14.0144C9.38896 15.3017 10.8293 16.1046 12.3825 16.1046C12.4673 16.1046 12.5379 16.1046 12.6226 16.1046C14.6418 15.9939 15.1078 14.9972 15.5597 14.8034C15.6303 14.7896 15.7574 14.7619 15.8845 14.7619Z" fill="white" />
				</g>
				<defs>
					<clipPath id="clip0_6_2">
						<rect width="24" height="24" fill="white" />
					</clipPath>
				</defs>
			</svg>

		);
	}
	return (
		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M0 5C0 2.23858 2.23858 0 5 0H19C21.7614 0 24 2.23858 24 5V19C24 21.7614 21.7614 24 19 24H5C2.23858 24 0 21.7614 0 19V5Z" fill="black" />
			<path d="M16.2587 14.8242C16.5793 14.8242 16.9975 14.9868 17.2903 15.6781C17.3321 15.773 17.36 15.8815 17.36 15.9763C17.36 17.1149 14.6973 18.1451 13.1498 18.2264C13.0383 18.2264 12.9128 18.24 12.8013 18.24C10.5011 18.24 8.36809 17.0472 7.26675 15.1224C6.75094 14.2142 6.5 13.2112 6.5 12.1946C6.5 11.0153 6.84852 9.83601 7.53163 8.8194C8.06139 8.00611 9.09302 6.93528 10.9053 6.35243C11.3654 6.20332 12.174 6 13.0941 6C13.9863 6 14.9761 6.18977 15.8404 6.82684C17.0533 7.70791 17.3042 8.6703 17.3042 9.29382C17.3042 9.56492 17.2624 9.76824 17.2206 9.89023C16.9418 10.8797 16.0077 11.6388 14.9203 11.7879C14.6694 11.815 14.4603 11.8421 14.2651 11.8421C13.2753 11.8421 12.9407 11.4355 12.9407 10.9746C12.9407 10.3511 13.5402 9.61914 13.9723 9.61914C14.0281 9.61914 14.0839 9.63269 14.1257 9.6598C14.2372 9.72757 14.3766 9.75468 14.5021 9.75468C14.5439 9.75468 14.5718 9.75468 14.6136 9.74113C14.9622 9.70046 15.1434 9.47003 15.1434 9.18538C15.1434 8.65674 14.5021 7.93834 13.108 7.93834C12.6619 7.93834 12.1461 8.00611 11.5606 8.18232C10.3338 8.54831 9.65065 9.40226 9.30213 9.9309C8.84208 10.6086 8.61902 11.3948 8.61902 12.1674C8.61902 12.8316 8.78632 13.4958 9.1209 14.0922C9.84583 15.3528 11.2678 16.139 12.8013 16.139C12.885 16.139 12.9547 16.139 13.0383 16.139C15.0319 16.0306 15.4919 15.0546 15.938 14.8648C16.0077 14.8513 16.1332 14.8242 16.2587 14.8242Z" fill="white" />
		</svg>
	);
}

/**
 * Returns the ECD inner text and tooltip text when the ECD is disabled.
 *
 * @param {String} visibilityStatus the visibility status of the post (either public or exclusive).
 *
 * @return {object} ECD inner text and helper text.
*/
function getDescription( visibilityStatus ) {
	if ( visibilityStatus !== 'exclusive' ) {
		return (
			<Tooltip text={ __( 'Enable exclusive content in the post settings', 'coil-web-monetization' ) } >
				<span>
					<p className="coil-hint">
						{ __( 'Exclusive content disabled for this post. Content below is visible to everyone. ', 'coil-web-monetization' ) }
					</p>
				</span>
			</Tooltip>
		);
	} else if ( exclusiveContentStatus === 'off' ) {
		return (
			<Tooltip text={ __( 'Enable exclusive content under WP Admin > Coil > Exclusive Content', 'coil-web-monetization' ) } >
				<span>
					<p className="coil-hint">
						{ __( 'Exclusive content disabled for this site. Content below is visible to everyone. ', 'coil-web-monetization' ) }
					</p>
				</span>
			</Tooltip>
		);
	}
	return (
		<p>{ __( 'Exclusive content for Coil members starts below ', 'coil-web-monetization' ) }</p>
	);
}

/**
 * Returns the black or greyed out arrow icon.
 *
 * @param {String} visibilityStatus the visibility status of the post (either public or exclusive).
 *
 * @return {object} arrow svg.
*/
function getArrow( visibilityStatus ) {
	if ( exclusiveContentStatus === 'off' || visibilityStatus !== 'exclusive' ) {
		return (
			<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M4 0.5C4.55228 0.5 5 0.947715 5 1.5V8.08579L6.29289 6.79289C6.68342 6.40237 7.31658 6.40237 7.70711 6.79289C8.09763 7.18342 8.09763 7.81658 7.70711 8.20711L4.70711 11.2071C4.31658 11.5976 3.68342 11.5976 3.29289 11.2071L0.292893 8.20711C-0.097631 7.81658 -0.097631 7.18342 0.292893 6.79289C0.683417 6.40237 1.31658 6.40237 1.70711 6.79289L3 8.08579V1.5C3 0.947715 3.44772 0.5 4 0.5Z" fill="#AAAAAA" />
			</svg>
		);
	}
	return (
		<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path fillRule="evenodd" clipRule="evenodd" d="M4 0.5C4.55228 0.5 5 0.947715 5 1.5V8.08579L6.29289 6.79289C6.68342 6.40237 7.31658 6.40237 7.70711 6.79289C8.09763 7.18342 8.09763 7.81658 7.70711 8.20711L4.70711 11.2071C4.31658 11.5976 3.68342 11.5976 3.29289 11.2071L0.292893 8.20711C-0.0976311 7.81658 -0.0976311 7.18342 0.292893 6.79289C0.683417 6.40237 1.31658 6.40237 1.70711 6.79289L3 8.08579V1.5C3 0.947715 3.44772 0.5 4 0.5Z" fill="#2D333A" />
		</svg>
	);
}

/**
 * Uses withSelect to subscribe to any changes in the post meta data.
 * The post's visibility status can be extracted in real time from the meta attribute.
 * If the visibility is not exclusive the ECD will be greyed out to warn users that it won't have an effect.
*/
export const ExclusiveContentDivider = withSelect( ( select ) => {
	const meta = select( 'core/editor' ).getEditedPostAttribute( 'meta' );

	let visibility;
	// If the meta object or the visibility status property are defined then the default visibility will be used.
	// If the visibility status is 'default' it should be replaced with the actual default value from the database.
	if ( ! meta || ! meta._coil_visibility_post_status || meta._coil_visibility_post_status === 'default' ) {
		visibility = postVisibilityDefault;
	} else {
		visibility = meta._coil_visibility_post_status;
	}

	return {
		visibilityStatus: visibility,
	};
} )( ( props ) => (
	<div className={ props.blockTypeClassName + 'post-visibility-' + props.visibilityStatus + ' exclusive-content-' + exclusiveContentStatus } >
		<div className="coil-exclusive-content-divider-inner">
			{ getCoilLogo( props.visibilityStatus ) }
			<div className="coil-exclusive-content-divider-text">
				{ getDescription( props.visibilityStatus ) }
			</div>
			{ getArrow( props.visibilityStatus ) }
		</div>
	</div>
) );
