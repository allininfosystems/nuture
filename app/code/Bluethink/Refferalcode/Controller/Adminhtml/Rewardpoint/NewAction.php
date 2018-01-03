<?php
namespace Bluethink\Refferalcode\Controller\Adminhtml\Rewardpoint;
use Magento\Backend\App\Action;
class NewAction extends \Magento\Backend\App\Action
{
     public function execute()
    {
		$this->_forward('edit');
    }
}
