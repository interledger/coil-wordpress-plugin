/**
 * Paywall settings.
*/

describe( 'Paywall settings', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
	} );

	it( 'Checks paywall appearance defaults', () => {
		// Checks the button text and link deafults
		checkTextInputs( '', '', '', '' );

		cy
			.get( '#light_color_theme' )
			.should( 'be.checked' );

		cy
			.get( '#coil_branding' )
			.should( 'have.value', 'coil_logo' );

		cy
			.get( '#coil_message_font' )
			.should( 'not.be.checked' );
	} );

	it( 'Checks the paywall appearance settings can be changed', () => {
		const title = 'Coil Eyes Only';
		const message = ' ';
		const buttonText = 'Coil Eyes Only';
		const buttonLink = 'https://example.com/';

		cy
			.get( '#coil_paywall_title' )
			.type( `{selectall}${ title }` );
		cy
			.get( '#coil_paywall_message' )
			.type( `{selectall}${ message }` );
		cy
			.get( '#coil_paywall_button_text' )
			.type( `{selectall}${ buttonText }` );
		cy
			.get( '#coil_paywall_button_link' )
			.type( `{selectall}${ buttonLink }` );

		cy
			.get( '#dark_color_theme' )
			.click();

		cy
			.get( '#coil_branding' )
			.select( 'no_logo' );

		cy
			.get( '#coil_message_font' )
			.click();

		cy
			.get( '#submit' )
			.click();

		checkTextInputs( title, message, buttonText, buttonLink );

		cy
			.get( '#dark_color_theme' )
			.should( 'be.checked' );

		cy
			.get( '#coil_branding' )
			.should( 'have.value', 'no_logo' );

		cy
			.get( '#coil_message_font' )
			.should( 'be.checked' );
	} );

	it( 'Checks that link to Appearance Settings only appears when site_logo is selected', () => {
		cy
			.get( '.set-site-logo-description' )
			.should( 'not.be.visible' );

		cy
			.get( '#coil_branding' )
			.select( 'site_logo' );

		cy
			.get( '.set-site-logo-description' )
			.should( 'be.visible' );

		cy
			.get( '#coil_branding' )
			.select( 'no_logo' );

		cy
			.get( '.set-site-logo-description' )
			.should( 'not.be.visible' );

		cy
			.get( '#coil_branding' )
			.select( 'site_logo' );
		cy
			.get( '#submit' )
			.click();

		cy
			.get( '.set-site-logo-description' )
			.should( 'be.visible' );
	} );
} );

/**
 * Checks the text input contents in the Exclusive Content tab.
 *
 * @param {String} title The paywall's expected title.
 * @param {String} message The paywall's expected text.
 * @param {String} buttonText The button's expected text.
 * @param {String} buttonLink The button's expected link.
 */
function checkTextInputs( title, message, buttonText, buttonLink ) {
	cy
		.get( '#coil_paywall_title' )
		.should( 'have.attr', 'placeholder', 'Keep Reading with Coil' )
		.should( 'have.value', title );

	cy
		.get( '#coil_paywall_message' )
		.should( 'have.attr', 'placeholder', 'We partnered with Coil to offer exclusive content. Access this and other great content with a Coil membership.' )
		.should( 'have.value', message );

	cy
		.get( '#coil_paywall_button_text' )
		.should( 'have.attr', 'placeholder', 'Become a Coil Member' )
		.should( 'have.value', buttonText );

	cy
		.get( '#coil_paywall_button_link' )
		.should( 'have.attr', 'placeholder', 'https://coil.com/' )
		.should( 'have.value', buttonLink );
}
