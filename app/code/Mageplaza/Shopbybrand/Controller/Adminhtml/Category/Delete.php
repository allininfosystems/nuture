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

/**
 * Class Delete
 * @package Mageplaza\Shopbybrand\Controller\Adminhtml\Category
 */
class Delete extends \Magento\Backend\App\Action
{
	/**
	 * Delete page item
	 */
	public function execute()
	{
		$id = $this->getRequest()->getParam('cat_id');
		try {
			$cat = $this->_objectManager->create('Mageplaza\Shopbybrand\Model\Category')->load($id);
			if ($cat && $cat->getId()) {
				$cat->delete();
				$this->messageManager->addSuccessMessage(__('Delete successfully !'));
			} else {
				$this->messageManager->addErrorMessage(__('Cannot find category to delete.'));
			}
		} catch (\Exception $e) {
			$this->messageManager->addErrorMessage($e->getMessage());
		}

		$this->_redirect('*/*/');
	}
}
