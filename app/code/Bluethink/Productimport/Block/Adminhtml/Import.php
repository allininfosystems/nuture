<?php
namespace Bluethink\Productimport\Block\Adminhtml;
class Import extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        
        $this->_controller = 'adminhtml_import';
        $this->_blockGroup = 'Bluethink_Productimport';
        $this->_headerText = __('Import');
        $this->_addButtonLabel = __('Upload Simple Product Csv'); 
        parent::_construct();
        
    }
}
