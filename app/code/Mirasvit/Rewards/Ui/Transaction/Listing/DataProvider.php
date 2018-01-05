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



namespace Mirasvit\Rewards\Ui\Transaction\Listing;

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
        $fieldName = '';
        $fieldEmail = '';
        $fieldNameValue = '';
        $fieldEmailValue = '';

        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($this->getSearchCriteria()->getFilterGroups() as $group) {
            if (empty($group->getFilters())) {
                continue;
            }
            $filters = [];
            /** @var \Magento\Framework\Api\Filter $filter */
            foreach ($group->getFilters() as $filter) {
                $field = $filter->getField();
                if ($field == 'customer_name') {
                    $fieldName = $field;
                    $fieldNameValue = $filter->getValue();
                    continue;
                } elseif ($field == 'customer_email') {
                    $fieldEmail = $field;
                    $fieldEmailValue = $filter->getValue();
                    continue;
                }
                $filters[] = $filter;
            }
            $group->setFilters($filters);
            $groups[] = $group;
        }
        $this->getSearchCriteria()->setFilterGroups($groups);

        $collection = $this->getPreparedCollection($fieldName, $fieldNameValue, $fieldEmail, $fieldEmailValue);

        return $collection;
    }

    /**
     * @param string $fieldName
     * @param string $fieldNameValue
     * @param string $fieldEmail
     * @param string $fieldEmailValue
     * @return Mirasvit\Rewards\Model\ResourceModel\Transaction\Grid\Collection
     */
    protected function getPreparedCollection($fieldName, $fieldNameValue, $fieldEmail, $fieldEmailValue)
    {
        $collection = $this->reporting->search($this->getSearchCriteria());
        if ($fieldNameValue && $fieldName == 'customer_name') {
            $customerIds = $this->getCustomerNameIds($fieldNameValue);
            $collection->getSelect()
                ->where(
                    new \Zend_Db_Expr('customer_id IN (' . implode(',', $customerIds) . ')')
                );
        }
        if ($fieldEmailValue && $fieldEmail == 'customer_email') {
            $customerIds = $this->getCustomerEmailIds($fieldEmailValue);
            $collection->getSelect()
                ->where(
                    new \Zend_Db_Expr('customer_id IN (' . implode(',', $customerIds) . ')')
                );
        }
        $collection->joinCustomerGroup();

        return $collection;
    }

    /**
     * @param string $fieldValue
     * @return array
     */
    protected function getCustomerNameIds($fieldValue)
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

    /**
     * @param string $fieldValue
     * @return array
     */
    protected function getCustomerEmailIds($fieldValue)
    {
        $customerCollection = $this->customers->getCollection()
            ->addFieldToFilter('email', ["like" => $fieldValue]);

        $customerData = $customerCollection->getData();
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
