module.exports = function (grunt) {
    grunt.loadNpmTasks('grunt-contrib-uglify');    // npm install grunt-contrib-uglify  --save-dev
    grunt.loadNpmTasks('grunt-contrib-cssmin');    // npm install grunt-contrib-cssmin  --save-dev
    grunt.loadNpmTasks('grunt-contrib-watch');     // npm install grunt-contrib-watch   --save-dev
    grunt.loadNpmTasks('grunt-contrib-htmlmin');   // npm install grunt-contrib-htmlmin --save-dev
    grunt.loadNpmTasks('grunt-contrib-copy');      // npm install grunt-contrib-copy    --save-dev

    grunt.registerTask('default', ['uglify:prod',  'htmlmin:prod',  'cssmin']);
    grunt.registerTask('debug',   ['uglify:debug', 'htmlmin:debug', 'cssmin']);
    grunt.registerTask('vendor',  ['uglify:vendor','copy:vendor']);

    var modules = grunt.option('module') ? grunt.option('module').split(',') : ['feed', 'mail', 'bookmark', 'pass'];

    var jsPaths = [
        'assets/scripts/paginator/module.js',
        'assets/scripts/alerts/module.js',
        'assets/scripts/general/service/InstallWebApp.js',
        'assets/scripts/general/service/TemporaryStorage.js',
        'assets/scripts/general/service/Credentials.js',
        'assets/scripts/general/controller/Account.js',
        'assets/scripts/general/controller/Welcome.js',
        'assets/scripts/general/filter/UnixTimeStamp.js',
        'assets/scripts/general/filter/Base64.js',
        'assets/scripts/general/factory/REST.js',
        'assets/scripts/general/config.js'
    ];

    var jsPathsPass = [
        'assets/scripts/pass/factory/ActionWrapper.js',
        'assets/scripts/pass/factory/REST.js',
        'assets/scripts/pass/controller/Password.js',
        'assets/scripts/pass/config.js'
    ];

    var jsPathsFeed = [
        'assets/scripts/feed/factory/REST.js',
        'assets/scripts/feed/service/FeedBackend.js',
        'assets/scripts/feed/controller/Feed.js',
        'assets/scripts/feed/controller/FeedList.js',
        'assets/scripts/feed/controller/FeedManage.js',
        'assets/scripts/feed/controller/FeedManageAutoCleanup.js',
        'assets/scripts/feed/controller/FeedManageUpdateInterval.js',
        'assets/scripts/feed/controller/Item.js',
        'assets/scripts/feed/controller/Items.js',
        'assets/scripts/feed/controller/ItemQueue.js',
        'assets/scripts/feed/controller/ItemQueueMain.js',
        'assets/scripts/feed/filter/SumByKey.js',
        'assets/scripts/feed/config.js'
    ];

    var jsPathsMail = [
        'assets/scripts/mail/factory/REST.js',
        'assets/scripts/mail/factory/ActionWrapper.js',
        'assets/scripts/mail/controller/Mail.js',
        'assets/scripts/mail/controller/MailAuth.js',
        'assets/scripts/mail/controller/MailBoxes.js',
        'assets/scripts/mail/controller/MailList.js',
        'assets/scripts/mail/controller/MailManage.js',
        'assets/scripts/mail/controller/MailRead.js',
        'assets/scripts/mail/controller/MailReply.js',
        'assets/scripts/mail/controller/MailSend.js',
        'assets/scripts/mail/config.js',
    ];

    var jsPathsBookmark = [
        'assets/scripts/bookmark/factory/REST.js',
        'assets/scripts/bookmark/controller/BookMark.js',
        'assets/scripts/bookmark/config.js',
    ];

    var vendorJSFiles = [
        'assets/scripts/vendor-js/jquery/jquery-2.0.3.min.js',
        'assets/scripts/vendor-js/angular/angular.min.js',
        'assets/scripts/vendor-js/angular/angular-resource.min.js',
        'assets/scripts/vendor-js/angular/angular-animate.min.js',
        'assets/scripts/vendor-js/angular/angular-sanitize.min.js',
        'assets/scripts/vendor-js/angular/angular-ui-router.min.js',
        'assets/scripts/vendor-js/bootstrap/bootstrap.min.js',
        'assets/scripts/vendor-js/crypto-js/aes.js',
        'assets/scripts/vendor-js/crypto-js/pbkdf2.js'
    ];

    var htmlFiles = {
        'public/index.html':                             ['assets/index.html'],
        'public/views/general/login.html':               ['assets/views/general/login.html'],
        'public/views/general/logout.html':              ['assets/views/general/logout.html'],
        'public/views/general/welcome.html':             ['assets/views/general/welcome.html'],
        'public/views/general/about.html':               ['assets/views/general/about.html'],
        'public/views/paginator/directive.html':         ['assets/views/paginator/directive.html'],
        'public/views/alerts/directive.html':            ['assets/views/alerts/directive.html']
    };

    if (modules.indexOf('feed') >= 0) {
        jsPaths = jsPaths.concat(jsPathsFeed);

        htmlFiles['public/views/feed/main/item.html'] =              ['assets/views/feed/main/item.html'];
        htmlFiles['public/views/feed/main/list.html'] =              ['assets/views/feed/main/list.html'];
        htmlFiles['public/views/feed/main/queue.html'] =             ['assets/views/feed/main/queue.html'];
        htmlFiles['public/views/feed/manage/add.html'] =             ['assets/views/feed/manage/add.html'];
        htmlFiles['public/views/feed/manage/auto-cleanup.html'] =    ['assets/views/feed/manage/auto-cleanup.html'];
        htmlFiles['public/views/feed/manage/edit.html'] =            ['assets/views/feed/manage/edit.html'];
        htmlFiles['public/views/feed/manage/list.html'] =            ['assets/views/feed/manage/list.html'];
        htmlFiles['public/views/feed/manage/update-interval.html'] = ['assets/views/feed/manage/update-interval.html'];
        htmlFiles['public/views/feed/main.html'] =                   ['assets/views/feed/main.html'];
        htmlFiles['public/views/feed/manage.html'] =                 ['assets/views/feed/manage.html'];
    } else {
        jsPaths = jsPaths.concat(['assets/scripts/stubs/feed.js']);
    }

    if (modules.indexOf('mail') >= 0) {
        jsPaths = jsPaths.concat(jsPathsMail);

        htmlFiles['public/views/mail/main.html'] =                   ['assets/views/mail/main.html'];
        htmlFiles['public/views/mail/send.html'] =                   ['assets/views/mail/send.html'];
        htmlFiles['public/views/mail/auth.html'] =                   ['assets/views/mail/auth.html'];
        htmlFiles['public/views/mail/main/list.html'] =              ['assets/views/mail/main/list.html'];
        htmlFiles['public/views/mail/main/read.html'] =              ['assets/views/mail/main/read.html'];
        htmlFiles['public/views/mail/manage.html'] =                 ['assets/views/mail/manage.html'];
        htmlFiles['public/views/mail/manage/list.html'] =            ['assets/views/mail/manage/list.html'];
        htmlFiles['public/views/mail/manage/add.html'] =             ['assets/views/mail/manage/add.html'];
        htmlFiles['public/views/mail/manage/edit.html'] =            ['assets/views/mail/manage/edit.html'];
    } else {
        jsPaths = jsPaths.concat(['assets/scripts/stubs/mail.js']);
    }

    if (modules.indexOf('bookmark') >= 0) {
        jsPaths = jsPaths.concat(jsPathsBookmark);

        htmlFiles['public/views/bookmark/add.html'] =                ['assets/views/bookmark/add.html'];
        htmlFiles['public/views/bookmark/edit.html'] =               ['assets/views/bookmark/edit.html'];
        htmlFiles['public/views/bookmark/layout.html'] =             ['assets/views/bookmark/layout.html'];
        htmlFiles['public/views/bookmark/list.html'] =               ['assets/views/bookmark/list.html'];
    } else {
        jsPaths = jsPaths.concat(['assets/scripts/stubs/bookmark.js']);
    }

    if (modules.indexOf('pass') >= 0) {
        jsPaths = jsPaths.concat(jsPathsPass);

        htmlFiles['public/views/password/add.html'] =                ['assets/views/password/add.html'];
        htmlFiles['public/views/password/auth.html'] =               ['assets/views/password/auth.html'];
        htmlFiles['public/views/password/edit.html'] =               ['assets/views/password/edit.html'];
        htmlFiles['public/views/password/layout.html'] =             ['assets/views/password/layout.html'];
        htmlFiles['public/views/password/list.html'] =               ['assets/views/password/list.html'];
    } else {
        jsPaths = jsPaths.concat(['assets/scripts/stubs/pass.js']);
    }

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
        },
        copy: {
            vendor: {
                files: [
                    {expand: true,
                     flatten: true,
                     src: 'assets/scripts/vendor-js/zxcvbn/zxcvbn.min.js',
                     dest: 'public/scripts/vendor/zxcvbn',
                     filter: 'isFile'
                    }
                ]
            }
        }
    });
};