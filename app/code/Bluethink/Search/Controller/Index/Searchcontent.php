<?php

namespace Bluethink\Search\Controller\Index;

class Searchcontent extends \Magento\Framework\App\Action\Action {

    protected $_cacheTypeList;
    protected $_cacheState;
    protected $_cacheFrontendPool;
    protected $resultPageFactory;
    protected $_registry;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\App\Cache\StateInterface $cacheState, \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
    }

    public function execute() {
        $key = $this->getRequest()->getParam('key');
        $storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $store = $storeManager->getStore();
        $baseurl = $store->getBaseUrl();
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();

        $result = "";
        $result.='<ul role="listbox">';
        //product Search goes here..
        if ($key != "") {
            $productCollection = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
            $_productCollection = $productCollection->create()->addAttributeToSelect('*')->addAttributeToFilter('name', array('like' => '%' . $key . '%'))->load();
            $i = 0;
            foreach ($_productCollection as $_product) {
                if ($i == 3) {
                    break;
                }
                $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($_product->getId());
                $result.='<li class="" id="qs-option-0" role="option"><span class="qs-option-name"><a href="' . $_product->getProductUrl() . '">' . $product->getName() . '</a></span><span aria-hidden="true" class="amount">Product</span></li>';
                $i++;
            }
        }
        //End product Search ..
        //KnowledgeBase Search goes here..
        if ($key != "") {
            $articles = $this->_objectManager->get('Mirasvit\Kb\Model\ResourceModel\Article\Collection')->load()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter(array('name'), array(
                array('like' => '%' . $key . '%')
                    )
            );
            $kb_data = $articles->getData();
            $j = 0;
            foreach ($kb_data as $value) {
                $sql_5 = "SELECT mstcat.* FROM mst_kb_category as mstcat
                  INNER JOIN mst_kb_article_category as mstartcat ON mstartcat.ac_category_id = mstcat.category_id
                  INNER JOIN mst_kb_article as mstart ON mstartcat.ac_article_id = mstart.article_id where mstart.article_id=" . $value['article_id'] . "";
                $result1 = $connection->fetchAll($sql_5);
                $catname = $result1[0]['url_key'];
                $_model = $this->_objectManager->get('Mirasvit\Kb\Model\Article')->load($value['article_id']);
                $final_kb_url = 'knowledge-hub/' . $catname . '/' . $_model->getUrlKey() . '.html';
                if ($j == 2) {
                    break;
                }
                $result.='<li class="" id="qs-option-0" role="option"><span class="qs-option-name"><a href="' . $final_kb_url . '">' . $value['name'] . '</a></span><span aria-hidden="true" class="amount">KnowledgeBase</span></li>';
                $j++;
            }
        }
        //End KnowledgeBase Search goes here..
        //Blog Search goes here..
        if ($key != "") {
            $postcollection = $this->_objectManager->create('Mageplaza\Blog\Model\ResourceModel\Post\Collection')->load()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter(array('name'), array(
                array('like' => '%' . $key . '%')
                    )
            );
            $finaldata = $postcollection->getData();
            $k = 0;
            foreach ($finaldata as $bantoo) {
                if ($k == 2) {
                    break;
                }
                $_blogurl = $bantoo['url_key'];
                $final_blogurl = $baseurl . 'blog/post/' . $_blogurl;
                $result.='<li class="" id="qs-option-0" role="option"><span class="qs-option-name"><a href="' . $final_blogurl . '">' . $bantoo['name'] . '</a></span><span aria-hidden="true" class="amount">Blog</span></li>';
                $k++;
            }
        }
        $result.='</ul><div class="lookingfor"><a href="'.$baseurl.'lookingfor">Did not find what you are looking for - Click here</a></div>';
        echo $result;
    }

}
