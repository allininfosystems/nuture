<?php
namespace Magestore\Rewardpoints\Controller\Settings;

class SettingsPost extends \Magestore\Rewardpoints\Controller\AbstractAction
{
    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {

        if ($this->getRequest()->isPost() && $this->_customerSession->isLoggedIn()
        ) {
            $customerId = $this->_customerSession->getCustomerId();
            $rewardAccount = $this->_rewardpointsCustomerFactory->create()->load($customerId, 'customer_id');
            if (!$rewardAccount->getId()) {
                $rewardAccount->setCustomerId($customerId)
                    ->setData('point_balance', 0)
                    ->setData('holding_balance', 0)
                    ->setData('spent_balance', 0);
            }
            $rewardAccount->setIsNotification((boolean) $this->getRequest()->getPost('is_notification'))
                ->setExpireNotification((boolean) $this->getRequest()->getPost('expire_notification'));
            try {
                $rewardAccount->save();
                $this->messageManager->addSuccess(__('Your settings has been updated successfully.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this->_redirect('*/settings');
    }

}
