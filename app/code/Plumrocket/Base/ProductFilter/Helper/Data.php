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

namespace Plumrocket\ProductFilter\Helper;

use Plumrocket\ProductFilter\Model\Config\Source\AdditionalFilters;

class Data extends Main
{

    /**
     * Configuration path to enabled attributes
     */
    const FILTER_ENABLED_ATTRIBUTES = 'settings/attributes';

    /**
     * Configuration path to enabled attributes
     */
    const FILTER_ENABLED_CUSTOM_OPTIONS = 'settings/custom_options';

    /**
     * Configuration path to enable module
     */
    const FILTER_MODULE_ENABLED_PATH = 'general/enabled';

    const FILTER_SHOW_EMPTY_PATH = 'settings/empty_option';

    /**
     * Needed for main helper and base extension
     * @var string
     */
    protected $_configSectionId = 'prproductfilter';

    /**
     * Variable for request in custom opton
     * @var string
     */
    protected $_customOptionRequestVar = 'custom_opt';

    /**
     * Json helper
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * Excluded from filter attrbiutes
     * @var array
     */
    protected $_excludedAttributes;

    /**
     * Groups
     * @var array
     */
    protected $_groups;

    /**
     * Registry
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Inventory configuration
     * @var \Magento\CatalogInventory\Model\Configuration
     */
    protected $inventoryConfiguration;

    /**
     * ResourceConnection
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Config
     * @var \Magento\Config\Model\Config
     */
    protected $config;

