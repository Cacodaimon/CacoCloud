angular.module('caco.ClientPaginate', [])
    .filter('paginate', function(Paginator) {
        return function(input, rowsPerPage) {
            if (!input) {
                return input;
            }

            if (rowsPerPage) {
                Paginator.rowsPerPage = rowsPerPage;
            }

            Paginator.itemCount = input.length;

            return input.slice(parseInt(Paginator.page * Paginator.rowsPerPage), parseInt((Paginator.page + 1) * Paginator.rowsPerPage + 1) - 1);
        }
    })
    .filter('forLoop', function() {
        return function(input, start, end) {
            input = new Array(end - start);
            for (var i = 0; start < end; start++, i++) {
                input[i] = start;
            }

            return input;
        }
    })
    .service('Paginator', function ($rootScope) {
        this.page = 0;
        this.rowsPerPage = 50;
        this.itemCount = 0;
        this.limitPerPage = 10;

        this.setPage = function (page) {
            if (page > this.pageCount()) {
                return;
            }
            window.scrollTo(0, 0);

            this.page = page;
        };

        this.nextPage = function () {
            if (this.isLastPage()) {
                return;
            }
            window.scrollTo(0, 0);

            this.page++;
        };

        this.perviousPage = function () {
            if (this.isFirstPage()) {
                return;
            }
            window.scrollTo(0, 0);

            this.page--;
        };

        this.firstPage = function () {
            this.page = 0;
        };

        this.lastPage = function () {
            this.page = this.pageCount() - 1;
        };

        this.isFirstPage = function () {
            return this.page == 0;
        };

        this.isLastPage = function () {
            return this.page == this.pageCount() - 1;
        };

        this.pageCount = function () {
            var count = Math.ceil(parseInt(this.itemCount, 10) / parseInt(this.rowsPerPage, 10));
            if (count === 1) {
                this.page = 0;
            }

            return count;
        };

        this.lowerLimit = function() {
            var pageCountLimitPerPageDiff = this.pageCount() - this.limitPerPage;

            if (pageCountLimitPerPageDiff < 0) {
                return 0;
            }

            if (this.page > pageCountLimitPerPageDiff + 1) {
                return pageCountLimitPerPageDiff;
            }

            var low = this.page - (Math.ceil(this.limitPerPage/2) - 1);

            return Math.max(low, 0);
        };

        this.show = function () {
            return this.pageCount() > 1;
        };

        this.reset = function () {
            this.page = 0;
        };
    })
    .directive('paginator', function factory() {
        return {
            restrict: 'E',
            controller: function ($scope, Paginator) {
                $scope.paginator = Paginator;
            },
            templateUrl: 'views/paginator/directive.html'
        };
    });