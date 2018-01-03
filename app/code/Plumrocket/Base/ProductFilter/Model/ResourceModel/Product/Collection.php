<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Product Filter v3.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ProductFilter\Model\ResourceModel\Product;

class Collection extends \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
{
    /**
     * @var \Plumrocket\ProductFilter\Helper\Data
     */
    protected $helperData;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactory                $entityFactory
     * @param \Psr\Log\LoggerInterface                                        $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface    $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                       $eventManager
     * @param \Magento\Eav\Model\Config                                       $eavConfig
     * @param \Magento\Framework\App\ResourceConnection                       $resource
     * @param \Magento\Eav\Model\EntityFactory                                $eavEntityFactory
     * @param \Magento\Catalog\Model\ResourceModel\Helper                     $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory                   $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager
     * @param \Magento\Framework\Module\Manager                               $moduleManager
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State               $catalogProductFlatState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface              $scopeConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory                    $productOptionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Url                        $catalogUrl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface            $localeDate
     * @param \Magento\Customer\Model\Session                                 $customerSession
     * @param \Magento\Framework\Stdlib\DateTime                              $dateTime
     * @param \Magento\Customer\Api\GroupManagementInterface                  $groupManagement
     * @param \Magento\Search\Model\QueryFactory                              $catalogSearchData
     * @param \Magento\Framework\Search\Request\Builder                       $requestBuilder
     * @param \Magento\Search\Model\SearchEngine                              $searchEngine
     * @param \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory
     * @param \Plumrocket\ProductFilter\Helper\Data                           $helperData
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null             $connection
     * @param string                                                          $searchRequestName
     * @param \Magento\Framework\Api\Search\SearchResultFactory|null          $searchResultFactory
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Search\Model\QueryFactory $catalogSearchData,
        \Magento\Framework\Search\Request\Builder $requestBuilder,
        \Magento\Search\Model\SearchEngine $searchEngine,
        \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory,
        \Plumrocket\ProductFilter\Helper\Data $helperData,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        $searchRequestName = 'catalog_view_container',
        \Magento\Framework\Api\Search\SearchResultFactory $searchResultFactory = null
    ) {
        $this->helperData = $helperData;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $catalogSearchData,
            $requestBuilder,
            $searchEngine,
            $temporaryStorageFactory,
            $connection,
            $searchRequestName,
            $searchResultFactory
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        $this->searchResultFactory = $this->helperData;

        if (!$this->helperData->moduleEnabled()) {
            return parent::getSize();
        }

        $sql = $this->getSelectCountSql();
        $this->_totalRecords = $this->getConnection()->fetchOne($sql, $this->_bindParams);
        return intval($this->_totalRecords);
    }
}
