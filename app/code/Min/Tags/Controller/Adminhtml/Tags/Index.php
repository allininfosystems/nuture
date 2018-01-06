<?php
/**
 * Index controller
 * @author Min <dangquocmin@gmail.com>
 */
namespace Min\Tags\Controller\Adminhtml\Tags;
class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
        return $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/min_tags/"));
    }

}