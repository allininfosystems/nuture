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



namespace Mirasvit\Rewards\Controller\Adminhtml\Spending\Rule;

use Magento\Framework\Controller\ResultFactory;

class Save extends \Mirasvit\Rewards\Controller\Adminhtml\Spending\Rule
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        if ($data = $this->getRequest()->getParams()) {
            $spendingRule = $this->_initSpendingRule();

            $spendingRule->addData($data);
            if (isset($data['rule'])) {
                $spendingRule->loadPost($data['rule']);
            }

            try {
                if (!$spendingRule->validateFields()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __("Field 'Spend maximum' can not be less or equal then field 'Spend minimum'")
                    );
                }
                $spendingRule->getResource()->save($spendingRule);
                $this->messageManager->addSuccess(__('Spending Rule was successfully saved'));
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit',
                        ['id' => $spendingRule->getId(), 'store' => $spendingRule->getStoreId()]
                    );

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->backendSession->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

                return;
            }
        }
        $this->messageManager->addError(__('Unable to find Spending Rule to save'));
        $this->_redirect('*/*/');
    }
}
