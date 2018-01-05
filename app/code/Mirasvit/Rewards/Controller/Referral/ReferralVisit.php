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

class ReferralVisit extends \Mirasvit\Rewards\Controller\Referral
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $referralLink = $this->getRequest()->getParam('referral_link');
        if ($this->session->getReferral()) {
            $this->_redirect('/');

            return;
        }
        $link = $this->referralLinkCollectionFactory->create()
            ->addFieldToFilter('referral_link', $referralLink)
            ->getFirstItem();

        if ($customerId = (int) $link->getCustomerId()) {
            $referral = $this->referralFactory->create()
                ->setCustomerId($customerId)
                ->setStatus(Config::REFERRAL_STATUS_VISITED)
                ->setStoreId($this->storeManager->getStore()->getId())
                ->save();
            $this->session->setReferral($referral->getId());
        }

        $this->_redirect('/');
    }
}
