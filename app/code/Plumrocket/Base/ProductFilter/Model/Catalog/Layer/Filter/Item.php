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

namespace Plumrocket\ProductFilter\Model\Catalog\Layer\Filter;

class Item extends \Magento\Catalog\Model\Layer\Filter\Item
{

    /**
     * Request
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Url helper
     * @var \Plumrocket\ProductFilter\Helper\Data
     */
    protected $_urlHelper;

    protected $_dataHelper;

    /**
     * Constructore
     * @param \Magento\Framework\UrlInterface         $url
     * @param \Magento\Theme\Block\Html\Pager         $htmlPagerBlock
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Plumrocket\ProductFilter\Helper\Url    $urlHelper
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        \Plumrocket\ProductFilter\Helper\Url $urlHelper,
        \Plumrocket\ProductFilter\Helper\Data $dataHelper,
        \Magento\Framework\App\RequestInterface $request,
        array $data = []
    ) {
        $this->_request = $request;
        $this->_urlHelper = $urlHelper;
        $this->_dataHelper = $dataHelper;
        parent::__construct($url, $htmlPagerBlock, $data);

    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {

        if (!$this->_dataHelper->moduleEnabled()) {
            return parent::getUrl();
        }

        $requestVar = $this->getFilter()->getRequestVar();

        if ($this->getIsActive()) {
            return $this->getRemoveUrl();
        }

        if ($this->_urlHelper->useSeoFriendlyUrl()) {

            $removeCurrent = false;
            if ($this->getFilter()->getIsRadio()) {
                $removeCurrent = true;
            }

            return $this->_urlHelper->getUrlForItem(
                $requestVar,
                $this->getRewritedValue(),
                $removeCurrent
            );
        } else {
            $query = [
                $requestVar => $this->_getValue(),
                // exclude current page from urls
                $this->_htmlPagerBlock->getPageVarName() => null,
            ];

            return $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoveUrl()
    {
        if (!$this->_dataHelper->moduleEnabled()) {
            return parent::getUrl();
        }

        if ($this->_urlHelper->useSeoFriendlyUrl()) {
            $url = $this->_urlHelper->getResetUrl(
                $this->getFilter()->getRequestVar(),
                $this->getRewritedValue()
            );

            return preg_replace("/[%,]+([[:alnum:]_]*)/", '', $url);
        } else {
            $query = [$this->getFilter()->getRequestVar() => $this->_getResetValue()];

            $params['_current'] = true;
            $params['_use_rewrite'] = true;
            $params['_query'] = $query;
            $params['_escape'] = true;
            return $this->_url->getUrl('*/*/*', $params);
        }
    }

    protected function getRewritedValue()
    {
        $value = $this->getLabel();

        if ($this->getFilter() instanceof \Magento\CatalogSearch\Model\Layer\Filter\Price
            || $this->getFilter() instanceof \Magento\Catalog\Model\Layer\Filter\Price
            || $this->getFilter() instanceof \Magento\Catalog\Model\Layer\Filter\Category
            || $this->getFilter() instanceof \Magento\CatalogSearch\Model\Layer\Filter\Category
        ) {
            $value = $this->getValue();
            if (is_array($value)) {
                $value = implode('_', $value);
            }
        }

        return $this->_dataHelper->getConvertedAttributeValue($value);
    }

    /**
     * Get reset value for remove url
     * @return string
     */
    protected function _getResetValue()
    {
        $value = $this->getValue();
        $currentValue = $this->_request->getParam($this->getFilter()->getRequestVar());
        if ($currentValue == $value) {
            return null;
        }

        $_value = explode(',', $currentValue);
        unset($_value[ array_search($value, $_value) ]);

        if (!count($_value)) {
            return null;
        }

        return implode(',', $_value);
    }

    /**
     * Get value for url
     * @return string
     */
    protected function _getValue()
    {
        $value = $this->getValue();

        if ($this->getFilter()->getIsRadio()) {
            return $value;
        }

        $currentValue = $this->_request->getParam($this->getFilter()->getRequestVar());
        if ($currentValue) {
            $options = explode(',', $currentValue);

            if (is_array($value)) {
                $options = array_merge($options, $value);
            } else {
                $options[] = $value;
            }
            sort($options);
            $value = implode(',', $options);
        }

        return $value;
    }
}
