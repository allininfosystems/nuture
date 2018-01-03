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
namespace Mageplaza\SeoUrl\App\Request;

use Magento\Store\App\Request\PathInfoProcessor as StorePathInfoProcessor;

/**
 * Class PathInfoProcessor
 * @package Mageplaza\SeoUrl\App\Request
 */
class PathInfoProcessor extends StorePathInfoProcessor
{
	/**
	 * @type \Mageplaza\SeoUrl\Helper\Data
	 */
	protected $helper;

	/**
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Mageplaza\SeoUrl\Helper\Data $helper
	 */
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Mageplaza\SeoUrl\Helper\Data $helper
	)
	{
		$this->helper = $helper;

		parent::__construct($storeManager);
	}

	/**
	 * Process path info
	 *
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param string $pathInfo
	 * @return string
	 */
	public function process(\Magento\Framework\App\RequestInterface $request, $pathInfo)
	{
		$pathInfo = parent::process($request, $pathInfo);

		$decodeUrl = $this->helper->decodeFriendlyUrl($pathInfo);
		if (!$decodeUrl) {
			return $pathInfo;
		}

		$requestUri = $request->getRequestUri();
		$requestUri .= strpos($requestUri, '?') ? '&' : '?';
		foreach($decodeUrl['params'] as $key => $param){
			$requestUri .= $key . '=' . $param . '&';
		}
		$request->setRequestUri(trim($requestUri, '&'));

		return $decodeUrl['pathInfo'];
	}
}
