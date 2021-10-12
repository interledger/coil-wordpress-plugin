describe( 'Learn more button', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'checks that the "Get Coil to access" button text can be changed', function() {
		cy.visit( '/wp-admin/admin.php?page=coil_settings' );

		cy.get( '#coil-messaging-settings' )
			.click();

		// Test the backend.
		const label = 'New Button Text ' + Date.now();
		cy.get( '#coil_learn_more_button_text' ).clear().type( label );
		cy.get( '#submit' ).click();

		cy.visit( '/wp-admin/admin.php?page=coil_settings' );

		cy.get( '#coil-messaging-settings' )
			.click();

		cy.get( '#coil_learn_more_button_text' ).should( 'have.value', label );

		// Test the front-end.
		cy.visit( '/coil-members-only/' );
		cy.get( '.coil-message-button' )
			.contains( label );

		cy.visit( '/wp-admin/admin.php?page=coil_settings' );

		cy.get( '#coil-messaging-settings' )
			.click();

		// Test the text will revert to its default value.
		cy.get( '#coil_learn_more_button_text' ).clear();
		cy.get( '#submit' ).click();
		cy.visit( '/coil-members-only/' );
		cy.get( '.coil-message-button' )
			.contains( 'Get Coil to access' );
	} );

	it( 'checks that the "Get Coil to access" button link can be changed', function() {
		cy.visit( '/wp-admin/admin.php?page=coil_settings' );

		cy.get( '#coil-messaging-settings' )
			.click();

		// Test the backend.
		const link = 'http://new_button_link' + Date.now() + '.com';
		cy.get( '#coil_learn_more_button_link' ).clear().type( link );
		cy.get( '#submit' ).click();

		cy.visit( '/wp-admin/admin.php?page=coil_settings' );

		cy.get( '#coil-messaging-settings' )
			.click();

		cy.get( '#coil_learn_more_button_link' ).should( 'have.value', link );

		// Test the front-end.
		cy.visit( '/coil-members-only/' );
		cy.get( '.coil-message-button' )
			.invoke( 'attr', 'href' )
			.should( 'eq', link );

		cy.visit( '/wp-admin/admin.php?page=coil_settings' );

		cy.get( '#coil-messaging-settings' )
			.click();

		// Test the link will revert to its default value.
		cy.get( '#coil_learn_more_button_link' ).clear();
		cy.get( '#submit' ).click();
		cy.visit( '/coil-members-only/' );
		cy.get( '.coil-message-button' )
			.invoke( 'attr', 'href' )
			.should( 'eq', 'https://coil.com/' );
	} );
} );

