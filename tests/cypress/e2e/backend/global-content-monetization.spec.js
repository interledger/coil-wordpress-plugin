/**
 * Setting default monetization statuses.
*/

describe( 'Default monetization settings for pages and posts', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Checks that the default monetization is preset to enabled', () => {
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=general_settings' );
		cy
			.get( '#post_monetization_monetized' )
			.should( 'be.checked' );

		cy
			.get( '#page_monetization_monetized' )
			.should( 'be.checked' );
	} );

	it( 'Checks that the default can be set to different values which reflect correctly in the frontend', () => {
		// Disable monetization on pages and posts
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=general_settings' );
		cy
			.get( '#post_monetization_not-monetized' )
			.check();

		cy
			.get( '#page_monetization_not-monetized' )
			.check();
		cy
			.get( '#submit' )
			.click();

		// Check there is no monetization meta tag when monetization is diasabled.
		cy.visit( '/default-status-post/' );
		cy.get( 'head meta[name="monetization"]' ).should( 'not.exist' );
		cy.visit( '/default-status-page/' );
		cy.get( 'head meta[name="monetization"]' ).should( 'not.exist' );

		// Set the monetization on pages and posts to enabled
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=general_settings' );
		cy
			.get( '#post_monetization_monetized' )
			.check();

		cy
			.get( '#page_monetization_monetized' )
			.check();
		cy
			.get( '#submit' )
			.click();

		// Check that the correct body class has been added.
		cy.visit( '/default-status-post/' );
		cy.get( 'head meta[name="monetization"]' ).should( 'exist' );
		cy.get( 'body' ).should( 'have.class', 'coil-monetized' );
		cy.visit( '/default-status-page/' );
		cy.get( 'head meta[name="monetization"]' ).should( 'exist' );
		cy.get( 'body' ).should( 'have.class', 'coil-monetized' );
	} );

	it( 'Checks that when monetization is disabled that visibility is forced to be public', () => {
		// Make content on pages and posts exclusive
		cy.addSetting( 'coil_exclusive_settings_group', [
			{
				key: 'post_visibility',
				value: 'exclusive',
			},
			{
				key: 'page_visibility',
				value: 'exclusive',
			},
		] );

		// Disable monetization on pages and posts
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=general_settings' );
		cy
			.get( '#post_monetization_not-monetized' )
			.check();

		cy
			.get( '#page_monetization_not-monetized' )
			.check();
		cy
			.get( '#submit' )
			.click();

		cy.on( 'window:confirm', ( text ) => {
			expect( text ).to.contains( 'Removing monetization from posts and pages will set them as public by default.' );
		} );

		// Check that the visibility defaults have been changed to public
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
		cy
			.get( '#post_visibility_public' )
			.should( 'be.checked' );

		cy
			.get( '#page_visibility_public' )
			.should( 'be.checked' );

		// Check there is no monetization meta tag when monetization is diasabled
		// and that the coil-exclusive class wasn't added
		cy.visit( '/default-status-post/' );
		cy.get( 'head meta[name="monetization"]' ).should( 'not.exist' );
		cy.get( 'body' ).should( 'not.have.class', 'coil-exclusive' );
		cy.visit( '/default-status-page/' );
		cy.get( 'head meta[name="monetization"]' ).should( 'not.exist' );
		cy.get( 'body' ).should( 'not.have.class', 'coil-exclusive' );
	} );
} );
