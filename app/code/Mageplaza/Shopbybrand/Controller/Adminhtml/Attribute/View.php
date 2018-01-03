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
namespace Mageplaza\Shopbybrand\Controller\Adminhtml\Attribute;

/**
 * Class View
 * @package Mageplaza\Shopbybrand\Controller\Adminhtml\Attribute
 */
class View extends \Magento\Backend\App\Action
{
	/**
	 * @type \Mageplaza\Shopbybrand\Helper\Data
	 */
	protected $_brandHelper;

	/**
	 * @type \Magento\Catalog\Model\Product\Attribute\Repository
	 */
	protected $_productAttributeRepository;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Catalog\Model\Product\Attribute\Repository $productRespository
	 * @param \Mageplaza\Shopbybrand\Helper\Data $brandHelper
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Catalog\Model\Product\Attribute\Repository $productRespository,
		\Mageplaza\Shopbybrand\Helper\Data $brandHelper
	)
	{
		parent::__construct($context);

		$this->_brandHelper                = $brandHelper;
		$this->_productAttributeRepository = $productRespository;
	}

	/**
	 * execute
	 */
	public function execute()
	{
		$attributeCode = $this->_brandHelper->getAttributeCode('0');
		try {
			$attribute = $this->_productAttributeRepository->get($attributeCode);

			$this->_forward('edit', 'product_attribute', 'catalog', ['attribute_id' => $attribute->getId()]);
		} catch (\Exception $e) {
			$this->messageManager->addErrorMessage(__('You have to choose an attribute as brand in configuration.'));
			$this->_redirect('adminhtml/system_config/edit', ['section' => 'shopbybrand']);
		}
	}
}
