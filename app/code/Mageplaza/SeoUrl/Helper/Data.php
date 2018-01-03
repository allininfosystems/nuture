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
 * @package     Mageplaza_SeoUrl
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoUrl\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData as AbstractHelper;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class Data
 * @package Mageplaza\Osc\Helper
 */
class Data extends AbstractHelper
{
	/**
	 * General configuaration path
	 */
	const GENERAL_CONFIGUARATION = '';

	/**
	 * @type \Magento\Framework\Filter\FilterManager
	 */
	protected $_filter;

	/**
	 * @type null|array
	 */
	protected $_options = null;

	/**
	 * @type
	 */
	protected $categoryUrlSuffix;

	/**
	 * @type \Magento\UrlRewrite\Model\UrlFinderInterface
	 */
	protected $urlFinder;

	/**
	 * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
	 */
	protected $_attrOptionCollectionFactory;

	/**
	 * @var array Url Keys save for all options
	 */
	protected $_urlKeys = [];

	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 * @param \Magento\Framework\Filter\FilterManager $filter
	 * @param \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
	 * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
	 */
	public function __construct(
		Context $context,
		StoreManagerInterface $storeManager,
		ObjectManagerInterface $objectManager,
		FilterManager $filter,
		UrlFinderInterface $urlFinder,
		CollectionFactory $attrOptionCollectionFactory
	)
	{
		$this->_filter                      = $filter;
		$this->urlFinder                    = $urlFinder;
		$this->_attrOptionCollectionFactory = $attrOptionCollectionFactory;

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

		return $isModuleOutputEnabled;// && $this->getGeneralConfig('enabled', $store);
	}

	/**
	 * Encode friendly url
	 *
	 * @param $originUrl
	 * @return string
	 */
	public function encodeFriendlyUrl($originUrl)
	{
		if (!strpos($originUrl, '?')) {
			return $originUrl;
		}

		/** convert escape char to normal and remove hash value */
		$originUrl = str_replace('&amp;', '&', urldecode($originUrl));
		$posHash   = strpos($originUrl, '#');
		if ($posHash) {
			$originUrl = substr($originUrl, 0, $posHash);
		}

		list($url, $params) = explode('?', $originUrl);
		$params = explode('&', $params);

		foreach ($params as $key => $param) {
			list($attKey, $attValues) = explode('=', $param);
			$params[$attKey] = explode(',', $attValues);
			unset($params[$key]);
		}

		$urlKey           = [];
		$optionCollection = (array) $this->getOptionsCollection();
		foreach ($params as $key => $param) {
			$options = array_filter($optionCollection, function ($option) use ($key, $param) {
				return ($option['attribute_code'] == $key) && in_array($option['option_id'], $param);
			});

			if (sizeof($options)) {
				$urlKey = array_merge($urlKey, array_column($options, 'url_key'));
				unset($params[$key]);
			}
		}

		if (!sizeof($urlKey)) {
			return $originUrl;
		}

		$url       = rtrim($url, '/');
		$urlSuffix = $this->getCategoryUrlSuffix();
		if ($urlSuffix && ($urlSuffix != '/')) {
			$pos = strpos($url, $urlSuffix);
			if ($pos) {
				$url = substr($url, 0, $pos);
			} else {
				return $originUrl;
			}
		}

		$url .= '/' . implode('-', $urlKey) . $urlSuffix;
		if (sizeof($params)) {
			foreach ($params as $key => $param) {
				$url .= (!strpos($url, '?') ? '?' : '&') . $key . '=' . implode(',', $param);
			}
		}

		return $url;
	}

