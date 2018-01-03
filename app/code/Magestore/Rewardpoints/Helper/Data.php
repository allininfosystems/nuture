<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPoints Name and Image Helper
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Helper;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Data extends \Magestore\Rewardpoints\Helper\Config
{
    /**
     * @var Config
     */
    protected $helper;

    /**
     * @var Customer
     */
    protected $helperCustomer;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magestore\Rewardpoints\Model\Rate
     */
    public $_rateModel;

    /**
     * @var Point
     */
    public $_helperPoint;


    const XML_PATH_ENABLE = 'rewardpoints/general/enable';

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Config $globalConfig
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param Customer $customer
     * @param Point $helperPoint
     * @param \Magestore\Rewardpoints\Model\Rate $rateModel
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magestore\Rewardpoints\Helper\Config $globalConfig,
        \Magestore\Rewardpoints\Helper\Customer $customer,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        \Magestore\Rewardpoints\Model\Rate $rateModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency
        )
    {
        $this->helper = $globalConfig;
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->helperCustomer = $customer;
        $this->_helperPoint = $helperPoint;
        $this->_rateModel = $rateModel;
        $this->_storeManager = $storeManager;
        $this->_priceCurrency = $priceCurrency;
        parent::__construct($context);
    }

    public function getUrlBuilder(){
        return  $this->_urlBuilder;
    }

    /**
     * check reward points system is enabled
     *
     * @param mixed $store
     * @return boolean
     */
    public function isEnable($store = null)
    {
        return $this->helper->getConfig(self::XML_PATH_ENABLE, $store);
    }
    public function isEnableOutput(){
        return $this->isModuleOutputEnabled('Magestore_Rewardpoints');
    }
    public function isEnablePolicy($store = null){
        return $this->helper->getConfig('rewardpoints/general/show_policy_menu', $store);
    }
    public function getPolicyLink($store = null){
        if(!$this->isEnablePolicy()) return null;

        return $this->_urlBuilder->getUrl('rewardpoints/policy');
    }

    /**
     * get rewards points label to show on Account Navigation
     *
     * @return string
     */
    public function getMyRewardsLabel()
    {
        $pointAmount = $this->helperCustomer->getBalance();
        if ($pointAmount > 0) {
            $rate = $this->_rateModel->getRate(\Magestore\Rewardpoints\Model\Rate::POINT_TO_MONEY);
            if ($rate && $rate->getId()) {
                $baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
                $pointAmount = $this->convertAndFormat($baseAmount, true);
            }else{
                $pointAmount = $pointAmount .' '.(($pointAmount>1)?__('Points'):__('Point'));
            }
        }
        $imageHtml = $this->_helperPoint->getImageHtml(false);
        return __('My Rewards') . ' ' . $pointAmount . $imageHtml;
    }

    /**
     * @param $value
     * @param bool|true $format
     * @return mixed
     */
    public function convertPrice($value,  $store = null)
    {

        if(!$store){
            $store = $this->getStore();
        }
        return $this->_priceCurrency->convert($value, $store);
    }
    /**
     * @param $value
     * @return mixed
     */

    public function convertAndFormat($value, $includeContainer = true)
    {
        return $this->_priceCurrency->convertAndFormat($value,$includeContainer);
    }

    /**
     * Get store view
     * @param null $storeId
     * @return int
     */
    public function getStore($storeId = null){
        return $this->_storeManager->getStore($storeId);
    }

}
