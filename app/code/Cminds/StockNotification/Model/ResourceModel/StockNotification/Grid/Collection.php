<?php

namespace Cminds\StockNotification\Model\ResourceModel\StockNotification\Grid;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    private $attributeRepository;
    private $storeManager;

    /**
     * Collection initialization.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
    
        $this->attributeRepository = $attributeRepository;
        $this->storeManager = $storeManager;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Init Model and ResourceModel.
     */
    protected function _construct()
    {
        $this->_init(
            \Cminds\StockNotification\Model\StockNotification::class,
            \Cminds\StockNotification\Model\ResourceModel\StockNotification::class
        );
    }

    /**
     * Initial select for Collection.
     *
     * @return Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $collection = $this
            ->addRequestsColumn()
            ->addRequestsInPeriod('requests_last_week', '7', 'DAY')
            ->addRequestsInPeriod('requests_last_month', '1', 'MONTH')
            ->addRequestsInPeriod('requests_last_6_months', '6', 'MONTH')
            ->addLastRequestColumn()
            ->addFirstRequestColumn()
            ->joinProductSku()
            ->joinProductNames()
            ->addGrouping();

        return $collection;
    }

    /**
     * @param $columnName
     * @param $quantity
     * @param $unitType
     *
     * @return Collection
     */
    private function addRequestsInPeriod($columnName, $quantity, $unitType)
    {
        $this->getSelect()->columns([
            $columnName => (string)
                'SUM(if(main_table.created_at >= DATE_SUB(CURDATE(), INTERVAL '
                . $quantity . ' ' . $unitType . '), 1, 0))'
            ]);

        return $this;
    }

    /**
     * Add product grouping to show only one record for specific criteria.
     *
     * @return Collection
     */
    private function addGrouping()
    {
        $this->getSelect()->group('product_id');

        return $this;
    }

    /**
     * Count requests for specific product id.
     *
     * @return Collection
     */
    private function addRequestsColumn()
    {
        $this->getSelect()->columns(['requests' => 'COUNT(product_id)']);

        return $this;
    }

    /**
     * Return last request column.
     *
     * @return Collection
     */
    private function addLastRequestColumn()
    {
        $this->getSelect()->columns(['last_request' => 'MAX(main_table.created_at)']);

        return $this;
    }

    /**
     * Return first request column.
     *
     * @return Collection
     */
    private function addFirstRequestColumn()
    {
        $this->getSelect()->columns(['first_request' => 'MIN(main_table.created_at)']);

        return $this;
    }

    /**
     * Join product sku's to main table.
     *
     * @return Collection
     */
    private function joinProductSku()
    {
        $this->getSelect()
            ->joinLeft(
                ['product_entity' => $this->getTable('catalog_product_entity')],
                'main_table.product_id = product_entity.entity_id',
                ['entity_id', 'sku']
            );

        return $this;
    }

    /**
     * Join product names to main table.
     *
     * @return Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function joinProductNames()
    {
        $attr = $this->getAttributeIdOfProductName();
        $storeId = $this->getStoreId();

        $this->getSelect()
            ->joinLeft(
                ['catalog_product_entity_varchar' => $this->getTable('catalog_product_entity_varchar')],
                'main_table.product_id = catalog_product_entity_varchar.entity_id',
                ['product_name' => 'value']
            )
            ->where(
                'catalog_product_entity_varchar.store_id = ' . $storeId
            )
            ->where(
                'catalog_product_entity_varchar.attribute_id = ' . $attr
            );

        return $this;
    }

    /**
     * Get store id.
     *
     * @return int
     */
    private function getStoreId()
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        if ($storeId === 1) {
            return 0;
        }

        return $storeId;
    }

    /**
     * Get specific product attribute.
     *
     * @return int|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getAttributeIdOfProductName()
    {

        $productNameAttributeId = $this->attributeRepository
            ->get('catalog_product', 'name')
            ->getAttributeId();

        return $productNameAttributeId;
    }
}
