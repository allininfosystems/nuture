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

namespace Plumrocket\ProductFilter\Block;

use \Plumrocket\ProductFilter\Helper\Url as UrlHelper;

class Init extends \Magento\Framework\View\Element\Template
{
    /**
     * Data helper
     * @var \Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Constructor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\ProductFilter\Helper\Data            $dataHelper
     * @param array                                            $data
     */
    protected $_urlHelper;

    /**
     * Json helper
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * Layer
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $_layer;

    /**
     * Consturctor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param UrlHelper                                        $urlHelper
     * @param \Magento\Framework\Json\Helper\Data              $jsonHelper
     * @param \Plumrocket\ProductFilter\Helper\Data            $dataHelper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        UrlHelper $urlHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Plumrocket\ProductFilter\Helper\Data $dataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        array $data = []
    ) {
        $this->_urlHelper = $urlHelper;
        $this->_jsonHelper = $jsonHelper;
        $this->_dataHelper = $dataHelper;
        $this->_layer = $layerResolver->get();
        parent::__construct($context, $data);
    }

    /**
     * Check is currently auto mode
     * @return boolean
     */
    public function isAutoMode()
    {
        return $this->_dataHelper->isAutoMode();
    }

    /**
     * Does seo friendly url used
     * @return boolean
     */
    public function useSeoFriendlyUrl()
    {
        return $this->_urlHelper->useSeoFriendlyUrl();
    }

    /**
     * Retrieve filter parameter separator
     * @return string
     */
    public function getFilterParamSeparator()
    {
        if ($this->_urlHelper->useSeoFriendlyUrl()) {
            return UrlHelper::FILTER_PARAM_SEPARATOR;
        }

        return '=';
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->_dataHelper->moduleEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * Retrieve real request parameters
     * @return string
     */
    public function getRealParams()
    {
        return $this->_jsonHelper->jsonEncode($this->_request->getParams());
    }

    /**
     * Retrieve current selected parameters
     * @return string
     */
    public function getSelectedParams()
    {
        $selected = [];
        foreach ($this->_layer->getState()->getFilters() as $filter) {
           $selected[$filter->getFIlter()->getRequestVar()][] = $this->_dataHelper->getConvertedAttributeValue($filter->getLabel());
        }

        return count($selected) ? $this->_jsonHelper->jsonEncode($selected) : '{}';
    }

    /**
     * Retrieve category url sufix
     * @return string
     */
    public function getCategoryUrlSufix()
    {
        return $this->_urlHelper->getCategoryUrlSufix();
    }

    /**
     * Retrieve formatted current url
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        $url = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        if (!$this->useSeoFriendlyUrl()) {
            return $url;
        } else {
            return str_replace('catalogsearch/result/index', 'catalogsearch/result', $url);
        }
    }
}
