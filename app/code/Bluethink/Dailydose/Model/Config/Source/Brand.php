<?php

namespace Bluethink\Dailydose\Model\Config\Source;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Config
 *
 * @package Name\MyModule\Model\Config\Backend
 */
class Brand implements ArrayInterface
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    private $context;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
     protected $_allowedAttribute;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {
        $this->context = $context;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * Get allowed currencies
     *
     * @return  array of allowed Currencies
     *
     **/
    public function getAllowedAttributes()
    {

       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $eavConfig = $objectManager->get('\Magento\Eav\Model\Config');
       $attribute = $eavConfig->getAttribute('catalog_product', 'product_brand');
       $options = $attribute->getSource()->getAllOptions();
       $optionsExists = array();
       foreach($options as $option) 
       {
            $optionsExists[] = $option['label'];
       }
        return $optionsExists;
    }

    /**
     * Admin Config action
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllowedAttributes();
    }
}

?>