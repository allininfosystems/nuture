<?php
namespace Bluethink\Dailydose\Controller\Adminhtml\Brand;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Magento\Backend\App\Action
{
   /**
* @var \Magento\Framework\View\Result\PageFactory
*/
/**
* @var \Magento\Framework\View\Result\PageFactory
*/
 
/**
* @param \Magento\Framework\App\Action\Context $context
 
*/
 
public function __construct(
\Magento\Backend\App\Action\Context $context,
\Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
 
) {
 
$this->_fileUploaderFactory = $fileUploaderFactory;
parent::__construct($context);
 
}
    public function execute()
    {
      $data = $this->getRequest()->getParams();
      // echo "<pre>===";
      // print_r($data);
      // exit;
        if ($data) {
            $model = $this->_objectManager->create('Bluethink\Dailydose\Model\Brand');

    
        
             if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {

                try {
                        $uploader = $this->_fileUploaderFactory->create(array('fileId' => 'image'));
                        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(true);

                        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                            ->getDirectoryRead(DirectoryList::MEDIA);
                        $result = $uploader->save($mediaDirectory->getAbsolutePath('bluethink/brand/images'));
                       
                        $data['image'] = $result['file'];
                        
                } catch (Exception $e) {
                    $data['image'] = $_FILES['image']['file'];
                }
            }
            else{
                $data['image'] = $data['image']['value'];
            } 


            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            
            $model->setData($data);
            
            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Frist Grid Has been Saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('banner_id' => $this->getRequest()->getParam('banner_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
}
