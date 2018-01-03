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
namespace Mageplaza\Shopbybrand\Model;

class Sitemap extends \Magento\Sitemap\Model\Sitemap{

	protected $helper;

	protected $router;

	protected $_brandFactory;

public function __construct(
	\Mageplaza\Shopbybrand\Helper\Data $helper,
	\Magento\Framework\Model\Context $context,
	\Magento\Framework\Registry $registry,
	\Magento\Framework\Escaper $escaper,
	\Magento\Sitemap\Helper\Data $sitemapData,
	\Magento\Framework\Filesystem $filesystem,
	\Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory $categoryFactory,
	\Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory $productFactory,
	\Magento\Sitemap\Model\ResourceModel\Cms\PageFactory $cmsFactory,
	\Magento\Framework\Stdlib\DateTime\DateTime $modelDate,
	\Magento\Store\Model\StoreManagerInterface $storeManager,
	\Magento\Framework\App\RequestInterface $request,
	\Magento\Framework\Stdlib\DateTime $dateTime,
	\Mageplaza\Shopbybrand\Model\BrandFactory $brandFactory,
	\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
	\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = []
)
{
	$this->helper=$helper;
	$this->router = $this->helper->getShopByBrandConfig('general/route');
	$this->_brandFactory = $brandFactory;
	parent::__construct($context, $registry, $escaper, $sitemapData, $filesystem, $categoryFactory, $productFactory, $cmsFactory, $modelDate, $storeManager, $request, $dateTime, $resource, $resourceCollection, $data);
}


	public function getBrandsSiteMapCollection(){
		$brandCollection=$this->_brandFactory->create()->getCollection();
		$brandSiteMapCollection=[];
		if (!$this->router) {
			$this->router = 'brands';
		}
		foreach ($brandCollection as $item){
				$images = null;
				if ($item->getImage()) :
					$imagesCollection[] = new \Magento\Framework\DataObject(
						[
							'url' => $item->getImage(),
							'caption' => null,
						]
					);
					$images=new \Magento\Framework\DataObject(['collection'=>$imagesCollection]);
				endif;
			$brandSiteMapCollection[$item->getId()]=new \Magento\Framework\DataObject([
					'id'=>$item->getId(),
					'url'=>$this->router.'/'.$item->getUrlKey().'.html',
					'images' => $images,
				]);
		}
		return $brandSiteMapCollection;
	}

	public function _initSitemapItems()
	{
		$this->_sitemapItems[] = new \Magento\Framework\DataObject(
			[
				'collection' => $this->getBrandsSiteMapCollection(),
			]
		);
		parent::_initSitemapItems();
	}
}