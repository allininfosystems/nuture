<?php
namespace Bluethink\Faq\Block\Adminhtml;
class Faqcategory extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_faqcategory';/*block grid.php directory*/
        $this->_blockGroup = 'Bluethink_Faq';
        $this->_headerText = __('Faqcategory');
        $this->_addButtonLabel = __('Add Category'); 
        parent::_construct();
		
    }
}
