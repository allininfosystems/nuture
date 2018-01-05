define([
    'underscore',
    'ko',
    'uiElement'
], function (_, ko, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Mirasvit_Rewards/report/toolbar/filter/customer/group',

            exports: {
                customerGroupIds: '${ $.provider }:params.filters[${ $.column }]'
            },

            listens: {}
        },

        initialize: function () {
            this._super();

            _.bindAll(this, 'onChangeCustomerGroup');

            return this;
        },

        initObservable: function () {
            this._super();

            this.customerGroupIds = ko.observable();
            this.current = ko.observable(this.current);

            return this;
        },

        onChangeCustomerGroup: function (customerGroup) {
            this.customerGroupIds(customerGroup.customerGroupIds);
            this.current(customerGroup.label);
        }
    });
});
