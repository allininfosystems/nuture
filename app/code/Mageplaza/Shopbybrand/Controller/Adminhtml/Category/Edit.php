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

namespace Mageplaza\Shopbybrand\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;

/**
 * Class Edit
 * @package Mageplaza\Shopbybrand\Controller\Adminhtml\Category
 */
class Edit extends Action
{
	/**
	 * @var \Mageplaza\Shopbybrand\Helper\Data
	 */
	public $helper;

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	public $resultPageFactory;

	public $categoryFactory;

	public $registry;

	/**
	 * @var \Magento\Framework\Json\Helper\Data
	 */
	public $jsonHelper;

	/**
	 * Edit constructor.
	 * @param \Mageplaza\Shopbybrand\Helper\Data $data
	 * @param \Magento\Framework\Json\Helper\Data $jsonHelper
	 * @param \Magento\Framework\View\Result\PageFactory $pageFactory
	 * @param \Magento\Backend\App\Action\Context $context
	 */
	public function __construct(
		\Mageplaza\Shopbybrand\Helper\Data $data,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Framework\Registry $registry,
		\Mageplaza\Shopbybrand\Model\CategoryFactory $categoryFactory,
		\Magento\Backend\App\Action\Context $context
	)
	{
		$this->helper             = $data;
		$this->jsonHelper         = $jsonHelper;
		$this->registry           = $registry;
		$this->resultPageFactory  = $pageFactory;
		$this->categoryFactory = $categoryFactory;

		parent::__construct($context);
	}

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		if ($this->getRequest()->isAjax()) {
//			$attCode = $this->getRequest()->getParam('attributeCode');
//			$options = $this->helper->getAttributeOptions($attCode);
//			if (!empty($options)) {
//				return $this->getResponse()->representJson($this->jsonHelper->jsonEncode($options));
//			}
		}

		$cat = $this->categoryFactory->create();
		if ($id = $this->getRequest()->getParam('cat_id')) {
			$cat->load($id);
			if (!$cat->getId()) {
				$this->messageManager->addErrorMessage(__('The category doesnot exist.'));
				$this->_redirect('*/*/');

				return;
			}
		}

		//Set entered data if was error when we do save
		$data = $this->_session->getProductFormData(true);
		if (!empty($data)) {
			$cat->setData($data);
		}

		$this->registry->register('current_brand_category', $cat);

		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->set($cat->getId() ? $cat->getName() : __('New Category'));

		return $resultPage;
	}
}
