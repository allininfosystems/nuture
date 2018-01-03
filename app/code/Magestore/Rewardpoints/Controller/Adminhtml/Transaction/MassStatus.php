<?php
namespace Magestore\Rewardpoints\Controller\Adminhtml\Transaction;

use Magento\Backend\App\Action\Context;
use Magestore\Rewardpoints\Model\ResourceModel\Transaction\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magestore\Rewardpoints\Model\ResourceModel\Transaction\Collection;

/**
 * Class MassDelete
 */
class MassStatus extends AbstractMassAction
{
    protected $_modelTransaction;
    protected $_collectionFactoryTransaction;
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Magestore\Rewardpoints\Model\Transaction $transaction
    ) {
        $this->_modelTransaction = $transaction;
        $this->_collectionFactoryTransaction = $collectionFactory;
        parent::__construct($context, $filter, $collectionFactory);
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(Collection $collection)
    {
        $status = $this->getRequest()->getParam('status');
        switch ($status){
            case $this->_modelTransaction->getConst('STATUS_COMPLETED')://complete
                $this->massCompleteAction($collection);
            break;
            case $this->_modelTransaction->getConst('STATUS_CANCELED')://Canceled
                $this->massCancelAction($collection);
            break;
            case $this->_modelTransaction->getConst('STATUS_EXPIRED')://Expired
                $this->massExpireAction($collection);
            break;
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
    /**
     * mass complete transaction(s) action
     */
    protected  function massCompleteAction($collection){
        $collection
            ->addFieldToFilter('point_amount', array('gt' => 0))
            ->addFieldToFilter('status', array(
                'lt' => $this->_modelTransaction->getConst('STATUS_COMPLETED')
            ));
        $total = 0;
        foreach($collection as $model){
            try {
                if($model->completeTransaction()){
                    $total++;
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        if ($total > 0) {
            $this->messageManager->addSuccess(__('Total of %1 transaction(s) were successfully completed', $total));
        } else {
            $this->messageManager->addWarning(
                __('No transaction was completed')
            );
        }

    }
    /**
     * mass cancel transaction(s) action
     */
    protected  function massCancelAction($collection){
        $collection
            ->addFieldToFilter('point_amount', array('gt' => 0))
            ->addFieldToFilter('status', array(
                'lteq' => $this->_modelTransaction->getConst('STATUS_COMPLETED')
            ));
        $total = 0;
        foreach($collection as $transaction){
            try {
                if($transaction->cancelTransaction()){
                    $total++;
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        if ($total > 0) {
            $this->messageManager->addSuccess(__('Total of %1 transaction(s) were successfully canceled', $total));
        } else {
            $this->messageManager->addWarning(
                __('No transaction was canceled')
            );
        }

    }

    /**
     * mass expire selected transaction(s)
     */
    public function massExpireAction($collection)
    {
        $collection
            ->addAvailableBalanceFilter()
            ->addFieldToFilter('status', array(
                'lteq' => $this->_modelTransaction->getConst('STATUS_COMPLETED')
            ))
            ->addFieldToFilter('expiration_date', array('notnull' => true))
            ->addFieldToFilter('expiration_date', array('to' => date('Y-m-d H:i:s') ) );

        $total = 0;
        foreach ($collection as $transaction) {
            try {
                if($transaction->expireTransaction()){
                    $total++;
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        if ($total > 0) {
            $this->messageManager->addSuccess(__('Total of %1 transaction(s) were successfully expired', $total));
        } else {
            $this->messageManager->addWarning(
                __('No transaction was expired')
            );
        }
    }

    /**
     * Check the permission to Manage Customers
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Rewardpoints::Manage_transaction');
    }

}
