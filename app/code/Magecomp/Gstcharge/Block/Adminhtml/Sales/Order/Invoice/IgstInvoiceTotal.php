<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Sales\Order\Invoice;

use Magento\Framework\View\Element\Template\Context;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;

class IgstInvoiceTotal extends Template
{

    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Order invoice
     *
     * @var \Magento\Sales\Model\Order\Invoice|null
     */
    protected $_invoice = null;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

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

    public function getInvoice()
    {
        return $this->getParentBlock()->getInvoice();
    }
    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getInvoice();
        $this->getSource();

        if(!$this->getSource()->getIgstCharge() || $this->getSource()->getIgstCharge() <=0) {
            return $this;
        }
        $total = new DataObject(
            [
                'code' => 'igst_charge',
                'value' => $this->getSource()->getIgstCharge(),
                'label' => 'IGST',
            ]
        );

        $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        return $this;
    }
}
