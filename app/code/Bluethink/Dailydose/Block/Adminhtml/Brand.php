<?php
namespace Bluethink\Dailydose\Block\Adminhtml;
class Brand extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        
        $this->_controller = 'adminhtml_brand';
        $this->_blockGroup = 'Bluethink_Dailydose';
        $this->_headerText = __('Brand');
        $this->_addButtonLabel = __('Upload Brand'); 
        parent::_construct();
        
    }
}
