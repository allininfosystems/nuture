<?php

/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

namespace WIO\Forum\Controller\Adminhtml\Adminsettings;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action {

  protected $_modelSettings;
  protected $_dataProcessor;

  /**
   * @var \Magento\Framework\Image\AdapterFactory
   */
  protected $_adapterFactory;

  /**
   * @var \Magento\MediaStorage\Model\File\UploaderFactory
   */
  protected $_uploader;

  /**
   * @var \Magento\Framework\Filesystem
   */
  protected $_filesystem;

  public function __construct(
  Action\Context $context, 
  PostDataProcessor $dataProcessor, 
  \WIO\Forum\Model\UsersettingsFactory $modelSettings, 
  \Magento\Framework\Image\AdapterFactory $adapterFactory, 
  \Magento\MediaStorage\Model\File\UploaderFactory $uploader, 
  \Magento\Framework\Filesystem $filesystem
  ) {
    $this->_dataProcessor = $dataProcessor;
    $this->_modelSettings = $modelSettings->create();
    $this->_adapterFactory = $adapterFactory;
    $this->_uploader = $uploader;
    $this->_filesystem = $filesystem;
    parent::__construct($context);
  }

  /**
   * {@inheritdoc}
   */
  protected function _isAllowed() {
    return $this->_authorization->isAllowed('WIO_Forum:forum_save_adminsettings');
  }

  public function execute() {
    $data = $this->getRequest()->getPostValue();
    $resultRedirect = $this->resultRedirectFactory->create();

    $model = $this->_modelSettings->load(\WIO\Forum\Helper\Constant::WIO_FORUM_ADMIN_ID, 'system_user_id');
    $user_id = $model->getId();
    $avatar  = $model->getAvatar();

    if ($data) {
      $data = $this->_dataProcessor->filter($data);
      $avatarNew = $this->saveAvatar();
      $model->setData($data);
      if($avatarNew) {
        $model->setAvatar($avatarNew);
      }elseif(!empty($data['avatar']['delete'])){
        $model->setAvatar(null);
      }elseif($avatar) {
        $model->setAvatar($avatar);
      }
      $model->setSystemUserId(\WIO\Forum\Helper\Constant::WIO_FORUM_ADMIN_ID);
      if ($user_id) {
        $model->setUserId($user_id);
      }

      try {
        $model->save();
        $this->messageManager->addSuccess(__('You saved admin settings.'));
      } catch (Exception $ex) {
        $this->messageManager->addException($ex, __('Something went wrong while saving the admin settings.'));
      }
    }
    return $resultRedirect->setPath('*/*/');
  }

  protected function saveAvatar() {
    if (isset($_FILES['avatar']) && isset($_FILES['avatar']['name']) && strlen($_FILES['avatar']['name'])) {
      /*
       * Save image upload
       */
      try {
        $base_media_path = \WIO\Forum\Helper\Constant::WIO_FORUM_AVATR_FILE_PATH;
        $uploader = $this->_uploader->create(
                ['fileId' => 'avatar']
        );
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
        $imageAdapter = $this->_adapterFactory->create();
        $uploader->addValidateCallback('avatar', $imageAdapter, 'validateUploadFile');
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        $mediaDirectory = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $result = $uploader->save(
          $mediaDirectory->getAbsolutePath($base_media_path)
        );
        $avatar = $base_media_path . $result['file'];
        return $avatar;
      } catch (\Exception $e) {
        if ($e->getCode() == 0) {
          var_dump($e->getMessage());
          $this->messageManager->addError($e->getMessage());
          return false;
        }
      }
    }
  }
  
}
