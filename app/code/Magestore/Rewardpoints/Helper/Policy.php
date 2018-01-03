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
 * RewardPoints Policy Helper
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Helper;
use Magento\Framework\App\Helper\Context;
class Policy extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_SHOW_POLICY  = 'rewardpoints/general/show_policy_menu';
    const XML_PATH_POLICY_PAGE  = 'rewardpoints/general/policy_page';
    const XML_PATH_SHOW_WELCOME  = 'rewardpoints/general/show_welcome_page';
    protected $helper;

    public function __construct(
        Context $context,
        \Magestore\Rewardpoints\Helper\Config $globalConfig
    )
    {
        parent::__construct($context);
        $this->helper = $globalConfig;
    }

    /**
     * get Policy URL, return the url to view Policy
     * 
     * @return string
     */
    public function getPolicyUrl()
    {
        if (!$this->helper->getConfig(self::XML_PATH_SHOW_POLICY)) {
            return $this->_urlBuilder->getDirectUrl('rewardpoints/index/index/');
        }
        return $this->_urlBuilder->getDirectUrl('rewardpoints/policy/');
    }

    public function getWelcomeUrl()
    {
        if (!$this->helper->getConfig(self::XML_PATH_SHOW_WELCOME)) {
            return $this->_urlBuilder->getDirectUrl('rewardpoints/index/index/');
        }
        return $this->_urlBuilder->getUrl(null, array('_direct' => $this->helper->getConfig('rewardpoints/general/welcome_page')));
    }
    
    /**
     * Check policy menu configuration
     * 
     * @param mixed $store
     * @return boolean
     */
    public function showPolicyMenu($store = null)
    {
        return $this->helper->getConfig(self::XML_PATH_SHOW_POLICY, $store);
    }
}
