<?php
namespace Bluethink\Custom\Block;

class Categoryproduct extends \Magento\Framework\View\Element\Template
{
     protected $categoryFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,        
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    public function getCategoryProduct($categoryId)
    {
        $category = $this->categoryFactory->create()->load($categoryId)->getProductCollection()->addAttributeToSelect('*');
        return $category;
    }
}