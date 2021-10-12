describe( 'Tests for visibility settings in editor', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Checks that visibility settings of a post can be changed in Gutenberg', () => {
		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		// Removal nag modal and open panel.
		cy
			.get( '.interface-complementary-area' )
			.contains( 'Coil Web Monetization' )
			.click( { force: true } );

		const monetizationDropDown = '#inspector-select-control-1';
		const monetizationAndVisibilityCombinations = [
			{
				monetization: 'Enabled',
				visibility: '#inspector-radio-control-0-2', // Enabled with Split content
			},
			{
				monetization: 'Enabled',
				visibility: '#inspector-radio-control-0-0', // Enabled for Everyone
			},
			{
				monetization: 'Enabled',
				visibility: '#inspector-radio-control-0-1', // Enabled for Coil Members Only
			},
			{
				monetization: 'Disabled',
			},
		];
		monetizationAndVisibilityCombinations.forEach( ( selection ) => {
			cy
				.get( monetizationDropDown )
				.select( selection.monetization );
			if ( selection.monetization === 'Disabled' ) {
				cy
					.get( '.editor-post-publish-button' )
					.click();

				// Check the correct setting is still there
				cy
					.get( monetizationDropDown )
					.should( 'contain', selection.monetization );
			} else {
				cy
					.get( selection.visibility )
					.check();
				cy
					.get( '.editor-post-publish-button' )
					.click();

				// Check the correct setting is still there
				cy
					.get( selection.visibility )
					.should( 'be.checked' );
			}
		} );
	} );
} );
