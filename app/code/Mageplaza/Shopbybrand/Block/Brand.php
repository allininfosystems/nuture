<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Shopbybrand\Block;

/**
 * Class Brand
 *
 * @package Mageplaza\Shopbybrand\Block
 */
class Brand extends \Magento\Framework\View\Element\Template
{
    //*********************** Seo Meta Robot vars ******************************
    public $mpRobots;
    /**
     * @type \Mageplaza\Shopbybrand\Helper\Data
     */
    protected $helper;

    //********************** Product quantity vars *****************************
    protected $_productCollectionFactory;
    protected $_visibleProduts;

    //********************** Category List vars ********************************
    /**
     * @var \Mageplaza\Shopbybrand\Model\CategoryFactory
     */
    protected $_categoryFactory;

    //********************** Brand List vars ********************************
    /**
     * @type \Mageplaza\Shopbybrand\Model\BrandFactory
     */
    protected $_brandFactory;
    /**
     * @type string
     */
    protected $_char = '';

    //********************** Class View usage vars *****************************

    protected $_coreRegistry;
    protected $_blockFactory;

    //********************** Resize Image vars *****************************

    protected $_imageFactory;
    protected $_filesystem ;

    /**
     * Brand constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context               $context
     * @param \Magento\Catalog\Model\Product\Visibility                      $visibleProduts
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Mageplaza\Shopbybrand\Model\CategoryFactory                   $categoryFactory
     * @param \Mageplaza\Shopbybrand\Model\BrandFactory                      $brandFactory
     * @param \Magento\Framework\Registry                                    $coreRegistry
     * @param \Magento\Cms\Model\BlockFactory                                $blockFactory
     * @param \Magento\Framework\Image\AdapterFactory                        $imageFactory
     * @param \Mageplaza\Shopbybrand\Helper\Data                             $helper
     * @param array                                                          $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Product\Visibility $visibleProduts,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Mageplaza\Shopbybrand\Model\CategoryFactory $categoryFactory,
        \Mageplaza\Shopbybrand\Model\BrandFactory $brandFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Mageplaza\Shopbybrand\Helper\Data $helper,
        array $data = []
    ) {
        $this->_visibleProduts            = $visibleProduts;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_brandFactory    = $brandFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_blockFactory = $blockFactory;
        $this->_imageFactory = $imageFactory;
        $this->_filesystem = $context->getFilesystem();
        $this->helper = $helper;
        parent::__construct($context,$data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $category = $objectManager->create('\Mageplaza\Shopbybrand\Model\CategoryFactory');
        $this->mpRobots = $objectManager->create('\Mageplaza\Shopbybrand\Model\Config\Source\MetaRobots');
        $action = $this->getRequest()->getFullActionName();

        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            if ($action == 'mpbrand_index_index' || $action == 'mpbrand_index_view' ||  $action == 'mpbrand_category_view') {
                $breadcrumbsBlock->addCrumb(
                    'home',
                    ['label' => __('Home'), 'title' => __('Go to Home Page'),
                     'link'  => $this->_storeManager->getStore()->getBaseUrl()]
                );

                $this->additionCrumb($breadcrumbsBlock);
                if ($action == 'mpbrand_category_view') {

                    $catID = $this->getRequest()->getParams();
                    if ($category->create()->load($catID)->getData()) {
                        $breadcrumbsBlock->addCrumb(
                            'brand', ['label' => __($this->getPageTitle()),
                                      'link'  => $this->helper()->getBrandUrl()]
                        )->addCrumb(
                            $category->create()->load($catID)->getUrlKey(),
                            ['label' => $category->create()->load($catID)
                                ->getName(),
                             'title' => $category->create()->load($catID)
                                 ->getName()]
                        );
                    }
                    $this->pageConfig->getTitle()->set(
                        $category->create()->load($catID)->getName()
                    );

                    $this->applySeoCode($category->create()->load($catID));
                }
                else {
                    $this->pageConfig->getTitle()->set($this->getMetaTitle());
                }
            }
        }
        return parent::_prepareLayout();
    }

    /**
     * @param $block
     *
     * @return $this
     */
    protected function additionCrumb($block)
    {

        $title = $this->getPageTitle();
        $block->addCrumb('brand', ['label' => __($title)]);

        return $this;
    }

    /**
     * @return \Mageplaza\Shopbybrand\Helper\Data
     */
    public function helper()
    {
        return $this->helper;
    }

