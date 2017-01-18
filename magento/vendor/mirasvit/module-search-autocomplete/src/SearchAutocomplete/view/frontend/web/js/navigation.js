define([
    'jquery',
    'underscore',
    'ko',
    'uiComponent'
], function ($, _, ko, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            listens: {
                currentIdx: 'indexHasChanged',
                result: 'resultHasChanged'
            },
            imports: {
                result: '${ $.autocomplete }:result'
            },

            currentIdx: 0,
            totalItems: 0
        },

        initialize: function () {
            this._super();
        },

        initObservable: function () {
            var self = this;

            this._super()
                .observe('currentIdx')
                .observe('result')
            ;

            return this;
        },

        onKey: function (event) {
            switch (event.keyCode) {
                case 40: // down arrow
                    this._next();
                    return false;

                case 38: // up arrow
                    this._prev();
                    return false;

                case 27: // escape
                    break;

                case 13: //enter
                    if (this._getActiveItem()) {
                        this._enter();
                        return false;
                    }
            }

            return true;
        },

        indexHasChanged: function () {
            var idx = 0;
            _.each(this.result().indexes, function (index) {
                _.each(index.items, function (item) {
                    if (idx == this.currentIdx()) {
                        item.active(true);
                    } else {
                        item.active(false);
                    }
                    idx++;
                }, this);
            }, this);
        },

        resultHasChanged: function () {
            this.currentIdx(-1);
            this.totalItems = 0;
            _.each(this.result().indexes, function (index) {
                _.each(index.items, function () {
                    this.totalItems++;
                }, this);
            }, this);
        },

        _next: function () {
            if (this.currentIdx() >= this.totalItems - 1) {
                this.currentIdx(0);
            } else {
                this.currentIdx(this.currentIdx() + 1);
            }
        },

        _prev: function () {
            if (this.currentIdx() <= 0) {
                this.currentIdx(this.totalItems - 1);
            } else {
                this.currentIdx(this.currentIdx() - 1);
            }
        },

        _enter: function () {
            var item = this._getActiveItem();
            if (item) {
                item.enter();
            }
        },

        _getActiveItem: function() {
            var active = false;

            _.each(this.result().indexes, function (index) {
                _.each(index.items, function (item) {
                    if (item.active()) {
                        active = item;
                    }
                });
            });

            return active;
        }
    })
});