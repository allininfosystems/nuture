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
namespace Magenest\ZohocrmIntegration\Block\Adminhtml\Map\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magenest\ZohocrmIntegration\Model\FieldFactory;
use Magenest\ZohocrmIntegration\Model\Status;

/**
 * Class Form
 * @package Magenest\ZohocrmIntegration\Block\Adminhtml\Map\Edit
 */
class Form extends Generic
{
    /**
     * Status
     *
     * @var \Magenest\ZohocrmIntegration\Model\Status
     */
    protected $_status;

    /**
     * Type of mapping , it can be Leads,Contacts...
     *
     * @var string
     */
    protected $_type;

    /**
     * @var \Magenest\ZohocrmIntegration\Model\FieldFactory
     */
    protected $_fieldFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magenest\ZohocrmIntegration\Model\FieldFactory    $fieldFactory
     * @param \Magenest\ZohocrmIntegration\Model\Status          $status
     * @param array                                   $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        FieldFactory $fieldFactory,
        Status $status,
        array $data = []
    ) {
        $this->_fieldFactory = $fieldFactory;
        $this->_status       = $status;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare edit review form
     *
     * @return                                        $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model  = $this->_coreRegistry->registry('zoho_mapping');
        $_model = $this->_fieldFactory->create();
        $_type  = $_model->changeFields();
        $isElementDisabled = false;
        $zohoFields        = [];
        $magentoFields     = [];

        $form     = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Mapping  Details')]);
        if ($model->getId()) {
            $type          = $model->getType();
            $zohoFields    = $_model->getZohoFields($type);
            $table         = $_model->getAllTable();
            $m_table       = $table[$type];
            $magentoFields = $_model->getMagentoFields($m_table);
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
            $isElementDisabled = true;
        }

        $fieldset->addField(
            'type',
            'select',
            [
             'label'    => __('Type'),
             'required' => true,
             'name'     => 'type',
             'options'  => $_type,
             'disabled' => $isElementDisabled,
            ]
        );
        $fieldset->addField(
            'magento_field',
            'select',
            [
             'label'    => __('Magento Field'),
             'required' => true,
             'name'     => 'magento_field',
             'values'   => $magentoFields,
            ]
        );
        $fieldset->addField(
            'zoho_field',
            'select',
            [
             'label'    => __('Zoho Field'),
             'required' => true,
             'name'     => 'zoho_field',
             'values'   => $zohoFields,
            ]
        );
        $fieldset->addField(
            'description',
            'textarea',
            [
             'name'      => 'description',
             'title'     => __('Description'),
             'label'     => __('Description'),
             'maxlength' => '255',
             'required'  => true,
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
             'label'    => __('Status'),
             'required' => true,
             'name'     => 'status',
             'values'   => $this->_status->getOptionArray(),
            ]
        );
        $form->setUseContainer(true);
        $form->setValues($model->getData());
        $this->setForm($form);
        $form->setAction($this->getUrl('zohocrm/map/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        return parent::_prepareForm();
    }
}
