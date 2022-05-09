// Editor and Frontend Styles
import './styles/editor.scss';
import './styles/style.scss';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { withSelect, withDispatch } = wp.data;
const { RadioControl, SelectControl } = wp.components;
const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost; // WP >= 5.3.

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
					label={ __( 'Select a monetization status', 'coil-web-monetization' ) }
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
