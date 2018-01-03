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
 * @package     Mageplaza_LayeredNavigationUltimate
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LayeredNavigationUltimate\Model;

use Magento\Framework\Exception\ProductsListException;

/**
 * Class ProductsPage
 * @package Mageplaza\LayeredNavigationUltimate\Model
 */
class ProductsPage extends \Magento\Framework\Model\AbstractModel
{
	/**
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
	 * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
	 * @param array $data
	 */
	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		parent::__construct($context, $registry, $resource, $resourceCollection, $data);
	}

	/**
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('Mageplaza\LayeredNavigationUltimate\Model\ResourceModel\ProductsPage');
	}

	/**
	 * @return mixed
	 */
	public function isEnable()
	{
		return $this->getData('enable');
	}
}