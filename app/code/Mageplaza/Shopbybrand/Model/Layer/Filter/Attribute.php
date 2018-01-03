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
namespace Mageplaza\Shopbybrand\Model\Layer\Filter;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;

/**
 * Class Attribute
 * @package Mageplaza\Shopbybrand\Model\Layer\Filter
 */
class Attribute extends AbstractFilter
{
	/**
	 * Apply attribute option filter to product collection
	 *
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @return $this
	 */
	public function apply(\Magento\Framework\App\RequestInterface $request)
	{
		$attributeValue = $request->getParam($this->_requestVar);
		if (empty($attributeValue)) {
			return $this;
		}
		$attribute = $this->getAttributeModel();
		$this->getLayer()
			->getProductCollection()
			->addFieldToFilter($attribute->getAttributeCode(), $attributeValue);

		return $this;
	}
}
