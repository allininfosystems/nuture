<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Ljlogin;
class Setblock extends \Magestore\Sociallogin\Controller\Sociallogin
{

    public function execute()
    {

        try {

            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

}