<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ZohocrmIntegration extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ZohocrmIntegration
 * @author   ThaoPV
 */
namespace  Magenest\ZohocrmIntegration\Block\Adminhtml\Map;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class Edit Block
 *
 * @package Magenest\ZohocrmIntegration\Block\Adminhtml\Map
 */
class Edit extends Container
{
    /**
     * Initialize  edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'Magenest_ZohocrmIntegration';
        $this->_controller = 'adminhtml_map';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Mapping'));
        $this->buttonList->add(
            'saveandcontinue',
            [
             'label'          => __('Save and Continue Edit'),
             'class'          => 'save',
             'data_attribute' => [
                                  'mage-init' => [
                                                  'button' => [
                                                               'event'  => 'saveAndContinueEdit',
                                                               'target' => '#edit_form',
                                                              ],
                                                 ],
                                 ],
            ],
            -100
        );
        $this->buttonList->add(
            'updateallfields',
            [
             'label' => __('Update All Fields'),
            ],
            -90
        );
        $this->buttonList->remove('delete');
    }
}
