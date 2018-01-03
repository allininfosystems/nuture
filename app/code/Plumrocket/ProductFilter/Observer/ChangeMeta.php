<?php

namespace Plumrocket\ProductFilter\Observer;

use Magento\Framework\Event\ObserverInterface;

class ChangeMeta implements ObserverInterface
{
    /**
     * Data helper
     * @var \Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Page confi
     * @var Magento\Framework\View\Page\Config
     */
    protected $_pageConfig;

    /**
     * Catalog layer
     * @var Magento\Catalog\Model\Layer\Resolver
     */
    protected $_catalogLayer;

    /**
     * Url helper
     * @var \Plumrocket\ProductFilter\Helper\Url
     */
    protected $_urlHelper;

    /**
     * Constructor
     * @param \Plumrocket\ProductFilter\Helper\Data $dataHelper
     * @param \Magento\Framework\View\Page\Config   $pageConfig
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     */
    public function __construct(
        \Plumrocket\ProductFilter\Helper\Data $dataHelper,
        \Plumrocket\ProductFilter\Helper\Url $urlHelper,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_urlHelper = $urlHelper;
        $this->_catalogLayer = $layerResolver->get();
        $this->_pageConfig = $pageConfig;
    }

    /**
     * Changing attribute values
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $actionNames = [ProccessResponse::CATEGORY_VIEW_ACTION_NAME, ProccessResponse::CATALOG_SEARCH_ACTION_NAME];
        if ($this->_dataHelper->moduleEnabled()
            && in_array($observer->getFullActionName(), $actionNames)
            && $this->_dataHelper->addFilterToMeta()
        ) {

            $filters = $this->_catalogLayer->getState()->getFilters();
            $additionalMeta = [];
            foreach ($filters as $filter) {
                if (!is_numeric($filter->getLabel())) {
                    $additionalMeta[] = strip_tags($filter->getLabel());
                }
            }

            if (count($additionalMeta)) {
                $oldTitle = $this->_pageConfig->getTitle()->get();
                $oldDescription = $this->_pageConfig->getDescription();

                $this->_pageConfig->setRobots('NOINDEX,FOLLOW');

                $separator = $this->_dataHelper->getMetaFilterSeparator();
                $combined = implode($separator, $additionalMeta);
                $this->_pageConfig->getTitle()->set($oldTitle . $separator . $combined);
                $this->_pageConfig->setDescription($oldDescription . ' - ' . $combined);

                $oldKeywords = $this->_pageConfig->getKeywords();
                $this->_pageConfig->setKeywords($oldKeywords . ', ' . implode(', ', $additionalMeta));

                if ($this->_pageConfig->getAssetCollection()->getGroupByContentType('canonical')) {
                    //Remove current canonical url and add new
                    $canonicals = $this->_pageConfig->getAssetCollection()->getGroupByContentType('canonical')->getAll();
                    $canonical = array_shift($canonicals);
                    $this->_pageConfig->getAssetCollection()->remove($canonical->getUrl());

                    $this->_pageConfig->addRemotePageAsset(
                        $this->_urlHelper->getCanonicalUrl(),
                        'canonical',
                        ['attributes' => ['rel' => 'canonical']]
                    );
                }
            }
        }
    }
}
