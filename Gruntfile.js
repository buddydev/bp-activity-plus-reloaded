'use strict';

module.exports = function(grunt) {

	grunt.loadNpmTasks('grunt-wp-i18n');

	grunt.initConfig({
		makepot: {
			target: {
				options: {
					domainPath: 'languages/',
					type: 'wp-plugin',
					potFilename: 'bpfb-default.po',
					exclude: ['lib/external'],
					potHeaders: {
						'report-msgid-bugs-to': '\n',
						'project-id-version': 'Activity Plus\n'
					}
				}
			}
		}
	});
	grunt.registerTask('default', ['makepot']);
};
