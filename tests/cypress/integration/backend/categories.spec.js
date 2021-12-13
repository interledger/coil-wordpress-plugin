/**
 * Site setting / option tests.
 */

describe( 'Category Settings', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it.only( 'checks that Coil category settings are rendered correctly on the "Add Term" screen', function() {
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );

		// Create a category
		cy.get( '#tag-name' ).type( 'democategory' );
		cy.get( '#tag-slug' ).type( 'democategoryslug' );

		// The Default setting should be selected in the dropdown and the radio options should not be present
		checkMenuDefaultText('Enabled & public');

		// Checks that the radio buttons display or hide depending on the dropdown menu selection
		checkDisplayBehavior();

		// Change the default post status to "Disabled" and check it is refelected as "Default (Disabled)" in the monetization dropdown menu.
		cy.addSetting("coil_general_settings_group", [
			{
				"key": "post_monetization",
				"value": "not-monetized"
			}
		]);

		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );

		// Create a category
		cy.get( '#tag-name' ).type( 'democategory' );
		cy.get( '#tag-slug' ).type( 'democategoryslug' );

		// "Default (Disabled)" should now be the default text
		checkMenuDefaultText('Disabled');

		// Change the default post status to monetized and exclusive
		cy.addSetting("coil_general_settings_group", [
			{
				"key": "post_monetization",
				"value": "monetized"
			}
		]);
		cy.addSetting("coil_exclusive_settings_group", [
			{
				"key": "post_visibility",
				"value": "exclusive"
			}
		]);

		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );

		// Create a category
		cy.get( '#tag-name' ).type( 'democategory' );
		cy.get( '#tag-slug' ).type( 'democategoryslug' );

		// "Default (Enabled & exclusive)" should now be the default text
		checkMenuDefaultText('Enabled & exclusive');

	} );

	it( 'checks that Coil category settings are rendered correctly on the "Edit Term" screen', function() {
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );

		// Create a category
		cy.get( '#tag-name' ).type( 'democategory' );
		cy.get( '#tag-slug' ).type( 'democategoryslug' );

		// Leave it set to "Default"
		cy.get( '#submit' ).click();

		// Re-open and edit the category to check the correct status is selected
		const editCategory =
			'.row-actions span.edit a[aria-label="Edit “democategory”"]';
		cy.get( editCategory ).then( ( $element ) => {
			$element[ 0 ].click();
		} );

		// Re-open and edit the category to check the correct status is selected and change it to "Disabled".
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );
		cy.get( editCategory ).then( ( $element ) => {
			$element[ 0 ].click();
		} );

		// The Default setting should be selected in the dropdown and the radio options should not be present
		checkMenuDefaultText('Enabled & public');

		// Checks that the radio buttons display or hide depending on the dropdown menu selection
		checkDisplayBehavior();
	} );

	it( 'checks that Coil category settings can be updated', function() {
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );

		// Create a category
		cy.get( '#tag-name' ).type( 'democategory' );
		cy.get( '#tag-slug' ).type( 'democategoryslug' );

		// Make the category monetized and exclusive
		cy.get( '#monetization_dropdown' ).select('Enabled');
		cy.get( '#exclusive' ).click();
		cy.get( '#submit' ).click();

		// Re-open and edit the category to check the correct status is selected and change it to monetized and public.
		const editCategory =
			'.row-actions span.edit a[aria-label="Edit “democategory”"]';
		cy.get( editCategory ).then( ( $element ) => {
			$element[ 0 ].click();
		} );
		cy.get( '#monetization_dropdown' ).invoke('val').should('eq', 'monetized');
		cy.get( '#exclusive' ).should( 'be.checked' );
		cy.get( '#coil-visibility-settings > label[for="public"] input' ).click();
		cy.get( '.button' ).click();

		// Re-open and edit the category to check the correct status is selected and change it to "Disabled".
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );
		cy.get( editCategory ).then( ( $element ) => {
			$element[ 0 ].click();
		} );
		cy.get( '#monetization_dropdown' ).invoke('val').should('eq', 'monetized');
		cy.get( '#public' ).should( 'be.checked' );
		cy.get( '#monetization_dropdown' ).select('Disabled');
		cy.get( '.button' ).click();

		// Re-open and edit the category to check the correct status is selected
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );
		cy.get( editCategory ).then( ( $element ) => {
			$element[ 0 ].click();
		} );
		cy.get( '#monetization_dropdown' ).invoke('val').should('eq', 'not-monetized');
		cy.get('#coil-radio-selection').should('have.attr', 'style', 'display: none');
	} );
} );

/**
 * Checks the rendering of the monetization dropdown menu and the visibility radio buttons
 * When "Enabled" is selected the radio options should appear, otherwise they should be hidden
 */
 function checkDisplayBehavior() {
	 // The first value in each array is the text thet appears in the dropdown menu, e.g. 'Enabled'. It represents the item that Cypress will select.
	 // The second value in each array is the id (reflecting the the value) of the visibility radiobutton that is expected to be checked.
	 // Enabled is selected twice in this test to ensure the radio buttons reappear every time Enabled is selected.
	let settings = [
		['Enabled', 'public'],
		['Disabled', ''],
		['Enabled', 'public'],
		['Default (Enabled & public)', '']
	];
	settings.forEach((option)=>{
		cy.get( '#monetization_dropdown' ).select(option[0]);
		if( option[0] === 'Enabled') {
			cy.get('#coil-radio-selection').should('not.have.attr', 'style', 'display: none');
			cy.get( '#' + option[1] ).should( 'be.checked' );
		} else {
			cy.get('#coil-radio-selection').should('have.attr', 'style', 'display: none');
		}
	});
}

/**
 * Checks the rendering of the monetization dropdown menu
 *
 * @param defaultStatus This is the text that should appear in the dropdown menu in brackets next to "Default".
 */
 function checkMenuDefaultText(defaultStatus) {
	cy.get( '#monetization_dropdown' ).invoke('val').should('eq', 'default');
	cy.get('#monetization_dropdown').find('option:selected').should('have.text', 'Default (' + defaultStatus + ')');
}
