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
namespace Mageplaza\Shopbybrand\Block;

use Mageplaza\Shopbybrand\Helper\Data as Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Design
 * @package Mageplaza\Shopbybrand\Block
 */
class Design extends Template
{
	/**
	 * @var Config
	 */
	protected $_helperConfig;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Mageplaza\Shopbybrand\Helper\Data $helperConfig
	 * @param array $data
	 */
	public function __construct(
		Context $context,
		Config $helperConfig,
		array $data = []
	)
	{

		parent::__construct($context, $data);

		$this->_helperConfig = $helperConfig;
	}

	/**
	 * @return \Mageplaza\Shopbybrand\Helper\Data
	 */
	public function helper()
	{
		return $this->_helperConfig;
	}

	/**
	 * @return mixed
	 */
	public function getCustomCss()
	{
		return $this->_helperConfig->getBrandConfig('custom_css');
	}
}
