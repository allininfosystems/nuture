<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Block\Adminhtml\Earningrates\Edit;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;


class SaveButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param AccountManagementInterface $customerAccountManagement
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context, $registry);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [
            'label' => __('Save Earning Rate'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
        return $data;
    }
}
