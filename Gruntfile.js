module.exports = function(grunt) {
    
    // project configuration
    grunt.initConfig(
        {
            uglify: {
                combine: {
                    files: {
                        'dist/main.js': [
                            'assets/js/*.js', 
                            '!assets/js/html5shiv.min.js',
                            '!assets/js/jquery.inputmask.js',
                            '!assets/js/jquery-2.1.1.min.js',
                            '!sweet-alert.min.js'
                        ]
                    },
                    options: {
                        sourceMap: true
                    }
                }
            },
            sass: {
                dev: {
                    options: {
                        style: 'expanded',
                        compass: false
                    },
                    files: {
                        'dist/main.css': ['assets/sass/main.scss']
                    }
                },
                dist: {
                    options: {
                        style: 'compressed',
                        compass: false
                    },
                    files: {
                        'dist/main.css': ['assets/sass/main.scss']
                    }
                }
            },
            watch: {
                scripts: {
                    files: ['assets/js/*.js'],
                    tasks: ['uglify']
                },
                sass: {
                    files: 'assets/sass/*.scss',
                    tasks: ['sass:dev']
                }
            },
            jslint: {
                js: {
                    src: ['assets/js/*.js']
                },
                Gruntfile: {
                    src: ['Gruntfile.js']
                }
            }
        }
    );
    
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-jslint');
    
    grunt.registerTask('default', ['uglify', 'sass']);
    grunt.registerTask('linkt', ['jslint']);
};
