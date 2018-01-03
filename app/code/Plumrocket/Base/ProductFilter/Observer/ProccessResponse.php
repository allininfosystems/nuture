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

namespace Plumrocket\ProductFilter\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProccessResponse implements ObserverInterface
{

    const CATEGORY_VIEW_ACTION_NAME = 'catalog_category_view';

    const CATALOG_SEARCH_ACTION_NAME = 'catalogsearch_result_index';

    const AJAX_REQUEST_KEY = 'prfilter_ajax';

    const PRODUCT_FILTER_REMOVE_HANDLE = 'product_filter_ajax_request';

    /**
     * Request
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    protected $_dataHelper;

    /**
     * Layout
     * @var \Magento\Framework\View\Layout
     */
    protected $_layout;

    /**
     * Variables from view.xml
     * @var array
     */
    protected $_vars;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\Layout $layout,
        \Magento\Framework\Config\View $configView,
        \Plumrocket\ProductFilter\Helper\Data $dataHelper
    ) {
        $this->_layout = $layout;
        $this->_vars = $configView->getVars('Plumrocket_ProductFilter');
        $this->_request = $request;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * {@inheritdoc}
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if ($this->_canProcessResponse()) {

            $productListHtml = $this->_getProductListHtml();
            $leftnavHtml = $this->_getLeftNavHtml();

            $response = $observer->getEvent()->getData('response');
            $response->representJson(
                json_encode(
                    [
                        'productlist' => $productListHtml,
                        'leftnav' => $leftnavHtml,
                        'realParams' => $this->_request->getParams()
                    ]
                )
            );
        }
    }

    /**
     * Can process response
     * @return boolean
     */
    protected function _canProcessResponse()
    {
        return
            (
                $this->_request->getFullActionName() == self::CATEGORY_VIEW_ACTION_NAME
                || $this->_request->getFullActionName() == self::CATALOG_SEARCH_ACTION_NAME
            )
            && $this->_request->getParam(self::AJAX_REQUEST_KEY)
            && $this->_dataHelper->moduleEnabled();
    }

    /**
     * Retrieve left navigation html
     * @return string
     */
    protected function _getLeftNavHtml()
    {
        if ($this->_request->getFullActionName() == self::CATEGORY_VIEW_ACTION_NAME) {
            return $this->_layout->getBlock($this->_vars['catalog_left_navigation_block'])->toHtml();
        } elseif ($this->_request->getFullActionName() == self::CATALOG_SEARCH_ACTION_NAME) {
            return $this->_layout->getBlock($this->_vars['catalogsearch_left_navigation_block'])->toHtml();
        }

        return '';
    }

    /**
     * Retrieve product list html
     * @return string
     */
    protected function _getProductListHtml()
    {
        if ($this->_request->getFullActionName() == self::CATEGORY_VIEW_ACTION_NAME) {
            return $this->_layout->getBlock($this->_vars['catalog_product_list_block'])->toHtml();
        } elseif ($this->_request->getFullActionName() == self::CATALOG_SEARCH_ACTION_NAME) {
            return $this->_layout->getBlock($this->_vars['catalogsearch_product_list_block'])->toHtml();
        }

        return '';
    }
}
