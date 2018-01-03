<?php
namespace Webinse\Barcode\Block\Adminhtml\Import;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Webinse\Barcode\Block\Adminhtml\Generator\Edit\GenericButton;

class ImportButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Import'),
            'on_click' => sprintf("location.href = '%s';", $this->getImportUrl()),
            'class' => 'save primary',
            'sort_order' => 10
        ];
    }

    public function getImportUrl()
    {
        return $this->getUrl('webinse_barcode/import/import');
    }
}