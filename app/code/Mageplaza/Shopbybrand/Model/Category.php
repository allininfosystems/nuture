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

/**
 * Class Category
 *
 * @package Mageplaza\Shopbybrand\Model
 */
class Category extends \Magento\Framework\Model\AbstractModel
{

    public $categoryCollectionFactory;
    protected $tableBrandCategory;

    /**
     * Category constructor.
     *
     * @param \Mageplaza\Shopbybrand\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Framework\Model\Context                                      $context
     * @param \Magento\Framework\Registry                                           $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null          $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null                    $resourceCollection
     * @param array                                                                 $data
     */
    public function __construct(\Mageplaza\Shopbybrand\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct(
            $context, $registry, $resource, $resourceCollection, $data
        );
        $this->tableBrandCategory = $resourceConnection->getTableName(
            'mageplaza_shopbybrand_brand_category'
        );
    }

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('Mageplaza\Shopbybrand\Model\ResourceModel\Category');
    }

    /**
     * @return mixed
     */
    public function isEnable()
    {
        return $this->getData('enable');
    }

//************************* Get the Category Collection Depend on Custom Condition ***************************
    /**
     * @param null $whereCond
     * @param null $groupCond
     *
     * @return ResourceModel\Category\Collection
     */
    public function getCategoryCollection($whereCond = null, $groupCond = null)
    {
//      Mageplaza_shopbybrand_category join left mageplaza_shopbybrand_brand_category ;
        $collection = $this->categoryCollectionFactory->create();
        $collection->getSelect()->joinInner(
            ['brand_cat_tbl' => $this->tableBrandCategory],
            'main_table.cat_id = brand_cat_tbl.cat_id'
        );
        if ($whereCond != null) {
//      Example : $collection->getSelect()->where('brand_cat_tbl.option_id IN (213,214)');
            $collection->getSelect()->where($whereCond);
        }
        if ($groupCond != null) {
//      Example : $collection->getSelect()->group('main_table.cat_id');
            $collection->getSelect()->group($groupCond);
        }
        return $collection;
    }
}