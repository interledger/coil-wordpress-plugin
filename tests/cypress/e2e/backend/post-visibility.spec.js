/**
 * Coil menu in the post editor.
*/

describe( 'Tests for visibility settings in editor', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		// Removal nag modal and open panel.
		cy
			.get( '.interface-complementary-area' )
			.contains( 'Coil Web Monetization' )
			.click( { force: true } );
	} );

	it( 'Checks that visibility settings of a post can be changed in Gutenberg', () => {
		const monetizationDropDown = '#coil-monetization-dropdown';
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
		const monetizationDropDown = '#coil-monetization-dropdown';

		// Initially Default text should be Enabled & Public
		cy
			.get( monetizationDropDown )
			.find( 'option:selected' )
			.should( 'have.text', 'Default (Enabled & Public)' );

		// Change the default post status to monetized and exclusive
		cy.addSetting( 'coil_general_settings_group', [
			{
				key: 'post_monetization',
				value: 'monetized',
			},
		] );
		cy.addSetting( 'coil_exclusive_settings_group', [
			{
				key: 'post_visibility',
				value: 'exclusive',
			},
		] );
		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		// Default text should be Enabled & Exclusive
		cy
			.get( monetizationDropDown )
			.find( 'option:selected' )
			.should( 'have.text', 'Default (Enabled & Exclusive)' );

		// Change the default post status to disabled
		cy.addSetting( 'coil_general_settings_group', [
			{
				key: 'post_monetization',
				value: 'not-monetized',
			},
		] );

		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		// Default text should be Disabled
		cy
			.get( monetizationDropDown )
			.find( 'option:selected' )
			.should( 'have.text', 'Default (Disabled)' );

		// Change the default post status to monetized and public
		cy.addSetting( 'coil_general_settings_group', [
			{
				key: 'post_monetization',
				value: 'monetized',
			},
		] );
		cy.addSetting( 'coil_exclusive_settings_group', [
			{
				key: 'post_visibility',
				value: 'public',
			},
		] );

		cy.visit( '/wp-admin/post.php?post=1&action=edit' );

		// Default text should be Enabled & Public
		cy
			.get( monetizationDropDown )
			.find( 'option:selected' )
			.should( 'have.text', 'Default (Enabled & Public)' );
	} );
} );
