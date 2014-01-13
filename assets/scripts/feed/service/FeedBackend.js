angular.module('caco.feed.backend', ['caco.TemporaryStorage', 'caco.feed.REST'])
    .service('Feeds', function (FeedREST, FeedUpdateREST, ItemREST, FeedCalculateUpdateIntervalsREST, FeedUrlLookupREST, TempStorage, $rootScope) {
        this.get = function (callback) {
            if (!TempStorage.contains('feeds')) {
                FeedREST.all({}, {}, function (data) {
                    var feedOutdated = false;
                    for (var i = data.response.length - 1; i >= 0; i--) {
                        var feed = data.response[i];

                        if (feed.outdated) {
                            feedOutdated = true;
                            TempStorage.remove('items-' + feed.id);
                        }
                    }

                    TempStorage.setObject('feeds', data.response);
                    callback (data.response);

                    if (feedOutdated) {
                        TempStorage.remove('items-0');
                        FeedUpdateREST.perform({}, {}, function (updateData) {
                            FeedREST.all({}, {}, function (data2) {
                                TempStorage.setObject('feeds', data2.response);
                                $rootScope.$broadcast('FeedsUpdated');
                            });
                        });
                    }
                });
            } else {
                callback (TempStorage.getObject('feeds'));
            }
        };

        this.getOne = function (id, callback) {
            this.get(function (feeds) {
                for (var i = feeds.length - 1; i >= 0; i--) {
                    if (feeds[i].id != id) {
                        continue;
                    }

                    callback(feeds[i]);
                }
            });
        };

        this.remove = function (feed, callback) {
            FeedREST.remove({id: feed.id}, {}, function(data) {
                FeedREST.all({}, {}, function (feeds) {
                    TempStorage.setObject('feeds', feeds.response);
                    callback(feeds.response);
                    $rootScope.$broadcast('FeedsUpdated');
                });
            });
        };

        this.add = function (feed, callback) {
            FeedREST.add({}, feed, function () {
                FeedREST.all({}, {}, function (feeds) {
                    TempStorage.setObject('feeds', feeds.response);
                    callback(feeds.response);
                    $rootScope.$broadcast('FeedsUpdated');
                });
            });
        };


        this.edit = function (feed, callback) {
            FeedREST.edit({id: feed.id}, feed, function (data) {
                FeedREST.all({}, {}, function (feeds) {
                    TempStorage.setObject('feeds', feeds.response);
                    callback(feeds.response);
                    $rootScope.$broadcast('FeedsUpdated');
                });
            });
        };

        this.decRead = function (id) {
            if (!TempStorage.contains('feeds')) {
                $rootScope.$broadcast('FeedsUpdated');
                return;
            }

            var feeds = TempStorage.getObject('feeds');
            for (var i = feeds.length - 1; i >= 0; i--) {
                if (feeds[i].id != id) {
                    continue;
                }
                feeds[i].unread--;
            }
            TempStorage.setObject('feeds', feeds);
            $rootScope.$broadcast('FeedsUpdated');
        };
    })
    .service('Items', function (Feeds, ItemREST, TempStorage) {
        this.all = function (id, callback) {
            id = id ? id : 0;

            if (!TempStorage.contains('items-' + id)) {
                ItemREST.all(id ? {id: id} : {}, function (items) {  // load items from all feeds
                    TempStorage.setObject('items-' + id, items.response);
                    callback(items.response);
                });
                return;
            }

            callback(TempStorage.getObject('items-' + id));
        };

        var markRead = function (idItem, idFeed) {
            if (!TempStorage.contains('items-' + idFeed)) {
                return;
            }

            var itemsCacheTmp = TempStorage.getObject('items-' + idFeed);

            for (var i = itemsCacheTmp.length - 1; i >= 0; i--) {
                if (itemsCacheTmp[i].id != idItem) {
                    continue;
                }
                itemsCacheTmp[i].read = 1;
            }
            TempStorage.setObject('items-' + idFeed, itemsCacheTmp);
        };

        this.one = function (params, callback) {
            if (TempStorage.contains('item-' + params.id_item)) {
                callback(TempStorage.getObject('item-' + params.id_item));
            } else {
                ItemREST.one({id_item: params.id_item}, {}, function (item) {
                    var response = item.response;

                    TempStorage.setObject('item-' + params.id_item, response);
                    callback(response);

                    if (response.read == 0) {
                        markRead(response.id, response.id_feed);
                        markRead(response.id, 0);
                        Feeds.decRead(response.id_feed);
                    }
                });
            }
        };
    });