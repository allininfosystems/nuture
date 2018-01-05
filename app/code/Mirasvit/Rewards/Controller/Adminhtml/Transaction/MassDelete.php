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



namespace Mirasvit\Rewards\Controller\Adminhtml\Transaction;

use Mirasvit\Rewards\Model\TransactionFactory;
use Magento\Customer\Model\CustomerFactory;
use Mirasvit\Rewards\Helper\Balance;
use Magento\Framework\Registry;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory;

class MassDelete extends \Mirasvit\Rewards\Controller\Adminhtml\Transaction
{
    public function __construct(
        CookieManagerInterface $cookieManager,
        TransactionFactory $transactionFactory,
        CustomerFactory $customerFactory,
        Balance $rewardsBalance,
        Registry $registry,
        FileFactory $fileFactory,
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct(
            $cookieManager, $transactionFactory, $customerFactory, $rewardsBalance, $registry, $fileFactory, $context
        );
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->transactionFactory = $transactionFactory;

    }
    /**
     * @return void
     */
    public function execute()
    {
        $ids = [];

        if ($this->getRequest()->getParam('transaction_id')) {
            $ids = $this->getRequest()->getParam('transaction_id');
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
                    /** @var \Mirasvit\Rewards\Model\Transaction $transaction */
                    $transaction = $this->transactionFactory->create()
                        ->setIsMassDelete(true)
                        ->load($id);
                    $transaction->delete();
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
            $this->messageManager->addError(__('Please select Transaction(s)'));
        }
        $this->_redirect('*/*/index');
    }
}
