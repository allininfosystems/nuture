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
namespace Mageplaza\Shopbybrand\Plugin\Controller;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Download sample file controller
 */
class ImportDownloadSample
{
	/**
	 * Import file name
	 */
	const IMPORT_FILE = 'mageplaza_brand';

	/**
	 * Module name
	 */
	const SAMPLE_FILES_MODULE = 'Mageplaza_Shopbybrand';

	/**
	 * @type \Magento\Framework\App\Request\Http
	 */
	protected $request;

	/**
	 * @var \Magento\Framework\Controller\Result\RawFactory
	 */
	protected $resultRawFactory;

	/**
	 * @var \Magento\Framework\Filesystem\Directory\ReadFactory
	 */
	protected $readFactory;

	/**
	 * @var \Magento\Framework\Component\ComponentRegistrar
	 */
	protected $componentRegistrar;

	/**
	 * @var \Magento\Framework\App\Response\Http\FileFactory
	 */
	protected $fileFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
	 * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
	 * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
	 * @param \Magento\Framework\Component\ComponentRegistrar $componentRegistrar
	 * @param \Magento\Framework\App\Request\Http $request
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
		\Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
		\Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
		ComponentRegistrar $componentRegistrar,
		\Magento\Framework\App\Request\Http $request
	)
	{
		$this->fileFactory           = $fileFactory;
		$this->resultRawFactory      = $resultRawFactory;
		$this->readFactory           = $readFactory;
		$this->componentRegistrar    = $componentRegistrar;
		$this->request               = $request;
		$this->resultRedirectFactory = $context->getResultRedirectFactory();
		$this->messageManager        = $context->getMessageManager();
	}

	/**
	 * @param \Magento\ImportExport\Controller\Adminhtml\Import\Download $download
	 * @param \Closure $proceed
	 * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Raw
	 * @throws \Exception
	 */
	public function aroundExecute(\Magento\ImportExport\Controller\Adminhtml\Import\Download $download, \Closure $proceed)
	{
		if ($this->request->getParam('filename') != self::IMPORT_FILE) {
			return $proceed();
		}

		$fileName         = $this->request->getParam('filename') . '.csv';
		$moduleDir        = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, self::SAMPLE_FILES_MODULE);
		$fileAbsolutePath = $moduleDir . '/Files/Sample/' . $fileName;
		$directoryRead    = $this->readFactory->create($moduleDir);
		$filePath         = $directoryRead->getRelativePath($fileAbsolutePath);

		if (!$directoryRead->isFile($filePath)) {
			/* @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
			$this->messageManager->addError(__('There is no sample file for this entity.'));
			$resultRedirect = $this->resultRedirectFactory->create();
			$resultRedirect->setPath('*/import');

			return $resultRedirect;
		}

		$fileSize = isset($directoryRead->stat($filePath)['size'])
			? $directoryRead->stat($filePath)['size'] : null;

		$this->fileFactory->create(
			$fileName,
			null,
			DirectoryList::VAR_DIR,
			'application/octet-stream',
			$fileSize
		);

		/* @var \Magento\Framework\Controller\Result\Raw $resultRaw */
		$resultRaw = $this->resultRawFactory->create();
		$resultRaw->setContents($directoryRead->readFile($filePath));

		return $resultRaw;
	}
}
