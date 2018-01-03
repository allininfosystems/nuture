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
namespace  Magestore\Rewardpoints\Block\Adminhtml\Transaction\Edit;

use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Tab GeneralTab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected  $_storeManager;

    /**
     * GeneralTab constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_storeManager= $context->getStoreManager();
        $this->_systemStore =$systemStore;
    }


    /**
     * get registry model.
     *
     * @return \Magento\Framework\Model\AbstractModel|null
     */
    public function getRegistryModel()
    {
        return $this->_coreRegistry->registry('megamenu_model');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Transaction information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Transaction information');
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

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset('general_fieldset', ['legend' => __('Transaction Information')]);

        $fieldset->addField(
            'featured_customers',
            'text',
            [
                'name' => 'featured_customers',
                'label' => __('Customer'),
                'class' => 'rule-param',
                "required" => true,
                "readonly" => true,
                'after_element_html' => '<a  href="javascript:void(0)" onclick="toggleFeaturedProducts()">Select</a>
                <input type="hidden" id="selectedCustomer" name="customer_id"/>
		        <script type="text/javascript">
		            
                    function toggleFeaturedProducts(){
                        var url = "' . $this->getUrl('rewardpoints/widget/chooserCustomer') . '";
                        var params = ($("featured_products"))?$("featured_products").value.split(", "):"";
                        var parameters = {"form_key": FORM_KEY,"selected[]":params };
                        var request = new Ajax.Request(url,
                        {
                            evalScripts: true,
                            parameters: parameters,
                            onSuccess: function(transport) {
                                TINY.box.show({html:"",boxid:"tinycontentCustomer"});
                                $("tinycontentCustomer").update(transport.responseText);
                            }
                        });
                            
                    };
                  
                    require(["jquery","prototype"], function  (jQuery) {
                         jQuery("body").delegate(".radio","click",function(){
                            jQuery("#featured_customers").val(jQuery(this).parent().siblings(".col-email").text().trim());
                            jQuery("#selectedCustomer").val(jQuery(this).val().trim());
                            TINY.box.hide();
                         })
                    });
                    
                </script>'
            ]
        );

        $fieldset->addField(
            'point_amount',
            'text',
            [
                'name' => 'point_amount',
                'label' => __('Points'),
                'title' => __('Points'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'title',
            'textarea',
            [
                'name' => 'title',
                'label' => __('Transaction Title'),
                'title' => __('Transaction Title'),

            ]
        );
        $fieldset->addField(
            'expiration_day',
            'text',
            [
                'name' => 'expiration_day',
                'label' => __('Points expire after'),
                'title' => __('Points expire after'),

                'note' => __('day(s) since the transaction date. If empty or zero, there is no limitation.'),
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }


}
