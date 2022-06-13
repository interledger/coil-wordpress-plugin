/**
 * Setting default visibility statuses.
*/

describe( 'Default  visibility settings for pages and posts', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Checks that the default visibility is preset to public', () => {
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
		cy
			.get( '#post_visibility_public' )
			.should( 'be.checked' );

		cy
			.get( '#page_visibility_public' )
			.should( 'be.checked' );
	} );

	it( 'Checks that the default can be set to different values which reflect correctly in the frontend', () => {
		// Make content on pages and posts exclusive
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
		cy
			.get( '#post_visibility_exclusive' )
			.check();

		cy
			.get( '#page_visibility_exclusive' )
			.check();
		cy
			.get( '#submit' )
			.click();

		// Check that the correct body class has been added.
		cy.visit( '/default-status-post/' );
		cy.get( 'body' ).should( 'have.class', 'coil-exclusive' );
		cy.get( 'body' ).should( 'have.class', 'coil-monetized' );
		cy.visit( '/default-status-page/' );
		cy.get( 'body' ).should( 'have.class', 'coil-exclusive' );
		cy.get( 'body' ).should( 'have.class', 'coil-monetized' );

		// Make content on pages and posts public
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
		cy
			.get( '#post_visibility_public' )
			.check();

		cy
			.get( '#page_visibility_public' )
			.check();
		cy
			.get( '#submit' )
			.click();

		// Check that the correct body class has been added.
		cy.visit( '/default-status-post/' );
		cy.get( 'body' ).should( 'have.class', 'coil-public' );
		cy.get( 'body' ).should( 'have.class', 'coil-monetized' );
		cy.visit( '/default-status-page/' );
		cy.get( 'body' ).should( 'have.class', 'coil-public' );
		cy.get( 'body' ).should( 'have.class', 'coil-monetized' );
	} );

	it( 'Checks that when visibility is set to exclusive that monetization is forced to be enabled', () => {
		// Disable monetization on pages and posts
		cy.addSetting( 'coil_general_settings_group', [
			{
				key: 'coil_payment_pointer',
				value: 'https:\/\/example.com\/.well-known\/pay',
			},
			{
				key: 'post_monetization',
				value: 'not-monetized',
			},
			{
				key: 'page_monetization',
				value: 'not-monetized',
			},
		] );

		// Make content on pages and posts exclusive
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
		cy
			.get( '#post_visibility_exclusive' )
			.check();

		cy
			.get( '#page_visibility_exclusive' )
			.check();
		cy
			.get( '#submit' )
			.click();

		cy.on( 'window:confirm', ( text ) => {
			expect( text ).to.contains( 'Making posts and pages exclusive will also set them as monetized by default.' );
		} );

		// Check that monetization has been enabled
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=general_settings' );
		cy
			.get( '#post_monetization_monetized' )
			.should( 'be.checked' );

		cy
			.get( '#page_monetization_monetized' )
			.should( 'be.checked' );

		// Check there is a monetization meta tag because monetization should be enabled
		// and that the coil-exclusive class should be added
		cy.visit( '/default-status-post/' );
		cy.get( 'head meta[name="monetization"]' ).should( 'exist' );
		cy.get( 'body' ).should( 'have.class', 'coil-exclusive' );
		cy.get( 'body' ).should( 'have.class', 'coil-monetized' );
		cy.visit( '/default-status-page/' );
		cy.get( 'head meta[name="monetization"]' ).should( 'exist' );
		cy.get( 'body' ).should( 'have.class', 'coil-exclusive' );
		cy.get( 'body' ).should( 'have.class', 'coil-monetized' );
	} );
} );
