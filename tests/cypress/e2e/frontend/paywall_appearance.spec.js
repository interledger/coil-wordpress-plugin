/**
 * Paywall settings.
*/

describe( 'Fully restricted posts for WM-enabled users', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/coil-members-only/' );
		cy.startWebMonetization();
	} );

	afterEach( () => {
		cy.stopWebMonetization();
	} );

	it( 'Checks that a WM enabled user can view monetized content', () => {
		cy
			.contains( 'This is a test post for the Coil Members Only state.' )
			.should( 'be.visible' );

		cy
			.get( '.coil-message-inner' )
			.should( 'not.exist' );
	} );
} );

describe( 'Fully restricted posts for non WM-enabled users', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Checks default wording on exclusive post block', () => {
		const paywallTitle = 'Keep Reading with Coil';
		const paywallMessage = 'We partnered with Coil to offer exclusive content. Access this and other great content with a Coil membership.';
		const paywallButtonText = 'Become a Coil Member';
		const paywallButtonLink = 'https://coil.com/';

		checkPaywallText( paywallTitle, paywallMessage, paywallButtonText, paywallButtonLink );
	} );

	it( 'Checks that you can edit text that appears on fully restricted posts', () => {
		const paywallTitle = 'Coil Eyes Only';
		const paywallMessage = 'With Coil we offer exclusive content.';
		const paywallButtonText = 'Learn more';
		const paywallButtonLink = 'https://example.com/';

		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );

		cy
			.get( '#coil_paywall_title' )
			.type( `{selectall}${ paywallTitle }` );
		cy
			.get( '#coil_paywall_message' )
			.type( `{selectall}${ paywallMessage }` );
		cy
			.get( '#coil_paywall_button_text' )
			.type( `{selectall}${ paywallButtonText }` );
		cy
			.get( '#coil_paywall_button_link' )
			.type( `{selectall}${ paywallButtonLink }` );
		cy
			.get( '#submit' )
			.click();

		checkPaywallText( paywallTitle, paywallMessage, paywallButtonText, paywallButtonLink );

		// If you delete custom text it reverts to the defaults
		const defaultTitle = 'Keep Reading with Coil';
		const defaultMessage = 'We partnered with Coil to offer exclusive content. Access this and other great content with a Coil membership.';
		const defaultButtonText = 'Become a Coil Member';
		const defaultButtonLink = 'https://coil.com/';

		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );

		cy
			.get( '#coil_paywall_title' )
			.clear();
		cy
			.get( '#coil_paywall_message' )
			.clear();
		cy
			.get( '#coil_paywall_button_text' )
			.clear();
		cy
			.get( '#coil_paywall_button_link' )
			.clear();
		cy
			.get( '#submit' )
			.click();

		checkPaywallText( defaultTitle, defaultMessage, defaultButtonText, defaultButtonLink );
	} );

	it( 'Checks the styling and branding of the pawyall message', () => {
		cy.visit( '/coil-members-only/' );

		// The default theme is the light theme
		cy
			.get( '.coil-message-container.coil-dark-theme' )
			.should( 'not.exist' );

		// By default the coil logo is displayed
		cy
			.get( '.coil-message-inner img' )
			.should( 'exist' );

		// By default the theme's font isn't inherited
		cy
			.get( '.coil-message-container.coil-inherit-theme-font' )
			.should( 'not.exist' );

		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );

		cy
			.get( '#dark_color_theme' )
			.click()
			.check();

		cy
			.get( '#coil_branding' )
			.select( 'Show no logo' );

		cy
			.get( '#coil_message_font' )
			.check();

		cy
			.get( '#submit' )
			.click();

		cy.visit( '/coil-members-only/' );

		cy
			.get( '.coil-message-container.coil-dark-theme' )
			.should( 'exist' );

		cy
			.get( '.coil-message-inner img' )
			.should( 'not.exist' );

		cy
			.get( '.coil-message-container.coil-inherit-theme-font' )
			.should( 'exist' );
	} );
} );

/**
 * Checks the paywall title, message and button has been updated appropriately.
 *
 * @param {String} paywallTitle The paywall's expected title.
 * @param {String} paywallMessage The paywall's expected message.
 * @param {String} paywallButtonText The paywall's button's expected text.
 * @param {String} paywallButtonLink The paywall's button's expected link.
 */
function checkPaywallText( paywallTitle, paywallMessage, paywallButtonText, paywallButtonLink ) {
	cy.visit( '/coil-members-only/' );
	cy
		.get( '.coil-message-inner' )
		.contains( paywallTitle )
		.should( 'be.visible' );
	cy
		.get( '.coil-message-inner' )
		.contains( paywallMessage )
		.should( 'be.visible' );
	cy
		.get( '.coil-message-button' )
		.contains( paywallButtonText );
	cy
		.get( '.coil-message-button' )
		.invoke( 'attr', 'href' )
		.should( 'eq', paywallButtonLink );
}
