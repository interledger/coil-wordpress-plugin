/**
 * Coil button settings.
*/

describe( 'Coil button for WM-enabled users', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=coil_button' );
	} );

	afterEach( () => {
		cy.stopWebMonetization();
	} );

	it( 'Checks the Coil button can be set to not display for Coil members', () => {
		// Set the Coil button to not display for Coil members.
		cy
			.get( '#coil_button_member_display' )
			.uncheck();

		cy
			.get( '#post_button_visibility_show' )
			.should( 'be.checked' );

		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );

		cy
			.get( '.coil-button' )
			.should( 'be.visible' );

		cy.startWebMonetization();

		cy
			.get( '.coil-button' )
			.should( 'not.exist' );
	} );

	it( 'Checks the Coil button is not shown to Coil members when it is disabled', () => {
		// Disable the Coil button.
		cy
			.get( '.coil-checkbox' )
			.click();

		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );

		cy.startWebMonetization();

		cy
			.get( '.coil-button' )
			.should( 'not.exist' );
	} );

	it( 'Checks the Coil button can have a customized message for Coil members', () => {
		// Set a custom message for Coil members.
		const buttonMemberText = 'Thank you!';

		cy
			.get( '#coil_members_button_text' )
			.type( `{selectall}${ buttonMemberText }` );
		cy
			.get( '#coil_button_member_display' )
			.should( 'be.checked' );
		cy
			.get( '#post_button_visibility_show' )
			.should( 'be.checked' );
		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );
		cy.startWebMonetization();

		cy
			.get( '.coil-button' )
			.should( 'be.visible' );
		cy
			.get( '.coil-button > a' )
			.should( 'contain', buttonMemberText );
	} );

	it( 'Checks the Coil button shows a streaming logo for Coil members', () => {
		cy.visit( '/monetized-and-public/' );
		cy.startWebMonetization();

		cy.get( '.coil-button a img' ).invoke( 'attr', 'src' ).should( 'match', /coil-icn-white-streaming.svg/ );
	} );

	it( 'Checks the Coil button displays correctly on posts that are monetized and public', () => {
		cy.visit( '/monetized-and-public/' );
		cy.startWebMonetization();
		cy
			.get( '.coil-button' )
			.should( 'be.visible' );
	} );

	it( 'Checks the Coil button displays correctly on posts that are exclusive', () => {
		cy.visit( '/coil-members-only/' );
		cy.startWebMonetization();
		cy
			.get( '.coil-button' )
			.should( 'be.visible' );
	} );

	it( 'Checks the Coil button displays correctly on posts that are split', () => {
		cy.visit( '/block-visibility/' );
		cy.startWebMonetization();
		cy
			.get( '.coil-button' )
			.should( 'be.visible' );
	} );
} );

describe( 'Coil button for non WM-enabled users', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Checks the Coil button displays correctly on posts that are not monetized', () => {
		// The Coil button doesn't display on pages which are not monetized.
		cy.visit( '/no-monetization/' );
		cy
			.get( '.coil-button' )
			.should( 'not.exist' );
	} );

	it( 'Checks the Coil button display does not display in conjunction with the paywall on exclusive posts', () => {
		cy.visit( '/coil-members-only/' );
		cy
			.get( '.coil-button' )
			.should( 'not.exist' );
	} );

	it( 'Checks the Coil button displays does not display in conjunction with the split content message', () => {
		cy.visit( '/block-visibility/' );
		cy
			.get( '.coil-button' )
			.should( 'not.exist' );
	} );

	it( 'Checks that the Coil button be can be enabled/disabled', function() {
		// Disable the Coil button.
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=coil_button' );
		cy
			.get( '.coil-checkbox' )
			.click();

		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );
		cy
			.get( '.coil-button' )
			.should( 'not.exist' );

		// Enable the Coil button and set it to display.
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=coil_button' );
		cy
			.get( '.coil-checkbox' )
			.click();
		cy
			.get( '#post_button_visibility_show' )
			.should( 'be.checked' );
		cy
			.get( '#post_button_visibility_show' )
			.should( 'be.checked' );

		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );
		cy
			.get( '.coil-button' )
			.should( 'be.visible' );
	} );

	it( 'Checks the Coil button can have a customized message and link', () => {
		// Set a custom message and link.
		const buttonText = 'Coil Eyes Only';
		const buttonLink = 'https://example.com/';

		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=coil_button' );
		cy
			.get( '#coil_button_text' )
			.type( `{selectall}${ buttonText }` );
		cy
			.get( '#coil_button_link' )
			.type( `{selectall}${ buttonLink }` );

		cy
			.get( '#post_button_visibility_show' )
			.should( 'be.checked' );
		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );

		cy
			.get( '.coil-button > a' )
			.should( 'contain', buttonText );
		cy
			.get( '.coil-button > a' )
			.should( 'have.attr', 'href', buttonLink );
	} );

	it( 'Checks the Coil button settings can be customized', () => {
		const topMargin = '0';
		const rightMargin = '10px';
		const bottomMargin = '-40';
		const leftMargin = 'abc';

		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=coil_button' );
		cy
			.get( '#light_color_theme' )
			.click();

		cy
			.get( '#small_size' )
			.click();

		cy
			.get( '#position_dropdown' )
			.select( 'top-left' );

		cy
			.get( '#coil_button_top_margin' )
			.type( `{selectall}${ topMargin }` );
		cy
			.get( '#coil_button_right_margin' )
			.type( `{selectall}${ rightMargin }` );
		cy
			.get( '#coil_button_bottom_margin' )
			.type( `{selectall}${ bottomMargin }` );
		cy
			.get( '#coil_button_left_margin' )
			.type( `{selectall}${ leftMargin }` );

		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );

		cy
			.get( '.coil-button-message-container.top.left.coil-light-theme.coil-button-small' )
			.should( 'exist' );

		cy
			.get( '.coil-button' )
			.should( 'have.attr', 'style', 'margin: 0px 42px -40px 32px;' );
	} );

	it( 'Checks the button can be hidden on a post level', () => {
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=coil_button' );

		cy
			.get( '#post_button_visibility_hide' )
			.click();

		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );

		cy
			.get( '.coil-button' )
			.should( 'not.exist' );

		// Hiding the Coil button for posts shouldn't affect their display on pages
		cy.visit( '/monetized-and-public-page/' );

		cy
			.get( '.coil-button' )
			.should( 'exist' );
	} );

	it( 'Checks that you can dissmiss the Coil button', () => {
		cy.visit( '/monetized-and-public/' );
		cy
			.get( '.coil-button' )
			.should( 'be.visible' );

		cy
			.get( '#js-coil-button-dismiss' )
			.should( 'not.be.visible' );

		cy
			.get( '.coil-button' )
			.trigger( 'mouseover' );

		cy
			.get( '#js-coil-button-dismiss' )
			.should( 'be.visible' );

		cy
			.get( '#js-coil-button-dismiss' )
			.click();

		cy
			.get( '.coil-button' )
			.should( 'not.exist' );

		cy.reload();

		cy
			.get( '.coil-button' )
			.should( 'not.exist' );

		cy
			.getCookie( 'ShowCoilButtonMsg' )
			.should( 'have.property', 'value', '1' );
	} );
} );
