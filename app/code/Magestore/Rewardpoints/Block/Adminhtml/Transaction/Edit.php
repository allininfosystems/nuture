<?php

/**
 * Magestore.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Megamenu
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Rewardpoints\Block\Adminhtml\Transaction;

/**
 * Form containerEdit
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
{
    $this->_objectId = 'reward_id';
    $this->_blockGroup = 'Magestore_Rewardpoints';
    $this->_controller = 'adminhtml_transaction';

    parent::_construct();

    $this->buttonList->update('save', 'label', __('Save Transaction'));
    $this->buttonList->update('delete', 'label', __('Delete'));

    $this->buttonList->add(
        'saveandcontinue',
        [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
            ],
        ],
        -100
    );

}

}
