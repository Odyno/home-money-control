/* jshint node:true */
module.exports = function(grunt) {
  'use strict';
  require('load-grunt-tasks')(grunt);


  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    // setting folder templates
    dirs: {
      css: 'assets/css',
      less: 'assets/css',
      js: 'assets/js',
      js_partial: 'assets/js/partials'
    },

    // Compile all .less files.
    less: {
      compile: {
        options: {
          // These paths are searched for @imports
          paths: ['<%= less.css %>/']
        },
        files: [{
          expand: true,
          cwd: '<%= dirs.css %>/',
          src: [
            '*.less',
            '!mixins.less'
          ],
          dest: '<%= dirs.css %>/',
          ext: '.css'
        }]
      }
    },

    // Minify all .css files.
    cssmin: {
      minify: {
        expand: true,
        cwd: '<%= dirs.css %>/',
        src: ['*.css'],
        dest: '<%= dirs.css %>/',
        ext: '.min.css'
      }
    },

    concat: {
      options: {
        separator: '\n',
        stripBanners: true,
        banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %> */\n' +
        'jQuery(document).ready(function ($) {\n',
        footer: '\n});'
      },
      admin: {
        src: [
          '<%= dirs.js_partial %>/admin/base_admin.js',
          '<%= dirs.js_partial %>/admin/models/count.js',
          '<%= dirs.js_partial %>/admin/models/count_type.js',
          '<%= dirs.js_partial %>/admin/models/counts.js',
          '<%= dirs.js_partial %>/admin/models/report.js',
          '<%= dirs.js_partial %>/admin/models/reports.js',
          '<%= dirs.js_partial %>/admin/models/all_stat.js',
          '<%= dirs.js_partial %>/admin/models/statistic.js',
          '<%= dirs.js_partial %>/admin/models/transaction_stat.js',

          '<%= dirs.js_partial %>/admin/views/count_table_field.js',
          '<%= dirs.js_partial %>/admin/views/counts_table.js',
          '<%= dirs.js_partial %>/admin/views/report_view.js',
          '<%= dirs.js_partial %>/admin/views/calendar.js',
          '<%= dirs.js_partial %>/admin/views/pie_chart.js',
          '<%= dirs.js_partial %>/admin/views/report_table_field.js',
          '<%= dirs.js_partial %>/admin/views/report_table.js',
          '<%= dirs.js_partial %>/admin/views/budget_view.js',
        ],
        dest: '<%= dirs.js %>/admin.js'
      },
      frontend: {
        src: ['<%= dirs.js_partial %>/frontend.js'],
        dest: '<%= dirs.js %>/frontend.js'
      },
      settings: {
        src: ['<%= dirs.js_partial %>/settings.js'],
        dest: '<%= dirs.js %>/settings.js'
      }
    },

    // Minify .js files.
    uglify: {
      options: {
        preserveComments: 'some'
      },
      jsfiles: {
        files: [{
          expand: true,
          cwd: '<%= dirs.js %>/',
          src: [
            '*.js',
            '!*.min.js',
            '!Gruntfile.js',
          ],
          dest: '<%= dirs.js %>/',
          ext: '.min.js'
        }]
      }
    },

    // Watch changes for assets
    watch: {
      less: {
        files: [
          '<%= dirs.less %>/*.less',
        ],
        tasks: ['less', 'cssmin'],
      },
      concat: {
        files: [
          '<%= dirs.js_partial %>/**/*js',
          '!<%= dirs.js %>/*.min.js'
        ],
        tasks: ['concat']
      },
      js: {
        files: [
          '<%= dirs.js %>/*js',
          '!<%= dirs.js %>/*.min.js'
        ],
        tasks: ['uglify']
      }
    },

  });

  // Load NPM tasks to be used here


  // Register tasks
  grunt.registerTask('default', [
    'less',
    'cssmin',
    'concat',
    'uglify',
    'watch'
  ]);

};