angular.module('caco.Alerts', [])
    .service('Alerts', function () {
        this.alerts = [];

        this.id = 0;

        this.add = function (type, title, message) {
            this.alerts.push({
                id: ++this.id,
                type: type,
                title: title,
                message: typeof(message) === 'undefined' ? null : message
            })

            return this.id;
        };

        this.remove = function (id) {
            for (var i = this.alerts.length - 1; i >= 0; i--) {
                if (this.alerts[i].id != id) {
                    continue;
                }

                this.alerts.splice(i, 1);
            };
        };

        this.addSuccess = function (title, message) {
            return this.add('success', title, message);
        };

        this.addInfo = function (title, message) {
            return this.add('info', title, message);
        };

        this.addWarning = function (title, message) {
            return this.add('warning', title, message);
        };

        this.addDanger = function (title, message) {
            return this.add('danger', title, message);
        };
    })
    .directive('alerts', function factory() {
        return {
            restrict: 'E',
            controller: function ($scope, Alerts) {
                $scope.alerts = Alerts.alerts;

                $scope.remove = function (id) {
                    Alerts.remove(id);
                };
            },
            templateUrl: 'views/alerts/directive.html'
        };
    });