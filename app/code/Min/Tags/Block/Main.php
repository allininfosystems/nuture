<?php
/**
 * Main block
 * @author Min <dangquocmin@gmail.com>
 */
namespace Min\Tags\Block;

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Search\Model\QueryFactory;

class Main extends \Magento\CatalogSearch\Block\Result
{
    protected $_tag;

    /**
     * @param Context $context
     * @param LayerResolver $layerResolver
     * @param Data $catalogSearchData
     * @param QueryFactory $queryFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        LayerResolver $layerResolver,
        Data $catalogSearchData,
        QueryFactory $queryFactory,
        array $data = []
    )
    {
        $this->scopeConfig = $context->getScopeConfig();
        $tag = str_replace('-', '+', $context->getRequest()->getParam('tag'));
        $this->setTag(urldecode($tag));
        parent::__construct($context, $layerResolver, $catalogSearchData, $queryFactory, $data);
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->_tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag)
    {
        $this->_tag = $tag;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $title = $this->getTag();
        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            )->addCrumb(
                'search',
                ['label' => $title, 'title' => $title]
            );
        }
    }

    protected function _getProductCollection()
    {
        if (null === $this->productCollection) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
                ->addAttributeToFilter($this->_getAttrToFilter(), '', 'left')->addAttributeToFilter(
                    'status', array('eq' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                )->setFlag('has_stock_status_filter', true)->addAttributeToSelect(array('name', 'small_image', 'price', 'special_price'));
            $this->productCollection = $productCollection;
            $this->getListBlock()->setCollection($productCollection);
        }

        return $this->productCollection;
    }

    public function getSearchQueryText()
    {
        return __("Search results for: '%1'", $this->getTag());
    }

    public function getListBlock()
    {
        return $this->getChildBlock('tag_result_list');
    }

    public function getAdditionalHtml()
    {
        return $this->getLayout()->getBlock('tag_result_list')->getChildHtml('additional');
    }

    public function getProductListHtml()
    {
        return $this->getChildHtml('tag_result_list');
    }

    public function getNoResultText()
    {
        return $this->_getData('no_result_text');
    }

    protected function _getAttrToFilter()
    {
        $enable = $this->scopeConfig->getValue('min_tags/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $result = array(array('attribute' => 'product_tags', 'like' => '%' . $this->getTag() . '%'));
        if ($enable) {
            $result[] = array('attribute' => 'meta_keyword', 'like' => '%' . $this->getTag() . '%');
        }
        return $result;
    }
}
