<?php

namespace Plumrocket\ProductFilter\Model\CatalogSearch\Layer\Filter;

class Category extends \Magento\CatalogSearch\Model\Layer\Filter\Category
{
    /**
     * @var \Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Plumrocket\ProductFilter\Helper\Url
     */
    protected $_urlHelper;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Category
     */
    private $dataProvider;

    /**
     * Category constructor.
     *
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory                  $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface                       $storeManager
     * @param \Magento\Catalog\Model\Layer                                     $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder             $itemDataBuilder
     * @param \Magento\Framework\Escaper                                       $escaper
     * @param \Plumrocket\ProductFilter\Helper\Data                            $dataHelper
     * @param \Plumrocket\ProductFilter\Helper\Url                             $urlHelper
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Escaper $escaper,
        \Plumrocket\ProductFilter\Helper\Data $dataHelper,
        \Plumrocket\ProductFilter\Helper\Url $urlHelper,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_urlHelper = $urlHelper;

        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $escaper,
            $categoryDataProviderFactory,
            $data
        );

        $this->escaper = $escaper;
        $this->dataProvider = $categoryDataProviderFactory->create(['layer' => $this->getLayer()]);
    }

    /**
     * {@inheritdoc}
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if ($this->_dataHelper->moduleEnabled() && $request->getParam($this->_requestVar)) {
            $categoryId = $request->getParam($this->_requestVar) ?: $request->getParam('id');

            if (empty($categoryId)) {
                return $this;
            }

            $categoryIds = explode(',', $categoryId);
            $this->dataProvider
                ->setCanProceed(true)
                ->setCategoryIds($categoryIds);

            $categories = $this->dataProvider->getCategories();

            $this->addCategoriesFilter($this->getLayer()->getProductCollection(), ['eq' => $categories->getAllIds()]);

            foreach ($categories as $category) {
                if (in_array($category->getId(), $categoryIds)) {
                    $value = $this->_urlHelper->useSeoFriendlyUrl() ? $category->getUrlKey() : $category->getId();
                    $this->getLayer()
                        ->getState()
                        ->addFilter(
                            $this->_createItem($category->getName(), $value)->setIsActive(true)
                        );
                }
            }


            return $this;
        }
        return parent::apply($request);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getItemsData()
    {
        if ($this->_dataHelper->moduleEnabled()) {


            $category = $this->getLayer()->getCurrentCategory();
            $productCollection = $this->getLayer()->getProductCollection();
            $optionsFacetedData = $productCollection->getFacetedData('category');
            $collectionSize = $productCollection->getSize();
            $categories = $this->getChildrenCategories($category);

            if ($category->getIsActive()) {
                foreach ($categories as $category) {

                    if ($category->getIsActive()
                        && isset($optionsFacetedData[$category->getId()])
                    ) {
                        $urlParam = $this->_urlHelper->useSeoFriendlyUrl() ? $category->getUrlKey() : $category->getId();
                        $this->itemDataBuilder->addItemData(
                            $this->escaper->escapeHtml($category->getName()),
                            $urlParam,
                            $optionsFacetedData[$category->getId()]['count']
                        );
                    }
                }
            }

            $item = $this->itemDataBuilder->build();

            return $item;
        }

        return parent::_getItemsData();
    }

    /**
     * Return child categories
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected function getChildrenCategories($category)
    {
        $collection = $category->getCollection();
        /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        $collection->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('all_children')
            ->addAttributeToSelect('is_anchor')
            ->addAttributeToFilter('is_active', 1)
            ->addIdFilter($category->getChildren())
            ->setOrder(
                'position',
                \Magento\Framework\DB\Select::SQL_ASC
            );

        return $collection;
    }

    /**
     * Filter Product by Categories
     *
     * @param array                                                   $categoriesFilter
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return $this
     */
    protected function addCategoriesFilter($collection, array $categoriesFilter)
    {
        foreach ($categoriesFilter as $conditionType => $values) {
            $connection = $collection->getConnection();
            $categorySelect = $connection->select()->from(
                ['cat' => $collection->getTable('catalog_category_product_index')],
                'cat.product_id'
            )->where($connection->prepareSqlCondition('cat.category_id', ['in' => $values]))
                ->where($connection->prepareSqlCondition('cat.store_id', ['eq' => $this->getStoreId()]));

            $selectCondition = [
                $this->mapConditionType($conditionType) => $categorySelect,
            ];
            $collection->getSelect()->where(
                $connection->prepareSqlCondition('e.entity_id', $selectCondition)
            );
        }
        return $this;
    }

    /**
     * Map equal and not equal conditions to in and not in
     *
     * @param string $conditionType
     * @return mixed
     */
    private function mapConditionType($conditionType)
    {
        $conditionsMap = [
            'eq' => 'in',
            'neq' => 'nin'
        ];
        return isset($conditionsMap[$conditionType]) ? $conditionsMap[$conditionType] : $conditionType;
    }
}
