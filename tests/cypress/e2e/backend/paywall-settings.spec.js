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

	it( 'Checks the paywall title and message input can be blank', () => {
		cy
			.get( '#coil_paywall_title' )
			.type( `{selectall}${ ' ' }` )
			.blur();
		cy
			.get( '#coil_paywall_message' )
			.type( `{selectall}${ ' ' }` )
			.blur();

		// Title and message can be blank
		cy.checkForInvalidAlert( false, '#coil_paywall_title' );
		cy.checkForInvalidAlert( false, '#coil_paywall_message' );
	} );

	it( 'Checks the paywall button text cannot be blank', () => {
		cy
			.get( '#coil_paywall_button_text' )
			.type( `{selectall}${ ' ' }` )
			.blur();

		// Button text cannot be blank
		cy.checkForInvalidAlert( true, '#coil_paywall_button_text' );
	} );

	it( 'Checks the paywall button link is validated', () => {
		cy
			.get( '#coil_paywall_button_link' )
			.type( `{selectall}${ ' ' }` )
			.blur();

		// Button link cannot be white space
		cy.checkForInvalidAlert( true, '#coil_paywall_button_link' );

		cy
			.get( '#coil_paywall_button_link' )
			.clear();

		cy.checkForInvalidAlert( false, '#coil_paywall_button_link' );

		cy
			.get( '#coil_paywall_button_link' )
			.type( `{selectall}${ 'a.' }` )
			.blur();

		// Button link must have something dot something
		cy.checkForInvalidAlert( true, '#coil_paywall_button_link' );

		cy
			.get( '#coil_paywall_button_link' )
			.type( 'a' )
			.blur();

		cy.checkForInvalidAlert( false, '#coil_paywall_button_link' );
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

	it( 'Checks the paywall preview defaults', () => {
		cy
			.get( '.coil-paywall-container h3' )
			.should( 'contain', 'Keep Reading with Coil' );

		cy
			.get( '.coil-paywall-container p' )
			.should( 'contain', 'We partnered with Coil to offer exclusive content. Access this and other great content with a Coil membership.' );

		cy
			.get( '.coil-paywall-container a' )
			.should( 'contain', 'Become a Coil Member' );

		cy
			.get( '.coil-paywall-container' )
			.should( 'have.attr', 'data-theme' )
			.and( 'equal', 'light' );

		cy
			.get( '.coil-paywall-container img.coil_logo' )
			.should( 'exist' );
	} );

	it( 'Checks that the paywall preview reacts correctly', () => {
		const title = 'Coil Eyes Only';
		const message = ' ';
		const buttonText = 'Coil Eyes Only';

		cy.addSetting( 'coil_exclusive_settings_group', [
			{
				key: 'coil_paywall_title',
				value: title,
			},
			{
				key: 'coil_paywall_message',
				value: message,
			},
			{
				key: 'coil_paywall_button_text',
				value: buttonText,
			},
			{
				key: 'coil_message_color_theme',
				value: 'dark',
			},
			{
				key: 'coil_message_branding',
				value: 'no_logo',
			},
		] );

		cy.reload();

		cy
			.get( '.coil-paywall-container h3' )
			.should( 'contain', title );

		cy
			.get( '.coil-paywall-container p' )
			.should( 'contain', message );

		cy
			.get( '.coil-paywall-container a' )
			.should( 'contain', buttonText );

		cy
			.get( '.coil-paywall-container' )
			.should( 'have.attr', 'data-theme' )
			.and( 'equal', 'dark' );

		cy
			.get( '.coil-paywall-container img.no_logo' )
			.should( 'have.attr', 'style', 'display: none;' );
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
