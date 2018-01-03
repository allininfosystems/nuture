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
namespace Magestore\Rewardpoints\Block\Adminhtml\Transaction\Widget;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;

class ChooserCustomer extends \Magento\Backend\Block\Widget\Grid\Extended {

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_cpCollection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_cpCollectionInstance;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    protected $_eavAttSetCollection;

    protected $_customerGroupCollection;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $eavAttSetCollection
     * @param  \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $cpCollection,
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $eavAttSetCollection,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $cpCollection,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollection,
        array $data = []
    ) {
        $this->_customerGroupCollection = $customerGroupCollection;
        $this->_cpCollection = $cpCollection;
        $this->_eavAttSetCollection = $eavAttSetCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct() {
        parent::_construct();

        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } else {
            $this->setId('skuChooserGrid_' . $this->getId());
        }

        $this->setDefaultSort('name');
        $this->setUseAjax(true);

    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $js = "
            function (grid, event) {
                var trElement = Event.findElement(event, 'tr');
                var isInput = Event.element(event).tagName == 'INPUT';
                var input = $('featured_products');
                if (trElement) {
                    var checkbox = Element.select(trElement, 'input');
                    if (checkbox[0]) {
                        var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                        if(checked){
                            if(input.value == '')
                                input.value = checkbox[0].value;
                            else
                                input.value = input.value + ', '+checkbox[0].value;

                        }else{
                            var vl = checkbox[0].value;
                            if(input.value.search(vl) == 0){
                                if(input.value == vl) input.value = '';
                                input.value = input.value.replace(vl+', ','');
                            }else{
                                input.value = input.value.replace(', '+ vl,'');
                            }
                        }
                        checkbox[0].checked =  checked;
                        grid.reloadParams['selected[]'] = input.value.split( ', ');
                    }
                }
            }
        ";
        return $js;
    }
    public function getCheckboxCheckCallback(){
        $js = ' function (grid, element, checked) {
        var input = $("featured_products");
        if (checked) {
            $$("#'.$this->getId().' input[type=checkbox][class=checkbox admin__control-checkbox]").each(function(e){
                if(e.name != "check_all"){
                    if(!e.checked){
                        if(input.value == "")
                            input.value = e.value;
                        else
                            input.value = input.value + ", "+e.value;
                        e.checked = true;
                        grid.reloadParams["selected[]"] = input.value.split(", ");
                    }
                }
            });
        }else{
            $$("#'.$this->getId().' input[type=checkbox][class=checkbox admin__control-checkbox]").each(function(e){
                if(e.name != "check_all"){
                    if(e.checked){
                        var vl = e.value;
                        if(input.value.search(vl) == 0){
                            if(input.value == vl) input.value = "";
                            input.value = input.value.replace(vl+", ","");
                        }else{
                            input.value = input.value.replace(", "+ vl,"");
                        }
                        e.checked = false;
                        grid.reloadParams["selected[]"] = input.value.split(", ");
                    }
                }
            });

        }
    } ';
        return $js;
    }
    public function getRowInitCallback(){
        $js =' function (grid, row) {
                if($$(".input-text.admin__control-text.no-changes")[6].value!=""||
                        $$(".input-text.admin__control-text.no-changes")[7].value!="" ||
                        $$(".input-text.admin__control-text.no-changes")[8].value!=""||
                        $$(".no-changes.admin__control-select")[7].value!="" ||
                        $$(".no-changes.admin__control-select")[8].value!=""){
                                 $$(".action-default")[11].show();
                                  $$(".no-changes.admin__control-select")[0].show();
                        }
                        else{
                            $$(".action-default")[11].hide();
                            $$(".no-changes.admin__control-select")[0].hide();
                        }
                grid.reloadParams["selected[]"] = $("featured_products").value.split(", ");
        } ';
        return $js;
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $selected = $this->_getSelectedProducts();
            if (empty($selected)) {
                $selected = '';
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare Catalog Product Collection for attribute SKU in Promo Conditions SKU chooser
     *
     * @return $this
     */
    protected function _prepareCollection() {
        $collection = $this->_getCpCollectionInstance();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Get catalog product resource collection instance
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getCpCollectionInstance() {
        if (!$this->_cpCollectionInstance) {
            $this->_cpCollectionInstance = $this->_cpCollection->create();
        }
        return $this->_cpCollectionInstance;
    }

    /**
     * Define Cooser Grid Columns and filters
     *
     * @return $this
     */
    protected function _prepareColumns() {

        $this->addColumn('in_customers', array(
            'header_css_class'  => 'a-center',
            'type'              => 'radio',
            'html_name'         => 'in_customers',
            'align'             => 'center',
            'index'             => 'entity_id',
            'filter'            => false,
            'sortable'          => false,
            'html_class'=>      'radio_customer'
        ));

        $this->addColumn(
            'entity_id',
            ['header' => __('ID'), 'sortable' => true, 'width' => '60px', 'index' => 'entity_id']
        );

        $this->addColumn(
            'firstname',
            [
                'header' => __('First Name'),
                'index' => 'firstname',
                'type' => 'text',
                'sortable' => true,
            ]
        );

        $this->addColumn(
            'lastname',
            [
                'header' => __('Last Name'),
                'index' => 'lastname',
                'type' => 'text',
                'sortable' => true,
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'index' => 'email',
                'type' => 'text',
                'sortable' => true,
            ]
        );

        $this->addColumn(
            'point_balance',
            [
                'header' => __('Point Balance'),
                'index' => 'point_balance',
                'type' => 'text',
                'sortable' => true,
                'format' => 'number',
            ]
        );
        $this->addColumn(
            'group_id',
            [
                'header' => __('Group'),
                'index' => 'group_id',
                'type' => 'options',
                'options' => $this->_customerGroupCollection->create()->toOptionHash(),
                'sortable' => true,
            ]
        );

        return parent::_prepareColumns();
    }
    /**
     * @return string
     */
    public function getGridUrl() {
        return $this->getUrl(
            'rewardpoints/widget/chooserCustomer',
            ['_current' => true, 'current_grid_id' => $this->getId(), 'collapse' => null,'selected'  => $this->getRequest()->getParam('selected')]
        );
    }

    /**
     * @return mixed
     */
    protected function _getSelectedProducts() {
        $products = $this->getRequest()->getPost('selected', []);

        return $products;
    }
}
