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

namespace Mageplaza\Shopbybrand\Plugin\Block;

use Mageplaza\Shopbybrand\Model\BrandFactory;
use Magento\Framework\Registry;
/**
 * Class ListProduct
 */
class ListProduct
{

	/** @var \Mageplaza\Shopbybrand\Helper\Data  */
	protected $helper;

	/** @var \Mageplaza\Shopbybrand\Model\BrandFactory */
	protected $_brandFactory;
	/**
	 * ListProduct constructor.
	 * @param \Mageplaza\Shopbybrand\Helper\Data $helper
	 * @param \Mageplaza\Shopbybrand\Model\BrandFactory $brandFactory
	 */
    /**
     * @type \Magento\Framework\Registry
     */
    protected $_coreRegistry;

	public function __construct(
		\Mageplaza\Shopbybrand\Helper\Data $helper,
        Registry $coreRegistry,
		BrandFactory $brandFactory
	)

	{
		$this->helper             = $helper;
		$this->_brandFactory = $brandFactory;
        $this->_coreRegistry        = $coreRegistry;
	}
    //************************* Get BrandName in Product Detail ***************************
    /**
     * @param $product
     *
     * @return mixed
     */
	public function getProductBrand($product)
	{
		$attCode = $this->helper->getAttributeCode();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$product = $objectManager->create('Magento\Catalog\Model\Product')->load($product->getId());
		$optionId = $product->getData($attCode);
		return $this->_brandFactory->create()->loadByOption($optionId)->getValue();
	}
    //************************* Around getProductPrice function in Product list ***************************
    /**
     * @param \Magento\Catalog\Block\Product\ListProduct $listProduct
     * @param callable                                   $proceed
     * @param \Magento\Catalog\Model\Product             $product
     *
     * @return string
     */
	public function aroundGetProductPrice(\Magento\Catalog\Block\Product\ListProduct $listProduct,callable $proceed,\Magento\Catalog\Model\Product $product)
	{
		$result = ($this->helper->getShopByBrandConfig('brandpage/show_brandname')) ? $this->getProductBrand($product).$proceed($product) : $proceed($product);
		return $result;
	}

    //************************* Around getAdditionalHtml function in Product list ***************************
    /**
     * @param \Magento\Catalog\Block\Product\ListProduct $listProduct
     * @param callable                                   $proceed
     *
     * @return string
     */
//	public function aroundGetAdditionalHtml(\Magento\Catalog\Block\Product\ListProduct $listProduct,callable $proceed){
//	    $result = $proceed();
//        $currentBrand = $this->_coreRegistry->registry('current_brand');
//        if ($currentBrand) {
//            $brandTitle = $currentBrand->getPageTitle() ? $currentBrand->getPageTitle() : $currentBrand->getValue();
//            $result
//                = '<div class="page-title-wrapper"><h1 class="page-title" id="page-title-heading" aria-labelledby="page-title-heading toolbar-amount" style="margin-bottom: 0">
//        <span class="base" data-ui-id="page-title-wrapper">'
//                . $brandTitle . '</span></h1></div>' . $proceed();
//        }
//	    return $result;
//    }
}
