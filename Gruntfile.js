module.exports = function(grunt) {
	'use strict';

	var sass = require( 'node-sass' );

	require('load-grunt-tasks')(grunt);

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		// Update developer dependencies
		devUpdate: {
			packages: {
				options: {
					packageJson: null,
					packages: {
						devDependencies: true,
						dependencies: false
					},
					reportOnlyPkgs: [],
					reportUpdated: false,
					semver: true,
					updateType: 'force'
				}
			}
		},

		// SASS to CSS
		sass: {
			options: {
				implementation: sass,
				sourcemap: 'none'
			},
			dist: {
				files: {
					'assets/css/admin/coil.css' : 'assets/scss/admin.scss',
					'assets/css/frontend/coil.css' : 'assets/scss/frontend/coil.scss'
				}
			}
		},

		// Post CSS
		postcss: {
			options: {
				//map: false,
				processors: [
					require('autoprefixer')({
						overrideBrowserslist: [
							'> 0.1%',
							'ie 8',
							'ie 9'
						]
					})
				]
			},
			dist: {
				src: [
					'!assets/css/admin/*.min.css',
					'assets/css/admin/*.css',
					'!assets/css/frontend/*.min.css',
					'assets/css/frontend/*.css'
				]
			}
		},

		// Minify CSS
		cssmin: {
			options: {
				processImport: false,
				roundingPrecision: -1,
				shorthandCompacting: false
			},
			admin: {
				files: [{
					expand: true,
					cwd: 'assets/css/admin',
					src: [
						'*.css',
						'!*.min.css'
					],
					dest: 'assets/css/admin',
					ext: '.min.css'
				}]
			},
			style: {
				files: {
					'assets/css/frontend/coil.min.css': [ 'assets/css/frontend/coil.css' ],
				}
			},
			blocks: {
				files: {
					'dist/blocks.editor.build.min.css': [ 'dist/blocks.editor.build.css' ],
					'dist/blocks.style.build.min.css': [ 'dist/blocks.style.build.css' ]
				}
			}
		},

		// Minify JavaScript
		uglify: {
			options: {
				compress: {
					global_defs: {
						"EO_SCRIPT_DEBUG": false
					},
					dead_code: true
				},
				banner: '/*! <%= pkg.title %> v<%= pkg.version %> <%= grunt.template.today("dddd dS mmmm yyyy HH:MM:ss TT Z") %> */'
			},
			build: {
				files: [{
					expand: true, // Enable dynamic expansion.
					src: [
						// Admin
						'assets/js/admin/*.js',
						'!assets/js/admin/*.min.js',

						// Frontend
						'assets/js/*.js',
						'!assets/js/*.min.js',
					],
					ext: '.min.js', // Dest filepaths will have this extension.
				}]
			}
		},

		// Watch for changes made in SASS and JavaScript.
		watch: {
			css: {
				files: [
					'assets/scss/*.scss',
					'assets/scss/admin/*.scss',
					'assets/scss/frontend/*.scss',
				],
				tasks: ['sass', 'postcss']
			},
			js: {
				files: [
					// Admin
					'assets/js/admin/*.js',
					'!assets/js/admin/*.min.js',

					// Frontend
					'assets/js/*.js',
					'!assets/js/*.min.js',
				],
				tasks: [
					'jshint',
					'uglify'
				]
			}
		},

		// Check for Javascript errors with "grunt-contrib-jshint"
		// Reports provided by "jshint-stylish"
		jshint: {
			options: {
				reporter: require('jshint-stylish'),
				globals: {
					"EO_SCRIPT_DEBUG": false,
				},
				'-W099': true, // Mixed spaces and tabs
				'-W083': true, // Fix functions within loop
				'-W082': true, // Declarations should not be placed in blocks
				'-W020': true, // Read only - error when assigning EO_SCRIPT_DEBUG a value.
			},
			all: [
				// Admin
				'assets/js/admin/*.js',
				'!assets/js/admin/*.min.js',

				// Frontend
				'assets/js/*.js',
				'!assets/js/*.min.js'
			]
		},

		// Check for Sass errors with "stylelint"
		stylelint: {
			options: {
				configFile: '.stylelintrc'
			},
			all: [
				'assets/scss/**/*.scss',
			]
		},

		// Generate .pot file
		makepot: {
			target: {
				options: {
					cwd: '',
					domainPath: 'languages',                                  // Where to save the POT file.
					exclude: [
						'releases',
						'node_modules'
					],
					mainFile: '<%= pkg.name %>.php', // Main project file.
					potComments: 'Copyright (c) {year} \nThis file is distributed under the same license as the Coil for WordPress package.', // The copyright at the beginning of the POT file.
					potFilename: '<%= pkg.name %>.pot', // Name of the POT file.
					potHeaders: {
						'poedit': true,                                       // Includes common Poedit headers.
						'x-poedit-keywordslist': true,                        // Include a list of all possible gettext functions.
						'Report-Msgid-Bugs-To': 'https://bitbucket.org/pragmaticweb/coil-for-wp/issues',
						'language-team': 'Sébastien Dumont <sebastien@pragmatic.agency>',
						'language': 'en_US'
					},
					processPot: function( pot ) {
						var translation,
							excluded_meta = [
								'Plugin Name of the plugin/theme',
								'Plugin URI of the plugin/theme',
								'Description of the plugin/theme',
								'Author of the plugin/theme',
								'Author URI of the plugin/theme'
							];

						for ( translation in pot.translations[''] ) {
							if ( 'undefined' !== typeof pot.translations[''][ translation ].comments.extracted ) {
								if ( excluded_meta.indexOf( pot.translations[''][ translation ].comments.extracted ) >= 0 ) {
									console.log( 'Excluded meta: ' + pot.translations[''][ translation ].comments.extracted );
									delete pot.translations[''][ translation ];
								}
							}
						}

						return pot;
					},
					type: 'wp-plugin',                                        // Type of project.
					updateTimestamp: true,                                    // Whether the POT-Creation-Date should be updated without other changes.
				}
			}
		},

		// Check strings for localization issues
		checktextdomain: {
			options:{
				text_domain: '<%= pkg.name %>', // Project text domain.
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src:  [
					'*.php',
					'**/*.php', // Include all files
					'!node_modules/**' // Exclude node_modules/
				],
				expand: true
			},
		},

		potomo: {
			dist: {
				options: {
					poDel: false
				},
				files: [{
					expand: true,
					cwd: 'languages',
					src: ['*.po'],
					dest: 'languages',
					ext: '.mo',
					nonull: false
				}]
			}
		},

		// Bump version numbers (replace with version in package.json)
		replace: {
			php: {
				src: [ '<%= pkg.name %>.php' ],
				overwrite: true,
				replacements: [
					{
						from: /Description:.*$/m,
						to: "Description: <%= pkg.description %>"
					},
					{
						from: /Version:.*$/m,
						to: "Version: <%= pkg.version %>"
					},
					{
						from: /public static \$version = \'.*.'/m,
						to: "public static $version = '<%= pkg.version %>'"
					}
				]
			},
			readme: {
				src: [
					'readme.txt',
					'README.md'
				],
				overwrite: true,
				replacements: [
					{
						from: /Requires at least:(\*\*|)(\s*?)[0-9.-]+(\s*?)$/mi,
						to: 'Requires at least:$1$2<%= pkg.requires %>$3'
					},
					{
						from: /Requires PHP:(\*\*|)(\s*?)[0-9.-]+(\s*?)$/mi,
						to: 'Requires PHP:$1$2<%= pkg.requires_php %>$3'
					},
					{
						from: /Stable tag:(\*\*|)(\s*?)[0-9.-]+(\s*?)$/mi,
						to: 'Stable tag:$1$2<%= pkg.version %>$3'
					},
					{
						from: /Tested up to:(\*\*|)(\s*?)[0-9.-]+(\s*?)$/mi,
						to: 'Tested up to:$1$2<%= pkg.tested_up_to %>$3'
					},
				]
			}
		},

		// Copies the plugin to create deployable plugin.
		copy: {
			build: {
				files: [
					{
						expand: true,
						src: [
							'**',
							'!.*',
							'!**/*.{gif,jpg,jpeg,json,log,md,sh,txt,xml,zip}',
							'!.*/**',
							'!.DS_Store',
							'!.htaccess',
							'!<%= pkg.name %>-git/**',
							'!<%= pkg.name %>-svn/**',
							'!github/**',
							'!node_modules/**',
							'!releases/**',
							'readme.txt'
						],
						dest: 'build/',
						dot: true
					}
				]
			}
		},

		// Compresses the deployable plugin folder.
		compress: {
			zip: {
				options: {
					archive: './releases/<%= pkg.name %>-v<%= pkg.version %>.zip',
					mode: 'zip'
				},
				files: [
					{
						expand: true,
						cwd: './build/',
						src: '**',
						dest: '<%= pkg.name %>'
					}
				]
			}
		},

		// Deletes the deployable plugin folder once zipped up.
		clean: {
			build: [ 'build/' ]
		}
	});

	// Set the default grunt command to run test cases.
	grunt.registerTask( 'default', [ 'test' ] );

	// Checks for developer dependence updates.
	grunt.registerTask( 'check', [ 'devUpdate' ] );

	// Checks for errors.
	grunt.registerTask( 'test', [ 'jshint', 'stylelint', 'checktextdomain' ]);

	// Build CSS only.
	grunt.registerTask( 'css', [ 'sass', 'postcss', 'cssmin' ] );

	// Build CSS, minify CSS and runs i18n tasks.
	grunt.registerTask( 'build', [ 'sass', 'postcss', 'cssmin', 'uglify', 'update-pot' ]);

	// Update version of plugin.
	grunt.registerTask( 'version', [ 'replace' ] );

	/**
	 * Run i18n related tasks.
	 *
	 * This includes extracting translatable strings, updating the master pot file.
	 * If this is part of a deploy process, it should come before zipping everything up.
	 */
	grunt.registerTask( 'update-pot', [ 'checktextdomain', 'makepot' ]);

	/**
	 * Creates a deployable plugin zipped up ready to upload
	 * and install on a WordPress installation.
	 */
	grunt.registerTask( 'zip', [ 'copy:build', 'compress', 'clean:build' ]);
};
