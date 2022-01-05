/**
 * Welcome settings tab.
*/

describe( 'Plugin Settings Panel', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'check that admin users can access the plugins settings screen', function() {
		cy.visit( '/wp-admin/' );
		cy.get( '#adminmenu' )
			.find( 'div.wp-menu-name' )
			.contains( 'Coil' )
			.click();

		cy.get( 'div.plugin-branding > .plugin-branding' ).should( 'contain', 'Coil Web Monetization' );
	} );

	it( 'check that the "Useful links & how to guides" is visible', function() {
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=welcome' );
		cy.get( '.coil.settings-sidebar' )
			.should( 'be.visible' );

		cy.get( 'div.plugin-branding > .plugin-branding' ).should( 'contain', 'Coil Web Monetization' );
	} );
} );
