<?php
namespace Bluethink\Faq\Block\Adminhtml;
class Faq extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_faq';/*block grid.php directory*/
        $this->_blockGroup = 'Bluethink_Faq';
        $this->_headerText = __('Faq');
        $this->_addButtonLabel = __('Add FAQ'); 
        parent::_construct();
		
    }
}
