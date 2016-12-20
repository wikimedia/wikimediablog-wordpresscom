/*!
 * Grunt file
 */

/*jshint node:true */
module.exports = function ( grunt ) {
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-stylelint' );

	grunt.initConfig( {

		// Lint – Styling
		stylelint: {
			src: [
				'**/*.css',
				'!css/font-awesome.min.css',
				'!node_modules/**'
			]
		},

		// Development
		watch: {
			files: [
				'**/*.css',
				'!css/font-awesome.min.css',
				'.{stylelintrc}'
			],
			tasks: 'default'
		}

	} );

	grunt.registerTask( 'lint', [ 'stylelint' ] );
	grunt.registerTask( 'default', 'lint' );
};
