<?php
namespace Nuluv\Liker\Block\Adminhtml;
class Liker extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_liker';/*block grid.php directory*/
        $this->_blockGroup = 'Nuluv_Liker';
        $this->_headerText = __('Liker');
        $this->_addButtonLabel = __('Add New Entry'); 
        parent::_construct();
        $this->removeButton('add'); // Add this code to remove the button
		
    }
}
