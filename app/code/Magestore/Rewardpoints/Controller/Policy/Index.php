<?php
namespace Magestore\Rewardpoints\Controller\Policy;


class Index extends \Magestore\Rewardpoints\Controller\AbstractAction
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $page = $this->getPage();
        if ($page && $page->getId()) {
            $resultPage->getConfig()->getTitle()->set($page->getContentHeading());
        } else {
            $resultPage->getConfig()->getTitle()->set(__('Reward Policy'));
        }
        return $resultPage;
    }

    /**
     * @return mixed
     */
    public function getPageIdentifier(){
        return $this->_helperConfig->getConfig(
            \Magestore\Rewardpoints\Helper\Policy::XML_PATH_POLICY_PAGE
        );
    }
    /**
     * @return mixed
     */
    public function getPageId(){
        $pageId = $this->_modelPage->checkIdentifier($this->getPageIdentifier(), $this->_storeManager->getStore()->getId());
        return $pageId;
    }

    /**
     * @return \Magento\Cms\Model\Page
     */
    public function getPage(){
        return $this->_modelPage->load($this->getPageId());
    }


}
