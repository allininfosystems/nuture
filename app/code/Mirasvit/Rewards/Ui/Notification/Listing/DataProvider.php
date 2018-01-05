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



namespace Mirasvit\Rewards\Ui\Notification\Listing;

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
        $fieldWebsiteValue = '';

        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($this->getSearchCriteria()->getFilterGroups() as $group) {
            if (empty($group->getFilters())) {
                continue;
            }
            $filters = [];
            /** @var \Magento\Framework\Api\Filter $filter */
            foreach ($group->getFilters() as $filter) {
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

        $collection = $this->getPreparedCollection($fieldWebsiteValue);

        return $collection;
    }

    /**
     * @param string $fieldWebsiteValue
     * @return Mirasvit\Rewards\Model\ResourceModel\Notification\Grid\Collection
     */
    protected function getPreparedCollection($fieldWebsiteValue)
    {
        $collection = $this->reporting->search($this->getSearchCriteria());
        if ($fieldWebsiteValue) {
            $notificationRuleIds = $this->getNotificationRuleIds($fieldWebsiteValue);
            $collection->getSelect()
                ->where(
                    new \Zend_Db_Expr('notification_rule_id IN (' . implode(',', $notificationRuleIds) . ')')
                );
        }

        return $collection;
    }

    /**
     * @param string $fieldWebsiteValue
     * @return array
     */
    protected function getNotificationRuleIds($fieldWebsiteValue)
    {
        $query = 'SELECT notification_rule_id FROM '
            . $this->connection->getTableName('mst_rewards_notification_rule_website')
            . ' WHERE website_id IN (' . addslashes(implode(',', $fieldWebsiteValue)) . ')';

        $websiteData = $this->connection->getConnection('read')->fetchAll($query);
        $notificationRuleIds = [];
        foreach ($websiteData as $website) {
            $notificationRuleIds[] = $website['notification_rule_id'];
        }

        if (!$notificationRuleIds) {
            $notificationRuleIds[] = 0;
        }

        return $notificationRuleIds;
    }

}
