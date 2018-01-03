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
namespace Mageplaza\Shopbybrand\Controller\Adminhtml\Attribute;

use Magento\Framework\App\Filesystem\DirectoryList;
use Mageplaza\Shopbybrand\Helper\Data as BrandHelper;

/**
 * Class Save
 * @package Mageplaza\Shopbybrand\Controller\Adminhtml\Attribute
 */
class Save extends \Magento\Backend\App\Action
{
	/**
	 * @type \Magento\Framework\Json\Helper\Data
	 */
	protected $_jsonHelper;

	/**
	 * @type \Mageplaza\Shopbybrand\Model\BrandFactory
	 */
	protected $_brandFactory;

	/**
	 * @type \Magento\Framework\Filesystem
	 */
	protected $_fileSystem;

	/**
	 * @type \Magento\Framework\View\Result\PageFactory
	 */
	protected $_resultPageFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Mageplaza\Shopbybrand\Helper\Data $brandHelper
	 * @param \Magento\Framework\Json\Helper\Data $jsonHelper
	 * @param \Mageplaza\Shopbybrand\Model\BrandFactory $brandFactory
	 * @param \Magento\Framework\Filesystem $fileSystem
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Mageplaza\Shopbybrand\Helper\Data $brandHelper,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Mageplaza\Shopbybrand\Model\BrandFactory $brandFactory,
		\Magento\Framework\Filesystem $fileSystem,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		parent::__construct($context);

		$this->_brandHelper       = $brandHelper;
		$this->_jsonHelper        = $jsonHelper;
		$this->_brandFactory      = $brandFactory;
		$this->_fileSystem        = $fileSystem;
		$this->_resultPageFactory = $resultPageFactory;
	}

	/**
	 * execute
	 */
	public function execute()
	{
		$result = ['success' => true];
		$data   = $this->getRequest()->getPostValue();

		$this->_uploadImage($data, $result);

		if ($result['success']) {
			$data['url_key'] = isset($data['url_key']) ? $this->_brandHelper->formatUrlKey($data['url_key']) : '';
			try {
				$brand = $this->_brandFactory->create();

				if ($data['store_id'] != \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
					$defaultBrand = $brand->loadByOption($data['option_id'], \Magento\Store\Model\Store::DEFAULT_STORE_ID);
					if (!$defaultBrand->getBrandId()) {
						$brand->setData($data)
							->setId(null)
							->setStoreId(0)
							->save();
					}
				}

				$brand->setData($data)
					->setId($this->getRequest()->getParam('id'))
					->save();

				$resultPage     = $this->_resultPageFactory->create();
				$result['html'] = $resultPage->getLayout()->getBlock('brand.attribute.html')
					->setOptionData($brand->getData())
					->toHtml();

				$result['message'] = __('Brand option has been saved successfully.');
			} catch (\Exception $e) {
				$result['success'] = false;
				$result['message'] = $e->getMessage();//__('An error occur. Please try again later.');
			}
		}

		$this->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
	}

	/**
	 * @param $data
	 * @param $result
	 * @return $this
	 */
	protected function _uploadImage(&$data, &$result)
	{
		if (isset($data['image']) && isset($data['image']['delete']) && $data['image']['delete']) {
			$data['image'] = '';
		} else {
			try {
				$uploader = $this->_objectManager->create(
					'Magento\MediaStorage\Model\File\Uploader',
					['fileId' => 'image']
				);
				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
				$uploader->setAllowRenameFiles(true);

				$image = $uploader->save(
					$this->_fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(BrandHelper::BRAND_MEDIA_PATH)
				);

				$data['image'] = BrandHelper::BRAND_MEDIA_PATH . '/' . $image['file'];
			} catch (\Exception $e) {
				$data['image'] = isset($data['image']['value']) ? $data['image']['value'] : '';
				if ($e->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
					$result['success'] = false;
					$result['message'] = $e->getMessage();
				}
			}
		}

		return $this;
	}
}
