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
    .service('Items', function (Feeds, ItemREST, TempStorage, ItemQueueREST) {
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
            setValue(idItem, idFeed, 'read', 1)
        };

        var setValue = function (idItem, idFeed, field, value) {
            if (!TempStorage.contains('items-' + idFeed)) {
                return;
            }

            var itemsCacheTmp = TempStorage.getObject('items-' + idFeed);

            for (var i = itemsCacheTmp.length - 1; i >= 0; i--) {
                if (itemsCacheTmp[i].id != idItem) {
                    continue;
                }
                itemsCacheTmp[i][field] = value;
            }
            TempStorage.setObject('items-' + idFeed, itemsCacheTmp);
        };

        this.one = function (params, callback) {
            if (TempStorage.contains('item-' + params.id_item)) {
                callback(TempStorage.getObject('item-' + params.id_item));
            } else {
                ItemREST.one({id_item: params.id_item}, {}, function (response) {
                    var item = response.response;

                    TempStorage.setObject('item-' + params.id_item, item);
                    callback(item);

                    if (item.read == 0) {
                        markRead(item.id, item.id_feed);
                        markRead(item.id, 0);
                        Feeds.decRead(item.id_feed);
                    }
                });
            }
        };

        this.enqueue = function (item) {
            ItemQueueREST.enqueue({id: item.id}, {}, function () {
                setValue(item.id, item.id_feed, 'queued', 1);
                item.queued = 1;
            });
        };

        this.dequeue = function (callback) {
            ItemQueueREST.dequeue({}, {}, function(response) {
                var item = response.response;
                setValue(item.id, item.id_feed, 'queued', 0);
                if (item.read == 0) {
                    markRead(item.id, item.id_feed);
                    markRead(item.id, 0);
                    Feeds.decRead(item.id_feed);
                }
                callback(item, true);
            }, function () {
                callback(null, false);
            });
        };
    });