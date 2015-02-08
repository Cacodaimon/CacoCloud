/*
 * A simple local storage module with cache expires time and offline mode.
 *
 * By Guido Krömer <mail@cacodaemon.de>
 * */
angular.module('caco.TemporaryStorage', [])
    .service('TempStorage', function () {
        this.expires = 60000; //10 Min

        this.getObject = function (key) {
            return JSON.parse(this.get(key))
        };

        this.setObject = function (key, value, expires) {
            this.set(key, JSON.stringify(value), expires);
        };

        this.get = function (key) {
            if (this.cacheValid(key)) {
                return localStorage.getItem(key);
            }

            return null;
        };

        this.set = function (key, value, expires) {
            if (!expires) {
                expires = this.expires;
            }

            try {
                this.cacheAdd(key, expires);
                localStorage.setItem(key, value);
            } catch (e) {
                switch (e.name) {
                    case 'QuotaExceededError': // Chrome and IE
                    case 'QUOTA_EXCEEDED_ERR': // Chrome
                    case 'NS_ERROR_DOM_QUOTA_REACHED': // Firefox
                        localStorage.clear();
                        break;
                    default:
                        console.log('LocalStorage quota exceeded? ' + e.name);
                }
            }
        };

        this.remove = function (key) {
            localStorage.removeItem(key);
            localStorage.removeItem('time-' + key);
        };

        this.contains = function (key) {
            if (this.cacheValid(key)) {
                return localStorage.getItem(key) ? true : false;
            }

            return false;
        };

        this.cacheValid = function (key) {
            var time = parseInt(localStorage.getItem('time-' + key));

            if (!time) {
                return false;
            }

            if (!navigator.onLine) {
                return true;
            }

            if (time < new Date().getTime()) {
                this.remove(key);

                return false;
            }

            return true;
        };

        this.cacheAdd = function (key, expires) {
            localStorage.setItem('time-' + key, parseInt(expires) + new Date().getTime());
        };

        this.clear = function () {
            localStorage.clear();
        };
    });