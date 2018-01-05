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


namespace Mirasvit\Rewards\Controller\Adminhtml\Report;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Aggregate extends Action
{
    /**
     * @param Context  $context
     */
    public function __construct(
        \Mirasvit\Rewards\Model\ResourceModel\Report\PointsFactory $reportPointsFactory,
        Context $context
    ) {
        $this->reportPointsFactory = $reportPointsFactory;
        $this->context             = $context;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $this->reportPointsFactory->create()->aggregate();
            $this->messageManager->addSuccessMessage(
                __('Statistics was successfully updated')
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Zend_Db_Statement_Exception $e) {
            $this->messageManager->addErrorMessage(__('Database error. Please contact developers.'));
        }

        $this->_redirect('*/*/view');
    }
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Rewards::reward_points_report');
    }
}