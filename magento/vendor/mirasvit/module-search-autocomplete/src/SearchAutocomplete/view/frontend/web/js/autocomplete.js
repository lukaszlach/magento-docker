define([
    'jquery',
    'uiComponent',
    'ko',
    'priceUtils',
    'uiRegistry',
    'Mirasvit_SearchAutocomplete/js/lib/jquery.highlight'
], function ($, Component, ko, priceUtils, registry) {
    'use strict';

    ko.bindingHandlers.highlight = {
        init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            bindingContext.$parentContext.$parentContext.$parent.query.subscribe(function (query) {
                ko.bindingHandlers.highlight.highlight(element, query);
            }, this);

            ko.bindingHandlers.highlight.highlight(
                element,
                bindingContext.$parentContext.$parentContext.$parent.query()
            );
        },

        highlight: function (element, query) {
            $(element).highlight(query);
        },

        update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
        }
    };

    ko.bindingHandlers.price = {
        init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            var $element = $(element);
            $element.html(priceUtils.formatPrice(
                $element.html(),
                bindingContext.$parentContext.$parentContext.$parent.priceFormat
            ));
        }
    };

    return Component.extend({
        defaults: {
            template: 'Mirasvit_SearchAutocomplete/autocomplete',
            query: false,
            localStorage: $.initNamespaceStorage('autocomplete').localStorage,
            delay: 100,
            minSearchLength: 1,

            _hasFocus: false,

            isVisible: false,
            isSubmitted: false,
            priceFormat: false,
            searchLabel: '[data-role=minisearch-label]',
            result: {
                totalItems: 0,
                items: [],
                noResults: false
            },
            listens: {
                query: 'updateIsVisible updateQuery',
                hasFocus: 'updateIsVisible',
                result: 'updateIsVisible',
                loading: 'updateIsVisible'
            },
            exports: {
                query: '${ $.provider }:params.q'
            },
            imports: {
                result: '${ $.provider }:result',
                loading: '${ $.provider }:loading'
            }
        },

        initialize: function () {
            var self = this;
            this._super();

            this.searchLabel = $(this.searchLabel);
        },

        initObservable: function () {
            this._super()
                .observe('query')
                .observe('result')
                .observe('loading')
                .observe('_hasFocus')
                .observe('isVisible')
                .observe('isSubmitted');

            this.hasFocus = ko.computed(this._hasFocus).extend({throttle: 100});

            return this;
        },

        onKey: function (key, event) {
            var result = true;
            registry.get('autocompleteNavigation', function (navigation) {
                result = navigation.onKey(event);
            });
            return result;
        },

        updateIsVisible: function () {
            if (this.hasFocus()) {
                if (this.result().totalItems || this.result().noResults || this.loading()) {
                    this.isVisible(true);
                } else {
                    this.isVisible(false);
                }
            } else {
                window.setTimeout($.proxy(function () {
                    this.isVisible(false);
                }, this), 100);
            }
        },

        onSubmit: function () {
            if (this.query()) {
                this.isSubmitted(true);
                return true;
            }

            return false;
        },

        updateQuery: function () {
            if (this.query() != this.injection.activeInput.val()) {
                this.injection.activeInput.val(this.query());
            }
        }
    });
});
