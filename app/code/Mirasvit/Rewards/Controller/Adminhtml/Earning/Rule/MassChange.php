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

use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\CollectionFactory;

class MassChange extends \Mirasvit\Rewards\Controller\Adminhtml\Earning\Rule
{
    public function __construct(
        \Mirasvit\Rewards\Model\Earning\RuleFactory $earningRuleFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($earningRuleFactory, $localeDate, $registry, $context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->earningRuleFactory = $earningRuleFactory;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $ids = [];

        if ($this->getRequest()->getParam('earning_rule_id')) {
            $ids = $this->getRequest()->getParam('earning_rule_id');
        }

        if ($this->getRequest()->getParam(Filter::SELECTED_PARAM)) {
            $ids = $this->getRequest()->getParam(Filter::SELECTED_PARAM);
        }

        if (!$ids) {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $ids = $collection->getAllIds();
        }

        if ($ids && is_array($ids)) {
            try {
                $isActive = $this->getRequest()->getParam('status');
                $isActive = ($isActive == 1) ? 1 : 0;
                foreach ($ids as $id) {
                    /** @var \Mirasvit\Rewards\Model\Earning\Rule $earningRule */
                    $earningRule = $this->earningRuleFactory->create()->load($id);
                    $earningRule->setIsActive($isActive);
                    $earningRule->save();
                }
                $this->messageManager->addSuccess(
                    __(
                        'Total of %1 record(s) were successfully updated', count($ids)
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $this->messageManager->addError(__('Please select Earning Rule(s)'));
        }
        $this->_redirect('*/*/index');
    }
}
