describe( 'Fully restricted posts', () => {
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
	} );

	// TODO fix startWebMonetization 
	// it( 'Checks that a VM enabled user can view monetized content', () => {
	// 	cy.visit( '/coil-members-only/' );
		
	// 	cy.startWebMonetization();
		
	// 	cy
	// 		.contains( 'This is a test post for the Coil Members Only state.' )
	// 		.should( 'be.visible' );

	// 	cy
	// 		.get( '.coil-message-inner' )
	// 		.should( 'not.exist' );

	// 	cy.stopWebMonetization();
	// } );
} );

/**
 * Checks the paywall title, message and button has been updated appropriately.
 *
 * @param paywallTitle The paywall's expected title.
 * @param paywallMessage The paywall's expected message.
 * @param paywallButtonText The paywall's button's expected text.
 * @param paywallButtonLink The paywall's button's expected link.
 */
function checkPaywallText( paywallTitle, paywallMessage, paywallButtonText, paywallButtonLink) {

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
		.contains( paywallButtonText )
	cy
		.get( '.coil-message-button' )
		.invoke( 'attr', 'href' )
		.should( 'eq', paywallButtonLink )
}
