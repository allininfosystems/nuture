<?php
namespace Webinse\Barcode\Block\Adminhtml\Import;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Webinse\Barcode\Block\Adminhtml\Generator\Edit\GenericButton;

/**
 * Class BackButton
 */
class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('webinse_barcode/generator/');
    }
}