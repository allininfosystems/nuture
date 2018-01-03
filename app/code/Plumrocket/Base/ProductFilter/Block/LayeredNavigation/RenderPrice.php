<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Product Filter v3.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ProductFilter\Block\LayeredNavigation;

class RenderPrice extends \Magento\Framework\View\Element\Template
{

    const FILTER_PRICE_SLIDER_TEMPLATE = 'Plumrocket_ProductFilter::layer/renderer/price/slider.phtml';
    const FILTER_PRICE_INPUT_TEMPLATE = 'Plumrocket_ProductFilter::layer/renderer/price/input.phtml';
    const FILTER_PRICE_RANGE_TEMPLATE = 'Plumrocket_ProductFilter::layer/filter.phtml';

    /**
     * Data helper
     * @var \Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Json Helper
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * Amp Helper
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $ampHelper;

    /**
     * Constructor
     * @param \Plumrocket\ProductFilter\Helper\Data            $dataHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Plumrocket\ProductFilter\Helper\Data $dataHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get Items
     * @return array
     */
    public function getItems()
    {
        $item = $this->getFilter()->getItems();
        return $this->getFilter()->getItems();
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $types = $this->_getPriceDisplayTypes();

        $this->assign('filterItems', $this->getItems());

        if ($this->ampHelper === null) {
            if ($this->_dataHelper->moduleExists('Amp')) {
                $this->ampHelper = $this->_dataHelper->getModuleHelper('Amp');
            } else {
                $this->ampHelper = false;
            }
        }

        // render only RANGE_TEMPLATE in AMP
        if ($this->ampHelper && $this->ampHelper->isAmpRequest()) {
            $this->setTemplate(self::FILTER_PRICE_RANGE_TEMPLATE);
            $this->assign('filterItems', []);
            return parent::_toHtml();
        }

        $html = '';
        if (in_array(\Plumrocket\ProductFilter\Model\Config\Source\PriceDisplay::PRICE_DISPLAY_RANGE, $types)) {
            $this->setTemplate(self::FILTER_PRICE_RANGE_TEMPLATE);
            $html .= parent::_toHtml();
        }

        if (in_array(\Plumrocket\ProductFilter\Model\Config\Source\PriceDisplay::PRICE_DISPLAY_SLIDER, $types)) {
            $this->setTemplate(self::FILTER_PRICE_SLIDER_TEMPLATE);
            $html .= parent::_toHtml();
        }

        if (in_array(\Plumrocket\ProductFilter\Model\Config\Source\PriceDisplay::PRICE_DISPLAY_INPUT, $types)) {
            $this->setTemplate(self::FILTER_PRICE_INPUT_TEMPLATE);
            $html .= parent::_toHtml();
        }

        $this->assign('filterItems', []);

        return $html;
    }

    /**
     * Retrieve from value
     * @return string
     */
    public function getFromValue()
    {
        $fromRequest = $this->_getRequestedPrice();
        if (isset($fromRequest['min'])) {
            return $fromRequest['min'];
        }

        return $this->getMinValue();
    }

    /**
     * Retrieve "to'" value
     * @return string
     */
    public function getToValue()
    {
        $fromRequest = $this->_getRequestedPrice();
        if (isset($fromRequest['max'])) {
            return $fromRequest['max'];
        }

        return $this->getMaxValue();
    }

    /**
     * Get min value
     * @return string
     */
    public function getMinValue()
    {
        return $this->getFilter()->getLayer()
                ->getProductCollection()
                ->getMinPrice();
    }

    /**
     * Get max value
     * @return string
     */
    public function getMaxValue()
    {
        return $this->getFilter()->getLayer()
                ->getProductCollection()
                ->getMaxPrice();
    }

    /**
     * Retrieve prices from request
     * @return string
     */
    public function getRequestedPrice()
    {
        $result = $this->_getRequestedPrice();
        return $this->_jsonHelper->jsonEncode($result);
    }

    /**
     * Retrieve requested price
     * @return array
     */
    private function _getRequestedPrice()
    {
        $result = [];
        if ($this->_request->getParam('price')) {
            $prices = explode('-', $this->_request->getParam('price'));

            if (!empty($prices[0])) {
                $result['min'] = $prices[0];
            }

            if (!empty($prices[1])) {
                $result['max'] = $prices[1];
            }
        }

        return $result;
    }

    /**
     * Retrieve price display types from configs
     * @return array
     */
    protected function _getPriceDisplayTypes()
    {
        $path = $this->_dataHelper->getConfigSectionId() . '/' . \Plumrocket\ProductFilter\Model\Config\Source\PriceDisplay::CONFIG_PATH;
        $types = $this->_dataHelper->getConfig($path);
        return explode(',', $types);
    }

    /**
     * Retrieve currency symbol
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
    }
}
