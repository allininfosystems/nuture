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


namespace Plumrocket\ProductFilter\Plugin\Block\Catalog\Product\ProductList;

use \Plumrocket\ProductFilter\Model\Config\Source\Placement;
use \Plumrocket\ProductFilter\Observer\ProccessResponse;

class Toolbar
{

    /**
     * Hrlprt
     * @var Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Layout
     * @var Magento\Framework\View\Layout
     */
    protected $layout;

    /**
     * Request
     * @var Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Variables from view.xml
     * @var Array
     */
    protected $_vars;

    /**
     * Contructor
     * @param \Plumrocket\ProductFilter\Helper\Data $dataHelper
     * @param \Magento\Framework\Config\View        $configView
     * @param \Magento\Framework\App\Request\Http   $request
     * @param \Magento\Framework\View\Layout        $layout
     */
    public function __construct(
        \Plumrocket\ProductFilter\Helper\Data $dataHelper,
        \Magento\Framework\Config\View $configView,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\Layout $layout
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_vars = $configView->getVars('Plumrocket_ProductFilter');
        $this->layout = $layout;
        $this->request = $request;
    }

    /**
     * DEPRECATED. NOT USED.
     * Append to html filter on catalog search page
     * @param  \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param  string                                             $result
     * @return string
     */
    public function afterToHtml(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        $result
    ) {
        if ($this->_dataHelper->moduleEnabled()
            && $this->_dataHelper->getPlacement() == Placement::PLACEMENT_TOOLBAR
            && $this->request->getFullActionName() == ProccessResponse::CATALOG_SEARCH_ACTION_NAME
        ) {
            $result .= $this->layout->getBlock($this->_vars['catalogsearch_left_navigation_block'])->toHtml();
        }
        return $result;
    }
}
