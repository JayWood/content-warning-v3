module.exports = function ( grunt ){
	'use strict';

	grunt.initConfig({
		pkg: grunt.file.readJSON( 'package.json' ),
		uglify: {
			options: {
				mangle: false
			},
			dist: {
				expand: true,
				cwd: 'js',
				src: ['*.js','!*.min.js'],
				dest: 'js',
				ext: '.min.js'
			}
		},
		jshint: {
			options: {
				node: false,
				curly: false,
				eqeqeq: true,
				immed: true,
				latedef: true,
				newcap: true,
				noarg: true,
				sub: true,
				undef: true,
				unused: true,
				boss: true,
				eqnull: true,
				browser: true,
				globals: {
					'jQuery': true,
					'wp': true,
					'module': true
				}
			},
			gruntfile: {
				src: 'gruntfile.js'
			},
			lib_test: {
				src: ['js/*.js', '!js/*.min.js']
			}
		},
		sass: {
			options: {
				style: 'expanded'
			},
			dist: {
				files: {
					'css/cwv3.css': 'css/sass/cwv3.scss'
				}
			}
		},
		cssmin: {
			minify: {
				src: 'css/cwv3.css',
				dest: 'css/cwv3.min.css',
			}
		},
		watch: {
			gruntfile: {
				files: '<%= jshint.gruntfile.src %>',
				tasks: ['jshint:gruntfile']
			},
			lib_test: {
				files: '<%= jshint.lib_test.src %>',
				tasks: ['jshint:lib_test']
			},
			css: {
				files: 'css/**/*.scss',
				tasks: ['sass', 'cssmin']
			},
			js: {
				files: ['js/*.js', '!js/*.min.js'],
				tasks: ['uglify']
			}
		}
	});

	// These plugins provide necessary tasks
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-cssmin');

	// Default task
	grunt.registerTask('default', ['jshint', 'sass', 'uglify', 'cssmin']);
};