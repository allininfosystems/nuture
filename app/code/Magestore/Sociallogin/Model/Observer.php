<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Sociallogin\Model;

class Observer
{

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface $session
     *
     *
     */

    protected $_session;

    /**
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Session\SessionManagerInterface $session
    )
    {
        $this->_session = $session;
    }

    public function customer_edit($observer)
    {
        try {
            $customerId = $this->session->getCustomerIdSocialLogin();
            if ($customerId) {
                $this->session->getCustomer()->setEmail(' ');
            }
            $this->session->setCustomerIdSocialLogin();
        } catch (Exception $e) {
        }
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller = $observer->getControllerAction();
        if ($controller->getRequest()->getControllerName() != 'system_config'
            || $controller->getRequest()->getActionName() != 'edit'
        ) {
            return;
        }

        $section = $controller->getRequest()->getParam('section');
        if ($section != 'sociallogin') {
            return;
        }

    }
}