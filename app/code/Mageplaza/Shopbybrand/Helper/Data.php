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

namespace Mageplaza\Shopbybrand\Helper;

use Magento\Framework\App\Helper\Context;
use Mageplaza\Shopbybrand\Model\BrandFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData as AbstractHelper;

/**
 * Class Data
 * @package Mageplaza\Osc\Helper
 */
class Data extends AbstractHelper
{
	/**
	 * Image size default
	 */
	const IMAGE_SIZE = '135x135';

	/**
	 * General configuaration path
	 */
	const GENERAL_CONFIGUARATION = 'shopbybrand/general';

	/**
	 * Brand page configuration path
	 */
	const BRAND_CONFIGUARATION = 'shopbybrand/brandpage';

	/**
	 * Feature brand configuration path
	 */
	const FEATURE_CONFIGUARATION = 'shopbybrand/brandpage/feature';

	/**
	 * Search brand configuration path
	 */
	const SEARCH_CONFIGUARATION = 'shopbybrand/brandpage/search';

	/**
	 * Search brand configuration path
	 */
	const BRAND_DETAIL_CONFIGUARATION = 'shopbybrand/brandview';

	/**
	 * Brand media path
	 */
	const BRAND_MEDIA_PATH = 'mageplaza/brand';

	/**
	 * Default route name
	 */
	const DEFAULT_ROUTE = 'brand';

	/**
	 * @type \Magento\Framework\Filter\FilterManager
	 */

	const XML_PATH_SHOPBYBRAND = 'shopbybrand/';

	const CATEGORY = 'category';
    const BRAND_FIRST_CHAR ='char';

	protected $_filter;

	public $translitUrl;

	/**
	 * @var \Mageplaza\Shopbybrand\Model\CategoryFactory
	 */
	public $categoryFactory;

	/**
	 * @type string
	 */
	protected $_char = '';

	/**
	 * @type \Mageplaza\Shopbybrand\Model\BrandFactory
	 */
	protected $_brandFactory;
	/**
	 * @type
	 */
	protected $_brandCollection;


	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 * @param \Magento\Framework\Filter\TranslitUrl $translitUrl
	 * @param \Magento\Framework\Filter\FilterManager $filter
	 */
	public function __construct(
		Context $context,
		StoreManagerInterface $storeManager,
		ObjectManagerInterface $objectManager,
		\Magento\Framework\Filter\TranslitUrl $translitUrl,
		FilterManager $filter,
		\Mageplaza\Shopbybrand\Model\CategoryFactory $categoryFactory,
		BrandFactory $brandFactory
	)
	{
		$this->_filter         = $filter;
		$this->translitUrl     = $translitUrl;
		$this->categoryFactory = $categoryFactory;
		$this->_brandFactory   = $brandFactory;
		parent::__construct($context, $objectManager, $storeManager);
	}

	/**
	 * Is enable module on frontend
	 *
	 * @param null $store
	 * @return bool
	 */
	public function isEnabled($store = null)
	{
		$isModuleOutputEnabled = $this->isModuleOutputEnabled();

		return $isModuleOutputEnabled && $this->getGeneralConfig('enabled', $store);
	}

	/**
	 * @return \Magento\Store\Model\StoreManagerInterface
	 */
	public function getStoreManager()
	{
		return $this->storeManager;
	}

	/**
	 * @param $position
	 * @return bool
	 */
	public function canShowBrandLink($position)
	{
		if (!$this->isEnabled()) {
			return false;
		}

		$positionConfig = explode(',', $this->getGeneralConfig('show_position'));

		return in_array($position, $positionConfig);
	}

	/**
	 * @param null $brand
	 * @return string
	 */
	public function getBrandUrl($brand = null)
	{
		$baseUrl = $this->storeManager->getStore()->getBaseUrl();
		$key     = is_null($brand) ? '' : '/' . $this->processKey($brand);

		return $baseUrl . $this->getRoute() . $key . $this->getUrlSuffix();
	}

	/**
	 * @param $brand
	 * @return string
	 */
	public function processKey($brand)
	{
		if (!$brand) {
			return '';
		}

		$str = $brand->getUrlKey() ?: $brand->getDefaultValue();

		return $this->formatUrlKey($str);
	}

	/**
	 * Format URL key from name or defined key
	 *
	 * @param string $str
	 * @return string
	 */
	public function formatUrlKey($str)
	{
		return $this->_filter->translitUrl($str);
	}

