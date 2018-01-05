define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'uiRegistry',
        'mage/storage',
        'Mirasvit_Rewards/js/model/messages',
        'Magento_Checkout/js/action/get-payment-information',
        'Mirasvit_Rewards/js/view/checkout/rewards/points_spend',
        'Mirasvit_Rewards/js/view/checkout/rewards/points_totals',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Checkout/js/model/totals'
    ],
    function(
        $,
        ko,
        Component,
        registry,
        storage,
        messageContainer,
        getPaymentInformationAction,
        rewardsSpend,
        rewardsEarn,
        quote,
        urlBuilder,
        totals
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

        var rewardsPointsName      = window.checkoutConfig.chechoutRewardsPointsName;
        var rewardsPointsAvailble  = window.checkoutConfig.chechoutRewardsPointsAvailble;
        var ApplayPointsUrl        = window.checkoutConfig.chechoutRewardsApplayPointsUrl;
        var PaymentMethodPointsUrl = window.checkoutConfig.chechoutRewardsPaymentMethodPointsUrl;

        var isMageplazaOsc = window.checkoutConfig.isMageplazaOsc;

        return Component.extend({
            defaults: {
                template: 'Mirasvit_Rewards/checkout/rewards/usepoints'
            },

            isLoading: ko.observable(false),
            isShowRewards: isShowRewards,
            isRemovePoints: isRemovePoints,
            rewardsPointsUsed: rewardsPointsUsed,
            rewardsPointsUsedOrigin: rewardsPointsUsedOrigin,
            useMaxPoints: useMaxPoints,
            addRequireClass: addRequireClass,

            chechoutRewardsPointsMax: chechoutRewardsPointsMax,
            rewardsPointsAvailble: rewardsPointsAvailble,
            rewardsPointsName: rewardsPointsName,

            ApplayPointsUrl: ApplayPointsUrl,
            PaymentMethodPointsUrl: PaymentMethodPointsUrl,

            isMageplazaOsc: isMageplazaOsc,

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
                $('input:disabled', form).removeAttr('disabled');//compatibility woth some onepagecheckout
                var data = $(form).serialize();
                this.isLoading(true);
                var self = this;
                $.ajax({
                    url: this.ApplayPointsUrl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: data,
                    complete: function (data) {
                        var deferred = $.Deferred();
                        getPaymentInformationAction(deferred);
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
                            self.isLoading(true);
                        });
                    }
                });
            },
            initialize: function(element, valueAccessor, allBindings) {
                this._super();
                var self = this;
                var serviceUrl = urlBuilder.createUrl('/rewards/mine/update', {});
                if (quote) {
                    quote.totals.subscribe(function () {
                        var request = $.Deferred();
                        var data = {};
                        if (quote.shippingMethod()) {
                            data = {
                                shipping_method: quote.shippingMethod()['method_code'],
                                shipping_carrier: quote.shippingMethod()['carrier_code']
                            };
                        }
                        // Mageplaza onespetcheckout override loader, so we should not call our
                        if (!self.isMageplazaOsc) {
                            self.isLoading(true);
                        }
                        storage.post(
                            serviceUrl, JSON.stringify(data), false
                        ).done(
                            function (response) {
                                self.rewardsPointsAvailble = response.chechout_rewards_points_availble;
                                self.chechoutRewardsPointsMax(response.chechout_rewards_points_max);
                                self.rewardsPointsUsed(response.chechout_rewards_points_used);
                                self.useMaxPoints(response.chechout_rewards_points_used == response.chechout_rewards_points_max);

                                var rewardsForm = registry.get('checkout.steps.billing-step.payment.afterMethods.rewards-form');
                                if (rewardsForm) {
                                    rewardsForm.isRemovePoints(response.chechout_rewards_points_used);
                                    rewardsForm.rewardsPointsUsed(response.chechout_rewards_points_used);
                                    rewardsForm.rewardsPointsUsedOrigin(response.chechout_rewards_points_used);
                                    rewardsForm.chechoutRewardsPointsMax(response.chechout_rewards_points_max);
                                    rewardsForm.useMaxPoints(
                                        response.chechout_rewards_points_used == response.chechout_rewards_points_max
                                    );
                                    rewardsForm.rewardsPointsAvailble = response.chechout_rewards_points_availble;
                                    rewardsForm.isShowRewards(response.chechout_rewards_is_show);

                                }
                                rewardsSpend().getValue(response.chechout_rewards_points_spend);
                                rewardsSpend().isDisplayed(response.chechout_rewards_is_show);
                                rewardsEarn().getValue(response.chechout_rewards_points);

                                request.resolve(response);
                                self.isLoading(false);
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
            }
        });
    }
);