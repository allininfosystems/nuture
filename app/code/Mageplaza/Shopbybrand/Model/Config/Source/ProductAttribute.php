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
 * Class ProductAttribute
 * @package Mageplaza\Shopbybrand\Model\Config\Source
 */
class ProductAttribute implements \Magento\Framework\Option\ArrayInterface
{
	/** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory  */
	protected $_collectionFactory;

	/**
	 * ProductAttribute constructor.
	 * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
	 */
	public function __construct(
		\Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
	)
	{
		$this->_collectionFactory = $collectionFactory;
	}

	/**
	 * @return array
	 */
	public function toOptionArray()
	{
		$attributes   = $this->_collectionFactory->create()->addVisibleFilter();
		$arrAttribute = [
			[
				'label' => __('-- Please select --'),
				'value' => '',
			],
		];

		foreach ($attributes as $attribute) {
			if ($attribute->getIsUserDefined() && in_array($attribute->getData('frontend_input'), ['select', 'swatch_visual', 'swatch_text'])) {
				$arrAttribute[] = [
					'label' => $attribute->getFrontendLabel(),
					'value' => $attribute->getAttributeCode()
				];
			}
		}

		return $arrAttribute;
	}
}
