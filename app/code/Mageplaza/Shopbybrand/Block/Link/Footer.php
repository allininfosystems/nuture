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
 * Class Footer
 * @package Mageplaza\Shopbybrand\Block\Link
 */
class Footer extends \Magento\Framework\View\Element\Html\Link\Current
{
	/**
	 * @var \Magento\Framework\App\Http\Context
	 */
	protected $helper;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
	 * @param \Mageplaza\Shopbybrand\Helper\Data $helper
	 * @param array $data
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\App\DefaultPathInterface $defaultPath,
		\Mageplaza\Shopbybrand\Helper\Data $helper,
		array $data = []
	)
	{
		parent::__construct($context, $defaultPath, $data);

		$this->helper = $helper;
	}

	/**
	 * @return string
	 */
	protected function _toHtml()
	{
		if (!$this->helper->canShowBrandLink(\Mageplaza\Shopbybrand\Model\Config\Source\BrandPosition::FOOTERLINK)) {
			return '';
		}

		$this->setData([
			'label' => $this->helper->getBrandTitle(),
			'path'  => $this->helper->getRoute()
		]);

		return parent::_toHtml();
	}

	/**
	 * @return string
	 */
	public function getHref()
	{
		return $this->helper->getBrandUrl();
	}
}
