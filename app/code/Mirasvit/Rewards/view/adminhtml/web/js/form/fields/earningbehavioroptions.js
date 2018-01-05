define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (__, _uiRegistry, select) {
    'use strict';
    return select.extend({

        initialize: function () {
            this._super();

            this.setVisibility();

            return this;
        },

        onUpdate: function (value) {
            var earning_behavior_style_first = _uiRegistry.get('index = monetary_step');
            if (earning_behavior_style_first
                && earning_behavior_style_first.visibleValue == value) {
                earning_behavior_style_first.show();
            } else if (earning_behavior_style_first) {
                earning_behavior_style_first.hide();
            }
            var earning_behavior_style_second = _uiRegistry.get('index = qty_step');
            if (earning_behavior_style_second
                && earning_behavior_style_second.visibleValue == value) {
                earning_behavior_style_second.show();
            } else if (earning_behavior_style_second) {
                earning_behavior_style_second.hide();
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