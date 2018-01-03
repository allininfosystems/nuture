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
 * RewardPoints Update Top Link Block
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block;

class TopLink extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $_objectManager;

    /**
     * @var \Magestore\Rewardpoints\Helper\Config
     */
    protected $helper;

    /**
     * @var
     */
    public $_href;

    /**
     * @var
     */
    protected $_label;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_modelUrl;

    /**
     * @var Name
     */
    protected $_blockName;

    /**
     * TopLink constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magestore\Rewardpoints\Helper\Config $globalConfig
     * @param \Magento\Customer\Model\Url $url
     * @param Name $blockName
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magestore\Rewardpoints\Helper\Config $globalConfig,
        \Magento\Customer\Model\Url $url,
        \Magestore\Rewardpoints\Block\Name $blockName,
        array $data = array()
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->helper = $globalConfig;
        $this->_modelUrl = $url;
        $this->_blockName = $blockName;
        $this->setTemplate('Magestore_Rewardpoints::rewardpoints/topLink.phtml');
    }

    public function getHref()
    {
        return $this->_modelUrl->getAccountUrl();
    }

    public function getLabel()
    {
        $nameBlock =  $this->_blockName;
        if($this->helper->getDisplayConfig('toplink')) {
            return __('My Account') . ' (' . $nameBlock->toHtml() . ')';
        }else {
            return __('My Account');
        }
    }

    public function getTitle(){
        return  __('My Account');
    }

    /**
     * functional block - using to change other block information
     *
     * @return string
     */
    protected function _toHtml()
    {

        return parent::_toHtml() ;
    }
}
