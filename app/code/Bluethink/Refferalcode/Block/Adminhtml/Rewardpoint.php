<?php
namespace Bluethink\Refferalcode\Block\Adminhtml;
class Rewardpoint extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        
        $this->_controller = 'adminhtml_rewardpoint';
        $this->_blockGroup = 'Bluethink_Refferalcode';
        $this->_headerText = __('Refferalcode');
        $this->_addButtonLabel = __('Set Refferalcode'); 
        parent::_construct();
        
    }
}
