<?php
namespace Magestore\Rewardpoints\Controller\Settings;

class Index extends \Magestore\Rewardpoints\Controller\AbstractAction
{
    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
