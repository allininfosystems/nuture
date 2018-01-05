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

namespace WIO\Forum\Controller\Customer;

class Save extends \Magento\Framework\App\Action\Action {

  protected $_customerSession;
  protected $_dataProcessor;
  protected $_forumUserSettings;
  
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
    \Magento\Framework\App\Action\Context $context, 
    \Magento\Customer\Model\Session $customerSession, 
    \Magento\Framework\Image\AdapterFactory $adapterFactory, 
    \Magento\MediaStorage\Model\File\UploaderFactory $uploader, 
    PostDataProcessor $dataProcessor, 
    \WIO\Forum\Model\UsersettingsFactory $forumUserSettings,      
    \Magento\Framework\Filesystem $filesystem
  ) {
    $this->_customerSession = $customerSession;
    $this->_adapterFactory = $adapterFactory;
    $this->_uploader = $uploader;
    $this->_filesystem = $filesystem;
    $this->_dataProcessor = $dataProcessor;
    $this->_forumUserSettings = $forumUserSettings;
    parent::__construct($context);
  }

  public function dispatch(
    \Magento\Framework\App\RequestInterface $request
  ) {
    if (!$this->_customerSession->isLoggedIn()) {
      $this->_customerSession->setAfterAuthUrl($this->_url->getCurrentUrl());
      $this->_customerSession->authenticate();
    }
    return parent::dispatch($request);
  }

  public function execute() {
    if(!$this->_customerSession->getId()) {
      $this->_redirect('*/*');
    }
    $post = $this->getRequest()->getPostValue();
    if($post) {
      $data = $this->_dataProcessor->filter($post);
      $forumUserModel = $this->_forumUserSettings->create()->load($this->_customerSession->getId(), 'system_user_id');
      $forumUserModel->setSignature($post['signature']);
      $forumUserModel->setNickname($post['nickname']);
      $forumUserModel->setSystemUserId($this->_customerSession->getId());
      $avatarNew = $this->saveAvatar();
      if($avatarNew) {
        $forumUserModel->setAvatar($avatarNew);
      }elseif(!empty($data['avatar']['delete'])){
        $forumUserModel->setAvatar(null);
      }
      $forumUserModel->save();
      $this->messageManager->addSuccess('You succesfully updated your forum settings');
    }
    $this->_redirect('*/*');
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
