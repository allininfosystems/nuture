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

namespace Plumrocket\ProductFilter\Block\LayeredNavigation\Navigation;

use \Plumrocket\ProductFilter\Helper\Url as UrlHelper;

class State extends \Magento\LayeredNavigation\Block\Navigation\State
{
    /**
     * Template
     * @var string
     */
    protected $_pfTemplate = 'Plumrocket_ProductFilter::layer/state.phtml';

    /**
     * \Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Url helper
     * @var Plumrocket\ProductFilter\Helper\Url
     */
    protected $_urlHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Plumrocket\ProductFilter\Helper\Data $dataHelper,
        UrlHelper $urlHelper,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_urlHelper = $urlHelper;
        parent::__construct($context, $layerResolver, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        if ($this->_dataHelper->moduleEnabled()) {
            return $this->_pfTemplate;
        }

        return 'Magento_LayeredNavigation::' . parent::getTemplate();
    }

    /**
     * {@inheritdoc}
     */
    public function getClearUrl()
    {
        $clearUrl = parent::getClearUrl();

        if ($this->_dataHelper->moduleEnabled() && $this->_urlHelper->useSeoFriendlyUrl()) {
            $additionalParam = '';
            $sufix = $this->_urlHelper->getCategoryUrlSufix();
            $varName = (stripos($clearUrl, 'catalogsearch/result') === false) ? 'clearUrl': 'additionalParam';

            $toolbarVars = $this->_urlHelper->getToolbarVars();
            foreach ($this->_request->getParams() as $param => $value) {
                if (in_array($param, $toolbarVars)) {
                    $$varName .= '/' . $param . UrlHelper::FILTER_PARAM_SEPARATOR . $value;
                }

            }
            $clearUrl = preg_replace(
                '/(catalogsearch\/result)\/.*?\/(.*?\/\?)/',
                "$1{$additionalParam}{$sufix}?",
                $clearUrl
            );
        }

        return $clearUrl;
    }
}
