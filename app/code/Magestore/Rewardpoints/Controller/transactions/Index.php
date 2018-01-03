<?php
namespace Magestore\Rewardpoints\Controller\Transactions;

class Index extends \Magestore\Rewardpoints\Controller\AbstractAction
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
