<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @module      RewardPoints
 * @author        Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */


namespace Magestore\Rewardpoints\Model\Transaction;


class TransactionRepository implements \Magestore\Rewardpoints\Api\TransactionRepositoryInterface
{
    /**
     * @var \Magestore\Rewardpoints\Api\Data\Transaction\TransactionSearchResultsInterfaceFactory
     */
    protected $_transactionSearchResults;

    /**
     * @var \Magestore\Rewardpoints\Model\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * TransactionRepository constructor.
     */
    public function __construct(
        \Magestore\Rewardpoints\Api\Data\Transaction\TransactionSearchResultsInterfaceFactory $transactionSearchResultsInterfaceFactory,
        \Magestore\Rewardpoints\Model\TransactionFactory $transactionFactory,
        \Magento\Framework\Api\SearchCriteriaInterfaceFactory $criteriaInterfaceFactory,
        \Magento\Framework\Webapi\Request $request,
        \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory,
        \Magento\Framework\Api\FilterFactory $filter,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        $this->_transactionSearchResults = $transactionSearchResultsInterfaceFactory;
        $this->_transactionFactory       = $transactionFactory;
        $this->_criteriaFactory          = $criteriaInterfaceFactory;
        $this->_request                  = $request;
        $this->_filterGroup              = $filterGroupFactory;
        $this->_filter                   = $filter;
        $this->_customer                 = $customerFactory;
    }

    /**
     * @param string $param
     * @return \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($param)
    {
        if (!is_numeric($param) && !filter_var($param, FILTER_VALIDATE_EMAIL)) {
            throw new \Magento\Framework\Webapi\Exception(__('Please enter Customer ID or email'));
        }
        /** @var \Magento\Framework\Api\Filter $filter */
        $filter = $this->_filter->create();
        is_numeric($param) ? $filter->setField('customer_id') : $filter->setField('customer_email');
        $filter->setValue($param)->setConditionType('eq');
        /** @var \Magento\Framework\Api\Search\FilterGroup $filterGroup */
        $filterGroup = $this->_filterGroup->create();
        $filterGroup->setFilters([$filter]);
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->_criteriaFactory->create();
        $searchCriteria->setFilterGroups([$filterGroup]);
        $searchCriteria->setPageSize($this->_request->getParam('pageSize'));
        $searchCriteria->setCurrentPage($this->_request->getParam('currentPage'));
        return $this->getList($searchCriteria);
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Rewardpoints\Api\Data\Transaction\TransactionSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magestore\Rewardpoints\Api\Data\Transaction\TransactionSearchResultsInterface $searchResult */
        $searchResult = $this->_transactionSearchResults->create();
        $searchResult->setSearchCriteria($searchCriteria);
        /** @var \Magestore\Rewardpoints\Model\Customer $rewardCustomer */
        $transaction           = $this->_transactionFactory->create();
        $transactionCollection = $transaction->getCollection();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $this->addFilterGroupToCollection($filterGroup, $transactionCollection);
        }
        $searchResult->setTotalCount($transactionCollection->getSize());
        /** @var \Magento\Framework\Api\SortOrder[] $sortOrders */
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $transactionCollection->addOrder($sortOrder->getField(), ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC');
            }
        }
        $transactionCollection->setCurPage($searchCriteria->getCurrentPage());
        $transactionCollection->setPageSize($searchCriteria->getPageSize());
        $items = [];
        foreach ($transactionCollection as $item) {
            $items[] = $item;
        }
        $searchResult->setItems($items);
        return $searchResult;
    }


    /**
     * add a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param mixed $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        $collection
    )
    {
        $fields     = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
        return $this;
    }

    /**
     * @param string $id
     * @return \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface
     */
    public function select($id)
    {
        /** @var \Magestore\Rewardpoints\Model\Transaction $transaction */
        $transaction = $this->_transactionFactory->create();
        $transaction->getResource()->load($transaction, $id);
        return $transaction;
    }

    /**
     * @param string $id
     * @return \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface
     */
    public function complete($id)
    {
        $transaction = $this->select($id);
        try {
            $transaction->completeTransaction();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Webapi\Exception(__($e->getMessage()));
        }
        return $this->select($id);
    }

    /**
     * @param string $id
     * @return \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface
     */
    public function cancel($id)
    {
        /** @var \Magestore\Rewardpoints\Model\Transaction $transaction */
        $transaction = $this->select($id);
        try {
            $transaction->cancelTransaction();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Webapi\Exception(__($e->getMessage()));
        }
        return $this->select($id);
    }

    /**
     * @param string $id
     * @return \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface
     */
    public function expire($id)
    {
        /** @var \Magestore\Rewardpoints\Model\Transaction $transaction */
        $transaction = $this->select($id);
        try {
            $transaction->expireTransaction();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Webapi\Exception(__($e->getMessage()));
        }
        return $this->select($id);
    }

    /**
     * @param \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface $transaction
     * @return \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface
     */
    public function save(\Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface $transaction)
    {
        $this->validateData($transaction);
        /** @var \Magestore\Rewardpoints\Model\Transaction $newTransaction */
        $newTransaction = $this->_transactionFactory->create();
        try {
            $newTransaction->createTransaction($transaction->getData());
        } catch (\Exception $e) {
            throw new \Magento\Framework\Webapi\Exception(__($e->getMessage()));
        }
        return $this->select($newTransaction->getId());
    }

    public function validateData(\Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface $transaction)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_customer->create();
        if ($transaction->getCustomerId()) {
            $customer = $customer->load($transaction->getCustomerId());
            $transaction->setCustomerEmail($customer->getEmail());
        } elseif ($transaction->getCustomerEmail()) {
            $websiteId = $this->_request->getParam('websiteId') ? $this->_request->getParam('websiteId') : 1;
            $customer->setWebsiteId($websiteId);
            $customer = $customer->loadByEmail($transaction->getCustomerEmail());
            $transaction->setCustomerId($customer->getId());
        } else {
            throw new \Magento\Framework\Webapi\Exception(__("customer_id or customer_email is required"));
        }
        /** check point amount */
        if (!$transaction->getPointAmount()) {
            throw new \Magento\Framework\Webapi\Exception(__("point_amount cannot be zero"));
        }
        if (!$transaction->getCreatedTime()) {
            $transaction->setCreatedTime(date('Y-m-d H:i:s'));
        }
        $transaction->setUpdatedTime(date('Y-m-d H:i:s'));
        return $this;
    }


}