	/**
	 * @param $brand
	 * @return string
	 */
	public function getBrandImageUrl($brand)
	{
		if ($brand->getImage()) {
			$image = $brand->getImage();
		} else if ($brand->getSwatchType() == \Magento\Swatches\Model\Swatch::SWATCH_TYPE_VISUAL_IMAGE) {
			$image = \Magento\Swatches\Helper\Media::SWATCH_MEDIA_PATH . $brand->getSwatchValue();
		} else if ($this->getBrandDetailConfig('default_image')) {
			$image = self::BRAND_MEDIA_PATH . '/' . $this->getBrandDetailConfig('default_image');
		} else {
			return \Magento\Framework\App\ObjectManager::getInstance()
				->get('Magento\Catalog\Helper\Image')
				->getDefaultPlaceholderUrl('small_image');
		}

		return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $image;
	}

	/**
	 * Get Brand Title
	 *
	 * @return string
	 */
	public function getBrandTitle()
	{
		return $this->getGeneralConfig('link_title') ?: __('Brands');
	}

	/**
	 * @param $brand
	 * @param bool|false $short
	 * @return mixed
	 */
	public function getBrandDescription($brand, $short = false)
	{
		if ($short) {
			return $brand->getShortDescription() ?: '';
		}

		return $brand->getDescription() ?: '';
	}

	/************************ General Configuration *************************
	 *
	 * @param      $code
	 * @param null $store
	 * @return mixed
	 */
	public function getGeneralConfig($code = '', $store = null)
	{
		$code = $code ? self::GENERAL_CONFIGUARATION . '/' . $code : self::GENERAL_CONFIGUARATION;

		return $this->getConfigValue($code, $store);
	}

	/**
	 * @param null $store
	 * @return mixed
	 */
	public function getAttributeCode($store = null)
	{
//		if ($store == null)
//		{
//			$store = '0';
//		}
		return $this->getGeneralConfig('attribute', $store);
	}

	/**
	 * Get route name for brand.
	 * If empty, default 'brands' will be used
	 *
	 * @param null $store
	 * @return string
	 */
	public function getRoute($store = null)
	{
		$route = $this->getGeneralConfig('route', $store) ?: self::DEFAULT_ROUTE;

		return $this->formatUrlKey($route);
	}

	/**
	 * Retrieve category rewrite suffix for store
	 *
	 * @param int $storeId
	 * @return string
	 */
	public function getUrlSuffix($storeId = null)
	{
		return $this->scopeConfig->getValue(
			\Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
			$storeId
		);
	}

	/************************ Brand Configuration *************************
	 *
	 * @param      $code
	 * @param null $store
	 * @return mixed
	 */
	public function getBrandConfig($code = '', $store = null)
	{
		$code = $code ? self::BRAND_CONFIGUARATION . '/' . $code : self::BRAND_CONFIGUARATION;

		return $this->getConfigValue($code, $store);
	}

	/**
	 * @param string $group
	 * @param null $store
	 * @return array
	 */
	public function getImageSize($group = '', $store = null)
	{
		$imageSize = $this->getBrandConfig($group . 'image_size') ?: self::IMAGE_SIZE;

		return explode('x', $imageSize);
	}

	/************************ Feature Brand Configuration *************************
	 *
	 * @param      $code
	 * @param null $store
	 * @return mixed
	 */
	public function getFeatureConfig($code = '', $store = null)
	{
		$code = $code ? self::FEATURE_CONFIGUARATION . '/' . $code : self::FEATURE_CONFIGUARATION;

		return $this->getConfigValue($code, $store);
	}

	/**
	 * @param null $store
	 * @return mixed
	 */
	public function enableFeature($store = null)
	{
		return $this->getSearchConfig('enable', $store);
	}

	/************************ Search Brand Configuration *************************
	 *
	 * @param      $code
	 * @param null $store
	 * @return mixed
	 */
	public function getSearchConfig($code = '', $store = null)
	{
		$code = $code ? self::SEARCH_CONFIGUARATION . '/' . $code : self::SEARCH_CONFIGUARATION;

		return $this->getConfigValue($code, $store);
	}

	/**
	 * @param null $store
	 * @return mixed
	 */
	public function enableSearch($store = null)
	{
		return $this->getSearchConfig('enable', $store);
	}

	/************************ Brand View Configuration *************************
	 *
	 * @param      $code
	 * @param null $store
	 * @return mixed
	 */
	public function getBrandDetailConfig($code = '', $store = null)
	{
		$code = $code ? self::BRAND_DETAIL_CONFIGUARATION . '/' . $code : self::BRAND_DETAIL_CONFIGUARATION;

		return $this->getConfigValue($code, $store);
	}

	/**
	 * @return array
	 */
	public function getAllBrandsAttributeCode()
	{
		$stores         = $this->storeManager->getStores();
		$attributeCodes = [];
		array_push($attributeCodes, $this->getAttributeCode('0'));
		foreach ($stores as $store) {
			array_push($attributeCodes, $this->getAttributeCode($store->getId()));
		}
		$attributeCodes = array_unique($attributeCodes);

		return $attributeCodes;
	}

