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
 * RewardPoints Show Spending Point on Shopping Cart Page
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block\Checkout\Cart;
class Point extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $_scopeConfig;
    /**
     * @var \Magestore\Rewardpoints\Helper\Block\Spend
     */
    protected $_blockSpend;
    /**
     * @var \Magestore\Rewardpoints\Helper\Point
     */
    protected $_helperPoint;
    /**
     * @var \Magestore\Rewardpoints\Helper\Customer
     */
    protected $_helperCustomer;
    /**
     * @var \Magestore\Rewardpoints\Helper\Calculation\Spending
     */
    protected $_calculationSpending;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    /**
     * @var \Magestore\Rewardpoints\Helper\Policy
     */
    protected $_helperPolicy;

    /**
     * Point constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\Rewardpoints\Helper\Block\Spend $blockSpend
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     * @param \Magestore\Rewardpoints\Helper\Customer $helperCustomer
     * @param \Magestore\Rewardpoints\Helper\Calculation\Spending $calculationSpending
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magestore\Rewardpoints\Helper\Policy $helperPolicy
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Rewardpoints\Helper\Block\Spend $blockSpend,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        \Magestore\Rewardpoints\Helper\Customer $helperCustomer,
        \Magestore\Rewardpoints\Helper\Calculation\Spending $calculationSpending,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magestore\Rewardpoints\Helper\Policy $helperPolicy
    )
    {
        parent::__construct($context, []);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_blockSpend = $blockSpend;
        $this->_helperPoint = $helperPoint;
        $this->_helperCustomer = $helperCustomer;
        $this->_calculationSpending = $calculationSpending;
        $this->_checkoutSession = $checkoutSession;
        $this->_helperPolicy = $helperPolicy;
    }

    /**
     * prepare block's layout
     *
     * @return Magestore_RewardPoints_Block_Image
     */
    public function _prepareLayout()
    {
//        $this->setTemplate('Magestore_Rewardpoints::rewardpoints/checkout/cart/point.phtml');
        return parent::_prepareLayout();
    }

    /**
     * @param $url
     * @param array $params
     * @return string
     */
    public function createUrl($url,$params = []){
        return $this->_urlBuilder->getDirectUrl($url,$params);
    }

    /**
     * get reward points spending block helper
     *
     * @return Magestore_RewardPoints_Helper_Block_Spend
     */
    public function getBlockHelper()
    {
        return $this->_blockSpend;
    }

    /**
     * get reward points helper
     *
     * @return Magestore_RewardPoints_Helper_Point
     */
      public function getPointHelper()
    {
        return $this->_helperPoint;
    }

    public function enableReward(){
        return $this->_blockSpend->enableReward();
    }

    public function getSliderRules(){
        return $this->_blockSpend->getSliderRules();
    }


    public function getCheckboxRules(){
        return $this->_blockSpend->getCheckboxRules();
    }

    public function getRulesArray(){
        return $this->_blockSpend->getRulesArray();
    }


    public function getSliderData(){
        return  $this->_blockSpend->getSliderData();
    }

    public function getRulesJson(){
        return $this->_blockSpend->getRulesJson();
    }

    public function formatDiscount($rule){
        return $this->_blockSpend->formatDiscount($rule);
    }
    /**
     * call method that defined from block helper
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
//    public function __call($method, $args) {
//        $helper = $this->getBlockHelper();
//        if (method_exists($helper, $method)) {
//            return call_user_func_array([$helper, $method], $args);
//            // return call_user_method_array($method, $helper, $args);
//        }
//        return parent::__call($method, $args);
//    }

    /**
     * @param $_rule
     * @param $_pointHelper
     * @return string
     */
    public function knockoutCheckRule(){
        $_rule = current($this->getSliderRules());
        $_pointHelper = $this->getPointHelper();
        if ($_rule->getId() == 'rate') {
            return __('Each of %1 gets %2 discount',
                $_pointHelper->format($_rule->getPointsSpended()),
                $this->formatDiscount($_rule) );
        }else{
            return $this->escapeHtml($_rule->getName()).
            '('. __('With %1', $_pointHelper->format($_rule->getPointsSpended())) .')';
        }
    }

    /**
     * @return bool
     */
    public function checkMaxpoint() {
        $customer = $this->_helperCustomer;
        $_spendingHelper = $this->_calculationSpending;
        $checkout = $this->_checkoutSession;

        if($customer->getBalance() && $_spendingHelper->isUseMaxPointsDefault() && (!$_spendingHelper->isUsePoint() || $checkout->getData('use_max'))){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * @return bool
     */
    public function checkEarnwhenSpend(){
        if (!$this->_scopeConfig->getValue('rewardpoints/earning/earn_when_spend', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getPointsUseMaxDefault(){
        $max_default_point = $this->_scopeConfig->getValue('rewardpoints/spending/max_points_per_order', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if( is_numeric($max_default_point) && $max_default_point > 0 ){
            return $max_default_point;
        }
        return "0";
    }

    /**
     * @return mixed
     */
    public function getUseMaxDefault(){
        return $this->_scopeConfig->getValue('rewardpoints/spending/max_point_default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function pointForRule(){
        $_sliderRules = $this->getSliderRules();
        if (count($_sliderRules)){
            $_rule = current($_sliderRules);
            if(isset($this->getRulesArray($this->getSliderRules())[$_rule->getId()]) && isset($this->getRulesArray($this->getSliderRules())[$_rule->getId()]['needPoint'])){
                return $pointForRule = $this->getRulesArray($this->getSliderRules())[$_rule->getId()]['needPoint'];
            }
        }
        return false;
    }

    /**
     * @return string
     */

    public function knockouttextNeedMorePoint(){
        $pointForRule = $this->pointForRule();
        if($pointForRule){
            return __('You need to earn more')
            ." <span id=\"rewardpoints-needmore-points\">".$pointForRule."</span> "
            .__('point(s) to use this rule.')
            .' '.__('Please click').' '.
            "<a href=\"".
            $this->_helperPolicy->getPolicyUrl()."\">"
            .__('here')."</a> "
            .__('to learn about it.');
        }
    }

    /**
     * @get data for knockout
     * @return array
     */
    public function knockoutData(){
        $results = [];
        if (count($this->getSliderRules()) || count($this->getCheckboxRules()) ){
            $customerId = $this->_helperCustomer->getCustomerId();
            $results['enableReward'] = $this->enableReward() && $customerId;
            $results['rule'] = $this->knockoutCheckRule();
            $results['textPoint'] = $this->getPointHelper()->getPluralName();
            $results['usePoint'] = $this->getSliderData()->getUsePoint() ? $this->getSliderData()->getUsePoint() : 0;
            $results['getRulesJson'] = $this->getRulesJson($this->getSliderRules());
            $results['textNeedMorePoint'] = $this->knockouttextNeedMorePoint();
            $results['checkMaxpoint'] = $this->checkMaxpoint();
            $results['checkEarnwhenSpend'] = $this->checkEarnwhenSpend();
            $results['urlUpdateTotal'] = $this->createUrl('rewardpoints/checkout/updateTotal/');
            $results['urlRefreshPoint'] = $this->createUrl('rewardpoints/checkout/refreshPoint/');
        }else{
            $results['enableReward'] = false;
        }
        if($this->getNameInLayout() == 'rewardpoint_checkout_cart_point'){
            $results['template'] = 'Magestore_Rewardpoints/checkout/summary/spending';
        }else if($this->getNameInLayout() == 'rewardpoint_checkout_index_point'){
            $results['template'] = 'Magestore_Rewardpoints/checkout/payment/spending';
        }
        return $results;
    }

    public function getJsLayout(){
        return \Zend_Json::encode([
            'components'=> [
                'block-rewardpoints' => [
                    'component'=>'Magestore_Rewardpoints/js/view/checkout/payment/spending',
                    'sortOrder'=>20
                ]
            ]
        ]);
    }
}
