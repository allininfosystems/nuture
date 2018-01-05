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



namespace Mirasvit\Rewards\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        \Mirasvit\Rewards\Model\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->storeFactory           = $storeFactory;
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->config                 = $config;
        $this->storeManager           = $storeManager;
        $this->design                 = $design;
        $this->priceCurrency          = $priceCurrency;
        $this->context                = $context;

        parent::__construct($context);
    }

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $_currentStore;

    /**
     * Sets current store for translation.
     *
     * @param \Magento\Store\Model\Store $store
     *
     * @return void
     */
    public function setCurrentStore($store)
    {
        $this->_currentStore = $store;
    }

    /**
     * Returns current store.
     *
     * @return \Magento\Store\Model\Store
     */
    public function getCurrentStore()
    {
        if (!$this->_currentStore) {
            $this->_currentStore = $this->storeManager->getStore();
        }

        return $this->_currentStore;
    }

    /**
     * @return array
     */
    public function getCoreStoreOptionArray()
    {
        $arr = $this->storeCollectionFactory->create()->toArray();
        foreach ($arr['items'] as $value) {
            $result[$value['store_id']] = $value['name'];
        }

        return $result;
    }

    /************************/

    /**
     * Translates backend messages independently from backend locale.
     *
     * Params:
     * param1  string   Message to translate
     * param2  string[] Infinite number of params for vsprintf
     *
     * @return string
     */
    public function ____()
    {
        $args = func_get_args();

        return call_user_func_array('__', $args);

        $locale = $this->context->getScopeConfig()->getValue('general/locale/code', $this->getCurrentStore()->getId());
        $localeCsv = Mage::getBaseDir('locale').'/'.$locale.'/'.'Mirasvit_Rewards.csv';
        if (!file_exists($localeCsv)) {
            return call_user_func_array(['Mirasvit_Rewards_Helper_Data', '__'], $args);
        }

        $translator = new \Zend_Translate(
            [
                'adapter' => 'csv',
                'content' => $localeCsv,
                'locale' => substr($locale, 0, 2),
                'delimiter' => ',',
            ]
        );
        $msg = $translator->_($args[0]);
        unset($args[0]);

        return vsprintf($msg, $args);
    }

    /**
     * @return \Mirasvit\Rewards\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getPointsName()
    {
        $unit = $this->getConfig()->getGeneralPointUnitName();
        $unit = str_replace(['(', ')'], '', $unit);

        return $unit;
    }

    /**
     * @param float $points
     * @return string
     */
    public function formatPoints($points)
    {
        $unit = $this->getConfig()->getGeneralPointUnitName($this->getCurrentStore()->getId());
        if ($points == 1) {
            $unit = preg_replace("/\([^)]+\)/", '', $unit);
        } else {
            $unit = str_replace(['(', ')'], '', $unit);
        }

        return $points.' '.$unit;
    }

    /**
     * @param float $points
     * @return string
     */
    public function formatPointsWithCutUnitName($points)
    {
        $result = $this->formatPoints($points);
        $regexp = '/p{P}/';
        $result = trim(preg_replace($regexp, ' ', $result));
        if (count(explode(' ', $result)) > 2) {
            $regexp = '/[^\p{Lu}]/u';
            return $points .' '. preg_replace($regexp, '', ucwords(strtolower($result)));
        }

        return $result;
    }

    /**
     * @param float $value
     * @return float
     */
    public function formatCurrency($value)
    {
        return $this->priceCurrency->format($value);
    }

    /**
     * @return bool
     */
    public function isMultiship()
    {
        return false;
    }

    /**
     * @param int $storeId
     * @return int|null|string
     */
    public function getWebsiteId($storeId)
    {
        return $this->storeFactory->create()->load($storeId)->getWebsiteId();
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->storeManager->getStore()->isAdmin() || $this->design->getArea() == 'adminhtml';
    }

    /**
     * @param etring $text
     * @return string
     */
    public function convertToHtml($text)
    {
        $html = nl2br($text);

        return $html;
    }
}
