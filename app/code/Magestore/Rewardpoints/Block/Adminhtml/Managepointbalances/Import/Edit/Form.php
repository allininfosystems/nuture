<?php

namespace Magestore\Rewardpoints\Block\Adminhtml\Managepointbalances\Import\Edit;


class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _prepareForm(){

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/processImport'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );

        // fieldset for file uploading
        $fieldsets['profile_fieldset'] = $form->addFieldset(
            'upload_file_fieldset',
            ['legend' => __('Import Form'), 'class' => 'import_form']
        );
        $fieldsets['profile_fieldset']->addField(
            'filecsv',
            'file',
            [
                'name' => 'filecsv',
                'label' => __('Select File to Import'),
                'title' => __('Select File to Import'),
                'required' => true,
                'class' => 'input-file'
            ]
        );
        $fieldsets['profile_fieldset']->addField(
            'sample',
            'note',
            [
                'name' => 'sample',
                'label' => __('Download Sample CSV File'),
                'title' => __('Download Sample CSV File'),
                'required' => true,
                'class' => 'input-file',
                'text'  => '<a href="'.
                    $this->getUrl('*/*/downloadSample').
                    '" title="'.__('Download Sample CSV File').
                    '">import_point_balance_sample.csv</a>'
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
