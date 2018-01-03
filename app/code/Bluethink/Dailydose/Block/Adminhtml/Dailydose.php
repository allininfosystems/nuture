<?php
namespace Bluethink\Dailydose\Block\Adminhtml;
class Dailydose extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        
        $this->_controller = 'adminhtml_dailydose';
        $this->_blockGroup = 'Bluethink_Dailydose';
        $this->_headerText = __('Dailydose');
        $this->_addButtonLabel = __('Dailydose Product Type '); 
        parent::_construct();
        
    }
}
