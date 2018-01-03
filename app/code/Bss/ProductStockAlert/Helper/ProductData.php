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
 * @package    Bss_Simpledetailconfigurable
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductStockAlert\Helper;

use Magento\Framework\View\Element\Template\Context;
use Bss\ProductStockAlert\Model\Customer\Context as CustomerContex;

class ProductData extends \Magento\Framework\Url\Helper\Data
{
    private $productInfo;

    private $stockRegistry;

    private $configurableData;

    private $customerSession;

    private $storeManager;

    private $customer;

    private $httpContext;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ProductRepository $productInfo,
        \Magento\CatalogInventory\Model\StockRegistry $stockRegistry,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableData,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Customer $customer,
        \Bss\ProductStockAlert\Model\Stock $model
    ) {
        $this->productInfo = $productInfo;
        $this->stockRegistry = $stockRegistry;
        $this->configurableData = $configurableData;
        $this->httpContext = $httpContext;
        $this->storeManager = $storeManager;
        $this->customer = $customer;
        $this->model = $model;
        parent::__construct($context);
    }

    public function getAllData($productEntityId)
    {
        $result = [];
        $map_r = [];
        $parentProduct = $this->configurableData->getChildrenIds($productEntityId);
        $product = $this->productInfo->getById($productEntityId);

        $parentAttribute = $this->configurableData->getConfigurableAttributes($product);
        $result['entity'] = $productEntityId;
        foreach ($parentAttribute as $attrKey => $attrValue) {
            foreach ($product->getAttributes()[$attrValue->getProductAttribute()->getAttributeCode()]
                ->getOptions() as $tvalue) {
                $result['map'][$attrValue->getAttributeId()]['label'] = $attrValue->getLabel();
                $result['map'][$attrValue->getAttributeId()][$tvalue->getValue()] = $tvalue->getLabel();
                $map_r[$attrValue->getAttributeId()][$tvalue->getLabel()] = $tvalue->getValue();
            }
        }
        
        foreach ($parentProduct[0] as $simpleProduct) {
            $childProduct = [];
            $childProduct['entity'] = $simpleProduct;
            $child = $this->productInfo->getById($childProduct['entity']);
            $childStock = $this->stockRegistry->getStockItem($childProduct['entity']);
            $childProduct['stock_number'] = $childStock->getQty();
            $childProduct['stock_status'] = $childStock->getIsInStock();
            $childProduct['productId'] = $childStock->getId();
            $childProduct['action'] = $this->_getAction($childStock);
            $childProduct['url'] = $this->getAjaxUrl();
            //$childProduct['form'] = $this->_getFormNotify($childStock, $this->getCustomerId());
            $key = '';
            foreach ($parentAttribute as $attrKey => $attrValue) {
                $attrLabel = $attrValue->getProductAttribute()->getAttributeCode();
                $childRow = $child->getAttributes()[$attrLabel]->getFrontend()->getValue($child);
                $key .= $map_r[$attrValue->getAttributeId()][$childRow] . '_';
            }
            $result['child'][$key] = $childProduct;
        }
        return $result;
    }

    private function _getAction($childStock)
    {
        return $this->_getUrl(
            'productstockalert/add/stock',
            [
                'product_id' => $childStock->getId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
            ]
        );
    }

    /**
     * Retrieve form action
     *
     * @return string
     */
    private function getAjaxUrl()
    {
        return $this->_getUrl(
            'productstockalert/ajax/'
        );
    }
}
