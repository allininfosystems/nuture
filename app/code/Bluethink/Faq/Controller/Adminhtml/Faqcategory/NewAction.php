<?php
namespace Bluethink\Faq\Controller\Adminhtml\Faqcategory;
use Magento\Backend\App\Action;
class NewAction extends \Magento\Backend\App\Action
{
     public function execute()
    {
		$this->_forward('edit');
    }
}
