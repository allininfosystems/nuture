<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Sales\Order\Creditmemo;

use Magento\Framework\View\Element\Template\Context;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;

class ShippingIgstCreditTotal extends Template
{
    /**
     * Order invoice
     *
     * @var \Magento\Sales\Model\Order\Creditmemo|null
     */
    protected $_creditmemo = null;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $_dataHelper;

    /**
     * OrderFee constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        GstHelper $dataHelper,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function getCreditmemo()
    {
        return $this->getParentBlock()->getCreditmemo();
    }
    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getCreditmemo();
        $this->getSource();

        if(!$this->getCreditmemo()->getShippingIgstCharge()  || $this->getCreditmemo()->getShippingIgstCharge() <=0) {
            return $this;
        }
        $fee = new DataObject(
            [
                'code' => 'shipping_igst_charge',
                'strong' => false,
                'value' => $this->getCreditmemo()->getShippingIgstCharge(),
                'label' => 'Shipping IGST',
            ]
        );

        $this->getParentBlock()->addTotalBefore($fee, 'grand_total');

        return $this;
    }
}
