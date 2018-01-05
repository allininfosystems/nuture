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

class ApplyPointsIwdOnestepcheckout extends \Mirasvit\Rewards\Controller\Checkout
{
    /**
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $iwdVersion = Mage::getConfig()->getModuleConfig('IWD_Opc')->version;

        if ($iwdVersion >= '4.0.0') {
            /// this does not work for IWD '3.1.3'
            /// get list of available methods before discount changes
            $methodsBefore = $this->opcData->getAvailablePaymentMethods();
            ///////
        }

        $response = $this->processRequest();

        //// code from IWD_Opc_CouponController#couponPostAction
        /// it may be changed in other versions of checkout and that may be a problem
        $responseData = [];
        $responseData['message'] = $response['message'];
        $layout = $resultPage->getLayout();
        $block = $layout->createBlock('\Mirasvit\Rewards\Block\Checkout\Cart\Usepoints')
                        //->setTemplate('checkout/cart/iwd/form.phtml');
                        ->setTemplate('checkout/cart/usepoints_iwd_onestepcheckout.phtml');
        $responseData['rewards'] = $block->toHtml();

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()
                ->collectTotals()
                ->save();
            if ($iwdVersion >= '4.0.0') {
                /// this does not work for IWD '3.1.3'
                /// get list of available methods after discount changes
                $methodsAfter = $this->opcData->getAvailablePaymentMethods();
                ///////

                // check if need to reload payment methods
                $useMethod = $this->opcData->checkUpdatedPaymentMethods($methodsBefore, $methodsAfter);
                if ($useMethod != -1) {
                    $responseData['payments'] = $this->_getPaymentMethodsHtml($useMethod);
                }
                /////
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $responseData['message'] = __('Cannot apply points.');
            $this->logger->errorException($e);
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody($this->jsonEncoder->jsonEncode($responseData));
    }
}
