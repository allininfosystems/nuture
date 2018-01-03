<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_ProductStockAlert
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductStockAlert\Model\ResourceModel\Option;

/**
 * Bundle Options Resource Collection
 */
class Collection extends \Magento\Bundle\Model\ResourceModel\Option\Collection
{
    /**
     * All item ids cache
     *
     * @var array
     */
    protected $_itemIds;

    /**
     * True when selections appended
     *
     * @var bool
     */
    protected $_selectionsAppended = false;

    protected $helper;

    protected $httpContext;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Bss\ProductStockAlert\Helper\Data $helper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->httpContext = $httpContext;
        $this->helper = $helper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Init model and resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Bundle\Model\Option', 'Magento\Bundle\Model\ResourceModel\Option');
    }

    /**
     * Joins values to options
     *
     * @param int $storeId
     * @return $this
     */
    public function joinValues($storeId)
    {
        $this->getSelect()->joinLeft(
            ['option_value_default' => $this->getTable('catalog_product_bundle_option_value')],
            'main_table.option_id = option_value_default.option_id and option_value_default.store_id = 0',
            []
        )->columns(
            ['default_title' => 'option_value_default.title']
        );

        $title = $this->getConnection()->getCheckSql(
            'option_value.title IS NOT NULL',
            'option_value.title',
            'option_value_default.title'
        );
        if ($storeId !== null) {
            $this->getSelect()->columns(
                ['title' => $title]
            )->joinLeft(
                ['option_value' => $this->getTable('catalog_product_bundle_option_value')],
                $this->getConnection()->quoteInto(
                    'main_table.option_id = option_value.option_id and option_value.store_id = ?',
                    $storeId
                ),
                []
            );
        }
        return $this;
    }

    /**
     * Sets product id filter
     *
     * @param int $productId
     * @return $this
     */
    public function setProductIdFilter($productId)
    {
        $productTable = $this->getTable('catalog_product_entity');
        $linkField = $this->getConnection()->getAutoIncrementField($productTable);
        $this->getSelect()->join(
            ['cpe' => $productTable],
            'cpe.'.$linkField.' = main_table.parent_id',
            []
        )->where(
            "cpe.entity_id = ?",
            $productId
        );

        return $this;
    }

    /**
     * Set product link filter
     *
     * @param int $productLinkFieldValue
     *
     * @return $this
     */
    public function setProductLinkFilter($productLinkFieldValue)
    {
        $this->getSelect()->where(
            'main_table.parent_id = ?',
            $productLinkFieldValue
        );
        return $this;
    }

    /**
     * Sets order by position
     *
     * @return $this
     */
    public function setPositionOrder()
    {
        $this->getSelect()->order('main_table.position asc')->order('main_table.option_id asc');
        return $this;
    }

    /**
     * Append selection to options
     * stripBefore - indicates to reload
     * appendAll - indicates do we need to filter by saleable and required custom options
     *
     * @param \Magento\Bundle\Model\ResourceModel\Selection\Collection $selectionsCollection
     * @param bool $stripBefore
     * @param bool $appendAll
     * @return \Magento\Framework\DataObject[]
     */
    public function appendSelections($selectionsCollection, $stripBefore = false, $appendAll = true)
    {
        if ($stripBefore) {
            $this->_stripSelections();
        }

        if (!$this->_selectionsAppended) {
            foreach ($selectionsCollection->getItems() as $key => $selection) {
                $option = $this->getItemById($selection->getOptionId());
                if ($option) {
                    if($this->helper->checkCustomer(
                        $this->httpContext->getValue(
                            \Bss\ProductStockAlert\Model\Customer\Context::CONTEXT_CUSTOMER_GROUP_ID
                            )
                        ) && $this->helper->isStockAlertAllowed()) {
                        if ($appendAll || !$selection->getRequiredOptions()) {
                            $selection->setOption($option);
                            $option->addSelection($selection);
                        } else {
                            $selectionsCollection->removeItemByKey($key);
                        }
                    }else{
                        if ($appendAll || $selection->isSalable() && !$selection->getRequiredOptions()) {
                            $selection->setOption($option);
                            $option->addSelection($selection);
                        } else {
                            $selectionsCollection->removeItemByKey($key);
                        }
                    }
                }
            }
            $this->_selectionsAppended = true;
        }

        return $this->getItems();
    }

    /**
     * Removes appended selections before
     *
     * @return $this
     */
    protected function _stripSelections()
    {
        foreach ($this->getItems() as $option) {
            $option->setSelections([]);
        }
        $this->_selectionsAppended = false;
        return $this;
    }

    /**
     * Sets filter by option id
     *
     * @param array|int $ids
     * @return $this
     */
    public function setIdFilter($ids)
    {
        if (is_array($ids)) {
            $this->addFieldToFilter('main_table.option_id', ['in' => $ids]);
        } elseif ($ids != '') {
            $this->addFieldToFilter('main_table.option_id', $ids);
        }
        return $this;
    }

    /**
     * Reset all item ids cache
     *
     * @return $this
     */
    public function resetAllIds()
    {
        $this->_itemIds = null;
        return $this;
    }

    /**
     * Retrieve all ids for collection
     *
     * @return array
     */
    public function getAllIds()
    {
        if ($this->_itemIds === null) {
            $this->_itemIds = parent::getAllIds();
        }
        return $this->_itemIds;
    }
}
