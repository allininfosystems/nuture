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



namespace Mirasvit\Rewards\Model\ResourceModel\Referral;

/**
 * @method \Mirasvit\Rewards\Model\Referral getFirstItem()
 * @method \Mirasvit\Rewards\Model\Referral getLastItem()
 * @method \Mirasvit\Rewards\Model\ResourceModel\Referral\Collection|\Mirasvit\Rewards\Model\Referral[] addFieldToFilter
 * @method \Mirasvit\Rewards\Model\ResourceModel\Referral\Collection|\Mirasvit\Rewards\Model\Referral[] setOrder
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'referral_id'; //use in massaction

    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    protected $entityAttributeFactory;

    /**
     * @var \Magento\Framework\Data\Collection\EntityFactoryInterface
     */
    protected $entityFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Data\Collection\Db\FetchStrategyInterface
     */
    protected $fetchStrategy;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected $resource;

    /**
     * @param \Magento\Eav\Model\Entity\AttributeFactory                   $entityAttributeFactory
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface               $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb         $resource
     */
    public function __construct(
        \Magento\Eav\Model\Entity\AttributeFactory $entityAttributeFactory,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->entityAttributeFactory = $entityAttributeFactory;
        $this->entityFactory = $entityFactory;
        $this->logger = $logger;
        $this->fetchStrategy = $fetchStrategy;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->connection = $connection;
        $this->resource = $resource;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Rewards\Model\Referral', 'Mirasvit\Rewards\Model\ResourceModel\Referral');
    }

    /**
     * @param bool|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = ['value' => 0, 'label' => __('-- Please Select --')];
        }
        /** @var \Mirasvit\Rewards\Model\Referral $item */
        foreach ($this as $item) {
            $arr[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $arr;
    }

    /**
     * @param bool|false $emptyOption
     * @return array
     */
    public function getOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = __('-- Please Select --');
        }
        /** @var \Mirasvit\Rewards\Model\Referral $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    /************************/

    /**
     * Add Name to select.
     *
     * @return \Mirasvit\Rewards\Model\ResourceModel\Referral\Collection
     */
    public function addNameToSelect()
    {
        $this->getSelect()
            ->joinLeft(
                ['ce2' => $this->getTable('customer_entity')],
                'ce2.entity_id=main_table.customer_id ',
                new \Zend_Db_Expr("CONCAT(`ce2`.`firstname`, ' ',`ce2`.`lastname`) AS customer_name")
            );

        $this->getSelect()
            ->joinLeft(
                ['ce3' => $this->getTable('customer_entity')],
                'ce3.entity_id=main_table.new_customer_id ',
                new \Zend_Db_Expr("CONCAT(`ce3`.`firstname`, ' ',`ce3`.`lastname`) AS new_customer_name")
            );

        return $this;
    }
}
