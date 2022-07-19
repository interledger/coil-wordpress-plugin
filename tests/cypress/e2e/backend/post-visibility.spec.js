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

	it( 'Checks that a warning is displayed when exclusivity has been disabled', () => {
		cy.addSetting( 'coil_exclusive_settings_group', [
			{
				key: 'coil_exclusive_toggle',
				value: '0',
			},
		] );

		cy.reload();

		// A hint should appear if exclusivity has been disabled and a post is set to be exclusive.
		cy
			.get( '#coil-monetization-dropdown' )
			.select( 'monetized' );

		cy
			.contains( 'Coil Members Only' )
			.click();

		cy
			.get( '.coil-hint' )
			.should( 'be.visible' );

		cy.addSetting( 'coil_exclusive_settings_group', [
			{
				key: 'coil_exclusive_toggle',
				value: '1',
			},
		] );

		cy.reload();

		// The hint should not appear if exclusivity is enabled.
		cy
			.get( '#coil-monetization-dropdown' )
			.select( 'monetized' );

		cy
			.contains( 'Coil Members Only' )
			.click();

		cy
			.get( '.coil-hint' )
			.should( 'not.be.visible' );
	} );

	it( 'Checks the ECD appears in the post editor', () => {
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
} );
