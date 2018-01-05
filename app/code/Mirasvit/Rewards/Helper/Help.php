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



namespace Mirasvit\Rewards\Helper;

/**
 * @SuppressWarnings(PHPMD)
 */
class Help extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @var array
     */
    protected $_help = [
        'system' => [
            'general_point_unit_name' => '',
            'general_expires_after_days' => '',
            'general_is_earn_after_invoice' => '',
            'general_is_earn_after_shipment' => '',
            'general_earn_in_statuses' => '',
            'general_is_cancel_after_refund' => '',
            'general_is_restore_after_refund' => '',
            'general_is_earn_shipping' => '',
            'general_is_spend_shipping' => '',
            'general_is_allow_zero_orders' => '',
            'notification_sender_email' => '',
            'notification_balance_update_email_template' => '',
            'notification_points_expire_email_template' => '',
            'notification_send_before_expiring_days' => '',
            'referral_is_active' => '',
            'referral_invitation_email_template' => '',
        ],
    ];

    /************************/
}
