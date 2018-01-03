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

namespace Plumrocket\ProductFilter\Model\Catalog\Layer\Filter\DataProvider;

class Category extends \Magento\Catalog\Model\Layer\Filter\DataProvider\Category
{

    /**
     * Categories
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection | null
     */
    protected $categories;

    /**
     * Category ids
     * @var Array
     */
    protected $categoryIds;

    /**
     * Category Factory
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * Layer
     * @var \Magento\Catalog\Model\Layer
     */
    protected $layer;

    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Can procced filter logic
     * @var boolean
     */
    protected $canProceed;

    protected $categoryId;

    /**
     * @var int
     */
    protected $category;

    /**
     * @var bool
     */
    protected $isApplied = false;

    /**
     * @param Registry $coreRegistry
     * @param CategoryModelFactory $categoryFactory
     * @param Layer $layer
     * @internal param $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Layer $layer
    ) {
        parent::__construct($coreRegistry, $categoryFactory, $layer);
        $this->coreRegistry = $coreRegistry;
        $this->layer = $layer;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        if ($this->canProceed) {
            return $this->getCategories();
        }
        return parent::getCategory();
    }

    /**
     * Retrieve categories
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Exception
     */
    public function getCategories()
    {
        if ($this->categories === null) {
            /** @var CategoryModel|null $categories */
            $categories = null;

            if ($this->categoryIds === null) {
                if ($this->categoryId) {
                    $this->categoryIds = [$this->categoryId];
                } elseif ($this->getLayer()->getCurrentCategory()) {
                    $this->categoryIds = [$this->getLayer()->getCurrentCategory()->getId()];
                }
            }

            if (!is_array($this->categoryIds)) {
                throw new \Exception("Category Ids must be array");
            }

            if ($this->categoryIds !== null) {
                $categories = $this->categoryFactory->create()
                    ->setStoreId(
                        $this->getLayer()
                            ->getCurrentStore()
                            ->getId()
                    )
                    ->getCollection()
                    ->addAttributeToSelect(['name', 'url_key'])
                    ->addFieldToFilter('entity_id', ['in' => $this->categoryIds]);
            }

            $this->coreRegistry->register('current_category_filter', $categories, true);
            $this->categories = $categories;
        }

        return $this->categories;
    }

    /**
     * Can procced
     * @param bool $val
     */
    public function setCanProceed($val)
    {
        $this->canProceed = (boolean)$val;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
    */
    public function setCategoryId($categoryId)
    {
        $this->isApplied = true;
        $this->category = null;
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Category Ids
     * @param Array $ids
     */
    public function setCategoryIds($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $this->categoryIds = $ids;
        return $this;
    }

    /**
     * @return Layer
     */
    private function getLayer()
    {
        return $this->layer;
    }
}
