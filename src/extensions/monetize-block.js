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
const { InspectorControls } = wp.blockEditor || wp.editor; // wp.editor for WP < 5.2.
const { withSelect, withDispatch } = wp.data;
const { createHigherOrderComponent } = wp.compose;
const { PanelBody, RadioControl, SelectControl } = wp.components;
const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost; // WP >= 5.3.

/**
 /**
  * Adds our attributes for our monetization data and restrict to allowed blocks.
  *
  * @param  {Object} settings Settings for the block.
  * @return {Object} settings Modified settings.
  */
function addAttributes( settings ) {
	// Check if object exists for old Gutenberg version compatibility.
	// Set default to "always-show" for all currently added blocks and new blocks added.
	if ( typeof settings.attributes !== 'undefined' ) {
		settings.attributes = Object.assign( settings.attributes, {
			monetizeBlockDisplay: {
				type: 'string',
				default: 'always-show',
			},
		} );
	}
	return settings;
}

/**
 * Override the default edit UI to include a new block inspector control for
 * assigning monetization, if the block supports it.
 *
 * @param  {function|Component} BlockEdit Original component.
 * @return {string} Wrapped component.
 */
const monetizeBlockControls = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		let showInspector = false;

		const {
			attributes,
			setAttributes,
			isSelected,
		} = props;

		const { monetizeBlockDisplay } = attributes;

		// Fetch the post meta.
		const meta = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' );

		// Only show inspector options if set for block level monetization.
		showInspector = false;
		if ( typeof meta !== 'undefined' ) {
			if (
				typeof meta._coil_visibility_post_status === 'undefined' ||
				meta._coil_visibility_post_status === 'gate-tagged-blocks'
			) {
				showInspector = true;
			}
		}

		return (
			<Fragment>
				<BlockEdit { ...props } />
				{ isSelected && showInspector && (
					<InspectorControls>
						<PanelBody
							title={ __( 'Coil Web Monetization' ) }
							initialOpen={ false }
							className="coil-panel"
						>
							<RadioControl
								label={ __(
									'Set the block\'s visibility.',
									'coil-web-monetization',
								) }
								selected={ monetizeBlockDisplay }
								options={ [
									{
										label: __( 'Always Show' ),
										value: 'always-show',
									},
									{
										label: __( 'Only Show Coil Members' ),
										value: 'show-monetize-users',
									},
									{
										label: __( 'Hide For Coil Members' ),
										value: 'hide-monetize-users',
									},
								] }
								onChange={ ( value ) =>
									setAttributes( { monetizeBlockDisplay: value } )
								}
							/>
						</PanelBody>
					</InspectorControls>
				) }
			</Fragment>
		);
	};
}, 'monetizeBlockControls' );

/**
 * Add custom element class in save element.
 * Even though blockType is not used if it is removed the additional classes are not added on the paragraph level
 *
 * @param  {Object} extraProps Additional props applied to save element.
 * @param  {Object} blockType  Block type.
 * @param  {Object} attributes Current block attributes.
 * @return {Object} extraProps Filtered props applied to save element.
 */
function applyExtraClass( extraProps, blockType, attributes ) {
	const { monetizeBlockDisplay } = attributes;

	// Check if object exists for old Gutenberg version compatibility.
	// Add class only when monetizeBlockDisplay is set and is not always show.
	if (
		typeof monetizeBlockDisplay !== 'undefined' &&
		monetizeBlockDisplay !== 'always-show'
	) {
		extraProps.className = classnames(
			extraProps.className,
			'coil-' + monetizeBlockDisplay,
		);
	}

	return extraProps;
}

/**
 * Adds a wrapper class around the block in the editor to identify the monetization status.
 */
const wrapperClass = createHigherOrderComponent( ( BlockListBlock ) => {
	return ( props ) => {
		let wrapperProps = props.wrapperProps;
		let customData = {};
		let allowBlockIdentity = false; // Note: Boolean value is in reverse.

		const { attributes } = props;

		const { monetizeBlockDisplay } = attributes;

		// Fetch the post meta.
		const meta = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' );

		if (
			! meta ||
			typeof meta._coil_visibility_post_status === 'undefined' ||
			meta._coil_visibility_post_status === 'gate-tagged-blocks'
		) {
			allowBlockIdentity = true;
		} else {
			allowBlockIdentity = false;
		}

		customData = Object.assign( customData, {
			'data-coil-is-monetized': 1,
		} );

		wrapperProps = {
			...wrapperProps,
			...customData,
		};

		// Apply custom block wrapper class if monetization is set at the document level and block level.
		if (
			typeof monetizeBlockDisplay !== 'undefined' &&
			monetizeBlockDisplay !== 'always-show' &&
			allowBlockIdentity
		) {
			return (
				<BlockListBlock
					{ ...props }
					className={ 'coil-' + monetizeBlockDisplay }
					wrapperProps={ wrapperProps }
				/>
			);
		}
		return <BlockListBlock { ...props } />;
	};
}, 'wrapperClass' );

