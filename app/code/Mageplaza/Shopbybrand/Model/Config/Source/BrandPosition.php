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
namespace Mageplaza\Shopbybrand\Model\Config\Source;

/**
 * Class BrandPosition
 * @package Mageplaza\Shopbybrand\Model\Config\Source
 */
class BrandPosition implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * Show on Toplink
	 */
	const TOPLINK = 0;

	/**
	 * Show on Footerlink
	 */
	const FOOTERLINK = 1;

	/**
	 * Show on Menubar
	 */
	const CATEGORY = 2;

	/**
	 * @return array
	 */
	public function toOptionArray()
	{
		return [
			[
				'label' => __('-- Please select --'),
				'value' => 4,
			],
			[
				'label' => __('Toplink'),
				'value' => self::TOPLINK,
			],
			[
				'label' => __('Footer link'),
				'value' => self::FOOTERLINK,
			],
			[
				'label' => __('Category'),
				'value' => self::CATEGORY,
			],
		];
	}
}