    /**
     * Retrieve HTML title value separator (with space)
     *
     * @param null|string|bool|int|Store $store
     *
     * @return string
     */
    public function getTitleSeparator($store = null)
    {
        $separator = (string)$this->_scopeConfig->getValue(
            'catalog/seo/title_separator',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store
        );

        return ' ' . $separator . ' ';
    }

    //*********************** Get brand page list configure ***********************
    /**
     * @param $brand
     * @return string
     */
    public function getFilterClass($brand)
    {
        return $this->helper()->getFilterClass($brand);
    }
    /**
     * Is show description below Brand name
     *
     * @return mixed
     */
    public function showDescription()
    {
        return $this->helper->getBrandConfig('show_description');
    }

    /**
     * Is show product quantity near Brand name
     * @return mixed
     */
    public function showProductQty()
    {
        return $this->helper->getShopByBrandConfig('brandpage/show_product_qty');
    }

    /**
     * Is show quick view near Brand name
     * @return mixed
     */
    public function showQuickView()
    {
        return $this->helper->getShopByBrandConfig('brandpage/show_quick_view');
    }

    /*************** Get Brand, Category collection function  ******************
    /**
     * @param null $type
     * @param null $ids
     * @param null $char
     *
     * @return mixed|string
     */
    public function getCollection($type = null, $ids = null, $char = null)
    {
        $brandList = '';
        if ($type == null) {
            $brandList = $this->helper()->getBrandList();
        }elseif ($type == \Mageplaza\Shopbybrand\Helper\Data::CATEGORY) {
            $brandList = $this->helper()->getBrandList(\Mageplaza\Shopbybrand\Helper\Data::CATEGORY,$ids);
        }elseif ($type == \Mageplaza\Shopbybrand\Helper\Data::BRAND_FIRST_CHAR) {
            $brandList = $this->helper()->getBrandList(\Mageplaza\Shopbybrand\Helper\Data::BRAND_FIRST_CHAR, $ids, $char);
        }
        return $brandList;
    }

    /***************************** Apply seo code, Set meta title , meta keyword functions *****************************
    /**
     * @param null $post
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function applySeoCode($category = null)
    {
        if ($category) {
            $title = $category->getMetaTitle();
            $this->setPageData($title, 1, $category->getName());

            $description = $category->getMetaDescription();
            $this->setPageData($description, 2, $description);

            $keywords = $category->getMetaKeywords();
            $this->setPageData($keywords, 3, $keywords);

            $robot = $category->getMetaRobots();
            $array = $this->mpRobots->getOptionArray();
            $this->setPageData($array[$robot], 4);
            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle($category->getName());
            }
        }
    }

    /**
     * @return mixed
     */
    public function getPageTitle()
    {

        return $this->helper->getBrandConfig('name') ?: __('Brands');
    }

    /**
     * @return mixed
     */
    public function getMetaTitle()
    {
        return $this->getPageTitle();
    }
    /**
     * @param      $data
     * @param      $type
     * @param null $name
     *
     * @return string|void
     */
    public function setPageData($data, $type, $name = null)
    {
        if ($data) {
            return $this->setDataFromType($data, $type);
        }

        return $this->setDataFromType($name, $type);
    }

    /**
     * @param $data
     * @param $type
     *
     * @return $this|string|void
     */
    public function setDataFromType($data, $type)
    {
        switch ($type) {
            case 1:
                return $this->pageConfig->getTitle()->set($data);
                break;
            case 2:
                return $this->pageConfig->setDescription($data);
                break;
            case 3:
                return $this->pageConfig->setKeywords($data);
                break;
            case 4:
                return $this->pageConfig->setRobots($data);
                break;
        }

        return '';
    }
    /***************************** Get ProductQuantity function in Brandlist template *****************************
    /**
     *
     * @param $optionId
     *
     * @return int
     */
    public function getProductQuantity($optionId)
    {
        $attCode  = $this->helper->getAttributeCode();
        $collection = $this->_productCollectionFactory->create()->setVisibility($this->_visibleProduts->getVisibleInCatalogIds())
            ->addAttributeToSelect('*')->addAttributeToFilter($attCode, ['eq' => $optionId]);
        return $collection->getSize();
    }

    /*********************** get the first char brand value from collection to filte r***********************
    /**
     * @return array
     */
    public function getFirstChar()
    {
        $char = [];
        $action = $this->getRequest()->getFullActionName();
        $collection = ($action == 'mpbrand_category_view') ? $this->getCollection(\Mageplaza\Shopbybrand\Helper\Data::CATEGORY,$this->getOptionIds()) : $this->getCollection();
        foreach ($collection as $brand => $item)
        {
            if ($this->helper()->getShopByBrandConfig('brandpage/brand_filter/encode_key')){
                $char [] = mb_substr($item['value'],0,1,$this->helper()->getShopByBrandConfig('brandpage/brand_filter/encode_key'));

            }else{

                $char [] = mb_substr($item['value'],0,1,'UTF-8');
            }
        }
        $char = array_unique($char);
        sort($char);
        return $char;
    }

