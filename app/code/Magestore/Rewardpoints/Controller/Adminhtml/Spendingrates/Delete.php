<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace  Magestore\Rewardpoints\Controller\Adminhtml\Spendingrates;


/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Delete extends \Magento\Customer\Controller\Adminhtml\Index
{

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('id')) {
            $model = $this->_objectManager->create('Magestore\Rewardpoints\Model\Rate');
            try {
                $model->load($this->getRequest()->getParam('id'))->delete();
                $this->messageManager->addSuccess(__('Spending rate was deleted'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
            return $this->_redirect('*/*/');
        }
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
