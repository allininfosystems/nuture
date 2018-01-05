<?php

/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

namespace WIO\Forum\Model\ResourceModel\Forum\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use WIO\Forum\Model\ResourceModel\Forum\Collection as ForumCollection;

/**
 * Collection for displaying grid of forum
 */
class Collection extends ForumCollection implements SearchResultInterface {

  public function __construct(
  \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Store\Model\StoreManagerInterface $storeManager, $mainTable, $eventPrefix, $eventObject, $resourceModel, $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document', $connection = null, \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
  ) {
    parent::__construct(
            $entityFactory, $logger, $fetchStrategy, $eventManager, $storeManager, $connection, $resource
    );
    $this->_eventPrefix = $eventPrefix;
    $this->_eventObject = $eventObject;
    $this->_init($model, $resourceModel);
    $this->setMainTable($mainTable);
  }

  /**
   * @return AggregationInterface
   */
  public function getAggregations() {
    return $this->aggregations;
  }

  /**
   * @param AggregationInterface $aggregations
   * @return $this
   */
  public function setAggregations($aggregations) {
    $this->aggregations = $aggregations;
  }

  /**
   * Retrieve all ids for collection
   * Backward compatibility with EAV collection
   *
   * @param int $limit
   * @param int $offset
   * @return array
   */
  public function getAllIds($limit = null, $offset = null) {
    return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
  }

  /**
   * Get search criteria.
   *
   * @return \Magento\Framework\Api\SearchCriteriaInterface|null
   */
  public function getSearchCriteria() {
    return null;
  }

  /**
   * Set search criteria.
   *
   * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
   * @return $this
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null) {
    return $this;
  }

  /**
   * Get total count.
   *
   * @return int
   */
  public function getTotalCount() {
    $select = $this->getSelect();
    $select->where('is_category=?', 1)->where('is_deleted=?', 0);
    return $this->getSize();
  }

  /**
   * Set total count.
   *
   * @param int $totalCount
   * @return $this
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function setTotalCount($totalCount) {
    return $this;
  }

  /**
   * Set items list.
   *
   * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
   * @return $this
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function setItems(array $items = null) {
    return $this;
  }

  protected function _beforeLoad() {
    parent::_beforeLoad();
    return $this;
  }

}
