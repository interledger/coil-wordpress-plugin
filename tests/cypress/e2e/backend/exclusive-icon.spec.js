/**
 * Exclusive Icon settings.
*/

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
			.get( '#submit' )
			.click();

		cy
			.get( '#padlock_icon_position_after' )
			.should( 'be.checked' );

		cy
			.get( '#coil_padlock_icon_style_coil_icon' )
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
}
