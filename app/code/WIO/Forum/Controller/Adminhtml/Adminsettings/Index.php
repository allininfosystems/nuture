<?php

/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

namespace WIO\Forum\Controller\Adminhtml\Adminsettings;

class Index extends \WIO\Forum\Controller\Adminhtml\Index {

  public function execute() {
    
    $model = $this->_objectManager->create('WIO\Forum\Model\Usersettings');
    $model->load(\WIO\Forum\Helper\Constant::WIO_FORUM_ADMIN_ID, 'system_user_id');
    
    /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
    $resultPage = $this->resultPageFactory->create();
    
    $this->initPageForum($resultPage)->addBreadcrumb(
      __('Forum Admin Profile'), __('Forum Admin Profile')
    );
    $this->_coreRegistry->register('admsettings_model', $model);
    
    $resultPage->getConfig()->getTitle()->prepend(__('Forum Admin Profile'));
    return $resultPage;
  }

}
