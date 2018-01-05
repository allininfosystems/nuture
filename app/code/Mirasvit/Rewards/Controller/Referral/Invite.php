<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Controller\Referral;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Rewards\Model\Config as Config;

class Invite extends \Mirasvit\Rewards\Controller\Referral
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $id = (int) $this->getRequest()->getParam('id');
        $referral = $this->referralFactory->create()->load($id);
        if ($referral->getStatus() != Config::REFERRAL_STATUS_SENT) {
            $this->_redirect('/');

            return;
        }
        $referral->setStatus(Config::REFERRAL_STATUS_VISITED)
                 ->save();
        $this->_getSession()->setReferral($referral->getId());
        $this->_redirect('/');
    }
}
