<?php
namespace Magenest\ZohocrmIntegration\Block\System\Config\Form\Button;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field as ConfigFormField;

/**
 * Class Button
 * @package Magenest\ZohocrmIntegration\Block\System\Config\Form\Button
 */
class Button extends ConfigFormField
{

    /**
     * Sync Button Label
     *
     * @var string
     */
    protected $_buttonLabel = 'Sync Now';

    /**
     * SyncButton constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Set template
     *
     * @return \Magenest\ZohocrmIntegration\Block\System\Config\Form\Button\Button
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/button/button.phtml');
        }
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel = !empty($originalData['button_label']) ? $originalData['button_label'] : $this->_buttonLabel;
        $router = !empty($originalData['button_url']) ? $originalData['button_url'] : '*/dashboard/index';
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'html_id' => $element->getHtmlId(),
                'button_url' => $this->getUrl($router)
            ]
        );
        return $this->_toHtml();
    }
}
