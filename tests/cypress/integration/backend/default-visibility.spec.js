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
		cy.visit( '/default-monetization-post/' );
		cy.get( 'body' ).should( 'have.class', 'coil-exclusive' );
		cy.get( 'body' ).should( 'have.class', 'coil-monetized' );
		cy.visit( '/default-monetization-page/' );
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
		cy.visit( '/default-monetization-post/' );
		cy.get( 'body' ).should( 'have.class', 'coil-public' );
		cy.get( 'body' ).should( 'have.class', 'coil-monetized' );
		cy.visit( '/default-monetization-page/' );
		cy.get( 'body' ).should( 'have.class', 'coil-public' );
		cy.get( 'body' ).should( 'have.class', 'coil-monetized' );
	} );

	it( 'Checks that when visibility is set to exclusive that monetization is forced to be enabled', () => {
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
		cy.visit( '/default-monetization-post/' );
		cy.get( 'head meta[name="monetization"]' ).should( 'exist' );
		cy.get( 'body' ).should( 'have.class', 'coil-exclusive' );
		cy.get( 'body' ).should( 'have.class', 'coil-monetized' );
		cy.visit( '/default-monetization-page/' );
		cy.get( 'head meta[name="monetization"]' ).should( 'exist' );
		cy.get( 'body' ).should( 'have.class', 'coil-exclusive' );
		cy.get( 'body' ).should( 'have.class', 'coil-monetized' );
	} );
} );
