<?php

namespace Magestore\Rewardpoints\Block\Adminhtml\Managepointbalances\Import;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

//        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
        $this->buttonList->update('save', 'label', __('Import'));

        $this->_objectId = 'import_id';
        $this->_blockGroup = 'Magestore_Rewardpoints';
        $this->_controller = 'adminhtml_managepointbalances_import';

    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Import');
    }
}
