define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, uiRegistry, selecttrigger) {
    'use strict';
    return selecttrigger.extend({

        initialize: function () {
            this._super();

            this.setVisibility();

            return this;
        },

        onUpdate: function (value) {
            var earning_style_first = uiRegistry.get('index = inactivity_period');
            if (earning_style_first
                && earning_style_first.visibleValue == value) {
                earning_style_first.show();
            } else if (earning_style_first) {
                earning_style_first.hide();
            }
            var earning_style_second = uiRegistry.get('index = earning_style');
            if (earning_style_second
                && earning_style_second.visibleValue == value) {
                earning_style_second.show();
            } else if (earning_style_second) {
                earning_style_second.hide();
            }

            return this._super();
        },

        setVisibility: function () {
            var value = this.value();
            if (!value) {
                value = 'signup';
            }
            this.onUpdate(value);
        },
    });
});