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

namespace Mageplaza\Shopbybrand\Block\Adminhtml\Form\Renderer;

/**
 * Class RenderDefaultAttributes
 * @package Mageplaza\LayeredNavigationUltimate\Block\Adminhtml\Form\Renderer
 */
class BrandCategory extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
	/** @var string Template */
	protected $_template = 'Mageplaza_Shopbybrand::category/brands.phtml';

	/**
	 * @var \Mageplaza\Shopbybrand\Helper\Data
	 */
	public $helperData;

	/**
	 * @var \Mageplaza\Shopbybrand\Block\Brand
	 */
	public $brands;

	public $systemStore;

	public $coreRegistry;

	/**
	 * BrandCategory constructor.
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Mageplaza\Shopbybrand\Helper\Data $helperData
	 * @param array $data
	 */
	public function __construct(
		\Mageplaza\Shopbybrand\Helper\Data $helperData,
		\Mageplaza\Shopbybrand\Model\BrandFactory $brand,
		\Magento\Store\Model\System\Store $systemStore,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Backend\Block\Template\Context $context,
		array $data = []
	)
	{
		$this->helperData   = $helperData;
		$this->brands       = $brand;
		$this->coreRegistry = $coreRegistry;
		$this->systemStore  = $systemStore;

		parent::__construct($context, $data);
	}

	/**
	 * render custom form element
	 * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
	 * @return string
	 */
	public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
	{
		$this->_element = $element;
		$html           = $this->toHtml();

		return $html;
	}

	/**
	 * get brand collection
	 * @return \Mageplaza\Shopbybrand\Block\Brand
	 */
	public function getBrands()
	{
		return $this->brands->create()->getBrandCollection();
	}

	/**
	 * get all store as array
	 * @return array
	 */
	public function getStoreViews()
	{
		return $this->systemStore->getStoreValuesForForm();
	}

	/**
	 * check is single store
	 * @return bool
	 */
	public function isSingleStoreMode()
	{
		return $this->_storeManager->isSingleStoreMode();
	}

	public function getSelectedBrands()
	{
		$optionIds     = [];
		$model         = $this->coreRegistry->registry('current_brand_category');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource      = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection    = $resource->getConnection();
		if ($model->getId()) {
			$sql    = "select option_id from " . $resource->getTableName('mageplaza_shopbybrand_brand_category') . " where cat_id =" . $model->getId();
			$result = $connection->fetchAll($sql);

			foreach ($result as $item) {
				$optionIds[] = $item['option_id'];
			}
		}

		return $optionIds;
	}
}
