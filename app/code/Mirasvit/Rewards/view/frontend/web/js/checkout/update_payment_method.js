define(
    [
        'jquery',
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Mirasvit_Rewards/js/view/checkout/rewards/points_totals'
    ],
    function(
        $,
        registry,
        quote,
        totals,
        rewardsEarn
    ) {
        'use strict';
        return function (data) {
            if (typeof data == 'undefined') {
                data = {payment: quote.paymentMethod()};
            } else if (typeof data['payment'] == 'undefined') {
                data.payment = quote.paymentMethod();
            }

            totals.isLoading(true);
            $.ajax({
                url: window.checkoutConfig.chechoutRewardsPaymentMethodPointsUrl,
                type: 'POST',
                dataType: 'JSON',
                data: data,
                complete: function (data) {
                    var rewardsForm = registry.get('checkout.steps.billing-step.payment.afterMethods.rewards-form');
                    if (rewardsForm) {
                        rewardsForm.isRemovePoints(data.responseJSON.chechoutRewardsPointsUsed);
                        rewardsForm.rewardsPointsUsed(data.responseJSON.chechoutRewardsPointsUsed);
                        rewardsForm.rewardsPointsUsedOrigin(data.responseJSON.chechoutRewardsPointsUsed);
                        rewardsForm.chechoutRewardsPointsMax(data.responseJSON.chechoutRewardsPointsMax);
                        rewardsForm.useMaxPoints(
                            data.responseJSON.chechoutRewardsPointsUsed == data.responseJSON.chechoutRewardsPointsMax
                        );
                        rewardsForm.rewardsPointsAvailble = data.responseJSON.chechoutRewardsPointsAvailble;
                        rewardsForm.isShowRewards(data.responseJSON.chechoutRewardsIsShow);

                    }
                    rewardsEarn().isDisplayed(data.responseJSON.success);
                    rewardsEarn().getValue(data.responseJSON.points);
                    totals.isLoading(false);
                },
                error: function (data) {
                    totals.isLoading(false);
                }
            });
        }
    }
);