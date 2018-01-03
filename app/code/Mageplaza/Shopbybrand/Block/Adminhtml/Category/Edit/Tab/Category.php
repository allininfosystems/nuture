<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Shopbybrand\Block\Adminhtml\Category\Edit\Tab;

/**
 * Class Category
 *
 * @package Mageplaza\Shopbybrand\Block\Adminhtml\Category\Edit\Tab
 */
class Category extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Config\Model\Config\Source\Enabledisable
     */
    protected $_booleanOptions;

    /**
     * @var \Mageplaza\Shopbybrand\Model\Config\Source\MetaRobots
     */
    public $metaRobotsOptions;

    /**
     * Category constructor.
     *
     * @param \Mageplaza\Shopbybrand\Model\Config\Source\MetaRobots $metaRobotsOptions
     * @param \Magento\Config\Model\Config\Source\Enabledisable     $booleanOptions
     * @param \Magento\Backend\Block\Template\Context               $context
     * @param \Magento\Framework\Registry                           $registry
     * @param \Magento\Framework\Data\FormFactory                   $formFactory
     * @param \Magento\Store\Model\System\Store                     $systemStore
     * @param array                                                 $data
     */
    public function __construct(
        \Mageplaza\Shopbybrand\Model\Config\Source\MetaRobots $metaRobotsOptions,
        \Magento\Config\Model\Config\Source\Enabledisable $booleanOptions,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore, array $data = array()
    ) {
        $this->_booleanOptions = $booleanOptions;
        $this->_systemStore = $systemStore;
        $this->metaRobotsOptions = $metaRobotsOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Mageplaza\Shopbybrand\Model\Category */
        $model = $this->_coreRegistry->registry('current_brand_category');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('cat_');

        $fieldset = $form->addFieldset(
            'base_fieldset', array('legend' => __('Category Information'))
        );

        if ($model->getId()) {
            $fieldset->addField('cat_id', 'hidden', array('name' => 'cat_id'));
        }

        $fieldset->addField(
            'name', 'text',
            ['name'     => 'name', 'label' => __('Name'), 'title' => __('Name'),
             'required' => true]
        );

        $fieldset->addField(
            'url_key', 'text', ['name'  => 'url_key', 'label' => __('Url key'),
                                'title' => __('Url key')]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store_ids', 'multiselect',
                ['name'   => 'store_ids', 'label' => __('Stores view'),
                 'title'  => __('Stores view'),
                 'values' => $this->_systemStore->getStoreValuesForForm(
                     false, true
                 )]
            );
        }

        $fieldset->addField(
            'status', 'select', ['name'   => 'status', 'label' => __('Status'),
                                 'title'  => __('Status'),
                                 'values' => $this->_booleanOptions->toOptionArray(
                                 )]
        );
        $fieldset->addField(
            'meta_title', 'text',
            ['name'  => 'meta_title', 'label' => __('Meta Title'),
             'title' => __('Meta Title')]
        );
        $fieldset->addField(
            'meta_keywords', 'text',
            ['name'  => 'meta_keywords', 'label' => __('Meta Keywords'),
             'title' => __('Meta Keywords')]
        );
        $fieldset->addField(
            'meta_description', 'textarea',
            ['name'  => 'meta_description', 'label' => __('Meta Description'),
             'title' => __('Meta Description')]
        );
        $fieldset->addField(
            'meta_robots', 'select',
            ['name'   => 'meta_robots', 'label' => __('Meta Robots'),
             'title'  => __('Meta Robots'),
             'values' => $this->metaRobotsOptions->toOptionArray(),]
        );
        if (!$model->getId()) {
            $model->addData(
                ['status' => 1, 'store_ids' => '0']
            );
        }

        $savedData = $model->getData();
        $form->setValues($savedData);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Category');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Category');
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
}
