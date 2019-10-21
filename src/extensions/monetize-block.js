/**
 * External Dependencies
 */
import classnames from 'classnames';

// Editor and Frontend Styles
import './styles/editor.scss';
import './styles/style.scss';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { addFilter } = wp.hooks;
const { Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { createHigherOrderComponent } = wp.compose;
const { Dashicon, PanelBody, RadioControl } = wp.components;

// Allow only specific blocks to use the extension attribute.
const allowedBlocks = [
	'core/paragraph',
	'core/heading',
	'core/image',
	'core/gallery',
	'core/verse',
	'core/spacer',
	'core/subhead',
	'core/preformatted',
	'core/code',
	'core/cover',
	'core/group',
	'core/columns',
	'core/media-text',
	'core/pullquote',
	'core/quote',
	'core/button',
	'core/list',
	'core/separator',
	'core/text-columns',
	'core/video',
	'core/audio',
	'core-embed',
	'core-embed/youtube',
	'core-embed/twitter',
	'core-embed/facebook',
	'core-embed/instagram',
	'core-embed/wordpress',
	'core-embed/soundcloud',
	'core-embed/spotify',
	'core-embed/flickr',
	'core-embed/vimeo',
	'core-embed/animoto',
	'core-embed/cloudup',
	'core-embed/collegehumor',
	'core-embed/crowdsignal',
	'core-embed/dailymotion',
	'core-embed/hulu',
	'core-embed/imgur',
	'core-embed/issuu',
	'core-embed/kickstarter',
	'core-embed/meetup-com',
	'core-embed/mixcloud',
	'core-embed/reddit',
	'core-embed/reverbnation',
	'core-embed/screencast',
	'core-embed/scribd',
	'core-embed/slideshare',
	'core-embed/smugmug',
	'core-embed/speaker-deck',
	'core-embed/ted',
	'core-embed/tumblr',
	'core-embed/videopress',
	'core-embed/wordpress-tv',
	'core-embed/amazon-kindle'
];

// Restrict the blocks the extension attribute can not be applied to.
const restrictedBlocks = [
	'core/block',
	'core/freeform',
	'core/shortcode',
	'core/template',
	'core/nextpage'
];

/**
 * Is the block allowed to support monetization.
 *
 * @param {string} name The name of the block.
 */
function allowedBlockTypes( name ) {
	return allowedBlocks.includes( name );
} // END allowedBlockTypes()

/**
 * Is the block not allowed to support monetization.
 *
 * @param {string} name The name of the block.
 */
function restrictedBlockTypes( name ) {
	return restrictedBlocks.includes( name );
} // END restrictedBlockTypes()

/**
/**
 * Adds our attributes for our monetization data and restrict to allowed blocks.
 *
 * @param  {Object} settings Settings for the block.
 * @return {Object} settings Modified settings.
 */
function addAttributes( settings ) {
	// If this block is not allowed to use the extension attribute then move on to the next. - Disabled for now!
	/*if ( ! restrictedBlockTypes( settings.name ) ) {
		return settings;
	}*/

	// Check if this block is allowed to use the extension attribute. - Disabled for now!
	/*if ( ! allowedBlockTypes( settings.name ) ) {
		return settings;
	}*/

	// Check if object exists for old Gutenberg version compatibility.
	// Set default to "always-show" for all currently added blocks and new blocks added.
	if ( typeof settings.attributes !== 'undefined' ) {
		settings.attributes = Object.assign( settings.attributes, {
			monetizeBlockDisplay: {
				type: 'string',
				default: 'always-show'
			}
		});
	}

	return settings;
} // END addAttributes()

/**
 * Override the default edit UI to include a new block inspector control for
 * assigning monetization, if the block supports it.
 *
 * @param  {function|Component} BlockEdit Original component.
 * @return {string} Wrapped component.
 */
const monetizeBlockControls = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {

		let hideInspector = false; // Note: Boolean value is in reverse.

		const {
			name,
			attributes,
			setAttributes,
			isSelected,
		} = props;

		const {
			monetizeBlockDisplay,
		} = attributes;

		// Fetch the post meta.
		const meta = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' );

		// If the block is not supported then don't show the inspector.
		/*if ( ! allowedBlockTypes( props.name ) || ! restrictedBlockTypes( props.name ) || ! props.isSelected ) {
			return <BlockEdit { ...props } />;
		}*/

		if ( typeof meta._coil_monetize_post_status === 'undefined' || ( typeof meta._coil_monetize_post_status !== 'undefined' && meta._coil_monetize_post_status === 'no' ) ) {
			hideInspector = false;
		} else {
			hideInspector = true;
		}

		return (
			<Fragment>
				<BlockEdit { ...props } />
				{ isSelected && hideInspector &&
					<InspectorControls>
						<PanelBody
							title={ __( 'Web Monetization - Coil' ) } 
							initialOpen={ false }
							className="coil-panel"
						>
							<RadioControl
								selected={ monetizeBlockDisplay }
								options={
									[
										{
											label: __( 'Always show' ),
											value: 'always-show'
										},
										{
											label: __( 'Show for monetized users' ),
											value: 'show-monetize-users'
										},
										{
											label: __( 'Hide for monetized users' ),
											value: 'hide-monetize-users'
										},
									]
								}
								help={ __( 'Set the visibility based on the monetization you prefer.' ) }
								onChange={ ( value ) => setAttributes( { monetizeBlockDisplay: value } ) }
							/>

						</PanelBody>
					</InspectorControls>
				}
			</Fragment>
		);
	};
}, 'monetizeBlockControls');

