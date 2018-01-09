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
 * @package   mirasvit/module-kb
 * @version   1.0.26
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Kb\Block\Category;

class Sidebar extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Mirasvit\Kb\Helper\Data
     */
    protected $kbData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Mirasvit\Kb\Helper\Data                                    $kbData
     * @param \Magento\Framework\Registry                                 $registry
     * @param \Magento\Framework\View\Element\Template\Context            $context
     * @param array                                                       $data
     */
    public function __construct(
        \Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Mirasvit\Kb\Helper\Data $kbData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->kbData = $kbData;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @var array|null
     */
    protected $tree = null;

    /**
     * @return bool
     */
    public function isRootCategory()
    {
        if ($this->registry->registry('kb_current_category')
            && $this->registry->registry('kb_current_category')->getId() == $this->kbData->getRootCategory()->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @param object $category
     *
     * @return bool
     */
    public function isActive($category)
    {
        if ($this->registry->registry('kb_current_category')
            && $this->registry->registry('kb_current_category')->getId() == $category->getId()) {
            return true;
        }

        if ($this->registry->registry('current_article')
            && in_array($category->getId(), $this->registry->registry('current_article')->getCategoryIds())) {
            return true;
        }

        return false;
    }
	
	public function getCurrentCatId()
	{
		return $this->registry->registry('kb_current_category')->getId();
	}

    /**
     * @return array|null
     */
    public function getCategoryTree()
    {
        if ($this->tree == null) {
            $this->tree = $this->_getCategoryTree();
        }

        return $this->tree;
    }

    /**
     * @return int
     */
    public function getMinCategoryLevel()
    {
        $min = 100;
        foreach ($this->getCategoryTree() as $item) {
            if ($item->getLevel() < $min) {
                $min = $item->getLevel();
            }
        }

        return $min;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->context->getStoreManager()->getStore()->getId();
    }

    /**
     * @param null|int $parentId
     *
     * @return array
     */
    protected function _getCategoryTree($parentId = null)
    {
        $list = [];

        if ($parentId == null) {
            $parentId = $this->kbData->getRootCategory()->getId();
        }

        $collection = $this->categoryCollectionFactory->create()
            ->addFieldToFilter('is_active', true)
            ->addFieldToFilter('parent_id', $parentId)
            ->addStoreIdFilter($this->context->getStoreManager()->getStore()->getId())
            ->setOrder('position', 'asc')
            ;

        foreach ($collection as $item) {
            $list[] = $item;
            if ($item->hasChildren()) {
                $childrens = $this->_getCategoryTree($item->getId());
                foreach ($childrens as $child) {
                    $list[] = $child;
                }
            }
        }

        return $list;
    }
}
