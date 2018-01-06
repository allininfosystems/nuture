<?php
/**
 * Product Tags block
 * @author Min <dangquocmin@gmail.com>
 */
namespace Min\Tags\Block;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class Tags extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Product
     */
    private $product;

    public function __construct(Template\Context $context, Registry $registry, array $data)
    {
        $this->registry = $registry;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }


    /**
     * @return Product
     */
    private function getProduct()
    {
        if (is_null($this->product)) {
            $this->product = $this->registry->registry('product');

            if (!$this->product->getId()) {
                throw new LocalizedException(__('Failed to initialize product'));
            }
        }

        return $this->product;
    }

    public function getProductName()
    {
        return $this->getProduct()->getName();
    }

    public function getTags()
    {
        $tags = $this->getProduct()->getData($this->_getAttrUse());
        return explode(',', $tags);
    }

    protected function _getAttrUse()
    {
        $enable = $this->scopeConfig->getValue('min_tags/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $enable && !$this->getProduct()->getProductTags() ? 'meta_keyword' : 'product_tags';
    }
}
