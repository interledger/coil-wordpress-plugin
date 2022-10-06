/**
 * Exclusive Icon settings.
*/

const serializer = new XMLSerializer();

describe( 'Exclusive icon settings', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
	} );

	it( 'Checks Exclusive Icon Appearance defaults', () => {
		cy
			.get( '#coil_title_padlock' )
			.should( 'be.checked' );

		cy
			.get( '#padlock_icon_position_before' )
			.should( 'be.checked' );

		cy
			.get( '#coil_padlock_icon_style_lock' )
			.should( 'be.checked' );

		cy
			.get( '#padlock_icon_size_small' )
			.should( 'be.checked' );
	} );

	it( 'Checks the icon preview defaults', () => {
		// Ensures the preview icon matches the padlock icon.
		cy.get( '.coil-preview svg' ).then( ( $previewIcon ) => {
			cy.get( '[for="coil_padlock_icon_style_lock"] > svg' ).then( ( $icon ) => {
				const previewSvg = serializer.serializeToString( $previewIcon[ 0 ] );
				const lockIconSvg = serializer.serializeToString( $icon[ 0 ] );
				expect( previewSvg === lockIconSvg ).to.be.true; // eslint-disable-line no-unused-expressions
			} );
		} );

		cy
			.get( '.coil-title-preview-container' )
			.should( 'have.attr', 'data-padlock-icon-position' )
			.and( 'equal', 'before' );
	} );

	it( 'Checks that the icon preview reacts correctly', () => {
		cy
			.get( '#padlock_icon_position_after' )
			.click();

		cy
			.get( '#coil_padlock_icon_style_bonus' )
			.click();

		cy.get( '#submit' ).click();

		// Ensures the preview icon matches the bonus icon.
		cy.get( '.coil-preview svg' ).then( ( $previewIcon ) => {
			cy.get( '[for="coil_padlock_icon_style_bonus"] > svg' ).then( ( $icon ) => {
				const previewSvg = serializer.serializeToString( $previewIcon[ 0 ] );
				const bonusIconSvg = serializer.serializeToString( $icon[ 0 ] );
				expect( previewSvg === bonusIconSvg ).to.be.true; // eslint-disable-line no-unused-expressions
			} );
		} );

		cy
			.get( '.coil-title-preview-container' )
			.should( 'have.attr', 'data-padlock-icon-position' )
			.and( 'equal', 'after' );
	} );

	it( 'Checks that the icon options are shown or hidden depending on whether the title icon is selected', () => {
		// By default the icon is displayed to members.
		checkIconOptionsVisibility( 'show' );

		// Remove the icon from the title of exclusive posts.
		cy
			.get( '#coil_title_padlock' )
			.click();

		checkIconOptionsVisibility( 'hidden' );

		// Enable the icon to appear in the title of exclusive posts.
		cy
			.get( '#coil_title_padlock' )
			.click();

		checkIconOptionsVisibility( 'show' );
	} );

	it( 'Checks that when the title icon has been disabled that the icon options are hidden', () => {
		cy
			.get( '#coil_title_padlock' )
			.click();

		cy.get( '#submit' ).click();

		cy.reload();

		checkIconOptionsVisibility( 'hidden' );
	} );

	it( 'Checks Exclusive Icon Appearance settings can be changed', () => {
		cy
			.get( '#padlock_icon_position_after' )
			.click();

		cy
			.get( '#coil_padlock_icon_style_coil_icon' )
			.click();

		cy
			.get( '#padlock_icon_size_medium' )
			.click();

		cy
			.get( '#submit' )
			.click();

		cy
			.get( '#padlock_icon_position_after' )
			.should( 'be.checked' );

		cy
			.get( '#coil_padlock_icon_style_coil_icon' )
			.should( 'be.checked' );

		cy
			.get( '#padlock_icon_size_medium' )
			.should( 'be.checked' );
	} );
} );

/**
 * Checks the visibility status of the icon options.
 *
 * @param {String} visibilityStatus Whether the elements should be 'shown' or 'hidden'.
*/
function checkIconOptionsVisibility( visibilityStatus ) {
	let assertion;
	if ( visibilityStatus === 'show' ) {
		assertion = 'be.visible';
	} else {
		assertion = 'not.be.visible';
	}
	cy
		.get( '#coil_title_padlock + label + h4' )
		.should( 'contain', 'Icon Position' )
		.should( assertion );

	cy
		.get( '#coil_title_padlock + label + h4 + .coil-radio-group' )
		.should( assertion );

	cy
		.get( '#coil_title_padlock + label + h4 + .coil-radio-group + h4' )
		.should( 'contain', 'Icon Style' )
		.should( assertion );

	cy
		.get( '#coil_title_padlock + label + h4 + .coil-radio-group + h4 + .coil-radio-group' )
		.should( assertion );

	cy
		.get( '#coil_title_padlock + label + h4 + .coil-radio-group + h4 + .coil-radio-group + h4' )
		.should( 'contain', 'Icon Size' )
		.should( assertion );

	cy
		.get( '#coil_title_padlock + label + h4 + .coil-radio-group + h4 + .coil-radio-group + h4 + .coil-radio-group' )
		.should( assertion );

	cy
		.get( '.coil-title-preview-container' )
		.should( assertion );
}
