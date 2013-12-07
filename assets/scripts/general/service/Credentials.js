angular.module('caco.Credentials', ['caco.TemporaryStorage'])
    .service('Credentials', function (TempStorage) {
        this.init = function () {
            this.key = {
                server: null,
                email: null
            };

            this.basicAuth = {
                user: null,
                pass: null
            };
        };

        this.persist = function () {
            TempStorage.setObject('caco.Credentials', this.basicAuth, 8640000 /*one day*/);
        };

        this.load = function () {
            if (TempStorage.contains('caco.Credentials')) {
                this.basicAuth = TempStorage.getObject('caco.Credentials');
            }
        }

        this.logout = function () {
            this.init();
            TempStorage.clear();
        };

        this.empty = function () {
            return this.basicAuth.user == null || this.basicAuth.pass == null;
        };

        this.emptyServerKey = function() {
            return this.key.server == null;
        };

        this.emptyEmailKey = function() {
            return this.key.email == null;
        };

        this.basicAuthHeader = function () {
            return 'Basic ' + window.btoa(this.basicAuth.user + ':' + this.basicAuth.pass);
        };

        this.init();
        this.load();
    });