	/**
	 * Decode friendly url
	 *
	 * @param $pathInfo
	 * @return array|null
	 */
	public function decodeFriendlyUrl($pathInfo)
	{
		if (!$this->isEnabled()) {
			return null;
		}

		$pathInfo = trim($pathInfo, '/');
		if (!$pathInfo) {
			return null;
		}

		$urlSuffix = $this->getCategoryUrlSuffix();
		if ($urlSuffix && ($urlSuffix != '/')) {
			$pos = strpos($pathInfo, $urlSuffix);
			if ($pos) {
				$pathInfo = substr($pathInfo, 0, $pos);
			} else {
				return null;
			}
		}

		$pathInfo = explode('/', $pathInfo);
		if (sizeof($pathInfo) <= 1) {
			return null;
		}

		$urlParams = explode('-', array_pop($pathInfo));
		$pathInfo  = implode('/', $pathInfo) . $urlSuffix;
		$rewrite   = $this->getRewrite($pathInfo, $this->storeManager->getStore()->getId());
		if ($rewrite === null) {
			return null;
		}

		$urlKey           = '';
		$params           = [];
		$optionCollection = (array) $this->getOptionsCollection();
		foreach ($urlParams as $param) {
			$urlKey  .= ($urlKey ? '-' : '') . $param;
			$options = array_filter($optionCollection, function ($option) use ($urlKey) {
				return ($option['url_key'] == $urlKey);
			});

			if (sizeof($options)) {
				$urlKey                            = '';
				$option                            = array_shift($options);
				$params[$option['attribute_code']] = isset($params[$option['attribute_code']]) ?
					$params[$option['attribute_code']] . ',' . $option['option_id'] :
					$option['option_id'];
			}
		}

		if ($urlKey != '') {
			return null;
		}

		return ['pathInfo' => $pathInfo, 'params' => $params];
	}

	/**
	 * @param string $requestPath
	 * @param int $storeId
	 * @return UrlRewrite|null
	 */
	protected function getRewrite($requestPath, $storeId)
	{
		$rewrite = $this->urlFinder->findOneByData([
			UrlRewrite::REQUEST_PATH => trim($requestPath, '/'),
			UrlRewrite::STORE_ID     => $storeId,
		]);

		if ($rewrite === null) {
			$object = new \Magento\Framework\DataObject(['pathInfo' => $requestPath, 'store_id' => $storeId, 'rewrite' => $rewrite]);
			$this->_eventManager->dispatch('seo_friendly_url_get_rewrite_path', ['object' => $object]);

			$rewrite = $object->getData('rewrite');
		}

		return $rewrite;
	}

	/**
	 * Retrieve category rewrite suffix for store
	 *
	 * @param int $storeId
	 * @return string
	 */
	public function getCategoryUrlSuffix($storeId = null)
	{
		if ($storeId === null) {
			$storeId = $this->storeManager->getStore()->getId();
		}
		if (!isset($this->categoryUrlSuffix)) {
			$this->categoryUrlSuffix = $this->scopeConfig->getValue(
				\Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX,
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
				$storeId
			);
		}

		return $this->categoryUrlSuffix;
	}

	/**
	 * * Format URL key from name or defined key
	 *
	 * @param $option
	 * @return string
	 */
	public function processKey($option)
	{
		$optionData = $option->getData();
		if (!isset($optionData['url_key'])) {
			$key = $this->_filter->translitUrl($optionData['default_value']);
			$key = str_replace('-', '', $key);

			if(array_key_exists($key, $this->_urlKeys)){
				$this->_urlKeys[$key]++;
			} else {
				$this->_urlKeys[$key] = 0;
			}

			$optionData['url_key'] = $key . ($this->_urlKeys[$key] ?: '');
		}

		return $optionData;
	}

	/**
	 * Get all option with url_key value
	 *
	 * @return null|Array
	 */
	public function getOptionsCollection()
	{
		if (!$this->_options) {
			$collection = $this->_attrOptionCollectionFactory->create()
				->setPositionOrder('asc')
				->setStoreFilter($this->storeManager->getStore()->getId());

			$collection->getSelect()
				->joinLeft(
					['at' => $collection->getTable('eav_attribute')],
					"main_table.attribute_id = at.attribute_id",
					['attribute_code']
				);

			$this->_options = $collection->walk([$this, 'processKey']);
		}

		return $this->_options;
	}

	/************************ General Configuration *************************
	 *
	 * @param      $code
	 * @param null $store
	 * @return mixed
	 */
	public function getGeneralConfig($code = '', $store = null)
	{
		$code = $code ? '/' . $code : '';

		return $this->getConfigValue(self::GENERAL_CONFIGUARATION . $code, $store);
	}
}
