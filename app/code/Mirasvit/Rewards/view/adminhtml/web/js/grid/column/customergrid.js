define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/grid/columns/multiselect'
], function (_, registry, el) {
    'use strict';

    return el.extend({
        initialize: function () {
            this._super();
            this.setTransactionUserCookie('m__in_transaction_rewards_user', '', 2);
            return this;
        },

        /**
         * Callback method to handle changes of selected items.
         *
         * @param {Array} selected - An array of currently selected items.
         */
        onSelectedChange: function (selected) {
            var selectedValue = String(selected);
            if (selectedValue) {
                selectedValue = selectedValue + '=true';
            }
            selectedValue = selectedValue.replace(/,/g, '=true,');
            this.setTransactionUserCookie("m__in_transaction_rewards_user", selectedValue, 2);
            document.getElementsByName("in_transaction_user")[0].setAttribute("value", selectedValue);


            this.updateExcluded(selected)
                .countSelected()
                .updateState();
        },

        setTransactionUserCookie: function (cname, cvalue, exdays) {
            var path = "path=/";
            var d    = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires="+d.toUTCString();
            document.cookie = cname + "=" + cvalue + "; " + expires + "; " + path;
        },
    });
});
