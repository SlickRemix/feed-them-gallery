/* jshint node:true */
'use strict';

module.exports = function( grunt ) {

	// auto load grunt tasks
	require( 'load-grunt-tasks' )( grunt );

	var pluginConfig = {

		// gets the package vars
		pkg: grunt.file.readJSON( 'package.json' ),

		// plugin directories
		dirs: {
			main: {
				lang: 'languages'
			}
		},

		// pot file
		makepot: {
			target: {
				options: {
					domainPath: '<%= dirs.main.lang %>/',    // Where to save the POT file.
					exclude: ['build/.*'],
					mainFile: 'feed-them-gallery.php',    // Main project file.
					potFilename: 'feed-them-gallery.pot',    // Name of the POT file.
					potHeaders: {
						poedit: true,                 // Includes common Poedit headers.
						'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
								},
					type: 'wp-plugin',    // Type of project (wp-plugin or wp-theme).
					updateTimestamp: true,    // Whether the POT-Creation-Date should be updated without other changes.
					processPot: function( pot ) {
						pot.headers['report-msgid-bugs-to'] = 'https://slickremix.com/';
						pot.headers['last-translator'] = 'WP-Translations (http://wp-translations.org/)';
						pot.headers['language-team'] = 'WP-Translations <wpt@wp-translations.org>';
						pot.headers.language = 'en_US';
						var translation, // Exclude meta data from pot.
							excluded_meta = [
								'SlickRemix',
								'https://slickremix.com',
								'SlickRemix',
								'https://slickremix.com'
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
					}
				}
			}
		},

		// checktextdomain
		checktextdomain: {
			options:{
				text_domain: 'feed-them-gallery',
				create_report_file: false,
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
					'_n:1,2,3,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d',
					' __ngettext:1,2,3d',
					'__ngettext_noop:1,2,3d',
					'_c:1,2d',
					'_nc:1,2,4c,5d'
					]
			},
			files: {
				src: [
					'**/*.php', // Include all files
					'!node_modules/**', // Exclude node_modules/
					'!build/.*', // Exclude build/
					'!tests/**', // Exclude tests
					],
				expand: true
			}
		},

		// javascript linting with jshint
		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'<%= dirs.main.js %>/clients.js',
				'<%= dirs.main.js %>/metabox.js'
			]
		},

		// cssmin
		cssmin:	{
			build:	{
				files: {
					'<%= dirs.main.css %>/styles.min.css': ['<%= dirs.main.css %>/styles.css'],
					'<%= dirs.main.templates %>/ftg-cm-styles.min.css': ['<%= dirs.main.templates %>/ftg-cm-styles.css']
				}
			}
		},

		// uglify to concat and minify
		uglify: {
			dist: {
				files: {
					'<%= dirs.main.js %>/clients.min.js': ['<%= dirs.main.js %>/clients.js'],
					'<%= dirs.main.js %>/metabox.min.js': ['<%= dirs.main.js %>/metabox.js']
				}
			}
		},

		// watch for changes and trigger jshint and uglify
		watch: {
			js: {
				files: [
					'<%= jshint.all %>'
				],
				tasks: ['jshint', 'uglify']
			}
		},
	};

	// initialize grunt config
	// --------------------------
	grunt.initConfig( pluginConfig );

	// register tasks
	// --------------------------

	// default task
	grunt.registerTask( 'default', [
		'checktextdomain',
		//'cssmin',
		//'jshint',
		//'uglify',
		'makepot'
		//'potomo',
		//'glotpress_download'
	] );
};