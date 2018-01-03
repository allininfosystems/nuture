<?php
/** 
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */

namespace Manadev\LayeredNavigation\Blocks;

use Magento\Framework\View\Element\Template;
use Manadev\LayeredNavigation\Configuration;
use Manadev\LayeredNavigation\Engine;
use Manadev\LayeredNavigation\EngineFilter;
use Manadev\LayeredNavigation\UrlGenerator;

class Navigation extends Template {
    /**
     * @var Engine
     */
    protected $engine;
    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;
    /**
     * @var Configuration
     */
    protected $config;

    protected $_scripts = [];

    public function __construct(Template\Context $context, Engine $engine, UrlGenerator $urlGenerator,
        Configuration $config,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->engine = $engine;
        $this->urlGenerator = $urlGenerator;
        $this->config = $config;
    }

    public function renderScripts() {
        return json_encode($this->_scripts, JSON_PRETTY_PRINT);
    }

    public function getApplyHtml() {
        /* @var \Manadev\LayeredNavigationAjax\Blocks\Apply $block */
        if (!($block = $this->getLayout()->getBlock('mana.layered-nav.apply'))) {
            return '';
        }

        return $block->getHtml();
    }

    protected function _prepareLayout() {
        $this->engine->prepareFiltersToShowIn($this->getData('position'));

        return $this;
    }

    public function addScript($scriptName, $config = array(), $target = '*') {
        if(!isset($this->_scripts[$target])) {
            $this->_scripts[$target] = [];
        }

        $this->_scripts[$target][$scriptName] = $config;

        return $this;
    }

    public function getScripts() {
        return $this->_scripts;
    }

    public function setCategoryId($category_id) {
        $this->engine->setCurrentCategory($category_id);
        $this->engine->prepareFiltersToShowIn($this->getData('position'));
    }

    public function isVisible() {
        $this->engine->getProductCollection()->loadFacets();
        foreach ($this->engine->getFiltersToShowIn($this->getData('position')) as $engineFilter) {
            if ($engineFilter->isVisible()) {
                return true;
            }
        }

        return false;
    }

    public function hasState() {
        foreach ($this->engine->getFilters() as $engineFilter) {
            if ($engineFilter->isApplied()) {
                return true;
            }
        }

        return false;
    }

    public function getClearUrl() {
        return $this->escapeUrl($this->urlGenerator->getClearAllUrl());
    }

    public function getClearLinkAttributes() {
        return $this->urlGenerator->renderLastAction();
    }

    public function getRemoveFilterUrl(EngineFilter $engineFilter) {
        /** @var FilterRenderer $filterRenderer */
        $filterRenderer = $this->getChildBlock('filter_renderer');

        return $filterRenderer->getRemoveItemUrl($engineFilter);
    }


    public function getRemoveFilterLinkAttributes($filter) {
        return $this->urlGenerator->renderLastAction();
    }

    /**
     * @return EngineFilter[]
     */
    public function getFilters() {
        foreach ($this->engine->getFiltersToShowIn($this->getData('position')) as $engineFilter) {
            if ($engineFilter->isVisible()) {
                yield $engineFilter;
            }
        }
    }

    /**
     * @return EngineFilter[]
     */
    public function getAppliedFilters() {
        foreach ($this->engine->getFilters() as $engineFilter) {
            if ($engineFilter->isApplied()) {
                yield $engineFilter;
            }
        }
    }

    public function renderFilter(EngineFilter $engineFilter) {
        /* @var $filterRenderer FilterRenderer */
        $filterRenderer = $this->getChildBlock('filter_renderer');

        return $filterRenderer->render($engineFilter);
    }

    /**
     * @return int
     */
    public function getAppliedOptionCount() {
        $count = 0;
        foreach ($this->getAppliedFilters() as $engineFilter) {
            foreach ($engineFilter->getAppliedItems() as $item) {
                $count++;
            }
        }

        return $count;
    }

    public function renderAppliedItem(EngineFilter $engineFilter, $item) {
        /* @var $appliedItemRenderer AppliedItemRenderer */
        $appliedItemRenderer = $this->getChildBlock('applied_item_renderer');

        return $appliedItemRenderer->render($engineFilter, $item);
    }

    /**
     * @return bool
     */
    public function isAppliedFilterVisible() {
        return $this->config->isAppliedFilterVisible($this->getData('position'));
    }
}