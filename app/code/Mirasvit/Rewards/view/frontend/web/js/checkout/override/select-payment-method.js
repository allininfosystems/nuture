define(
    [
        'Magento_Checkout/js/model/quote',
        'Mirasvit_Rewards/js/checkout/update_payment_method'
    ],
    function(
        quote,
        updatePaymentMethod
    ) {
        'use strict';
        return function (paymentMethod) {
            quote.paymentMethod(paymentMethod);

            updatePaymentMethod();
        }
    }
);