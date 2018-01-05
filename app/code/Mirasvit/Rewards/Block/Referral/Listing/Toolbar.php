<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Block\Referral\Listing;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Toolbar extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $_collection = null;
    /**
     * @var string
     */
    protected $_pageVarName = 'p';
    /**
     * @var string
     */
    protected $_orderVarName = 'order';
    /**
     * @var string
     */
    protected $_directionVarName = 'dir';
    /**
     * @var string
     */
    protected $_modeVarName = 'mode';
    /**
     * @var string
     */
    protected $_limitVarName = 'limit';
    /**
     * @var array
     */
    protected $_availableOrder = [];
    /**
     * @var array
     */
    protected $_availableMode = [];
    /**
     * @var bool
     */
    protected $_enableViewSwitcher = true;
    /**
     * @var bool
     */
    protected $_isExpanded = true;
    /**
     * @var string
     */
    protected $_orderField = null;
    /**
     * @var string
     */
    protected $_direction = 'asc';
    /**
     * @var array
     */
    protected $_availableLimit = [];
    /**
     * @var array
     */
    protected $_defaultAvailableLimit = [10 => 10,20 => 20,50 => 50];
    /**
     * @var bool
     */
    protected $_paramsMemorizeAllowed = true;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        switch ($this->getAvailableListModes()) {
            case 'grid':
                $this->_availableMode = ['grid' => __('Grid')];
                break;

            case 'list':
                $this->_availableMode = ['list' => __('List')];
                break;

            case 'grid-list':
                $this->_availableMode = ['grid' => __('Grid'), 'list' => __('List')];
                break;

            case 'list-grid':
                $this->_availableMode = ['list' => __('List'), 'grid' => __('Grid')];
                break;
        }
    }

    /**
     * Disable list state params memorizing.
     *
     * @return $this
     */
    public function disableParamsMemorizing()
    {
        $this->_paramsMemorizeAllowed = false;

        return $this;
    }

    /**
     * Memorize parameter value for session.
     *
     * @param string      $param parameter name
     * @param int|string  $value parameter value
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    protected function _memorizeParam($param, $value)
    {
        $session = $this->customerSession;
        if ($this->_paramsMemorizeAllowed && !$session->getParamsMemorizeDisabled()) {
            $session->setData($param, $value);
        }

        return $this;
    }

    /**
     * Set collection to pager.
     *
     * @param \Magento\Framework\Data\Collection $collection
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int) $this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }

        return $this;
    }

    /**
     * Return products collection instance.
     *
     * @return \Magento\Framework\Data\Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Getter for $_pageVarName.
     *
     * @return string
     */
    public function getPageVarName()
    {
        return $this->_pageVarName;
    }

    /**
     * Retrieve order field GET var name.
     *
     * @return string
     */
    public function getOrderVarName()
    {
        return $this->_orderVarName;
    }

    /**
     * Retrieve sort direction GET var name.
     *
     * @return string
     */
    public function getDirectionVarName()
    {
        return $this->_directionVarName;
    }

    /**
     * Retrieve view mode GET var name.
     *
     * @return string
     */
    public function getModeVarName()
    {
        return $this->_modeVarName;
    }

    /**
     * Getter for $_limitVarName.
     *
     * @return string
     */
    public function getLimitVarName()
    {
        return $this->_limitVarName;
    }

    /**
     * Return current page from request.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        if ($page = (int) $this->getRequest()->getParam($this->getPageVarName())) {
            return $page;
        }

        return 1;
    }

    /**
     * Get grit products sort order field.
     *
     * @return string
     */
    public function getCurrentOrder()
    {
        $order = $this->_getData('_current_grid_order');
        if ($order) {
            return $order;
        }

        $orders = $this->getAvailableOrders();
        if (!$orders) {
            return false;
        }
        $defaultOrder = $this->_orderField;

        if (!isset($orders[$defaultOrder])) {
            $keys = array_keys($orders);
            $defaultOrder = $keys[0];
        }

        $order = $this->getRequest()->getParam($this->getOrderVarName());
        if ($order && isset($orders[$order])) {
            if ($order == $defaultOrder) {
                $this->customerSession->unsSortOrder();
            } else {
                $this->_memorizeParam('sort_order', $order);
            }
        } else {
            $order = $this->customerSession->getSortOrder();
        }
        // validate session value
        if (!$order || !isset($orders[$order])) {
            $order = $defaultOrder;
        }
        $this->setData('_current_grid_order', $order);

        return $order;
    }

    /**
     * Retrieve current direction.
     *
     * @return string
     */
    public function getCurrentDirection()
    {
        $dir = $this->_getData('_current_grid_direction');
        if ($dir) {
            return $dir;
        }

        $directions = ['asc', 'desc'];
        $dir = strtolower($this->getRequest()->getParam($this->getDirectionVarName()));
        if ($dir && in_array($dir, $directions)) {
            if ($dir == $this->_direction) {
                $this->customerSession->unsSortDirection();
            } else {
                $this->_memorizeParam('sort_direction', $dir);
            }
        } else {
            $dir = $this->customerSession->getSortDirection();
        }
        // validate direction
        if (!$dir || !in_array($dir, $directions)) {
            $dir = $this->_direction;
        }
        $this->setData('_current_grid_direction', $dir);

        return $dir;
    }

    /**
     * Set default Order field.
     *
     * @param string $field
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function setDefaultOrder($field)
    {
        if (isset($this->_availableOrder[$field])) {
            $this->_orderField = $field;
        }

        return $this;
    }

    /**
     * Set default sort direction.
     *
     * @param string $dir
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function setDefaultDirection($dir)
    {
        if (in_array(strtolower($dir), ['asc', 'desc'])) {
            $this->_direction = strtolower($dir);
        }

        return $this;
    }

    /**
     * Retrieve available Order fields list.
     *
     * @return array
     */
    public function getAvailableOrders()
    {
        return $this->_availableOrder;
    }

    /**
     * Set Available order fields list.
     *
     * @param array $orders
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function setAvailableOrders($orders)
    {
        $this->_availableOrder = $orders;

        return $this;
    }

    /**
     * Add order to available orders.
     *
     * @param string $order
     * @param string $value
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function addOrderToAvailableOrders($order, $value)
    {
        $this->_availableOrder[$order] = $value;

        return $this;
    }

    /**
     * Remove order from available orders if exists.
     *
     * @param string $order
     *
     * @return object
     */
    public function removeOrderFromAvailableOrders($order)
    {
        if (isset($this->_availableOrder[$order])) {
            unset($this->_availableOrder[$order]);
        }

        return $this;
    }

    /**
     * Compare defined order field vith current order field.
     *
     * @param string $order
     *
     * @return bool
     */
    public function isOrderCurrent($order)
    {
        return ($order == $this->getCurrentOrder());
    }

    /**
     * Retrieve Pager URL.
     *
     * @param string $order
     * @param string $direction
     *
     * @return string
     */
    public function getOrderUrl($order, $direction)
    {
        if ($order === null) {
            $order = $this->getCurrentOrder() ? $this->getCurrentOrder() : $this->_availableOrder[0];
        }

        return $this->getPagerUrl([
            $this->getOrderVarName() => $order,
            $this->getDirectionVarName() => $direction,
            $this->getPageVarName() => null,
        ]);
    }

    /**
     * Return current URL with rewrites and additional parameters.
     *
     * @param array $params Query parameters
     *
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (count($params) > 0) {
            $query = http_build_query($params);
            $url .= '?'.$query;
        }

        return $url;
    }

    /**
     * Retrieve current View mode.
     *
     * @return string
     */
    public function getCurrentMode()
    {
        $mode = $this->_getData('_current_grid_mode');
        if ($mode) {
            return $mode;
        }
        $modes = array_keys($this->_availableMode);
        $defaultMode = current($modes);
        $mode = $this->getRequest()->getParam($this->getModeVarName());
        if ($mode) {
            if ($mode == $defaultMode) {
                $this->customerSession->unsDisplayMode();
            } else {
                $this->_memorizeParam('display_mode', $mode);
            }
        } else {
            $mode = $this->customerSession->getDisplayMode();
        }

        if (!$mode || !isset($this->_availableMode[$mode])) {
            $mode = $defaultMode;
        }
        $this->setData('_current_grid_mode', $mode);

        return $mode;
    }

    /**
     * Compare defined view mode with current active mode.
     *
     * @param string $mode
     *
     * @return bool
     */
    public function isModeActive($mode)
    {
        return $this->getCurrentMode() == $mode;
    }

    /**
     * Retrieve availables view modes.
     *
     * @return array
     */
    public function getModes()
    {
        return $this->_availableMode;
    }

    /**
     * Set available view modes list.
     *
     * @param array $modes
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function setModes($modes)
    {
        if (!isset($this->_availableMode)) {
            $this->_availableMode = $modes;
        }

        return $this;
    }

    /**
     * Retrive URL for view mode.
     *
     * @param string $mode
     *
     * @return string
     */
    public function getModeUrl($mode)
    {
        return $this->getPagerUrl([$this->getModeVarName() => $mode, $this->getPageVarName() => null]);
    }

    /**
     * Disable view switcher.
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function disableViewSwitcher()
    {
        $this->_enableViewSwitcher = false;

        return $this;
    }

    /**
     * Enable view switcher.
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function enableViewSwitcher()
    {
        $this->_enableViewSwitcher = true;

        return $this;
    }

    /**
     * Is a enabled view switcher.
     *
     * @return bool
     */
    public function isEnabledViewSwitcher()
    {
        return $this->_enableViewSwitcher;
    }

    /**
     * Disable Expanded.
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function disableExpanded()
    {
        $this->_isExpanded = false;

        return $this;
    }

    /**
     * Enable Expanded.
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function enableExpanded()
    {
        $this->_isExpanded = true;

        return $this;
    }

    /**
     * Check is Expanded.
     *
     * @return bool
     */
    public function isExpanded()
    {
        return $this->_isExpanded;
    }

    /**
     * Retrieve default per page values.
     *
     * @return string (comma separated)
     */
    public function getDefaultPerPageValue()
    {
        if ($this->getCurrentMode() == 'list') {
            if ($default = $this->getDefaultListPerPage()) {
                return $default;
            }

            return $this->context->getScopeConfig()->getValue('catalog/frontend/list_per_page');
        } elseif ($this->getCurrentMode() == 'grid') {
            if ($default = $this->getDefaultGridPerPage()) {
                return $default;
            }

            return $this->context->getScopeConfig()->getValue('catalog/frontend/grid_per_page');
        }

        return 0;
    }

    /**
     * Add new limit to pager for mode.
     *
     * @param string $mode
     * @param string $value
     * @param string $label
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function addPagerLimit($mode, $value, $label = '')
    {
        if (!isset($this->_availableLimit[$mode])) {
            $this->_availableLimit[$mode] = [];
        }
        $this->_availableLimit[$mode][$value] = empty($label) ? $value : $label;

        return $this;
    }

    /**
     * Retrieve available limits for current view mode.
     *
     * @return array
     */
    public function getAvailableLimit()
    {
        $currentMode = $this->getCurrentMode();
        if (in_array($currentMode, ['list', 'grid'])) {
            return $this->_getAvailableLimit($currentMode);
        } else {
            return $this->_defaultAvailableLimit;
        }
    }

    /**
     * Retrieve available limits for specified view mode.
     *
     * @param string $mode
     *
     * @return array
     */
    protected function _getAvailableLimit($mode)
    {
        if (isset($this->_availableLimit[$mode])) {
            return $this->_availableLimit[$mode];
        }
        $perPageConfigKey = 'catalog/frontend/'.$mode.'_per_page_values';
        $perPageValues = (string) $this->context->getScopeConfig()->getValue($perPageConfigKey);
        $perPageValues = explode(',', $perPageValues);
        $perPageValues = array_combine($perPageValues, $perPageValues);
        if ($this->context->getScopeConfig()->getValueFlag('catalog/frontend/list_allow_all')) {
            return ($perPageValues + ['all' => __('All')]);
        } else {
            return $perPageValues;
        }
    }

    /**
     * Get specified products limit display per page.
     *
     * @return string
     */
    public function getLimit()
    {
        $limit = $this->_getData('_current_limit');
        if ($limit) {
            return $limit;
        }

        $limits = $this->getAvailableLimit();
        $defaultLimit = $this->getDefaultPerPageValue();
        if (!$defaultLimit || !isset($limits[$defaultLimit])) {
            $keys = array_keys($limits);
            $defaultLimit = $keys[0];
        }

        $limit = $this->getRequest()->getParam($this->getLimitVarName());
        if ($limit && isset($limits[$limit])) {
            if ($limit == $defaultLimit) {
                $this->customerSession->unsLimitPage();
            } else {
                $this->_memorizeParam('limit_page', $limit);
            }
        } else {
            $limit = $this->customerSession->getLimitPage();
        }
        if (!$limit || !isset($limits[$limit])) {
            $limit = $defaultLimit;
        }

        $this->setData('_current_limit', $limit);

        return $limit;
    }

    /**
     * Retrieve Limit Pager URL.
     *
     * @param int $limit
     *
     * @return string
     */
    public function getLimitUrl($limit)
    {
        return $this->getPagerUrl([
            $this->getLimitVarName() => $limit,
            $this->getPageVarName() => null,
        ]);
    }

    /**
     * @param int $limit
     * @return bool
     */
    public function isLimitCurrent($limit)
    {
        return $limit == $this->getLimit();
    }

    /**
     * @return int
     */
    public function getFirstNum()
    {
        $collection = $this->getCollection();

        return $collection->getPageSize() * ($collection->getCurPage() - 1) + 1;
    }

    /**
     * @return int
     */
    public function getLastNum()
    {
        $collection = $this->getCollection();

        return $collection->getPageSize() * ($collection->getCurPage() - 1) + $collection->count();
    }

    /**
     * @return int
     */
    public function getTotalNum()
    {
        return $this->getCollection()->getSize();
    }

    /**
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->getCollection()->getCurPage() == 1;
    }

    /**
     * @return int
     */
    public function getLastPageNum()
    {
        return $this->getCollection()->getLastPageNumber();
    }

    /**
     * Render pagination HTML.
     *
     * @return string
     */
    public function getPagerHtml()
    {
        $pagerBlock = $this->getLayout()
            ->createBlock('\Mirasvit\Rewards\Block\Referral\List_TOFIX\Pager', 'rewards.referral.pager');
        $pagerBlock->setAvailableLimit($this->getAvailableLimit());
        $pagerBlock->setUseContainer(false)
            ->setShowPerPage(false)
            ->setShowAmounts(false)
            ->setLimitVarName($this->getLimitVarName())
            ->setPageVarName($this->getPageVarName())
            ->setLimit($this->getLimit())
            ->setFrameLength($this->context->getScopeConfig()->getValue('design/pagination/pagination_frame'))
            ->setJump($this->context->getScopeConfig()->getValue('design/pagination/pagination_frame_skip'))
            ->setCollection($this->getCollection());

        return $pagerBlock->toHtml();
    }
}

/**
 * Class Pager.
 */
class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * @param array $params
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (count($params) > 0) {
            $query = http_build_query($params);
            $url .= '?'.$query;
        }

        return $url;
    }
}
