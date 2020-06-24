describe('The Login Page', function () {
  it('sets auth cookie when logging in via form submission', function() {
    cy.logInToWordPress('admin', 'password');
    cy.visit('/wp-admin/');

    // we should be redirected to /dashboard
    cy.url().should('include', '/wp-admin')
    cy.get('.wrap > h1').should('contain', 'Dashboard');
  });
});
