define(["prototype", "validation"], function () {
    SecureCheckoutRewardsPoints = Class.create();
    SecureCheckoutRewardsPoints.prototype = {
        initialize: function (config) {
            this.isPointsApplyed = config.isPointsApplyed;
            this.applyRewardsPointsButton = $$(config.applyRewardsPointsButton).first();
            this.cancelRewardsPointsButton = $$(config.cancelRewardsPointsButton).first();
            this.allPointsRewardsPointsBlock = $$(config.allPointsRewardsPointsBlock).first();
            this.pointsAmountInput = $(config.pointsAmountInput);
            this.rewardsPointsContainer = $$(config.rewardsPointsContainer).first();
            this.applyRewardsPointsUrl = config.applyRewardsPointsUrl;
            this.validationRequireMessage = config.validationRequireMessage;
            this.errorMessageField = $$(config.errorMessageField).first();

            this.initObserver();
        },

        initObserver: function () {
            var rewardsCheckoutBlock = jQuery('#rewards__checkout-cart-usepoints').parent();
            jQuery('#one-step-checkout-order-review').parent().before(rewardsCheckoutBlock);
            rewardsCheckoutBlock.show();
            if (this.isPointsApplyed) {
                this.applyRewardsPointsButton.hide();
                this.allPointsRewardsPointsBlock.hide();
                this.cancelRewardsPointsButton.show();

                this.pointsAmountInput.writeAttribute('readonly', 'readonly');
            } else {
                this.applyRewardsPointsButton.show();
                this.allPointsRewardsPointsBlock.show();
                this.cancelRewardsPointsButton.hide();

                this.pointsAmountInput.removeAttribute('readonly');
            }
            this.applyRewardsPointsButton.observe('click', function () {
                this.processRewardsPoints();
            }.bind(this));
            this.cancelRewardsPointsButton.observe('click', function () {
                this.processRewardsPoints(1);
            }.bind(this));
        },
        processRewardsPoints: function (removePoints) {
            if (this.pointsAmountInput.getValue() < 1) {
                this.pointsAmountInput.addClassName('validation-failed');
                this.errorMessageField.update(this.validationRequireMessage);
                this.errorMessageField.show();
                return;
            }
            this.errorMessageField.update('');
            this.errorMessageField.hide();
            this.pointsAmountInput.removeClassName('validation-failed');
            this._removeAllMessage();
            var originActionPattern = MagecheckoutSecureCheckout.actionPattern;
            var self = this;
            var requestOptions = {
                method: 'post',
                parameters: {
                    points_amount: this.pointsAmountInput.getValue(),
                    'remove-points': parseInt(removePoints)
                },
                onComplete: function (transport) {
                    if (transport && transport.responseText) {
                        var response = transport.responseText;
                        response = JSON.parse(response);
                        if (self.isPointsApplyed) {
                            self.applyRewardsPointsButton.show();
                            self.allPointsRewardsPointsBlock.show();
                            self.cancelRewardsPointsButton.hide();

                            self.pointsAmountInput.removeAttribute('readonly');
                            self.updatePoints(response.spend_points);
                        }
                        else {
                            self.applyRewardsPointsButton.hide();
                            self.allPointsRewardsPointsBlock.hide();
                            self.cancelRewardsPointsButton.show();

                            self.updatePoints(response.spend_points);
                            self.pointsAmountInput.writeAttribute('readonly', 'readonly');
                        }
                        self.isPointsApplyed = !self.isPointsApplyed;
                        MagecheckoutSecureCheckout.actionPattern = originActionPattern;
                    }
                }
            }
            MagecheckoutSecureCheckout.actionPattern = /rewards\/checkout\/([^\/]+)\//;
            MagecheckoutSecureCheckout.Request(this.applyRewardsPointsUrl, requestOptions);
        },
        updatePoints: function (points) {
            if (points) {
                this.pointsAmountInput.setValue(points);
            } else {
                this.pointsAmountInput.setValue('');
            }
        },
        _removeAllMessage: function () {
            MagecheckoutSecureCheckout.removeMessage(this.rewardsPointsContainer, this.errorClass);
            MagecheckoutSecureCheckout.removeMessage(this.rewardsPointsContainer, this.successClass);
        }
    };
    return SecureCheckoutRewardsPoints;
});