// Add filters
addFilter( 'blocks.registerBlockType', 'coil/addAttributes', addAttributes );

addFilter(
	'editor.BlockEdit',
	'coil/monetizeBlockControls',
	monetizeBlockControls,
);

addFilter(
	'blocks.getSaveContent.extraProps',
	'coil/applyExtraClass',
	applyExtraClass,
);

addFilter( 'editor.BlockListBlock', 'coil/wrapperClass', wrapperClass );

// Post monetization and visibility meta fields
const PostMetaFields = withDispatch( ( dispatch, props ) => {
	return {
		// Updates the visibility meta value when a visibility radio button is selected.
		updateVisibilityMetaValue: ( value ) => {
			dispatch( 'core/editor' ).editPost( {
				meta: {
					[ props.visibilityMetaFieldName ]: value,
				},
			} );
		},
		// Updates the monetization and visibility meta values when an option is selected from the monetization dropdown menu.
		// When the monetization status is selected the visibility status must remain compatible.
		// When monetization is disabled visibility must be public.
		// When monetization is default, visibility must also be on default (radio buttons should not appear).
		// If Enabled is selected, then visibility will be default until that is overwritten by the radio button selection.
		updateMetaValues: ( value ) => {
			let visibility = 'public';
			if ( value === 'default' ) {
				visibility = 'default';
			} else if ( value === 'monetized' ) {
				visibility = coilEditorParams.visibilityDefault; // eslint-disable-line no-undef
			}
			dispatch( 'core/editor' ).editPost( {
				meta: {
					[ props.monetizationMetaFieldName ]: value,
					[ props.visibilityMetaFieldName ]: visibility,
				},
			} );
		},
	};
} )(
	withSelect( ( select, props ) => {
		const meta = select( 'core/editor' ).getEditedPostAttribute( 'meta' );
		let defaultLabel = __( 'Enabled & Public' );

		if ( 'not-monetized' === coilEditorParams.monetizationDefault ) { // eslint-disable-line no-undef
			defaultLabel = __( 'Disabled' );
		} else if ( 'exclusive' === coilEditorParams.visibilityDefault ) { // eslint-disable-line no-undef
			defaultLabel = __( 'Enabled & Exclusive' );
		}

		return {
			[ props.monetizationMetaFieldName ]: meta && meta._coil_monetization_post_status,
			[ props.visibilityMetaFieldName ]: meta && meta._coil_visibility_post_status,
			defaultLabel: defaultLabel,
		};
	} )( ( props ) => (
		<div>
			<div>
				<SelectControl
					label={ __( 'Select a Web Monetization status', 'coil-web-monetization' ) }
					value={ props[ props.monetizationMetaFieldName ] ? props[ props.monetizationMetaFieldName ] : 'default' }
					onChange={ ( value ) => props.updateMetaValues( value ) }
					options={ [
						{ value: 'default', label: 'Default (' + props.defaultLabel + ')' },
						{ value: 'monetized', label: 'Enabled' },
						{ value: 'not-monetized', label: 'Disabled' },
					] }
				/>
			</div>
			<div
				className={ `coil-post-monetization-level ${ props[ props.monetizationMetaFieldName ] ? props[ props.monetizationMetaFieldName ] : 'default'
				}` }
			>
				<RadioControl
					label={ __( 'Who can access this content?', 'coil-web-monetization' ) }
					selected={
						props[ props.visibilityMetaFieldName ] && props[ props.visibilityMetaFieldName ] !== 'default' ? props[ props.visibilityMetaFieldName ] : coilEditorParams.visibilityDefault // eslint-disable-line no-undef
					}
					options={ [
						{
							label: __( 'Everyone', 'coil-web-monetization' ),
							value: 'public',
						},
						{
							label: __( 'Coil Members Only', 'coil-web-monetization' ),
							value: 'exclusive',
						},
						{
							label: __( 'Split', 'coil-web-monetization' ),
							value: 'gate-tagged-blocks',
						},
					] }
					onChange={ ( value ) => props.updateVisibilityMetaValue( value ) }
				/>
			</div>
		</div>
	) ),
);

// WP >= 5.3 only - register the panel.
if ( PluginDocumentSettingPanel ) {
	registerPlugin( 'coil-document-setting-panel', {
		render: () => {
			return (
				<PluginDocumentSettingPanel
					name="coil-meta"
					title={ __( 'Coil Web Monetization', 'coil-web-monetization' ) }
					initialOpen={ false }
					className="coil-document-panel"
				>
					<PostMetaFields monetizationMetaFieldName="_coil_monetization_post_status" visibilityMetaFieldName="_coil_visibility_post_status" />
				</PluginDocumentSettingPanel>
			);
		},
		icon: '',
	} );
}