/**
 * Add custom element class in save element.
 *
 * @param  {Object} extraProps Additional props applied to save element.
 * @param  {Object} blockType  Block type.
 * @param  {Object} attributes Current block attributes.
 * @return {Object} extraProps Filtered props applied to save element.
 */
function applyExtraClass( extraProps, blockType, attributes ) {
	const {
		monetizeBlockDisplay,
	} = attributes;

	// If this block is not allowed to use the extension attribute then move on to the next. - Disabled for now!
	/*if ( ! restrictedBlockTypes( blockType.name ) ) {
		return extraProps;
	}*/

	// Check if this block is allowed to use the extension attribute. - Disabled for now!
	/*if ( ! allowedBlockTypes( blockType.name ) ) {
		return extraProps;
	}*/

	// Check if object exists for old Gutenberg version compatibility.
	// Add class only when monetizeBlockDisplay is set and is not always show.
	if ( typeof monetizeBlockDisplay !== 'undefined' && monetizeBlockDisplay !== "always-show") {
		extraProps.className = classnames( extraProps.className, 'coil-' + monetizeBlockDisplay );
	}

	return extraProps;
} // END applyExtraClass()

// Adds a wrapper class around the block in the editor to identify the monetization status.
const wrapperClass = createHigherOrderComponent( ( BlockListBlock ) => {
	return ( props ) => {
		let wrapperProps = props.wrapperProps;
		let customData = {};
		let allowBlockIdentity = false; // Note: Boolean value is in reverse.

		const {
			attributes
		} = props;

		const {
			monetizeBlockDisplay,
		} = attributes;

		// Fetch the post meta.
		const meta = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' );

		if ( typeof meta._coil_monetize_post_status === 'undefined' || ( typeof meta._coil_monetize_post_status !== 'undefined' && meta._coil_monetize_post_status === 'no' ) ) {
			allowBlockIdentity = false;
		} else {
			allowBlockIdentity = true;
		}

		customData = Object.assign( customData, {
			'data-coil-is-monetized': 1,
		} );

		wrapperProps = {
			...wrapperProps,
			...customData,
		};

		// Apply custom block wrapper class if monetization is set at the document level and block level.
		if ( typeof monetizeBlockDisplay !== 'undefined' && monetizeBlockDisplay !== "always-show" && allowBlockIdentity ) {
			return <BlockListBlock { ...props } className={ 'coil-' + monetizeBlockDisplay } wrapperProps={ wrapperProps } />;
		} else {
			return <BlockListBlock {...props} />
		}
	};
}, 'wrapperClass' );

// Add filters
addFilter(
	'blocks.registerBlockType',
	'coil/addAttributes',
	addAttributes
);

addFilter(
	'editor.BlockEdit',
	'coil/monetizeBlockControls',
	monetizeBlockControls
);

addFilter(
	'blocks.getSaveContent.extraProps',
	'coil/applyExtraClass',
	applyExtraClass
);

addFilter(
	'editor.BlockListBlock',
	'coil/wrapperClass',
	wrapperClass
);