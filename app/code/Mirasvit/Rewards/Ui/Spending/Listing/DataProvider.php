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



namespace Mirasvit\Rewards\Ui\Spending\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\ResourceConnection;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @param  ResourceConnection $resource
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        ResourceConnection $resource,
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        $this->connection = $resource;
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
        $fieldValue = '';
        $fieldWebsiteValue = '';

        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($this->getSearchCriteria()->getFilterGroups() as $group) {
            if (empty($group->getFilters())) {
                continue;
            }
            $filters = [];
            /** @var \Magento\Framework\Api\Filter $filter */
            foreach ($group->getFilters() as $filter) {
                if ($filter->getField() == 'spend_points_str') {
                    $fieldValue = $filter->getValue();
                    continue;
                }
                if ($filter->getField() == 'website_ids') {
                    $fieldWebsiteValue = $filter->getValue();
                    continue;
                }
                $filters[] = $filter;
            }
            $group->setFilters($filters);
            $groups[] = $group;
        }
        $this->getSearchCriteria()->setFilterGroups($groups);

        $collection = $this->getPreparedCollection($fieldValue, $fieldWebsiteValue);

        return $collection;
    }

    /**
     * @param string $fieldValue
     * @param string $fieldWebsiteValue
     * @return Mirasvit\Rewards\Model\ResourceModel\Spending\Grid\Collection
     */
    protected function getPreparedCollection($fieldValue, $fieldWebsiteValue)
    {
        $collection = $this->reporting->search($this->getSearchCriteria());
        if ($fieldValue) {
            $collection->addSpendPointsColumn()->getSelect()
                ->where(
                    new \Zend_Db_Expr('IF (' .
                        'main_table.monetary_step > 0, ' .
                        'CONCAT("for each ", main_table.spend_points, " points give $", main_table.monetary_step, " discount"), ' .
                        'main_table.spend_points' .
                        ') like "' . addslashes($fieldValue) . '"'
                    )
                );
        }
        if ($fieldWebsiteValue) {
            $spendingRuleIds = $this->getSpendingRuleIds($fieldWebsiteValue);
            $collection->getSelect()
                ->where(
                    new \Zend_Db_Expr('spending_rule_id IN (' . implode(',', $spendingRuleIds) . ')')
                );
        }

        return $collection;
    }

    /**
     * @param string $fieldValue
     * @return array
     */
    protected function getSpendingRuleIds($fieldWebsiteValue)
    {
        $query = 'SELECT spending_rule_id FROM '
            . $this->connection->getTableName('mst_rewards_spending_rule_website')
            . ' WHERE website_id IN (' . addslashes(implode(',', $fieldWebsiteValue)) . ')';

        $websiteData = $this->connection->getConnection('read')->fetchAll($query);
        $spendingRuleIds = [];
        foreach ($websiteData as $website) {
            $spendingRuleIds[] = $website['spending_rule_id'];
        }

        if (!$spendingRuleIds) {
            $spendingRuleIds[] = 0;
        }

        return $spendingRuleIds;
    }

}
