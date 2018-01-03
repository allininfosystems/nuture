<?php

namespace Bluethink\Faq\Model\System\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Config
 *
 * @package Name\MyModule\Model\Config\Backend
 */
class Status implements ArrayInterface
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
        $colls = $objectManager->create(\Bluethink\Faq\Model\ResourceModel\Faqcategory\Collection::class);
        $options = [];
         $options[] = [
                'label' => __('------- Please choose option -------'),
                'value' => '',
            ];
        foreach($colls as $k=>$coll)
        {         
            $options[] = ['value' => $k , 'label' => $coll->getFaqCatName()];
        }
        return $options;
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