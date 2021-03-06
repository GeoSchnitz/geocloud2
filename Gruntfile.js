module.exports = function (grunt) {
    "use strict";
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        cssmin: {
            build: {
                files: {
                    // The Viewer
                    'public/apps/viewer/css/build/all.min.css': [
                        'public/js/bootstrap3/css/bootstrap.min.css',
                        'public/js/MultiLevelPushMenu/jquery.multilevelpushmenu.css',
                        'public/apps/viewer/css/styles.css'
                    ],
                    // The Viewer
                    'public/css/build/styles.min.css': [
                        'public/css/styles.css'
                    ]
                }
            }
        },
        jshint: {
            options: {
                funcscope: true,
                shadow: true,
                evil: true,
                validthis: true,
                asi: true,
                newcap: false,
                notypeof: false,
                eqeqeq: false,
                loopfunc: true,
                es3: true,
                devel: false,
                eqnull: true
            },
            all: ['public/js/*.js']
        },
        uglify: {
            //adhoc: {files: {'public/js/openlayers/OpenLayers.js': ['public/js/openlayers/OpenLayers.js']}},
            publish: {
                files: {
                    'public/js/ext/examples/ux/Spinner.min.js': ['public/js/ext/examples/ux/Spinner.js'],
                    // geocloud.js
                    'public/api/v3/js/geocloud.min.js': ['public/api/v3/js/geocloud.js'],
                    // The Viewer
                    'public/apps/viewer/js/build/all.min.js': [
                        'public/js/jquery/1.10.0/jquery.min.js',
                        'public//js/bootstrap3/js/bootstrap.min.js',
                        'public/js/hogan/hogan-2.0.0.js',
                        'public/js/div/jRespond.js',
                        'public/js/common.js',
                        'public/js/MultiLevelPushMenu/jquery.multilevelpushmenu.js',
                        'public/apps/viewer/js/templates.js',
                        'public/apps/viewer/js/viewer.js',
                        'public/js/leaflet/leaflet.js',
                        'public//js/leaflet/plugins/Leaflet.draw/leaflet.draw.js',
                        'public//js/leaflet/plugins/Leaflet.label/leaflet.label.js'
                    ],
                    //store
                    'public/js/build/store/all.min.js': [
                        'public/js/jquery/1.10.0/jquery.min.js',
                        'public/js/msg.js',
                        'public/js/store.js',
                        'public/js/edittablestructure.js',
                        'public/js/cartomobilesetup.js',
                        'public/js/editwmsclass.js',
                        'public/js/editwmslayer.js',
                        'public/js/classwizards.js',
                        'public/js/addshapeform.js',
                        'public/js/addbitmapform.js',
                        'public/js/addrasterform.js',
                        'public/js/addfromscratch.js',
                        'public/js/addviewform.js',
                        'public/js/addosmform.js',
                        'public/js/colorfield.js',
                        'public/js/httpauthform.js',
                        'public/js/apikeyform.js',
                        'public/js/plupload/js/moxie.min.js',
                        'public/js/plupload/js/plupload.min.js',
                        'public/js/plupload/js/jquery.plupload.queue/jquery.plupload.queue.min.js'
                    ],
                    //Editor
                    'public/js/build/editor/all.min.js': [
                        'public/js/jquery/1.10.0/jquery.min.js',
                        'public/js/msg.js',
                        'public/js/GeoExt/script/GeoExt.js',
                        'public/api/v1/js/api.js',
                        'public/api/v3/js/geocloud.js',
                        'public/js/wfseditor.js',
                        'public/js/attributeform.js',
                        'public/js/filterfield.js',
                        'public/js/filterbuilder.js',
                        'public/js/comparisoncomboBox.js',
                        'public/js/openlayers/proj4js-combined.js'
                    ]
                }
            }
        },
        hogan: {
            publish: {
                options: {
                    defaultName: function (filename) {
                        return filename.split('/').pop();
                    }
                },
                files: {
                    "public/apps/viewer/js/templates.js": ["public/apps/viewer/templates/body.tmpl"],
                    "public/apps/widgets/gc2map/js/templates.js": ["public/apps/widgets/gc2map/templates/body.tmpl"]
                }
            }
        },
        cacheBust: {
            options: {
                encoding: 'utf8',
                algorithm: 'md5',
                length: 16,
                rename: false,
                enableUrlFragmentHint: true,
                baseDir: "public/",
                ignorePatterns: ['php']
            },
            assets: {
                files: [{
                    src: [
                        'public/store.php',
                        'public/editor.php',
                        'public/apps/viewer/index.html',
                        'public/apps/widgets/gc2map/index.html',
                        'public/api/v3/js/async_loader.js',
                        'public/api/v3/js/geocloud.js',
                        'public/apps/widgets/gc2map/js/gc2map.js'
                    ]
                }]
            }
        },
        processhtml: {
            dist: {
                files: {
                    'public/store.php': ['public/store.php'],
                    'public/editor.php': ['public/editor.php'],
                    'public/apps/viewer/index.html': ['public/apps/viewer/index.html']
                }
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-processhtml');
    grunt.loadNpmTasks('grunt-templates-hogan');
    grunt.loadNpmTasks('grunt-cache-bust');

    grunt.registerTask('default', ['cssmin', 'jshint', 'hogan', 'uglify', 'cacheBust']);
    grunt.registerTask('production', ['processhtml', 'cacheBust']);
};




