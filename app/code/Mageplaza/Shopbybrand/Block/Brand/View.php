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

namespace Mageplaza\Shopbybrand\Block\Brand;

use Mageplaza\Shopbybrand\Block\Brand;


/**
 * Class View
 * @package Mageplaza\Shopbybrand\Block
 */
class View extends Brand
{
    /**
	 * @return $this
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	protected function _prepareLayout()
	{
		parent::_prepareLayout();

		$brand = $this->getBrand();
		$title = $brand->getPageTitle() ?: $brand->getValue();
		if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
			$breadcrumbsBlock->addCrumb('view', ['label' => $title]);
		}

		$description = $brand->getMetaDescription();
		if ($description) {
			$this->pageConfig->setDescription($description);
		}
		$keywords = $brand->getMetaKeywords();
		if ($keywords) {
			$this->pageConfig->setKeywords($keywords);
		}

		$pageMainTitle = $this->getLayout()->getBlock('page.main.title');
		if ($pageMainTitle) {
			$pageMainTitle->setPageTitle($title);
		}

		return $this;
	}

    //************************* Init BrandView Breadcrumbs ***************************
    /**
     * @param $block
     *
     * @return $this
     */
	protected function additionCrumb($block)
	{
		$title = $this->getPageTitle();
		$block->addCrumb(
			'brand',
			[
				'label' => __($title),
				'title' => __($title),
				'link'  => $this->helper->getBrandUrl()
			]
		);

		$brand      = $this->getBrand();
		$brandTitle = $brand->getPageTitle() ?: $brand->getValue();
		$block->addCrumb('view', ['label' => $brandTitle]);

		return $this;
	}

    /**
     * @return $this
     */
	protected function initBreadcrumbs()
	{
		if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
			$title = $this->getPageTitle();

			$breadcrumbsBlock->addCrumb(
				'home',
				[
					'label' => __('Home'),
					'title' => __('Go to Home Page'),
					'link'  => $this->_storeManager->getStore()->getBaseUrl()
				]
			);
			$breadcrumbsBlock->addCrumb(
				'brand',
				[
					'label' => __($title),
					'title' => __($title),
					'link'  => $this->helper->getBrandUrl()
				]
			);
		}

		return $this;
	}

    //************************* If Metatitle brand blank, PageTitle = Brand PageTitle in Config ***************************
	/**
	 * @return mixed
	 */
	public function getMetaTitle()
	{
		$brand = $this->getBrand();

		$metaTitle = $brand->getMetaTitle();
		if ($metaTitle) {
			return $metaTitle;
		}

		$title = $brand->getPageTitle() ?: $brand->getValue();

		return join($this->getTitleSeparator(), [$title, $this->getPageTitle()]);
	}

    //************************* Get Brand CMS Block information to show on Catalog_View frontend ***************************
	/**
	 * @return mixed
	 */
	public function getBrand()
	{
		return $this->_coreRegistry->registry('current_brand');
	}

	/**
	 * @return string
	 */
	public function getBrandImage()
	{
		if (!$this->helper->getBrandDetailConfig('show_image')) {
			return '';
		}

		$brand = $this->getBrand();

		return $this->helper()->getBrandImageUrl($brand);
	}

	/**
	 * @return string
	 */
	public function getBrandDescription()
	{
		if (!$this->helper->getBrandDetailConfig('show_description')) {
			return '';
		}

		return $this->helper()->getBrandDescription($this->getBrand());
	}

	/**
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getStaticContent()
	{
		if (!$this->helper->getBrandDetailConfig('show_block')) {
			return '';
		}

		$block = $this->getBrand()->getStaticBlock() ?: $this->helper->getBrandDetailConfig('default_block');

		$cmsBlock = $this->_blockFactory->create();
		$cmsBlock->load($block, 'identifier');

		$html = '';
		if ($cmsBlock && $cmsBlock->getId()) {
			$html = $this->getLayout()->createBlock('Magento\Cms\Block\Block')
				->setBlockId($cmsBlock->getId())
				->toHtml();
		}

		return $html;
	}

    //************************* Get Template Product->List  ***************************
	/**
	 * @return string
	 */
	public function getProductListHtml()
	{
		return $this->getChildHtml();
	}
}
