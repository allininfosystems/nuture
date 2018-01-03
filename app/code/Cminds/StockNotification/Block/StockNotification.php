<?php

namespace Cminds\StockNotification\Block;

use Cminds\StockNotification\Model\Config;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class StockNotification extends Template
{
    private $registry;
    private $product;
    private $customer;
    private $config;

    /**
     * StockNotification constructor.
     *
     * @param Template\Context $context
     * @param Registry         $registry
     * @param Config           $config
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        Config $config,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->config = $config;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->config->isActive() === false) {
            return '';
        }

        $this->product = $this->registry->registry('current_product');
        if ($this->product->isSalable() === true) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Get customer e-mail
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        if (!$this->customer === false) {
            return (string)$this->customer->getEmail();
        }
    }

    /**
     * Get the form action URL
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl(
            'stocknotification/index/signup',
            ['product_id' => $this->product->getId()]
        );
    }
}
