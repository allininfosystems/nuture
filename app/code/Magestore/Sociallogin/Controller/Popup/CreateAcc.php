<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Popup;

class CreateAcc extends \Magestore\Sociallogin\Controller\Sociallogin
{

    public function execute()
    {

        try {

            $this->_create();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    public function _create()
    {

        if ($this->_getSession()->isLoggedIn()) {
            $result = ['success' => false, 'Can Not Login!'];
        } else {
            $firstName = $this->getRequest()->getPost('firstname', false);
            $lastName = $this->getRequest()->getPost('lastname', false);
            $pass = $this->getRequest()->getPost('pass', false);
            $passConfirm = $this->getRequest()->getPost('passConfirm', false);
            $email = $this->getRequest()->getPost('email', false);
            $model = $this->_objectManager->create('Magento\Customer\Model\Customer');
            $customer = $model->setFirstname($firstName)
                ->setLastname($lastName)
                ->setEmail($email)
                ->setPassword($pass)
                ->setConfirmation($passConfirm);

            try {
                $customer->save();

                $manacustomer = $this->_objectManager->create('Magento\Framework\Event\ManagerInterface');

                $manacustomer->dispatch('customer_register_success',
                    ['customer' => $customer]
                );
                $result = ['success' => true];
                $customer->sendNewAccountEmail(
                    'confirmation',
                    $this->_getSession()->getBeforeAuthUrl()

                );
                $this->_getSession()->setCustomerAsLoggedIn($customer);

            } catch (\Exception $e) {
                $result = ['success' => false, 'error' => $e->getMessage()];
            }
        }
        $jsonEncode = $this->_objectManager->create('Magento\Framework\Json\Helper\Data');
        $this->getResponse()->setBody($jsonEncode->jsonEncode($result));
    }

}