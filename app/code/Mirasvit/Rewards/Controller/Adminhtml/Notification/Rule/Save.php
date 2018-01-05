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



namespace Mirasvit\Rewards\Controller\Adminhtml\Notification\Rule;

use Magento\Framework\Controller\ResultFactory;

class Save extends \Mirasvit\Rewards\Controller\Adminhtml\Notification\Rule
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        if ($data = $this->getRequest()->getParams()) {
            $notificationRule = $this->_initNotificationRule();

            $notificationRule->addData($data);
            if (isset($data['rule'])) {
                $notificationRule->loadPost($data['rule']);
            }

            try {
                $notificationRule->save();

                $this->messageManager->addSuccess(__('Notification Rule was successfully saved'));
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit',
                        ['id' => $notificationRule->getId(), 'store' => $notificationRule->getStoreId()]
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
        $this->messageManager->addError(__('Unable to find Notification Rule to save'));
        $this->_redirect('*/*/');
    }
}
