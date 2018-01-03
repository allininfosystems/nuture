<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Sales;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Directory\Model\Currency;
use Magento\Framework\DataObject;

class SgstTotal extends Template
{

    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $_dataHelper;
   

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    public function __construct(
        Context $context,
        GstHelper $dataHelper,
        Currency $currency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dataHelper = $dataHelper;
        $this->_currency = $currency;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     *
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getOrder();
        $this->getSource();

        if(!$this->getSource()->getSgstCharge() || $this->getSource()->getSgstCharge() <= 0) {
            return $this;
        }
        $total = new DataObject(
            [
                'code' => 'sgst_charge',
                'value' => $this->getSource()->getSgstCharge(),
                'label' => 'SGST',
            ]
        );
       
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');

        return $this;
    }
}
