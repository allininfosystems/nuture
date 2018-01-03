<?php
namespace Webinse\Barcode\Block\Adminhtml\Generator;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Request\Http;

/**
 * Class PrintBarcode
 */
class PrintBarcode extends Edit\GenericButton implements ButtonProviderInterface
{
    /** @var Http */
    protected $_request;

    /**
     * PrintBarcode constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Http $request
     */
    public function __construct(Context $context, Registry $registry, Http $request)
    {
        $this->_request = $request;
        parent::__construct($context, $registry);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Print Barcode'),
            'on_click' => sprintf("var q = prompt('How many barcode labels do you want to print?'); var str = '%s'; if (q !== null) {location.href = str.replace('quantity', q);}", $this->getPrintUrl()),
            'class' => 'primary',
            'sort_order' => 20
        ];
    }

    /**
     * Get URL for print button
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('*/*/printBarcode' . $this->getId(), ['q' => 'quantity']);
    }

    /**
     * Get id from page URL
     *
     * @return string
     */
    public function getId()
    {
        return '/id/' . $this->_request->getParams()['id'];
    }
}