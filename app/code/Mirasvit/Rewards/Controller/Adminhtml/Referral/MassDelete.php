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



namespace Mirasvit\Rewards\Controller\Adminhtml\Referral;

use Mirasvit\Rewards\Model\ReferralFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory;

class MassDelete extends \Mirasvit\Rewards\Controller\Adminhtml\Referral
{
    public function __construct(
        ReferralFactory $referralFactory,
        TimezoneInterface $localeDate,
        Registry $registry,
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($referralFactory, $localeDate, $registry, $context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->referralFactory = $referralFactory;

    }
    /**
     * @return void
     */
    public function execute()
    {
        $ids = [];

        if ($this->getRequest()->getParam('referral_id')) {
            $ids = $this->getRequest()->getParam('referral_id');
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
                foreach ($ids as $id) {
                    /** @var \Mirasvit\Rewards\Model\Referral $referral */
                    $referral = $this->referralFactory->create()
                        ->setIsMassDelete(true)
                        ->load($id);
                    $referral->delete();
                }
                $this->messageManager->addSuccess(
                    __(
                        'Total of %1 record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $this->messageManager->addError(__('Please select Referral(s)'));
        }
        $this->_redirect('*/*/index');
    }
}
