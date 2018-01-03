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


namespace Plumrocket\ProductFilter\Plugin\Block\LayeredNavigation\Navigation;

/**
 * Class FilterRenderer
 */
class FilterRenderer
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * Path to Render Price Block
     *
     * @var string
     */
    protected $blockPrice = 'Plumrocket\ProductFilter\Block\LayeredNavigation\RenderPrice';

    /**
     * Path to Render Rating Block
     *
     * @var string
     */
    protected $blockRating = 'Plumrocket\ProductFilter\Block\LayeredNavigation\RenderRating';

    /**
     * Active Filters
     * @var array
     */
    protected $_activeFilters;

    /**
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Plumrocket\ProductFilter\Helper\Data $swatchHelper
     */
    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \Plumrocket\ProductFilter\Helper\Data $dataHelper
    ) {
        $this->layout = $layout;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\LayeredNavigation\Block\Navigation\FilterRenderer $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundRender(
        \Magento\LayeredNavigation\Block\Navigation\FilterRenderer $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
    ) {

        if (!$this->_dataHelper->moduleEnabled()) {
            return $proceed($filter);
        }

        $layer = $filter->getLayer();
        foreach ($filter->getItems() as $item) {
            $item->setIsActive($this->_isActive($item, $layer));
        }

        $block = null;

        if ($filter instanceof \Magento\Catalog\Model\Layer\Filter\Price
            || $filter instanceof \Magento\CatalogSearch\Model\Layer\Filter\Price
        ) {
            $block = $this->blockPrice;
        } elseif ($filter instanceof \Plumrocket\ProductFilter\Model\Layer\Filter\Rating) {
             $block = $this->blockRating;
        }

        if ($block) {
            return $this->layout
                ->createBlock($block)
                ->setFilter($filter)
                ->toHtml();
        }

        return $proceed($filter);
    }

    /**
     * Is filter active
     * @param  object $item
     * @param object  $layer
     * @return boolean
     */
    protected function _isActive($item, $layer)
    {
        if ($item->getIsActive()) {
            return true;
        }

        $value = (string) $item->getValue();
        if (in_array(strtolower($value), $this->_getActiveFilters($layer))) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve active filter
     * @param object $layer
     * @return array
     */
    protected function _getActiveFilters($layer)
    {
        if ($this->_activeFilters === null) {
            if (count($layer->getState()->getFilters())) {
                foreach ($layer->getState()->getFilters() as $filter) {
                    $value = $filter->getValue();
                    if (!is_array($value)) {
                        $value = strtolower($value);
                    }
                    $this->_activeFilters[] = $value;
                }
            } else {
                $this->_activeFilters = [];
            }
        }

        return $this->_activeFilters;
    }
}
