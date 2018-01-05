define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Ui/js/model/messages',
    'uiLayout'
],
function (
    ko,
    $,
    Component,
    Messages,
    layout
) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super().initChildren();
            return this;
        },

        initChildren: function () {
            this.messageContainer = new Messages();
            this.createMessagesComponent();

            return this;
        },

        createMessagesComponent: function () {
            if (window.checkoutConfig.chechoutRewardsNotificationMessages) {
                for (var i = 0; i < window.checkoutConfig.chechoutRewardsNotificationMessages.length; i++) {
                    this.messageContainer.successMessages.push(
                        window.checkoutConfig.chechoutRewardsNotificationMessages[i]
                    );
                }

                var messagesComponent = {
                    parent: this.name,
                    name: this.name+'.messages',
                    appendTo: 'messages',
                    dataScope: 'messages',
                    component: 'Magento_Ui/js/view/messages',
                    config: {
                        messageContainer: this.messageContainer
                    },
                    isVisible: function() {
                        return true;
                    },
                    isDisplayed: function() {
                        return true;
                    }
                };

                layout([messagesComponent]);
            }

            return this;
        }
    });
});