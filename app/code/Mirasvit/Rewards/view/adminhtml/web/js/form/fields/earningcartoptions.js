define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, uiRegistry, select) {
    'use strict';
    return select.extend({

        initialize: function () {
            this._super();

            this.setVisibility();

            return this;
        },

        onUpdate: function (value) {
            var earning_style_first = uiRegistry.get('index = monetary_step');
            if (earning_style_first.visibleValue == value) {
                earning_style_first.show();
            } else {
                earning_style_first.hide();
            }
            var earning_style_second = uiRegistry.get('index = qty_step');
            if (earning_style_second.visibleValue == value) {
                earning_style_second.show();
            } else {
                earning_style_second.hide();
            }

            return this._super();
        },

        setVisibility: function () {
            var value = this.value();
            if (!value) {
                value = 'earning_style_give';
            }
            this.onUpdate(value);
        },
    });
});