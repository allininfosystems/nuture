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

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;


class View extends \Magento\Backend\Block\Widget\Form\Generic{


    /**
     * @var TimezoneInterface
     */
    protected $timezone;


    protected  $_transaction;
    /**
     * GeneralTab constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magestore\Megamenu\Model\Megamenu $megamenuModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magestore\Rewardpoints\Model\Transaction $transaction,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_storeManager= $context->getStoreManager();
        $this->_transaction = $transaction;

    }


    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resourceActions = $objectManager->create('Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction\Actions');

        $resourceStoreView = $objectManager->create('Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction\StoreView');

        $id  = $this->getRequest()->getParam('id');
        $transaction = $this->_transaction->load($id);
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('general_fieldset', ['legend' => __('Transaction Information')]);

        $fieldset->addField('transactionTitle', 'note', array(
            'label'     => __('Transaction Title'),
            'text'      => $transaction->getTitle(),
        ));
        $fieldset->addField('customerEmail', 'note', array(
            'label'     => __('Customer Email'),
            'text'      => '<a href="#">'.$transaction->getCustomerEmail().'</a>'
        ));
        $fieldset->addField('action', 'note', array(
            'label'     => __('Action'),
            'text'      => $resourceActions->toOptionHash()[$transaction->getAction()]
        ));
        $fieldset->addField('status', 'note', array(
            'label'     => __('Status'),
            'text'      => '<strong>'.$transaction->getStatusHash()[$transaction->getStatus()].'</strong>'
        ));
        $fieldset->addField('points', 'note', array(
            'label'     => __('Points'),
            'text'      => '<strong>'.$transaction->getPointAmount().' '.__('Points').'</strong>'
        ));
        $fieldset->addField('pointUsed', 'note', array(
            'label'     => __('Point Used'),
            'text'      => $transaction->getPointUsed().' '.__('Points')
        ));


        $fieldset->addField('createdTime', 'note', array(
            'label'     => __('Created time'),
            'text'      => date('F j, Y g:i A',strtotime($transaction->getCreatedTime()))
        ));
        $updatedTime = ($transaction->getUpdatedTime())?$transaction->getUpdatedTime():$transaction->getCreatedTime();
        $fieldset->addField('updatedAt', 'note', array(
            'label'     => __('Updated At'),
            'text'      =>  date('F j, Y g:i A',strtotime($updatedTime))
        ));
        $fieldset->addField('storeView', 'note', array(
            'label'     => __('Store View'),
            'text'      => $resourceStoreView->toOptionHash()[$transaction->getStoreId()]
        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