	public function getShopByBrandConfig($code, $storeId = null)
	{
		return $this->getConfigValue(self::XML_PATH_SHOPBYBRAND . $code, $storeId);
	}

	/**
	 * generate url_key for brand category
	 * @param $name
	 * @param $count
	 * @return string
	 */
	public function generateUrlKey($name, $count)
	{
		$name = $this->strReplace($name);
		$text = $this->translitUrl->filter($name);
		if ($count == 0) {
			$count = '';
		}
		if (empty($text)) {
			return 'n-a' . $count;
		}

		return $text . $count;
	}

	/**
	 * replace vietnamese characters to english characters
	 * @param $str
	 * @return mixed|string
	 */
	public function strReplace($str)
	{

		$str = trim(mb_strtolower($str));
		$str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
		$str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
		$str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
		$str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
		$str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
		$str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
		$str = preg_replace('/(đ)/', 'd', $str);

		return $str;
	}

	/**
	 * @param null $cat
	 * @return string
	 */
	public function getCatUrl($cat = null)
	{
		$baseUrl    = $this->storeManager->getStore()->getBaseUrl();
		$brandRoute = $this->getRoute();
		$key        = is_null($cat) ? '' : '/' . $this->processKey($cat);

		return $baseUrl . $brandRoute . '/' . self::CATEGORY . $key . $this->getUrlSuffix();
	}

	/**
	 * @param $routePath
	 * @param $routeSize
	 * @return bool
	 */
	public function isBrandRoute($routePath, $routeSize)
	{
		if ($routeSize > 3) {
			return false;
		}

		$urlSuffix  = $this->getUrlSuffix();
		$brandRoute = $this->getRoute();
		if ($urlSuffix) {
			$brandSuffix = strpos($brandRoute, $urlSuffix);
			if ($brandSuffix) {
				$brandRoute = substr($brandRoute, 0, $brandSuffix);
			}
		}

		return ($routePath[0] == $brandRoute);

	}

	/**
	 * @param $urlKey
	 * @return \Mageplaza\Shopbybrand\Model\Category|null
	 */
	public function getCategoryByUrlKey($urlKey)
	{
		$cat = $this->categoryFactory->create()->load($urlKey, 'url_key');
		if ($cat) {
			return $cat->getId();
		}

		return null;
	}

    //************************* Get Brand List Function ***************************
    /**
     * @param null $type
     * @param null $ids
     *
     * @param null $char
     *
     * @return mixed
     */
	public function getBrandList($type = null, $ids = null ,$char = null)
	{
	    $brands = $this->_brandFactory->create();
        switch ($type) {
            //Get Brand List by Category
            case self::CATEGORY :
                $list = $brands->getBrandCollection( null, ['main_table.option_id' => ['in' => $ids]]);
                break;
            //Get Brand List Filtered by Brand First Char
            case self::BRAND_FIRST_CHAR :
                $list = $brands->getBrandCollection(null, ['main_table.option_id' => ['in' => $ids]],"IF(tsv.value_id > 0, tsv.value, tdv.value) LIKE '".$char."%'");
                break;
            default :
            //Get Brand List
                $list = $brands->getBrandCollection();
        }

		return $list;
	}

    //************************* Get Category List Function ***************************
    public function getCategoryList()
    {
        $collection = $this->categoryFactory->create()
            ->getCollection()->addFieldToFilter('status', '1');
        return $collection;
    }

    //*********************** Get Category and Alpha bet Character Filter Class for Mixitup ***********************
	/**
	 * Get class for mixitup filter
	 *
	 * @param $brand
	 * @return string
	 */

	public function getFilterClass($brand)
	{
	    //vietnamese unikey format
		if ($this->getShopByBrandConfig('brandpage/brand_filter/encode_key')){
			$firstChar = mb_substr($brand->getValue(),0,1,$this->getShopByBrandConfig('brandpage/brand_filter/encode_key'));
		}else{
			$firstChar = mb_substr($brand->getValue(),0,1,'UTF-8');
		}

		return is_numeric($firstChar) ? 'num'.$firstChar : $firstChar;
	}

    /**
     * @param $optionId
     *
     * @return string
     */
    public function getCatFilterClass($optionId)
    {
        $catName = [];
        $sql = 'brand_cat_tbl.option_id IN ('. $optionId .')';
        $group = 'main_table.cat_id';

        $collection = $this->categoryFactory->create()->getCategoryCollection($sql,$group);
        foreach ($collection as $item){
            $catName[] = $item->getName();
        }
        $result = implode(' ',$catName);

        return $result;
    }

    public function getQuickViewUrl()
    {
        $baseUrl    = $this->storeManager->getStore()->getBaseUrl();
        return $baseUrl.'mpbrand/index/quickview';
    }

}
