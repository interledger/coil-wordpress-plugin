/**
 * sStreaming support widget settings.
*/

describe( 'Streaming support widget for WM-enabled users', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=streaming_widget' );
	} );

	afterEach( () => {
		cy.stopWebMonetization();
	} );

	it( 'Checks the streaming support widget can be set to not display for Coil members', () => {
		// Set the streaming support widget to not display for Coil members.
		cy
			.get( '#streaming_widget_member_display' )
			.uncheck();

		cy
			.get( '#post_streaming_widget_visibility_show' )
			.should( 'be.checked' );

		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );

		cy
			.get( '.streaming-widget' )
			.should( 'be.visible' );

		cy.startWebMonetization();

		cy
			.get( '.streaming-widget' )
			.should( 'not.exist' );
	} );

	it( 'Checks the streaming support widget is not shown to Coil members when it is disabled', () => {
		// Disable the streaming support widget.
		cy
			.get( '.coil-checkbox' )
			.click();

		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );

		cy.startWebMonetization();

		cy
			.get( '.streaming-widget' )
			.should( 'not.exist' );
	} );

	it( 'Checks the streaming support widget can have a customized message for Coil members', () => {
		// Set a custom message for Coil members.
		const widgetMemberText = 'Thank you!';

		cy
			.get( '#members_streaming_widget_text' )
			.type( `{selectall}${ widgetMemberText }` );
		cy
			.get( '#streaming_widget_member_display' )
			.should( 'be.checked' );
		cy
			.get( '#post_streaming_widget_visibility_show' )
			.should( 'be.checked' );
		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );
		cy.startWebMonetization();

		cy
			.get( '.streaming-widget' )
			.should( 'be.visible' );
		cy
			.get( '.streaming-widget > a' )
			.should( 'contain', widgetMemberText );
	} );

	it( 'Checks the streaming support widget shows a streaming logo for Coil members', () => {
		cy.visit( '/monetized-and-public/' );
		cy.startWebMonetization();

		cy.get( '.streaming-widget a img' ).invoke( 'attr', 'src' ).should( 'match', /coil-icn-white-streaming.svg/ );
	} );

	it( 'Checks the streaming support widget displays correctly on posts that are monetized and public', () => {
		cy.visit( '/monetized-and-public/' );
		cy.startWebMonetization();
		cy
			.get( '.streaming-widget' )
			.should( 'be.visible' );
	} );

	it( 'Checks the streaming support widget displays correctly on posts that are exclusive', () => {
		cy.visit( '/coil-members-only/' );
		cy.startWebMonetization();
		cy
			.get( '.streaming-widget' )
			.should( 'be.visible' );
	} );
} );

describe( 'Streaming support widget for non WM-enabled users', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Checks the streaming support widget displays correctly on posts that are not monetized', () => {
		// The streaming support widget doesn't display on pages which are not monetized.
		cy.visit( '/no-monetization/' );
		cy
			.get( '.streaming-widget' )
			.should( 'not.exist' );
	} );

	it( 'Checks the streaming support widget display does not display in conjunction with the paywall on exclusive posts', () => {
		cy.visit( '/coil-members-only/' );
		cy
			.get( '.streaming-widget' )
			.should( 'not.exist' );
	} );

	it( 'Checks that the streaming support widget be can be enabled/disabled', function() {
		// Disable the streaming support widget.
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=streaming_widget' );
		cy
			.get( '.coil-checkbox' )
			.click();

		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );
		cy
			.get( '.streaming-widget' )
			.should( 'not.exist' );

		// Enable the streaming support widget and set it to display.
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=streaming_widget' );
		cy
			.get( '.coil-checkbox' )
			.click();
		cy
			.get( '#post_streaming_widget_visibility_show' )
			.should( 'be.checked' );
		cy
			.get( '#post_streaming_widget_visibility_show' )
			.should( 'be.checked' );

		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );
		cy
			.get( '.streaming-widget' )
			.should( 'be.visible' );
	} );

	it( 'Checks the streaming support widget can have a customized message and link', () => {
		// Set a custom message and link.
		const widgetText = 'Coil Eyes Only';
		const widgetLink = 'https://example.com/';

		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=streaming_widget' );
		cy
			.get( '#streaming_widget_text' )
			.type( `{selectall}${ widgetText }` );
		cy
			.get( '#streaming_widget_link' )
			.type( `{selectall}${ widgetLink }` );

		cy
			.get( '#post_streaming_widget_visibility_show' )
			.should( 'be.checked' );
		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );

		cy
			.get( '.streaming-widget > a' )
			.should( 'contain', widgetText );
		cy
			.get( '.streaming-widget > a' )
			.should( 'have.attr', 'href', widgetLink );
	} );

	it( 'Checks the streaming support widget settings can be customized', () => {
		const bottomMargin = '-40';
		const leftMargin = 'abc';

		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=streaming_widget' );
		cy
			.get( '#light_color_theme' )
			.click();

		cy
			.get( '#small_size' )
			.click();

		cy
			.get( '#position_dropdown' )
			.select( 'bottom-left' );

		cy
			.get( '#streaming_widget_bottom_margin' )
			.type( `{selectall}${ bottomMargin }` );
		cy
			.get( '#streaming_widget_left_margin' )
			.type( `{selectall}${ leftMargin }` );

		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );

		cy
			.get( '.streaming-widget-container.bottom.left.coil-light-theme.streaming-widget-small' )
			.should( 'exist' );

		cy
			.get( '.streaming-widget' )
			.should( 'have.attr', 'style', 'margin: 0px 0px -40px 32px;' );
	} );

	it( 'Checks the streaming support widget can be hidden on a post level', () => {
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=streaming_widget' );

		cy
			.get( '#post_streaming_widget_visibility_hide' )
			.click();

		cy
			.get( '#submit' )
			.click();

		cy.visit( '/monetized-and-public/' );

		cy
			.get( '.streaming-widget' )
			.should( 'not.exist' );

		// Hiding the streaming support widget for posts shouldn't affect their display on pages
		cy.visit( '/monetized-and-public-page/' );

		cy
			.get( '.streaming-widget' )
			.should( 'exist' );
	} );

	it( 'Checks that you can dissmiss the streaming support widget', () => {
		cy.visit( '/monetized-and-public/' );
		cy
			.get( '.streaming-widget' )
			.should( 'be.visible' );

		cy
			.get( '#js-streaming-widget-dismiss' )
			.should( 'not.be.visible' );

		cy
			.get( '.streaming-widget' )
			.trigger( 'mouseover' );

		cy
			.get( '#js-streaming-widget-dismiss' )
			.should( 'be.visible' );

		cy
			.get( '#js-streaming-widget-dismiss' )
			.click();

		cy
			.get( '.streaming-widget' )
			.should( 'not.exist' );

		cy.reload();

		cy
			.get( '.streaming-widget' )
			.should( 'not.exist' );

		cy
			.getCookie( 'ShowStreamingWidgetMsg' )
			.should( 'have.property', 'value', '1' );
	} );
} );
