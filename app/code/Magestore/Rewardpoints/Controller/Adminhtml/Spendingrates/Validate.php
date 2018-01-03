<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Controller\Adminhtml\Spendingrates;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Message\Error;

class Validate extends \Magento\Customer\Controller\Adminhtml\Index
{

    /**
     * AJAX customer validation action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(0);
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }

    /**
     * Check the permission to Manage Customers
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Rewardpoints::Spending_Rates');
    }
}
