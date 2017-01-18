define([
    'jquery',
    'underscore',
    'mageUtils',
    'uiComponent',
    'mage/translate'
], function ($, _, utils, Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            listens: {
                '${ $.autocomplete }:hasFocus': 'focusHasChanged',
                '${ $.autocomplete }:query': 'queryHasChanged'
            },

            imports: {
                query: '${ $.autocomplete }:query'
            },

            exports: {
                result: '${ $.provider }:data',
                query: '${ $.autocomplete }:query'
            },

            result: [],

            limit: 30
        },

        initialize: function () {
            this._super();
        },

        initObservable: function () {
            this._super()
                .observe('result')
                .observe('query');

            return this;
        },

        focusHasChanged: function (focus) {
            if (focus && this.query.length == 0) {
                this._showQueries();
            }
        },

        queryHasChanged: function (focus) {
            if (this.query.length == 0) {
                this._showQueries();
            }
        },

        formHasSubmitted: function () {
            this._saveQuery();
        },

        _showQueries: function () {
            var self = this;
            var queries = this._getQueries();

            var items = [];
            _.each(queries, function (query, index) {
                if (index < this.limit) {
                    var item = {};
                    item.query = query;
                    item.enter = function () {
                        self.query(item.query);
                    };

                    items.push(item);
                }
            }, this);

            var result = {
                totalItems: items.length,
                query: '',
                indexes: [],
                isShowAll: false
            };

            var index = {
                totalItems: items.length,
                isShowTotals: false,
                items: items,
                code: 'popular',
                title: $t('Hot Searches')
            };
            result.indexes.push(index);

            this.result(result);
        },


        _getQueries: function () {
            return this.queries;
        }
    })
});