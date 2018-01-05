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



namespace Mirasvit\Rewards\Ui\Referral\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\ResourceConnection;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @param Customer $customers
     * @param ResourceConnection $resource
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Customer $customers,
        ResourceConnection $resource,
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        $this->customers = $customers;
        $this->connection = $resource->getConnection('read');
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [];
        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $arrItems['items'][] = $item->getData();
        }

        return $arrItems;
    }

    /**
     * Returns Search result
     *
     * @return SearchResultInterface
     */
    public function getSearchResult()
    {
        $groups     = [];
        $fieldCustomer = '';
        $fieldNewCustomer = '';
        $fieldCustomerValue = '';
        $fieldNewCustomerValue = '';

        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($this->getSearchCriteria()->getFilterGroups() as $group) {
            if (empty($group->getFilters())) {
                continue;
            }
            $filters = [];
            /** @var \Magento\Framework\Api\Filter $filter */
            foreach ($group->getFilters() as $filter) {
                $field = $filter->getField();
                if ($field == 'customer_id') {
                    $fieldCustomer = $field;
                    $fieldCustomerValue = $filter->getValue();
                    continue;
                } elseif ($field == 'new_customer_id') {
                    $fieldNewCustomer = $field;
                    $fieldNewCustomerValue = $filter->getValue();
                    continue;
                }
                $filters[] = $filter;
            }
            $group->setFilters($filters);
            $groups[] = $group;
        }
        $this->getSearchCriteria()->setFilterGroups($groups);

        $collection = $this->getPreparedCollection(
            $fieldCustomer, $fieldCustomerValue, $fieldNewCustomer, $fieldNewCustomerValue
        );

        return $collection;
    }

    /**
     * @param string $fieldCustomer
     * @param string $fieldCustomerValue
     * @param string $fieldNewCustomer
     * @param string $fieldNewCustomerValue
     * @return \Mirasvit\Rewards\Model\ResourceModel\Transaction\Grid\Collection
     */
    protected function getPreparedCollection(
        $fieldCustomer, $fieldCustomerValue, $fieldNewCustomer, $fieldNewCustomerValue
    ) {
        $collection = $this->reporting->search($this->getSearchCriteria());
        if ($fieldCustomerValue && $fieldCustomer == 'customer_id') {
            $customerIds = $this->getCustomerIds($fieldCustomerValue);
            $collection->getSelect()
                ->where(
                    new \Zend_Db_Expr('customer_id IN (' . implode(',', $customerIds) . ')')
                );
        }
        if ($fieldNewCustomerValue && $fieldNewCustomer == 'new_customer_id') {
            $customerIds = $this->getCustomerIds($fieldNewCustomerValue);
            $collection->getSelect()
                ->where(
                    new \Zend_Db_Expr('new_customer_id IN (' . implode(',', $customerIds) . ')')
                );
        }

        return $collection;
    }

    /**
     * @param string $fieldValue
     * @return array
     */
    protected function getCustomerIds($fieldValue)
    {
        $customerSelect = $this->customers->getCollection()->getSelect()
            ->where(
                new \Zend_Db_Expr('CONCAT(firstname, " ", lastname) like "' . addslashes($fieldValue) . '"')
            );

        $customerData = $this->connection->fetchAll($customerSelect);
        $customerIds = [];
        foreach ($customerData as $customer) {
            $customerIds[] = $customer['entity_id'];
        }

        if (!$customerIds) {
            $customerIds[] = 0;
        }

        return $customerIds;
    }

}
