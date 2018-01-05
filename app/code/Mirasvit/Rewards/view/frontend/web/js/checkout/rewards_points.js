define(
    [
        'Mirasvit_Rewards/js/checkout/cart/rewards_points'
    ],
    function(
        cartRewardsPoints
    ) {
        'use strict';

        if (typeof cartRewardsPoints == 'undefined') {
            return null;
        }

        return cartRewardsPoints.extend({
            defaults: {
                template: 'Mirasvit_Rewards/checkout/rewards/checkout/usepoints'
            }
        });
    }
);