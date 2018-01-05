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



namespace Mirasvit\Rewards\Block\Adminhtml;

use Magento\Framework\DataObject;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;

class Menu extends AbstractMenu
{
    /**
     * Menu constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->visibleAt(['rewards']);

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        $this->addItem([
            'resource' => 'Mirasvit_Rewards::reward_points_earning_rule',
            'title'    => __('Earning Rules'),
            'url'      => $this->urlBuilder->getUrl('rewards/earning_rule'),
        ])->addItem([
            'resource' => 'Mirasvit_Rewards::reward_points_spending_rule',
            'title'    => __('Spending Rules'),
            'url'      => $this->urlBuilder->getUrl('rewards/spending_rule'),
        ])->addItem([
            'resource' => 'Mirasvit_Rewards::reward_points_notification_rule',
            'title'    => __('Notification Rules'),
            'url'      => $this->urlBuilder->getUrl('rewards/notification_rule'),
        ])->addItem([
            'resource' => 'Mirasvit_Rewards::reward_points_transaction',
            'title'    => __('Transactions'),
            'url'      => $this->urlBuilder->getUrl('rewards/transaction'),
        ])->addItem([
            'resource' => 'Mirasvit_Rewards::reward_points_referral',
            'title'    => __('Customer Referrals'),
            'url'      => $this->urlBuilder->getUrl('rewards/referral'),
        ])->addItem([
            'resource' => 'Mirasvit_Rewards::reward_points_report',
            'title'    => __('Reports'),
            'url'      => $this->urlBuilder->getUrl('rewards/report/view'),
        ])->addItem([
            'resource' => 'Mirasvit_Rewards::reward_points_report',
            'title'    => __('Refresh Statistics'),
            'url'      => $this->urlBuilder->getUrl('rewards/report/aggregate'),
        ]);

        $this->addSeparator();

        $this->addItem([
            'resource' => 'Mirasvit_Rewards::reward_points_settings',
            'title'    => __('Settings'),
            'url'      => $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/rewards'),
        ]);
    }
}
