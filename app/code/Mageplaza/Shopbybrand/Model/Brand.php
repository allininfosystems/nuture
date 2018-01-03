<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Shopbybrand\Model;

use Magento\Eav\Model\Config;
use Mageplaza\Shopbybrand\Helper\Data as Helper;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;

/**
 * Class Brand
 * @package Mageplaza\Shopbybrand\Model
 */
class Brand extends \Magento\Framework\Model\AbstractModel
{
	/**
	 * Cache tag
	 *
	 * @var string
	 */
	const CACHE_TAG = 'mageplaza_shopbybrand_brand';

	/**
	 * Cache tag
	 *
	 * @var string
	 */
	protected $_cacheTag = 'mageplaza_shopbybrand_brand';

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'mageplaza_shopbybrand_brand';

	/**
	 * @type \Magento\Eav\Model\Config
	 */
	protected $eavConfig;

	/**
	 * @type \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @type \Mageplaza\Shopbybrand\Helper\Data
	 */
	protected $helper;

	/**
	 * @type \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
	 */
	protected $_attrOptionCollectionFactory;

	/**
	 * Brand constructor.
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Eav\Model\Config $eavConfig
	 * @param \Mageplaza\Shopbybrand\Helper\Data $helper
	 * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
	 * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
	 * @param array $data
	 */
	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		Config $eavConfig,
		Helper $helper,
		CollectionFactory $attrOptionCollectionFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		$this->eavConfig                    = $eavConfig;
		$this->helper                       = $helper;
		$this->_storeManager                = $storeManager;
		$this->_attrOptionCollectionFactory = $attrOptionCollectionFactory;

		parent::__construct($context, $registry, $resource, $resourceCollection, $data);
	}

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Mageplaza\Shopbybrand\Model\ResourceModel\Brand');
	}

	/**
	 * Get identities
	 *
	 * @return array
	 */
	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	/**
	 * @param null $storeId
	 * @param array $conditions
	 * @return mixed
	 */
	public function getBrandCollection($storeId = null, $conditions = [], $sqlString = null)
	{
		$storeId    = is_null($storeId) ? $this->_storeManager->getStore()->getId() : $storeId;
		$attribute  = $this->eavConfig->getAttribute('catalog_product', $this->helper->getAttributeCode());
		$collection = $this->_attrOptionCollectionFactory->create()
			->setPositionOrder('asc')
			->setAttributeFilter($attribute->getId())
			->setStoreFilter($storeId);

		$connection       = $collection->getConnection();
		$storeIdCondition = 0;
		if ($storeId) {
			$storeIdCondition = $connection->select()
				->from(['ab' => $collection->getTable('mageplaza_brand')], 'MAX(ab.store_id)')
				->where('ab.option_id = br.option_id AND ab.store_id IN (0, ' . $storeId . ')');
		}

		$collection->getSelect()
			->joinLeft(
				['br' => $collection->getTable('mageplaza_brand')],
				"main_table.option_id = br.option_id AND br.store_id = (" . $storeIdCondition . ')' . (is_string($conditions) ? $conditions : ''),
				[
					'brand_id' => new \Zend_Db_Expr($connection->getCheckSql('br.store_id = ' . $storeId, 'br.brand_id', 'NULL')),
					'store_id' => new \Zend_Db_Expr($storeId),
					'page_title',
					'url_key',
					'short_description',
					'description',
					'is_featured',
					'static_block',
					'meta_title',
					'meta_keywords',
					'meta_description',
					'image'
				]
			)
			->joinLeft(
				['sw' => $collection->getTable('eav_attribute_option_swatch')],
				"main_table.option_id = sw.option_id",
				['swatch_type' => 'type', 'swatch_value' => 'value']
			)
			->group('option_id');

		if (is_array($conditions)) {
			foreach ($conditions as $field => $condition) {
				$collection->addFieldToFilter($field, $condition);
			}
		}
		if ($sqlString){
            $collection->getSelect()->where($sqlString);
        }

		return $collection;
	}

	/**
	 * @param $optionId
	 * @param null $store
	 * @return mixed
	 */
	public function loadByOption($optionId, $store = null)
	{
		$collection = $this->getBrandCollection($store, ['main_table.option_id' => $optionId]);

		return $collection->getFirstItem();
	}
}
