define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_OneStepCheckout/js/view/payment/discount',
        'mage/storage',
        'Mirasvit_Rewards/js/model/messages',
        'Magento_Checkout/js/action/get-payment-information',
        'Magestore_OneStepCheckout/js/action/reload-shipping-method',
        'Mirasvit_Rewards/js/view/checkout/rewards/points_spend',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
    ],
    function(
        $,
        ko,
        Component,
        discount,
        storage,
        messageContainer,
        getPaymentInformationAction,
        reloadShippingMethod,
        rewardsSpend,
        quote,
        urlBuilder
    ) {
        'use strict';
        var form = '#reward-points-form';

        var isShowRewards            = ko.observable(window.checkoutConfig.chechoutRewardsIsShow);
        var isRemovePoints           = ko.observable(window.checkoutConfig.chechoutRewardsPointsUsed);
        var rewardsPointsUsed        = ko.observable(window.checkoutConfig.chechoutRewardsPointsUsed);
        var rewardsPointsUsedOrigin  = ko.observable(window.checkoutConfig.chechoutRewardsPointsUsed);
        var chechoutRewardsPointsMax = ko.observable(window.checkoutConfig.chechoutRewardsPointsMax);
        var useMaxPoints             = ko.observable(
            window.checkoutConfig.chechoutRewardsPointsUsed == window.checkoutConfig.chechoutRewardsPointsMax
        );
        var addRequireClass          = ko.observable(
            window.checkoutConfig.chechoutRewardsPointsUsed ? "{'required-entry':true}" : '{}'
        );

        var rewardsPointsAvailble = window.checkoutConfig.chechoutRewardsPointsAvailble;
        var ApplayPointsUrl       = window.checkoutConfig.chechoutRewardsApplayPointsUrl;

        return Component.extend({
            defaults: {
                template: 'Mirasvit_Rewards/onestepcheckout/usepoints'
            },

            isShowRewards: isShowRewards,
            isRemovePoints: isRemovePoints,
            rewardsPointsUsed: rewardsPointsUsed,
            rewardsPointsUsedOrigin: rewardsPointsUsedOrigin,
            useMaxPoints: useMaxPoints,
            addRequireClass: addRequireClass,

            chechoutRewardsPointsMax: chechoutRewardsPointsMax,
            rewardsPointsAvailble: rewardsPointsAvailble,

            ApplayPointsUrl: ApplayPointsUrl,

            rewardsFormSubmit: function (isRemove) {
                if (isRemove) {
                    this.addRequireClass('');
                    this.isRemovePoints(1);
                } else {
                    this.addRequireClass("{'required-entry':true}");
                    if (!this.validate()) {
                        this.addRequireClass('');
                        return;
                    }
                    this.isRemovePoints(0);
                }
                discount().showOverlay();
                discount().isLoading(true);
                this.submit();
            },
            setMaxPoints: function () {
                if (this.useMaxPoints()) {
                    this.useMaxPoints(false);
                    if (this.rewardsPointsUsedOrigin()) {
                        this.rewardsPointsUsed(this.rewardsPointsUsedOrigin());
                    } else {
                        this.rewardsPointsUsed(0);
                    }
                } else {
                    this.useMaxPoints(true);
                    this.rewardsPointsUsed(this.chechoutRewardsPointsMax());
                }
                return true;
            },
            validatePointsAmount: function () {
                if (parseInt(this.rewardsPointsUsed()) < this.chechoutRewardsPointsMax()) {
                    this.useMaxPoints(false);
                } else {
                    this.useMaxPoints(true);
                    this.rewardsPointsUsed(this.chechoutRewardsPointsMax());
                }
            },
            validate: function() {
                return $(form).validation() && $(form).validation('isValid');
            },
            submit: function () {
                var self = this;
                $.ajax({
                    url: this.ApplayPointsUrl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $(form).serialize(),
                    complete: function (data) {
                        var deferred = $.Deferred();
                        discount().isLoading(false);
                        getPaymentInformationAction(deferred);
                        reloadShippingMethod();
                        $.when(deferred).done(function () {
                            $('#ajax-loader3').hide();
                            $('#control_overlay_review').hide();
                            rewardsSpend().getValue(data.responseJSON.spend_points_formated);
                            if (data.responseJSON.message) {
                                messageContainer.addSuccessMessage({'message': data.responseJSON.message});
                            }

                            if (data.responseJSON) {
                                if (self.isRemovePoints()) {
                                    self.useMaxPoints(false);
                                    rewardsSpend().isDisplayed(0);
                                } else {
                                    rewardsSpend().isDisplayed(1);
                                }
                                self.rewardsPointsUsed(parseInt(data.responseJSON.spend_points));
                                self.rewardsPointsUsedOrigin(self.rewardsPointsUsed());
                            }
                        });
                    },
                });
            },
            initialize: function(element, valueAccessor, allBindings) {
                this._super();
                var self = this;
                var serviceUrl = urlBuilder.createUrl('/rewards/mine/update', {});
                quote.totals.subscribe(function () {
                    var request = $.Deferred();
                    storage.post(
                        serviceUrl, {}, false
                    ).done(
                        function (response) {
                            self.rewardsPointsAvailble = response.chechout_rewards_points_availble;
                            self.chechoutRewardsPointsMax(response.chechout_rewards_points_max);
                            self.rewardsPointsUsed(response.chechout_rewards_points_used);
                            self.useMaxPoints(response.chechout_rewards_points_used == response.chechout_rewards_points_max);

                            rewardsSpend().getValue(response.chechout_rewards_points_spend);

                            request.resolve(response);
                        }
                    ).fail(
                        function (response) {
                            request.reject(response);
                        }
                    ).always(
                        function () {

                        }
                    );
                    return request;
                });
            }
        });
    }
);