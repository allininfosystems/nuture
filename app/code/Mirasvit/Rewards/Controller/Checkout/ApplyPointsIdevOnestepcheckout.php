<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Controller\Checkout;

use Magento\Framework\Controller\ResultFactory;

class ApplyPointsIdevOnestepcheckout extends \Mirasvit\Rewards\Controller\Checkout
{
    /**
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $response = $this->processRequest();

        //// code from Idev_OneStepCheckout_AjaxController#add_couponAction
        /// it may be changed in other versions of checkout and that may be a problem
        $html = $resultPage->getLayout()
            ->createBlock('\Mirasvit\Checkout\Block\Onepage\Shipping\Method\Available')
            ->setTemplate('onestepcheckout/shipping_method.phtml')
            ->toHtml();

        $response['shipping_method'] = $html;

        $html = $resultPage->getLayout()
        ->createBlock('\Mirasvit\Checkout\Block\Onepage\Payment\Methods', 'choose-payment-method')
        ->setTemplate('onestepcheckout/payment_method.phtml');

        //IDEV Checkout v 4.0.7 does not have method hasEeCustomerbalanace
        if (false && $this->onestepcheckoutData->isEnterprise() && $this->customerData->isLoggedIn()) {
            if ($this->onestepcheckoutData->hasEeCustomerbalanace()) {
                $customerBalanceBlock = $resultPage->getLayout()
                    ->createBlock(
                        '\Mirasvit\Enterprise\Customerbalance\Block\Checkout\Onepage\Payment\Additional',
                        'customerbalance',
                        [
                            'template' => 'onestepcheckout/customerbalance/payment/additional.phtml',
                        ]
                    );
                $customerBalanceBlockScripts = $resultPage->getLayout()
                    ->createBlock(
                        '\Mirasvit\Enterprise\Customerbalance\Block\Checkout\Onepage\Payment\Additional',
                        'customerbalance_scripts',
                        [
                            'template' => 'onestepcheckout/customerbalance/payment/scripts.phtml',
                        ]
                    );
                $resultPage->getLayout()
                    ->getBlock('choose-payment-method')
                    ->append($customerBalanceBlock)
                    ->append($customerBalanceBlockScripts);
            }

            if ($this->onestepcheckoutData->hasEeRewards()) {
                $rewardPointsBlock = $resultPage->getLayout()
                    ->createBlock(
                        '\Mirasvit\Enterprise\Reward\Block\Checkout\Payment\Additional',
                        'reward.points',
                        [
                            'template' => 'onestepcheckout/reward/payment/additional.phtml',
                            'before' => '-',
                        ]
                    );
                $rewardPointsBlockScripts = $resultPage->getLayout()
                    ->createBlock(
                        '\Mirasvit\Enterprise\Reward\Block\Checkout\Payment\Additional',
                        'reward.scripts',
                        [
                            'template' => 'onestepcheckout/reward/payment/scripts.phtml',
                            'after' => '-',
                        ]
                    );
                $resultPage->getLayout()
                    ->getBlock('choose-payment-method')
                    ->append($rewardPointsBlock)
                    ->append($rewardPointsBlockScripts);
            }
        }

        //IDEV Checkout v 4.0.7 does not have method hasEeGiftcards
        if (false && $this->onestepcheckoutData->isEnterprise() && $this->onestepcheckoutData->hasEeGiftcards()) {
            $giftcardScripts = $resultPage->getLayout()
                ->createBlock(
                    '\Mirasvit\Enterprise\Giftcardaccount\Block\Checkout\Onepage\Payment\Additional',
                    'giftcardaccount_scripts',
                    [
                        'template' => 'onestepcheckout/giftcardaccount/onepage/payment/scripts.phtml',
                    ]
                );
            $html->append($giftcardScripts);
        }

        $response['payment_method'] = $html->toHtml();

          // Add updated totals HTML to the output
        $html = $resultPage->getLayout()
        ->createBlock('\Mirasvit\Onestepcheckout\Block\Summary')
        ->setTemplate('onestepcheckout/summary.phtml')
        ->toHtml();

        $response['summary'] = $html;

        $this->getResponse()->setBody(\Zend_Json::encode($response));
    }
}
