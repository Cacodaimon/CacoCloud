module.exports = function (grunt) {
    grunt.loadNpmTasks('grunt-contrib-uglify');    // npm install grunt-contrib-uglify  --save-dev
    grunt.loadNpmTasks('grunt-contrib-cssmin');    // npm install grunt-contrib-cssmin  --save-dev
    grunt.loadNpmTasks('grunt-contrib-watch');     // npm install grunt-contrib-watch   --save-dev
    grunt.loadNpmTasks('grunt-contrib-htmlmin');   // npm install grunt-contrib-htmlmin --save-dev

    grunt.registerTask('default', ['uglify:prod',  'htmlmin:prod',  'cssmin']);
    grunt.registerTask('debug',   ['uglify:debug', 'htmlmin:debug', 'cssmin:debug']);
    grunt.registerTask('vendor',  ['uglify:vendor']);

    var jsPaths = [
        'assets/scripts/paginator/module.js',
        'assets/scripts/general/service/InstallWebApp.js',
        'assets/scripts/general/service/TemporaryStorage.js',
        'assets/scripts/general/service/Credentials.js',
        'assets/scripts/general/controller/Account.js',
        'assets/scripts/general/controller/Welcome.js',
        'assets/scripts/pass/factory/ActionWrapper.js',
        'assets/scripts/pass/factory/REST.js',
        'assets/scripts/pass/controller/Password.js',
        'assets/scripts/pass/config.js',
        'assets/scripts/feed/factory/REST.js',
        'assets/scripts/feed/service/FeedBackend.js',
        'assets/scripts/feed/controller/Feed.js',
        'assets/scripts/feed/controller/FeedList.js',
        'assets/scripts/feed/controller/FeedManage.js',
        'assets/scripts/feed/controller/FeedManageAutoCleanup.js',
        'assets/scripts/feed/controller/FeedManageUpdateInterval.js',
        'assets/scripts/feed/controller/Item.js',
        'assets/scripts/feed/controller/Items.js',
        'assets/scripts/feed/filter/SumByKey.js',
        'assets/scripts/feed/config.js',
        'assets/scripts/bookmark/factory/REST.js',
        'assets/scripts/bookmark/controller/BookMark.js',
        'assets/scripts/bookmark/config.js',
        'assets/scripts/mail/factory/REST.js',
        'assets/scripts/mail/factory/ActionWrapper.js',
        'assets/scripts/mail/controller/Mail.js',
        'assets/scripts/mail/controller/MailAuth.js',
        'assets/scripts/mail/controller/MailBoxes.js',
        'assets/scripts/mail/controller/MailList.js',
        'assets/scripts/mail/controller/MailManage.js',
        'assets/scripts/mail/controller/MailRead.js',
        'assets/scripts/mail/controller/MailSend.js',
        'assets/scripts/mail/config.js',
        'assets/scripts/general/filter/UnixTimeStamp.js',
        'assets/scripts/general/filter/Base64.js',
        'assets/scripts/general/factory/REST.js',
        'assets/scripts/general/config.js'
    ];

    var vendorJSFiles = [
        'assets/scripts/vendor-js/jquery/jquery-2.0.3.min.js',
        'assets/scripts/vendor-js/angular/angular.min.js',
        'assets/scripts/vendor-js/angular/angular-resource.min.js',
        'assets/scripts/vendor-js/angular/angular-ui-router.min.js',
        'assets/scripts/vendor-js/bootstrap/bootstrap.min.js',
        'assets/scripts/vendor-js/crypto-js/aes.js',
        'assets/scripts/vendor-js/crypto-js/pbkdf2.js'
    ];

    var htmlFiles = {
        'public/index.html':                             ['assets/index.html'],
        'public/views/mail/main.html':                   ['assets/views/mail/main.html'],
        'public/views/mail/send.html':                   ['assets/views/mail/send.html'],
        'public/views/mail/auth.html':                   ['assets/views/mail/auth.html'],
        'public/views/mail/main/list.html':              ['assets/views/mail/main/list.html'],
        'public/views/mail/main/read.html':              ['assets/views/mail/main/read.html'],
        'public/views/mail/manage.html':                 ['assets/views/mail/manage.html'],
        'public/views/mail/manage/list.html':            ['assets/views/mail/manage/list.html'],
        'public/views/mail/manage/add.html':             ['assets/views/mail/manage/add.html'],
        'public/views/mail/manage/edit.html':            ['assets/views/mail/manage/edit.html'],
        'public/views/bookmark/add.html':                ['assets/views/bookmark/add.html'],
        'public/views/bookmark/edit.html':               ['assets/views/bookmark/edit.html'],
        'public/views/bookmark/layout.html':             ['assets/views/bookmark/layout.html'],
        'public/views/bookmark/list.html':               ['assets/views/bookmark/list.html'],
        'public/views/feed/main/item.html':              ['assets/views/feed/main/item.html'],
        'public/views/feed/main/list.html':              ['assets/views/feed/main/list.html'],
        'public/views/feed/manage/add.html':             ['assets/views/feed/manage/add.html'],
        'public/views/feed/manage/auto-cleanup.html':    ['assets/views/feed/manage/auto-cleanup.html'],
        'public/views/feed/manage/edit.html':            ['assets/views/feed/manage/edit.html'],
        'public/views/feed/manage/list.html':            ['assets/views/feed/manage/list.html'],
        'public/views/feed/manage/update-interval.html': ['assets/views/feed/manage/update-interval.html'],
        'public/views/feed/main.html':                   ['assets/views/feed/main.html'],
        'public/views/feed/manage.html':                 ['assets/views/feed/manage.html'],
        'public/views/general/login.html':               ['assets/views/general/login.html'],
        'public/views/general/logout.html':              ['assets/views/general/logout.html'],
        'public/views/general/welcome.html':             ['assets/views/general/welcome.html'],
        'public/views/paginator/directive.html':         ['assets/views/paginator/directive.html'],
        'public/views/password/add.html':                ['assets/views/password/add.html'],
        'public/views/password/auth.html':               ['assets/views/password/auth.html'],
        'public/views/password/edit.html':               ['assets/views/password/edit.html'],
        'public/views/password/layout.html':             ['assets/views/password/layout.html'],
        'public/views/password/list.html':               ['assets/views/password/list.html']
    };

    grunt.initConfig({
        uglify: {
            prod: {
                options: {
                    beautify: false,
                    mangle: false,
                    report: 'gzip'
                },
                files: {
                    'public/scripts/app.min.js': jsPaths
                }
            },
            vendor: {
                options: {
                    beautify: false,
                    mangle: true,
                    report: 'min'
                },
                files: {
                    'public/scripts/vendor.min.js': vendorJSFiles
                }
            },
            debug: {
                options: {
                    beautify: true,
                    mangle: false,
                    report: 'min'
                },
                files: {
                    'public/scripts/app.min.js': jsPaths
                }
            }
        },
        htmlmin: {
            prod: {
                options: {
                    removeComments:            true,
                    collapseWhitespace:        true,
                    removeRedundantAttributes: true,
                    removeEmptyAttributes:     true,
                    removeOptionalTags:        true,
                    removeAttributeQuotes:     true,
                    collapseBooleanAttributes: true
                },
                files: htmlFiles
            },
            debug: {
                files: htmlFiles
            }
        },
        cssmin: {
            options: {
                keepSpecialComments: 0,
                report: 'min'
            },
            minify: {
                expand: true,
                cwd: 'assets/css/',
                src: ['*.css', '!*.min.css'],
                dest: 'public/css/',
                ext: '.min.css'
            }
        },
        watch: {
            scripts: {
                files: jsPaths,
                tasks: ['debug']
            }
        }
    });
};