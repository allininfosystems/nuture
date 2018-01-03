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

namespace Mageplaza\Shopbybrand\Block\Adminhtml\Category\Edit;

/**
 * Class Form
 * @package Mageplaza\Shopbybrand\Block\Adminhtml\Category\Edit
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
	/**
	 * @return $this
	 */
	protected function _prepareForm()
	{
		/** @var \Magento\Framework\Data\Form $form */
		$form = $this->_formFactory->create(
			array(
				'data' => array(
					'id'      => 'edit_form',
					'action'  => $this->getUrl('*/*/save'),
					'method'  => 'post',
					'enctype' => 'multipart/form-data'
				)
			)
		);
		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}
}
