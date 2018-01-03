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

namespace Mageplaza\LayeredNavigationUltimate\Controller\Adminhtml\ProductsPage;

use Magento\Framework\Filter\FilterManager;

/**
 * Class Save
 * @package Mageplaza\LayeredNavigationUltimate\Controller\Adminhtml\ProductsPage
 */
class Save extends \Magento\Backend\App\Action
{
	/** @var \Mageplaza\LayeredNavigationUltimate\Model\ProductsPageFactory */
	public $pageFactory;

	/**
	 * @type \Magento\Framework\Filter\FilterManager
	 */
	protected $_filter;

	/**
	 * Save constructor.
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Mageplaza\LayeredNavigationUltimate\Model\ProductsPageFactory $pageFactory
	 * @param \Magento\Framework\Filter\FilterManager $filter
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Mageplaza\LayeredNavigationUltimate\Model\ProductsPageFactory $pageFactory,
		FilterManager $filter
	)
	{
		$this->pageFactory = $pageFactory;
		$this->_filter     = $filter;

		parent::__construct($context);
	}

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 * @return void
	 */
	public function execute()
	{
		$pageId = $this->getRequest()->getParam('page_id');
		$data   = $this->getRequest()->getParams();
		if ($data) {
			$this->prepareData($data);

			$page = $this->pageFactory->create();
			if ($pageId) {
				$page->load($pageId);
			}

			$errors = $this->validateData($data);
			if (sizeof($errors)) {
				foreach ($errors as $error) {
					$this->messageManager->addErrorMessage($error);
				}

				if ($pageId) {
					$this->_redirect('*/*/edit', array('page_id' => $pageId));
				} else {
					$this->_redirect('*/*/new');
				}

				return;
			}

			$page->setData($data);

			try {
				$page->save();

				$this->messageManager->addSuccessMessage(__('The page has been saved successfully.'));

				$this->_objectManager->get('Magento\Backend\Model\Session')->setProductFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('page_id' => $page->getId()));

					return;
				}

				$this->_redirect('*/*/');

				return;
			} catch (\RuntimeException $e) {
				$this->messageManager->addErrorMessage($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addErrorMessage($e->getMessage());
				$this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the page.'));
			}

			$this->_redirect('*/*/edit', array('page_id' => $this->getRequest()->getParam('page_id')));

			return;
		}
		$this->_redirect('*/*/');
	}

	/**
	 * @param $data
	 * @return mixed
	 */
	private function prepareData(&$data)
	{
		$data['route'] = $this->formatUrlKey($data['route']);

		$data['default_attributes'] = isset($data['default_attributes']) ? json_encode($data['default_attributes']) : '';
		unset($data['attributes']);

		$this->_getSession()->setProductFormData($data);

		return $data;
	}

	/**
	 * Format URL key from name or defined key
	 *
	 * @param string $str
	 * @return string
	 */
	public function formatUrlKey($str)
	{
		return $this->_filter->translitUrl($str);
	}

	/**
	 * Validate input data
	 *
	 * @param array $data
	 * @return array
	 */
	public function validateData(array $data)
	{
		$errors = [];

		if (!isset($data['name'])) {
			$errors[] = __('Please enter the page name.');
		}

		if (!isset($data['page_title'])) {
			$errors[] = __('Please enter the page title.');
		}

		if (!isset($data['route'])) {
			$errors[] = __('Please enter the page route.');
		} else {
			$pages = $this->pageFactory->create()->getCollection()
				->addFieldToFilter('route', $data['route']);
			if (sizeof($pages)) {
				if (!isset($data['page_id'])) {
					$errors[] = __('The url key "%1" has been used.', $data['route']);
				} else {
					foreach ($pages as $page) {
						if ($page->getId() != $data['page_id']) {
							$errors[] = __('The url key "%1" has been used.', $data['route']);
						}
					}
				}
			}
		}

		return $errors;
	}
}
