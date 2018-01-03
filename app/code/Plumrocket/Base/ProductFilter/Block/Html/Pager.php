<?php

namespace Plumrocket\ProductFilter\Block\Html;

class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * Url helper
     * @var \Plumrocket\ProductFilter\Helper\Url
     */
    protected $_urlHelper;

    /**
     * Data helper
     * @var \Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Constructor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\ProductFilter\Helper\Data            $dataHelper
     * @param \Plumrocket\ProductFilter\Helper\Url             $urlHelper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\ProductFilter\Helper\Data $dataHelper,
        \Plumrocket\ProductFilter\Helper\Url $urlHelper,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageUrl($page)
    {
        if ($this->_dataHelper->moduleEnabled() && $this->_urlHelper->useSeoFriendlyUrl()) {
            return $this->_urlHelper->getUrlForItem(
                \Magento\Catalog\Model\Product\ProductList\Toolbar::PAGE_PARM_NAME,
                $page,
                true
            );
        }

        return parent::getPageUrl($page);
    }
}
