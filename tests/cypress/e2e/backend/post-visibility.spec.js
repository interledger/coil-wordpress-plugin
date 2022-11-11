/**
 * Coil menu in the post editor.
*/

const monetizationDropDown = '#coil-monetization-dropdown';

describe( 'Tests for visibility settings in editor', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		// Usually the below code goes here but with some bugs in WordPress version 6.1 we're using a workaround measure
		// cy.visit( '/wp-admin/post.php?post=1&action=edit' );
		// // Removal nag modal and open panel.
		// cy
		// 	.get( '.interface-complementary-area' )
		// 	.contains( 'Coil Web Monetization' )
		// 	.click( { force: true } );
	} );

	// Workaround measures for WordPress version 6.1 bugs
	it( 'Checks that the Coil Web Monetization menu has been expanded', () => {
		cy.visit( '/wp-admin/post.php?post=1&action=edit' );
		// Removal nag modal and open panel.
		cy
			.get( '.interface-complementary-area' )
			.contains( 'Coil Web Monetization' )
			.click( { force: true } );

		cy
			.get( monetizationDropDown )
			.should( 'be.visible' );
	} );

	it( 'Checks that visibility settings of a post can be changed in Gutenberg', () => {
		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		const visibilityOptions = '.coil-post-monetization-level input';
		const monetizationAndVisibilityCombinations = [
			{
				monetization: 'Enabled',
				visibility: 'public', // Enabled for Everyone
			},
			{
				monetization: 'Enabled',
				visibility: 'exclusive', // Enabled for Coil Members Only
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
					.get( visibilityOptions )
					.check( selection.visibility );
				cy
					.get( '.editor-post-publish-button' )
					.click();

				// Check the correct setting is still there
				cy
					.get( visibilityOptions + ':checked' )
					.should( 'have.value', selection.visibility );
			}
		} );
	} );

	it( 'Checks that the Default status label is correct', () => {
		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		// Initially Default text should be Enabled & Public
		cy
			.get( monetizationDropDown )
			.find( 'option:selected' )
			.should( 'have.text', 'Default (Enabled & Public)' );
	} );

	it( 'Checks that the Default status label is Enabled and Exclusive', () => {
		// Change the default post status to monetized and exclusive
		selectVisibilityStatus( 'exclusive' );
		selectMonetizationStatus( 'monetized' );
		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		// Default text should be Enabled & Exclusive
		cy
			.get( monetizationDropDown )
			.find( 'option:selected' )
			.should( 'have.text', 'Default (Enabled & Exclusive)' );
	} );

	it( 'Checks that the Default status label is Disabled', () => {
		// Change the default post status to disabled
		selectMonetizationStatus( 'not-monetized' );
		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		// Default text should be Disabled
		cy
			.get( monetizationDropDown )
			.find( 'option:selected' )
			.should( 'have.text', 'Default (Disabled)' );
	} );

	it( 'Checks that the Default status label is Enabled and Public', () => {
		// Change the default post status to monetized and public
		selectVisibilityStatus( 'public' );
		selectMonetizationStatus( 'monetized' );

		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		// Default text should be Enabled & Public
		cy
			.get( monetizationDropDown )
			.find( 'option:selected' )
			.should( 'have.text', 'Default (Enabled & Public)' );
	} );

	it( 'Checks that a warning is not displayed when exclusivity is enabled', () => {
		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		// Exclusivity is enabled by default
		// The hint should not appear if exclusivity is enabled.
		cy
			.get( monetizationDropDown )
			.select( 'monetized' );

		cy
			.contains( 'Coil Members Only' )
			.click();

		cy
			.get( '.coil-hint' )
			.should( 'not.be.visible' );
	} );

	it( 'Checks that a warning is displayed when exclusivity has been disabled', () => {
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );

		cy
			.get( '.coil-checkbox' )
			.click();

		cy.get( '#submit' ).click();

		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		// A hint should appear if exclusivity has been disabled and a post is set to be exclusive.
		cy
			.get( monetizationDropDown )
			.select( 'monetized' );

		cy
			.contains( 'Coil Members Only' )
			.click();

		cy
			.get( '.coil-hint' )
			.should( 'be.visible' );
	} );

	it( 'Checks the ECD appears in the post editor', () => {
		selectVisibilityStatus( 'exclusive' );
		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		cy
			.get( '.components-button.block-editor-inserter__toggle' )
			.click();

		cy
			.get( '.components-search-control__input' )
			.type( `{selectall}${ 'coil' }` );

		cy
			.get( '.block-editor-block-types-list__item-title' )
			.contains( 'Coil Exclusive Content Divider' )
			.should( 'be.visible' );

		cy
			.get( '.block-editor-block-types-list__item-title' )
			.click();

		cy
			.get( '.coil-exclusive-content-divider-inner' )
			.should( 'be.visible' )
			.and( 'contain', 'Exclusive content for Coil members starts below' );
	} );

	it( 'Checks the ECD contains a warning when post is set to public', () => {
		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		cy
			.get( '.components-button.block-editor-inserter__toggle' )
			.click();

		cy
			.get( '.components-search-control__input' )
			.type( `{selectall}${ 'coil' }` );

		cy
			.get( '.block-editor-block-types-list__item-title' )
			.contains( 'Coil Exclusive Content Divider' )
			.should( 'be.visible' );

		cy
			.get( '.block-editor-block-types-list__item-title' )
			.click();

		cy
			.get( '.coil-exclusive-content-divider-inner' )
			.should( 'be.visible' )
			.and( 'contain', 'Exclusive content disabled for this post. Content below is visible to everyone.' );
	} );

	it( 'Checks the ECD contains a warning when exclusivity has been disabled', () => {
		// Set the post default to exclusive and disable exclusivity globally
		selectVisibilityStatus( 'exclusive' );

		cy
			.get( '.coil-checkbox' )
			.click();

		cy.get( '#submit' ).click();

		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		// Add the ECD
		cy
			.get( '.components-button.block-editor-inserter__toggle' )
			.click();

		cy
			.get( '.components-search-control__input' )
			.type( `{selectall}${ 'coil' }` );

		cy
			.get( '.block-editor-block-types-list__item-title' )
			.contains( 'Coil Exclusive Content Divider' )
			.should( 'be.visible' );

		cy
			.get( '.block-editor-block-types-list__item-title' )
			.click();

		cy
			.get( '.coil-exclusive-content-divider-inner' )
			.should( 'be.visible' )
			.and( 'contain', 'Exclusive content disabled for this site. Content below is visible to everyone.' );
	} );
} );

/**
 * Selects the appropriate settings in the Exclusive Content tab.
 *
 * @param {String} status The posts visibility status.
*/
function selectVisibilityStatus( status ) {
	cy.visit( 'wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
	if ( status === 'exclusive' ) {
		cy.get( '#post_visibility_exclusive' ).click();
	} else {
		cy.get( '#post_visibility_public' ).click();
	}

	cy
		.get( '#submit' )
		.click();
}

/**
 * Selects the appropriate settings in the General Settings tab.
 *
 * @param {String} status The posts monetization status.
*/
function selectMonetizationStatus( status ) {
	cy.visit( 'wp-admin/admin.php?page=coil_settings&tab=general_settings' );
	if ( status === 'not-monetized' ) {
		cy.get( '#post_monetization_not-monetized' ).click();
	} else {
		cy.get( '#post_monetization_monetized' ).click();
	}

	cy
		.get( '#submit' )
		.click();
}
