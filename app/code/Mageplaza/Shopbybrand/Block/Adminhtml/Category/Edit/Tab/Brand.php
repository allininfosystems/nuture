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

namespace Mageplaza\Shopbybrand\Block\Adminhtml\Category\Edit\Tab;

/**
 * Class Brand
 * @package Mageplaza\Shopbybrand\Block\Adminhtml\Category\Edit\Tab
 */
class Brand extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
	 * Brand constructor.
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		array $data = array()
	)
	{
		parent::__construct($context, $registry, $formFactory, $data);
	}

	/**
	 * Prepare form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		//todo create default attributes table
		/** @var \Mageplaza\Shopbybrand\Model\Category $model */
		$model = $this->_coreRegistry->registry('current_brand_category');
		$form  = $this->_formFactory->create();
		$form->setHtmlIdPrefix('cat_');

		$fieldset = $form->addFieldset(
			'base_fieldset',
			[
				'legend' => __('Brands'),
				'class'  => 'fieldset-wide'
			]
		);
		$field = $fieldset->addField(
			'brands',
			'text',
			[
				'label' => __('Brands'),
				'title' => __('Brands')
			]
		);

		/** @var \Magento\Framework\Data\Form\Element\Renderer\RendererInterface $renderer */
		$renderer = $this->getLayout()->createBlock('Mageplaza\Shopbybrand\Block\Adminhtml\Form\Renderer\BrandCategory');
		$field->setRenderer($renderer);

		$form->addValues($model->getData());
		$this->setForm($form);

		return parent::_prepareForm();
	}

	/**
	 * Prepare label for tab
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return __('Brands');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return __('Brands');
	}

	/**
	 * {@inheritdoc}
	 */
	public function canShowTab()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isHidden()
	{
		return false;
	}
}
