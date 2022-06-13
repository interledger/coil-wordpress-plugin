/**
 * Exclusive Content settings.
*/

describe( 'Exclusive Content settings tab', () => {
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

	it( 'Checks Exclusive Post Appearance defaults', () => {
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

	it( 'Checks Exclusive Post Appearance settings can be changed', () => {
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

	it( 'Checks Exclusive Content visibility defaults', () => {
		cy
			.get( '#post_visibility_public' )
			.should( 'be.checked' );

		cy
			.get( '#page_visibility_public' )
			.should( 'be.checked' );
	} );

	it( 'Checks Exclusive Content visibility settings can be changed', () => {
		cy
			.get( '#post_visibility_exclusive' )
			.click();

		cy
			.get( '#page_visibility_exclusive' )
			.click();

		cy
			.get( '#submit' )
			.click();

		cy
			.get( '#post_visibility_exclusive' )
			.should( 'be.checked' );

		cy
			.get( '#page_visibility_exclusive' )
			.should( 'be.checked' );
	} );

	it( 'Checks Excerpt Settings defaults', () => {
		cy
			.get( '#post_excerpt' )
			.should( 'not.be.checked' );

		cy
			.get( '#page_excerpt' )
			.should( 'not.be.checked' );
	} );

	it( 'Checks Excerpt Settings can be changed', () => {
		cy
			.get( '#post_excerpt' )
			.click();

		cy
			.get( '#page_excerpt' )
			.click();

		cy
			.get( '#submit' )
			.click();

		cy
			.get( '#post_excerpt' )
			.should( 'be.checked' );

		cy
			.get( '#page_excerpt' )
			.should( 'be.checked' );
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
