<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Sociallogin\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Customer Observer Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Socialloginpredispatch implements ObserverInterface
{

    public function __construct()
    {

    }

    /**
     * Address after save event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
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
