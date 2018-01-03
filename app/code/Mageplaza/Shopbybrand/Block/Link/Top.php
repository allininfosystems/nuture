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
namespace Mageplaza\Shopbybrand\Block\Link;

/**
 * Class Top
 * @package Mageplaza\Shopbybrand\Block\Link
 */
class Top extends \Magento\Framework\View\Element\Html\Link
{
	/**
	 * @type \Mageplaza\Shopbybrand\Helper\Data
	 */
	protected $helper;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Mageplaza\Shopbybrand\Helper\Data $helper
	 * @param array $data
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Mageplaza\Shopbybrand\Helper\Data $helper,
		array $data = []
	)
	{
		$this->helper = $helper;

		parent::__construct($context, $data);
	}

	/**
	 * @return string
	 */
	protected function _toHtml()
	{
        if (!$this->helper->canShowBrandLink(\Mageplaza\Shopbybrand\Model\Config\Source\BrandPosition::TOPLINK)) {
            return '';
        }

        return parent::_toHtml();
	}

	/**
	 * @return string
	 */
	public function getHref()
	{
		return $this->helper->getBrandUrl();
	}

	/**
	 * @return \Magento\Framework\Phrase
	 */
	public function getLabel()
	{
		return __($this->helper->getBrandTitle());
	}
}
