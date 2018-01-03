<?php
/**
 *
 * Copyright © 2015 Bluethinkcommerce. All rights reserved.
 */
namespace Bluethink\Custom\Controller\Cart;

class Update extends \Magento\Framework\App\Action\Action
{

	/**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $_storeManager;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
        $cart = $this->_objectManager->get('\Magento\Checkout\Model\Cart');

        // retrieve quote items collection
        $itemsCollection = $cart->getQuote()->getItemsCollection();

        // get array of all items what can be display directly
        $itemsVisible = $cart->getQuote()->getAllVisibleItems();

        // retrieve quote items array
        /*$items = $cart->getQuote()->getAllItems();*/
        $items = $cart->getQuote()->getAllVisibleItems();
        $grandTotal = number_format($cart->getQuote()->getGrandTotal(),2);

        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $html='';
        $html.= '<h4>My Shopping Bag : ₹'.$grandTotal.'<span class="close-cart-toggle"><i class="fa fa-close"></i></span></h4>';
        foreach($items as $item) {
            $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($item->getProductId());
			
            $html.= '<div class="cart-item clearfix">
                <div class="item-img"><a href="'.$product->getProductUrl().'"><img src="'.$baseUrl.'pub/media/catalog/product'.$product->getThumbnail().'"></a></div>
                <div class="item-details clearfix">
                    <span class="item-name">'.$item->getName().'</span>
                    <span class="price">Price : ₹'.number_format($item->getPrice(),2).'</span>
                    <span class="quantity">Quantity : '.$item->getQty().'</span>
                </div>
            </div>';
        }
        $html.='<div class="slide-link"><a href="'.$baseUrl.'checkout/"> Checkout</a></div>
            <div class="side-link"><a href="'.$baseUrl.'checkout/cart/">View cart</a></div>';

        $totalQuantity = $cart->getQuote()->getItemsQty();
        //echo round($totalQuantity);
        $response = array('html'=>$html,'count'=>round($totalQuantity));
        //echo "<pre>"; print_r($response); echo "</pre>";
        echo $result = json_encode($response);
        exit();   
    }
}