    /**
     * @return array
     */
    public function getOptionIds()
    {
        $catId  = $this->getRequest()->getParam('cat_id');
        $result = [];
        $sql = 'main_table.cat_id IN ('.$catId.')';
        $brands = $this->_categoryFactory->create()->getCategoryCollection($sql,null)->getData();
        foreach ($brands as $brand => $item) {
            $result[] = $item['option_id'];
        }

        return $result;
    }

    //*********************** Resize Image Function ***********************

    /**
     * @param $image
     *
     * @return string
     */
    public function getImageUrl($image)
    {
        return $this->helper->getBrandImageUrl($image);
    }
    /*
    * var Image Name, Width and Height
    * resize Image Function
    */
    public function resizeImage($image, $width = null, $height = null)
    {

        $absolutePath = $this->getImageUrl($image);

        $imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('mageplaza/resized/'.$width.'/').$image->getImage();

        //create image factory...
        $imageResize = $this->_imageFactory->create();
        $imageResize->open($absolutePath);
        $imageResize->constrainOnly(TRUE);
        $imageResize->keepTransparency(TRUE);
        $imageResize->keepFrame(FALSE);
        $imageResize->keepAspectRatio(TRUE);
        $imageResize->resize($width,$height);
        //destination folder
        $destination = $imageResized ;
        //save image
        $imageResize->save($destination);

        $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'mageplaza/resized/'.$width.'/'.$image->getImage();
        return $resizedURL;
    }

    /***************************** Get AlphaBet Collection *****************************
    /**
     * @return array
     */
    public function getAlphaBet()
    {
        $action = $this->getRequest()->getFullActionName();
        //Get Category Collection OR Brand List Collection
        switch ($action) {
            case 'mpbrand_category_view' :
                $collection = $this->getCollection(\Mageplaza\Shopbybrand\Helper\Data::CATEGORY,$this->getOptionIds());
                break;

            case 'mpbrand_index_index' :
                $collection = $this->getCollection();
                break;

            default :
                $collection = $this->getCollection();
        }

        $this->_char = array_unique(explode(',',str_replace(' ', '', $this->helper()->getShopByBrandConfig('brandpage/brand_filter/alpha_bet'))));

        //remove empty  field in array
        foreach ($this->_char as $offset => $row){
            if ('' == trim($row)){
                unset($this->_char[$offset]);
            }
        }
        //set default alphabet if leave alphabet config blank
        if (empty($this->_char)){
            $this->_char = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        }
        $alphaBet    = [];
        $activeChars = [];
        foreach ($collection as $brand) {
            $name = $brand->getValue();
            if (is_string($name) && strlen($name) > 0) {
                if ($this->helper()->getShopByBrandConfig('brandpage/brand_filter/encode_key')){
                    $firstChar =  mb_substr($name,0,1,$this->helper()->getShopByBrandConfig('brandpage/brand_filter/encode_key'));
                }else{
                    $firstChar =  mb_substr($name,0,1,'UTF-8');
                }
                if (!in_array($firstChar, $activeChars)) {
                    $activeChars[] = $firstChar;
                }
            }
        }
        foreach ($this->_char as $item){
            $alphaBet[] = [
                'char'   => $item,
                'active' => in_array($item, $activeChars)
            ];
        }
//        $alphaBet[] = [
//            'char'   => 'num',
//            'label'  => '0-9',
//            'active' => in_array('0-9', $activeChars)
//        ];
        return $alphaBet;
    }

    //*********************** Get Category Collection ***********************

    public function getCategories()
    {
        return $this->helper()->getCategoryList();
    }

    //*********************** Check layout  ***********************
    /**
     * @return bool
     */
    public function checkAction(){
        $action = $this->getRequest()->getFullActionName();
        if ($action =='mpbrand_category_view'){
            return true;
        }
        return false;
    }

    //*********************** Get Brand Quantity each Category ***********************
    public function getBrandQty($catId)
    {
        $sql = 'main_table.cat_id IN ('.$catId.')';
        $brands = $this->_categoryFactory->create()->getCategoryCollection($sql,null);
        $result = $brands->getSize();
        return (string)$result;
    }
}
