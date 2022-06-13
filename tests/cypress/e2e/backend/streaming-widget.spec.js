/**
 * Streaming support widget settings.
*/

describe( 'Streaming support widget settings tab', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=streaming_widget' );
	} );

	it( 'Checks streaming support widget setting defaults', () => {
		// Checks the widget text and link deafults
		checkWidgetText( '', '', '' );

		cy
			.get( '#dark_color_theme' )
			.should( 'be.checked' );

		cy
			.get( '#large_size' )
			.should( 'be.checked' );

		cy
			.get( '#streaming_widget_member_display' )
			.should( 'be.checked' );

		cy
			.get( '#position_dropdown' )
			.should( 'have.value', 'bottom-right' );

		// Checks the margin defaults
		checkWidgetMargins( '', '', '', '' );
	} );

	it( 'Checks that the member sections are shown or hidden depending on whether the streaming support widget is enabled for Coil members', () => {
		// By default the streaming support widget is displayed to members.
		checkMemberWidgetOptionsVisibility( 'show' );

		// Hide the streaming support widget from members and check that the other settings are hidden.
		cy
			.get( '#streaming_widget_member_display' )
			.click();

		checkMemberWidgetOptionsVisibility( 'hidden' );

		// Enabling the streaming support widget to members should reveal the other settings.
		cy
			.get( '#streaming_widget_member_display' )
			.click();

		checkMemberWidgetOptionsVisibility( 'show' );
	} );

	it( 'Checks that when the streaming support widget is set to hide for Coil members that the members settings are hidden', () => {
		cy
			.get( '#streaming_widget_member_display' )
			.click();

		cy.get( '#submit' ).click();

		cy.reload();

		checkMemberWidgetOptionsVisibility( 'hidden' );
	} );

	it( 'Checks the streaming support widget settings can be changed', () => {
		const widgetText = 'Coil Eyes Only';
		const widgetLink = 'https://example.com/';
		const widgetMemberText = 'Thank you for using Coil!';
		const topMargin = '0';
		const leftMargin = '5px';

		cy
			.get( '#streaming_widget_text' )
			.type( `{selectall}${ widgetText }` );
		cy
			.get( '#streaming_widget_link' )
			.type( `{selectall}${ widgetLink }` );
		cy
			.get( '#members_streaming_widget_text' )
			.type( `{selectall}${ widgetMemberText }` );

		cy
			.get( '#light_color_theme' )
			.click();

		cy
			.get( '#small_size' )
			.click();

		cy
			.get( '#streaming_widget_member_display' )
			.click();

		cy
			.get( '#position_dropdown' )
			.select( 'top-left' );

		cy
			.get( '#streaming_widget_top_margin' )
			.type( `{selectall}${ topMargin }` );

		cy
			.get( '#streaming_widget_left_margin' )
			.type( `{selectall}${ leftMargin }` );

		cy
			.get( '#submit' )
			.click();

		checkWidgetText( widgetText, widgetLink, widgetMemberText );

		cy
			.get( '#light_color_theme' )
			.should( 'be.checked' );

		cy
			.get( '#small_size' )
			.should( 'be.checked' );

		cy
			.get( '#streaming_widget_member_display' )
			.should( 'not.be.checked' );

		cy
			.get( '#position_dropdown' )
			.should( 'have.value', 'top-left' );

		checkWidgetMargins( '0px', '', '', '5px' );
	} );

	it( 'Checks streaming support widget visibility defaults', () => {
		cy
			.get( '#post_streaming_widget_visibility_show' )
			.should( 'be.checked' );

		cy
			.get( '#page_streaming_widget_visibility_show' )
			.should( 'be.checked' );
	} );

	it( 'Checks streaming support widget visibility settings can be changed', () => {
		cy
			.get( '#post_streaming_widget_visibility_hide' )
			.click();

		cy
			.get( '#page_streaming_widget_visibility_hide' )
			.click();

		cy
			.get( '#submit' )
			.click();

		cy
			.get( '#post_streaming_widget_visibility_hide' )
			.should( 'be.checked' );

		cy
			.get( '#page_streaming_widget_visibility_hide' )
			.should( 'be.checked' );
	} );
} );

/**
 * Checks the widget text and link contents in the Streaming Support Widget tab.
 *
 * @param {String} widgetText The widget's expected text.
 * @param {String} widgetLink The widget's expected link.
 * @param {String} widgetMemberText The member's widget expected text.

 */
function checkWidgetText( widgetText, widgetLink, widgetMemberText ) {
	cy
		.get( '#streaming_widget_text' )
		.should( 'have.attr', 'placeholder', 'Support us with Coil' )
		.should( 'have.value', widgetText );
	cy
		.get( '#streaming_widget_link' )
		.should( 'have.attr', 'placeholder', 'https://coil.com/' )
		.should( 'have.value', widgetLink );
	cy
		.get( '#members_streaming_widget_text' )
		.should( 'have.attr', 'placeholder', 'Thanks for your support!' )
		.should( 'have.value', widgetMemberText );
}

/**
 * Checks the widget margin contents in the Streaming Support Widget tab.
 *
 * @param {String} topMargin The widget's top margin.
 * @param {String} rightMargin The widget's right margin.
 * @param {String} bottomMargin The widget's bottom margin.
 * @param {String} leftMargin The widget's left margin.
 */
function checkWidgetMargins( topMargin, rightMargin, bottomMargin, leftMargin ) {
	cy
		.get( '#streaming_widget_top_margin' )
		.should( 'have.attr', 'placeholder', '32px' )
		.should( 'have.value', topMargin );
	cy
		.get( '#streaming_widget_right_margin' )
		.should( 'have.attr', 'placeholder', '32px' )
		.should( 'have.value', rightMargin );
	cy
		.get( '#streaming_widget_bottom_margin' )
		.should( 'have.attr', 'placeholder', '32px' )
		.should( 'have.value', bottomMargin );

	cy
		.get( '#streaming_widget_left_margin' )
		.should( 'have.attr', 'placeholder', '32px' )
		.should( 'have.value', leftMargin );
}

/**
 * Checks the visibility status of the streaming support widget settings for displaying to Coil members.
 *
 * @param {String} visibilityStatus Whether the elements should be 'shown' or 'hidden'.
*/
function checkMemberWidgetOptionsVisibility( visibilityStatus ) {
	let assertion;
	if ( visibilityStatus === 'show' ) {
		assertion = 'be.visible';
	} else {
		assertion = 'not.be.visible';
	}

	cy
		.get( '#streaming_widget_member_display + label + h4' )
		.should( 'contain', 'Message for Coil members' )
		.should( assertion );

	cy
		.get( '#members_streaming_widget_text' )
		.should( assertion );
}
