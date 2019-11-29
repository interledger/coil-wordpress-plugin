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
					'assets/css/frontend/coil.css' : 'assets/scss/frontend/coil.scss',
					'assets/css/messages/coil.css' : 'assets/scss/messages/coil.scss'
				}
			}
		},

		// Post CSS
		postcss: {
			options: {
				//map: false,
				processors: [
					require('autoprefixer')()
				]
			},
			dist: {
				src: [
					'!assets/css/admin/*.min.css',
					'assets/css/admin/*.css',
					'!assets/css/frontend/*.min.css',
					'assets/css/frontend/*.css',
					'!assets/css/messages/*.min.css',
					'assets/css/messages/*.css'
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
					'assets/css/messages/coil.min.css': [ 'assets/css/messages/coil.css' ],
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
					'assets/scss/messages/*.scss',
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
				// Frontend
				'assets/js/*.js',
				'!assets/js/*.min.js',
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
					'**/*.php',
					'!node_modules/**',
					'!vendor/**',
					'!languages/**',
					'!tests/**',
					'!build/**',
					'!releases/**',
				],
				expand: true
			},
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
							'!<%= pkg.name %>-git/**',
							'!<%= pkg.name %>-svn/**',
							'!node_modules/**',
							'!vendor/**',
							'!build/**',
							'!releases/**',
							'!tests/**',
							'!bitbucket-pipelines.yml',
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
					archive: './releases/<%= pkg.name %>.zip',
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
	grunt.registerTask( 'test', [ 'jshint', 'stylelint', 'checktextdomain' ] );

	// Build CSS only.
	grunt.registerTask( 'css', [ 'sass', 'postcss', 'cssmin' ] );

	// Build and minify CSS/JS.
	grunt.registerTask( 'build', [ 'sass', 'postcss', 'cssmin', 'uglify' ] );

	// Update version of plugin.
	grunt.registerTask( 'version', [ 'replace' ] );

	/**
	 * Creates a deployable plugin zipped up ready to upload
	 * and install on a WordPress installation.
	 */
	grunt.registerTask( 'zip', [ 'copy:build', 'compress', 'clean:build' ] );
};
