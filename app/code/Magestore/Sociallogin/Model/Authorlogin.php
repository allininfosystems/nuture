<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Authorlogin extends Sociallogin
{

    public function _construct()
    {
        $this->_init('Magestore\Sociallogin\Model\ResourceModel\Authorlogin');
    }

    public function addCustomer($authorId = null)
    {
        $customer = $this->_customerCollectionFactory->create();
        $customer = $customer->getLastItem();
        $customer_id = $customer->getData('entity_id');

        $model = $this->_authorloginFactory->create();
        $model->setData('author_id', $authorId)
            ->setData('customer_id', $customer_id)
            ->save();

        return true;
    }

}