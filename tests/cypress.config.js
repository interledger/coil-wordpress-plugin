const { defineConfig } = require( 'cypress' )

module.exports = defineConfig( {
  projectId: 'qiooce',
  video: true,
  videoUploadOnPasses: false,
  viewportWidth: 1920,
  viewportHeight: 1080,
  watchForFileChanges: false,
  retries: 1,
  e2e: {
    // We've imported your old cypress plugins here.
    // You may want to clean this up later by importing these.
    setupNodeEvents( on, config ) {
      return require( './cypress/plugins/index.js' )( on, config )
    },
    baseUrl: 'http://example.local/',
    specPattern: 'cypress/e2e/**/*.{js,jsx,ts,tsx}',
  },
} )
