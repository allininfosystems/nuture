<?php
namespace Magecomp\Gstcharge\Block;

use Magento\Framework\View\Element\Template; 
use Magento\Framework\View\Element\Template\Context;
use Magecomp\Gstcharge\Model\CgstFeeConfigProvider;

class Checkoutconfig extends Template
{
    /**
     * @var array
     */
    protected $jsLayout;
 
    /**
     * @var \Webkul\Knockout\Model\CustomConfigProvider
     */
    protected $configProvider;
 
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        CgstFeeConfigProvider $CgstFeeConfigProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
        $this->configProvider = $CgstFeeConfigProvider;
    }
 
    /**
     * @return string
     */
    public function getJsLayout()
    {
        return \Zend_Json::encode($this->jsLayout);
    }
 
    public function getCustomConfig()
    {
		
        return $this->configProvider->getConfig();
    }
}