    /**
     * @var string
     */
    public static $configEnablePath = 'prproductfilter/general/enabled';

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface     $objectManager
     * @param \Magento\Framework\Json\Helper\Data           $jsonHelper
     * @param \Magento\Framework\Registry                   $registry
     * @param \Magento\Framework\App\Helper\Context         $context
     * @param \Magento\CatalogInventory\Model\Configuration $inventoryConfiguration
     * @param \Magento\Framework\App\ResourceConnection     $resourceConnection
     * @param \Magento\Config\Model\Config                  $config
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\CatalogInventory\Model\Configuration $inventoryConfiguration,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Config\Model\Config $config
    ) {
        $this->jsonHelper               = $jsonHelper;
        $this->registry                 = $registry;
        $this->inventoryConfiguration   = $inventoryConfiguration;
        $this->resourceConnection       = $resourceConnection;
        $this->config                   = $config;
        parent::__construct($objectManager, $context);
    }

    /**
     * Is auto mode
     * @return boolean
     */
    public function isAutoMode()
    {
        $path = $this->getConfigSectionId() . '/' . \Plumrocket\ProductFilter\Model\Config\Source\Mode::CONFIG_PATH;
        return $this->getConfig($path) == \Plumrocket\ProductFilter\Model\Config\Source\Mode::FILTER_MODE_AUTO;
    }

    /**
     * Retrieve current placement
     * @return string
     */
    public function getPlacement()
    {
        return $this->getConfig(
            $this->_configSectionId . '/' . \Plumrocket\ProductFilter\Model\Config\Source\Placement::CONFIG_PATH
        );
    }

    /**
     * Return is use Seo Friendly Url
     * @return mixed
     */
    public function getUseSeoFriendlyUrl()
    {
        return $this->getConfig(
            $this->_configSectionId . '/' . Url::USE_SEO_URL_CONFIG_PATH
        );
    }

    /**
     * Is module enabled
     * @param  int $store store id
     * @return boolean
     */
    public function moduleEnabled($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId . '/' . self::FILTER_MODULE_ENABLED_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function disableExtension()
    {
        $connection = $this->resourceConnection->getConnection('core_write');
        $connection->delete(
            $this->resourceConnection->getTableName('core_config_data'),
            [$connection->quoteInto('path = ?', self::$configEnablePath)]
        );

        $this->config->setDataByPath($this->_configSectionId.'/general/enabled', 0);
        $this->config->save();
    }

    /**
     * Get selected attributes from configuration
     * @param  boolean $categoryFilter
     * @return array
     */
    public function getSelectedAttributes($categoryFilter = false)
    {

        $selectedAttrs = $this->_getAttributeConfig($this->getEnabledAttributePath());
        if (!$selectedAttrs) {
            return [];
        }
        $attributes = $this->jsonHelper->jsonDecode($selectedAttrs);

        if ($categoryFilter) {
            $this->_prepareExludedData();
            $attributes = $this->_addCategoryFilter($attributes, 'attribute');
        }

        return $attributes;
    }

    /**
     * Get config for attributes or cusotm options
     * @param  string $path
     * @return string
     */
    protected function _getAttributeConfig($path)
    {
        $store = null;
        $scope = null;

        if ($this->_request->getParam('section') && $this->_request->getParam('section') == $this->_configSectionId) {
            if ($store = $this->_request->getParam('website')) {
                $scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
            } elseif ($store = $this->_request->getParam('store')) {
                $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            } else {
                $store = 0;
            }
        } elseif ($category = $this->registry->registry('current_category')) {
            $store = $category->getStoreId();
        }
        return $this->getConfig($path, $store, $scope);
    }

    /**
     * Retrieve sekected attribute codes from configuratios
     * @param  boolean $categoryFilter
     * @return array
     */
    public function getSelectedAttributeCodes($categoryFilter = false)
    {
        $attributes = $this->getSelectedAttributes($categoryFilter);
        $codes = [];

        foreach ($attributes as $_code => $attribute) {
            if (is_array($attribute)) {
                foreach ($attribute as $_c => $groupAttribute) {
                    $codes[] = $_c;
                }
            } else {
                $codes[] = $_code;
            }
        }

        if ($categoryFilter) {
            $codes = $this->_addCategoryFilter($codes, 'attribute');
        }

        $codes = array_replace($codes,
            array_fill_keys(
                array_keys($codes, "categorie"),
                "category"
            )
        );

        return $codes;
    }

    /**
     * Retrieve custom options from configuration
     * @param  boolean $categoryFilter
     * @return array
     */
    public function getSelectedCustomOptions($categoryFilter = false)
    {
        $selected = $this->_getAttributeConfig($this->_configSectionId . '/' . self::FILTER_ENABLED_CUSTOM_OPTIONS);
        if (!$selected) {
            return [];
        }

        $options = $this->jsonHelper->jsonDecode($selected);

        if ($categoryFilter) {
            $this->_prepareExludedData();
            $options = $this->_addCategoryFilter($options, $this->_customOptionRequestVar);
        }

        return array_keys($options);
    }

    /**
     * Retrieve groups from backend
     * @return array
     */
    protected function _getSelectedGroups()
    {
        if ($this->_groups === null) {
            $attributes = $this->getSelectedAttributes();
            $this->_groups = [];
            foreach ($attributes as $code => $attribute) {
                if (is_array($attribute)) {
                    $this->_groups[$code] = [];
                    foreach ($attribute as $attrCode => $attr) {
                        $this->_groups[$code][] = $attrCode;
                    }
                }
            }
        }

        return $this->_groups;
    }

    /**
     * Is item in Group
     * If it's true than return name of group
     * @param  string  $code
     * @return boolean
     */
    public function getGroupName($code)
    {
        $groups = $this->_getSelectedGroups();
        foreach ($groups as $groupName => $group) {
            if (in_array($code, $group)) {
                return $groupName;
            }
        }

        return false;
    }

    /**
     * Braek filters by group
     * @param  array $filters
     * @return array
     */
    public function breakByGroup($filters)
    {
        $_filters = [];
        foreach ($filters as $filter)
        {
            if ($groupName = $this->getGroupName($filter->getPfAttributeCode())) {
                if (!$filter->getItemsCount()) {
                    continue;
                }
                $_filters[$groupName][$filter->getPfAttributeCode()] = $filter;
            } else {
                $_filters[$filter->getPfAttributeCode()] = $filter;
            }
        }

        return $_filters;
    }

    /**
     * Retrieve exluded from filter attributes
     * @param  \Magento\Catalog\Model\Category $category
     * @return array
     */
    protected function _prepareExludedData($category = null)
    {
        if ($this->_excludedAttributes === null) {
            if ($category === null) {
                $category = $this->registry->registry('current_category');
            }

            if (!$category) {
                return $this;
            }

            if ($category->getId()) {
                $excludedAttributes = $category->getPrExludedAttributes();
                $excludedAttributes = explode(',', $excludedAttributes);
                $this->_excludedAttributes = [];
                foreach ($excludedAttributes as $key => $attribute) {
                    if (strpos($attribute, $this->getCustomOptionRequestVar()) !== false) {
                        $this->_excludedAttributes[$this->_customOptionRequestVar][] = str_replace($this->getCustomOptionRequestVar(), '', $attribute);
                    } else {
                        $this->_excludedAttributes['attribute'][] = $attribute;
                    }
                }
            }
        }

        return $this->_excludedAttributes;
    }

    /**
     * Retrieve excluded attributes from category
     * @param string $type
     * @return array
     */
    protected function _getExludedAttributes($type = 'attribute')
    {
        if ($this->_excludedAttributes === null) {
            $this->_prepareExludedData();
        }

        if (!isset($this->_excludedAttributes[$type])) {
            return [];
        }

        return $this->_excludedAttributes[$type];
    }

    /**
     * Add category filter to selected attributes
     * Unset excluded from category filters
     * @param array     $attributes
     * @param string    $attributeType
     *
     * @return mixed
     */
    protected function _addCategoryFilter($attributes, $attributeType = 'attribute')
    {
        $excludedAttributes = $this->_getExludedAttributes($attributeType);
        foreach ($attributes as $key => $attribute) {
            if (in_array($attribute, $excludedAttributes)) {
                unset($attributes[$key]);
            }
        }

        return $attributes;
    }

    /**
     * Get enabled attribute path
     * @return string
     */
    public function getEnabledAttributePath()
    {
        return $this->_configSectionId . '/' . self::FILTER_ENABLED_ATTRIBUTES;
    }

    /**
     * Is stock status enabled
     * @return boolean
     */
    public function isStockFilterEnabled()
    {
        $active = $this->getSelectedAttributeCodes();
        $showOutOfStockProducts = $this->inventoryConfiguration->isShowOutOfStock();
        return array_search(AdditionalFilters::FILTER_STOCK_STATUS, $active) !== false && $showOutOfStockProducts;
    }

    /**
     * Is rating filter enabled
     * @return boolean
     */
    public function isRatingFilterEnabled()
    {
        $active = $this->getSelectedAttributeCodes();
        return array_search(AdditionalFilters::FILTER_RATING, $active) !== false;
    }

    /**
     * Checking is category filter enabled
     * @return boolean
     */
    public function isCategoryFilterEnabled()
    {
        return array_search(\Magento\Catalog\Model\Layer\FilterList::CATEGORY_FILTER, $this->getSelectedAttributeCodes()) !== false;
    }

    /**
     * Convert converted string to original
     * @param  string $title
     * @return string
     */
    public function convertToOrigin($title)
    {
        return str_replace('_', ' ', $title);
    }

    /**
     * Sufix to custom options
     * It for detecting where attribute and where custom options
     * @return string
     */
    public function getCustomOptionRequestVar()
    {
        return $this->_customOptionRequestVar . ':';
    }

    /**
     * Convert sting to slugify
     * @param  string $title
     * @return string
     */
    public function convertCustomOptionTitle($title = null)
    {
        if ($title === null) {
            return $this->_customOptionRequestVar;
        }

        return str_replace(' ', '_', $title);
    }

    /**
     * Convert string to url
     * @param  string $value
     * @return string
     */
    public function getConvertedAttributeValue($value)
    {
        if (is_array($value)) {
            $value = implode('-', $value);
        }
        $val =  str_replace(' ', '_', strtolower(strip_tags($value)));
        $val = str_replace('-', '_', $val);
        return $val;
    }

    /**
     * Add filter to meta tags
     * @return boolean
     */
    public function addFilterToMeta()
    {
        return true;
    }

    /**
     * Retrieve meta filter separator
     * @return string
     */
    public function getMetaFilterSeparator()
    {
        return ' | ';
    }
}
