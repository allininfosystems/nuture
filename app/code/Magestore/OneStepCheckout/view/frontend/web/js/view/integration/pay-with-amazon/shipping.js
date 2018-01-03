/*global define*/
define(
    [
        'Amazon_Payment/js/view/shipping'
    ],
    function (
        Component
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magestore_OneStepCheckout/integration/pay-with-amazon/shipping'
            }
        });
    }
);