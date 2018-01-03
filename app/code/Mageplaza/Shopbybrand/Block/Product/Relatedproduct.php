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
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Shopbybrand\Block\Product;

use Mageplaza\Shopbybrand\Helper\Data as Helper;
use Magento\Catalog\Block\Product\ListProduct;

class Relatedproduct extends ListProduct
{
    protected $_coreRegistry;
    protected $helper;
    protected $visibleProduts;
    protected $_productCollectionFactory;
    protected $limit;

    /*
    * Default related product page title
    */
    const TITLE = 'Products from the same brand';

    /*
    * Default limit related products
    */
    const LIMIT = '5';

    /**
     * Relatedproduct constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context                         $context
     * @param \Magento\Framework\Data\Helper\PostHelper                      $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver                          $layerResolver
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface               $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data                             $urlHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Mageplaza\Shopbybrand\Helper\Data                             $helper
     * @param \Magento\Catalog\Model\Product\Visibility                      $visibleProduts
     * @param array                                                          $data
     */
    public function __construct(\Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        Helper $helper,
        \Magento\Catalog\Model\Product\Visibility $visibleProduts,
        array $data = []
    ) {
        $this->_coreRegistry = $context->getRegistry();
        $this->helper = $helper;
        $this->visibleProduts = $visibleProduts;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->limit = $this->helper->getShopByBrandConfig('brandpage/related_products/limit_product');
        parent::__construct(
            $context, $postDataHelper, $layerResolver, $categoryRepository,
            $urlHelper, $data
        );
    }

    public function _construct()
    {
        $this->setTabTitle();
    }

    /**
     * set Tab Name
     */

    public function setTabTitle()
    {
        $limit = ($this->limit) ? $this->limit : SELF::LIMIT;
        $products = $this->_getProductCollection()->getSize();
        $result = ($products < $limit) ? $products : $limit;
        $title = __('More from this Brand' . ' (' . $result . ')');
        $this->setTitle($title);
    }

    /**
     * @return mixed
     * get ProductCollection in same brand ( filter by Atrribute Option_Id )
     */

    public function _getProductCollection()
    {
        $limit = ($this->limit) ? $this->limit : SELF::LIMIT;
        $product = $this->_coreRegistry->registry('current_product');
        if (($product instanceof \Magento\Catalog\Model\Product)
            && $product->getId()
        ) {
            $attCode = $this->helper->getAttributeCode();
            $optionId = $product->getData($attCode);
            $collection = $this->_productCollectionFactory->create()
                ->setVisibility($this->visibleProduts->getVisibleInCatalogIds())
                ->addAttributeToSelect('*')->addAttributeToFilter(
                    $attCode, ['eq' => $optionId]
                )->addFieldToFilter('entity_id', ['neq' => $product->getId()]);
            if ($limit > $collection->getSize()) {
                return $collection;
            } else {
                return $collection->setPageSize($limit);
            }
        }
        return null;
    }

    public function getToolbarHtml()
    {
        return null;
    }

    public function getAdditionalHtml()
    {
        return null;
    }

    public function getRelatedTitle()
    {
        $title = ($this->helper->getShopByBrandConfig(
            'brandpage/related_products/title'
        )) ? $this->helper->getShopByBrandConfig(
            'brandpage/related_products/title'
        ) : SELF::TITLE;
        return $title;
    }
}
