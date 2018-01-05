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

class ApplyPointsMagegiantstepcheckout extends \Mirasvit\Rewards\Controller\Checkout
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        Mage::app()->getRequest()->setActionName('applyCoupon');
        $response = $this->processRequest();
        $result = [
            'success' => true,
            'coupon_applied' => false,
            'messages' => [],
            'blocks' => [],
            'grand_total' => '',
        ];
        if ($response['message']) {
            $result['coupon_applied'] = true;
        }
        $result['messages'][] = $response['message'];
        $result['blocks'] = $this->getUpdater()->getBlocks();
        $result['grand_total'] = $this->onestepcheckoutData->getGrandTotal($this->getOnepage()->getQuote());

        $this->getResponse()->setBody($this->jsonEncoder->jsonEncode($result));
    }
}
