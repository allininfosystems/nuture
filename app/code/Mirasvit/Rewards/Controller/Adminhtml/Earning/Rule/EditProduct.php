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



namespace Mirasvit\Rewards\Controller\Adminhtml\Earning\Rule;

use Magento\Framework\Controller\ResultFactory;

class EditProduct extends \Mirasvit\Rewards\Controller\Adminhtml\Earning\Rule
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $earningRule = $this->_initEarningRule();

        if ($earningRule->getId()) {

            $this->initPage($resultPage)
                ->getConfig()->getTitle()->prepend(__("Edit Earning Rule '%1'", $earningRule->getName()));

            return $resultPage;
        } else {
            $this->messageManager->addError(__('The Earning Rule does not exist.'));
            $this->_redirect('*/*/');
        }
    }
}
