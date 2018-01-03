<?php

/**
 *
 * Copyright © 2015 Bluethinkcommerce. All rights reserved.
 */

namespace Bluethink\Custom\Controller\Index;

class Quickview extends \Magento\Framework\App\Action\Action {

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

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\App\Cache\StateInterface $cacheState, \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool, \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Flush cache storage
     *
     */
    public function execute() {
         
        $productid = $this->getRequest()->getParams();
        $id = $productid['id'];
        $controller = $productid['controller'];

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\App\Request\Http');
        $action_name = $request->getActionName();
        $module_name = $request->getModuleName();
        $controller_name = $request->getControllerName();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $store = $storeManager->getStore();
        $baseurl = $store->getBaseUrl();
        $FormKey = $objectManager->get('Magento\Framework\Data\Form\FormKey');
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
        $listBlock = $objectManager->get('\Magento\Catalog\Block\Product\ListProduct');
        $block = $objectManager->get('\Magento\Catalog\Block\Product\View');

        $productObj = $objectManager->get('Magento\Catalog\Model\Product')->load($id);
        $productName = $productObj->getName();
        $productprice = $productObj->getPrice();
        $productsku = $productObj->getSku();
        $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
        $RatingOb = $objectManager->create('Magento\Review\Model\Rating')->getEntitySummary($productObj->getId());
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');

        $wishlist_id = array();

        if ($customerSession->isLoggedIn()) {
            $customerid = $customerSession->getCustomer()->getId();
            $Wishlist_Model = $objectManager->create('\Magento\Wishlist\Controller\WishlistProviderInterface');
            $currentUserWishlist = $Wishlist_Model->getWishlist();
            if ($currentUserWishlist) {
                $wishlistItems = $currentUserWishlist->getItemCollection()->getData();
                foreach ($wishlistItems as $wishlistItems_value) {
                    $wishlist_id[] = $wishlistItems_value['product_id'];
                }
            }
        }
        if ($controller == 'product') {
            $adurl = $baseurl . 'checkout/cart/add/uenc/aHR0cDovL3N3aWZ0bHVicmljYW50cy5jb20vbnVsdXYvdGVzdC1wcm9kdWN0Lmh0bWw,/product/';
        } elseif ($controller == 'category') {
            $adurl = $baseurl . 'checkout/cart/add/uenc/aHR0cDovL3N3aWZ0bHVicmljYW50cy5jb20vbnVsdXYvbnVsdXYtbWFtYS5odG1s/product/';
        } else {
            $adurl = $baseurl . 'checkout/cart/add/uenc/aHR0cDovL3N3aWZ0bHVicmljYW50cy5jb20vbnVsdXYv/product/';
        }

        $images = $productObj->getMediaGalleryImages();
        $result = "";
        $result.='<div class="col-xs-12 col-sm-6" style="padding-right:0;">
              <div style="width: 98px;overflow: hidden;float: left;height: 411px;overflow-y: scroll;">
                <ul class="prd_thumb_list" id="thumbs">';
        $images1 = "";

        foreach ($images as $child) {
            $images1[] = $child->getUrl();
            $result.= '<li><a href="#"><img onclick="invokehello(this)" src=' . $child->getUrl() . '></a></li>';
        }
		
		 $imgpath=$baseurl . 'pub/static/frontend/Bluethink/Theme/en_US/Magento_Catalog/images/product/placeholder/small_image.jpg';
		 if(is_array($images1) && count($images1)>0){
			 $imgpath=$images1[0];
		 }
           $result.= ' </ul></div>
        <div class="product_view"><img id="largeImage" src=' . $imgpath . '></div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <h3 class="prd_title">' . $productObj->getName() . '</h3>
        <h6 class="prd_type">Nature’s baby Organics</h6>
        <div class="prd_price">' . $priceHelper->currency($productObj->getPrice(), true, false) . '</div>
        <div class="prd_dealer"><span class="stock_status instock">In Stock</span>Sold by <strong>Puracy LLC</strong> and Fulfilled by <strong>Nuluv</strong>.</div>
        <div class="prd_control">';
       
        $addToCartUrl = $listBlock->getAddToCartUrl($productObj);
        $result.='<form data-role="tocart-form" action="' . $adurl . $productObj->getId() . '" method="post" >';
        $result.= '<input name="form_key" type="hidden" value="' . $FormKey->getFormKey() . '">';
        $result.='<input type="number" name="qty" id="qty" maxlength="12" value="1" title="Qty" class="input-text prd_qty" />
                                                <button type="submit" title="Add to Cart" class="p-product-add">
                                                    <a class="add_cart">Add To Cart</a>
                                                </button>
                                                </form>
            
            <div class="like_count"><a href="#" class="action towishlist" data-action="add-to-wishlist"><span>';
        if ($customerSession->isLoggedIn()) {
            if (!in_array($productObj->getId(), $wishlist_id)) {
                $result.='<i class="fa fa-heart-o">';
            } else {
                $result.='<i class="fa fa-heart">';
            }
        } else {
            $result.='<i class="fa fa-heart-o">';
        }
        $result.=' </i></span></a></div>
         <div class="comment_count"><a href="#" ?><i class="fa fa-comment-o"></i></a> (';
        if ($RatingOb->getCount()) {
            $result.= $RatingOb->getCount();
        } else {
            $result.= '0';
        }

        $result.=')</div>
            
            <div class="clearfix"></div>
        </div>
        <ul class="peta_logo">
            <li><img src="' . $baseurl . '/pub/static/frontend/Bluethink/Theme/en_US/images/peta_logo_01.jpg"></li>
            <li><img src="' . $baseurl . '/pub/static/frontend/Bluethink/Theme/en_US/images/peta_logo_02.jpg"></li>
            <li><img src="' . $baseurl . '/pub/static/frontend/Bluethink/Theme/en_US/images/peta_logo_03.jpg"></li>
            <li><img src="' . $baseurl . '/pub/static/frontend/Bluethink/Theme/en_US/images/peta_logo_04.jpg"></li>
        <div class="clearfix"></div>
        </ul>
        <div class="prd_overview">
            <strong>Quick Overview</strong> : ' . $productObj->getShortDescription() . '...
            <div class="text-right"><a href=' . $productObj->getProductUrl() . '>View More...</a></div>
        </div>
    </div>
    <script>
    require(["jquery"],function(jQuery){
        jQuery(document).ready(function(){
    jQuery("#thumbs li").delegate("img","click", function(){
        alert("jji");
    jQuery("#largeImage").attr("src",jQuery(this).attr("src").replace("thumb","large"));
    
});
 });
    });
    </script>   
    ';

        echo $result;
        exit;
    }

}
