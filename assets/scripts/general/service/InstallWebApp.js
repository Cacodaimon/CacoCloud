angular.module('caco.InstallWebApp', [])
    .service('WebApp', function () {
        var loc = window.location;
        this.webappManifestURL = loc.protocol + '/' + loc.hostname + ':' + loc.port + loc.pathname + '/manifest.webapp';

        this.isWebAppDevice = function () {
            return typeof(navigator.mozApps) !== 'undefined';
        };

        this.install = function () {
            if (!this.isWebAppDevice()) {
                return;
            }

            navigator.mozApps.install(this.webappManifestURL)
        };

        this.checkInstalled = function (callBack) {
            if (!this.isWebAppDevice()) {
                return;
            }

            var request = navigator.mozApps.getSelf();
            request.onsuccess = function() {
                if (request.result) {
                    callBack(true);
                } else {
                    callBack(false);
                }
            };
        };
    });