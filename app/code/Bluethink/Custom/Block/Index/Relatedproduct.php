<?php
/**
 * Copyright © 2015 Bluethink . All rights reserved.
 */
namespace Bluethink\Custom\Block\Index;
use Bluethink\Custom\Block\BaseBlock;

class Relatedproduct extends \Magento\Framework\View\Element\Template
{
    protected $_registry;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {        
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }
    
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    }    
    
}
?>