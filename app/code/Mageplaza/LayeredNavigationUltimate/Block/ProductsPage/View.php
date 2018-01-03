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
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LayeredNavigationUltimate\Block\ProductsPage;

/**
 * Class View
 * @package Mageplaza\LayeredNavigationUltimate\Block\ProductsPage
 */
class View extends \Magento\Framework\View\Element\Template
{
	/** @var \Mageplaza\LayeredNavigationUltimate\Helper\Data */
	protected $_helper;

	/** @var \Magento\Framework\Registry */
	protected $_coreRegistry;

	/**
	 * Products constructor.
	 * @param \Magento\Catalog\Block\Product\Context $context
	 * @param \Mageplaza\LayeredNavigationUltimate\Helper\Data $helper
	 * @param array $data
	 */
	public function __construct(
		\Magento\Catalog\Block\Product\Context $context,
		\Mageplaza\LayeredNavigationUltimate\Helper\Data $helper,
		array $data = []
	)
	{
		$this->_helper       = $helper;
		$this->_coreRegistry = $context->getRegistry();

		parent::__construct($context, $data);
	}

	/**
	 * @return $this
	 */
	public function _prepareLayout()
	{
		parent::_prepareLayout();

		$page = $this->_coreRegistry->registry('current_product_page');

		$mainTitle     = $page->getPageTitle();
		$pageMainTitle = $this->getLayout()->getBlock('page.main.title');
		if ($pageMainTitle) {
			$pageMainTitle->setPageTitle($mainTitle);
		}
		$this->getLayout()->createBlock('Magento\Catalog\Block\Breadcrumbs');
		if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
			$breadcrumbsBlock->addCrumb('products', ['label' => $mainTitle]);
		}

		$this->pageConfig->getTitle()->set($page->getMetaTitle() ?: $mainTitle);

		if ($description = $page->getMetaDescription()) {
			$this->pageConfig->setDescription($description);
		}

		if ($keywords = $page->getMetaKeywords()) {
			$this->pageConfig->setKeywords($keywords);
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductListHtml()
	{
		return $this->getChildHtml('product_list');
	}
}