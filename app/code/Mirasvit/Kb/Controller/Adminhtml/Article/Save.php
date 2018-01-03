<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-kb
 * @version   1.0.26
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Kb\Controller\Adminhtml\Article;

class Save extends \Mirasvit\Kb\Controller\Adminhtml\Article
{
   
    public function execute()
    {
        if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '')
        {
            if(isset($_FILES['image']))
            {
            
                $file_name   = $_FILES['image']['name'];
                $file_size   = $_FILES['image']['size'];
                $file_tmp    = $_FILES['image']['tmp_name'];
                $file_type   = $_FILES['image']['type'];
                $expensions  = array("jpeg","jpg","png");
                $extension   = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $temp        = explode(".",$_FILES["image"]["name"]);
                $img1        = $temp[0].rand(1,99999).'.'.$extension;
                $path        = $_SERVER['DOCUMENT_ROOT'].'/nuture/pub/media/kb/'.$img1;
                move_uploaded_file($_FILES['image']['tmp_name'],$path);
            }
        }
        if ($data = $this->getRequest()->getParams()) {
            $model = $this->_initModel();
            if (!empty($data['categories'])) {
                $data['category_ids'] = explode(',', $data['categories']);
                if (!is_array($data['store_ids'])) {
                    $data['store_ids'] = explode(',', $data['store_ids']);
                }

                $categoryIds = [];
                $articleStoreIds = $this->articleManagement->getAvailableStores($model, $data['category_ids']);

                if (empty($data['store_ids'])) {
                    $data['store_ids'] = $articleStoreIds;
                } else {
                    if (in_array(0, $articleStoreIds)) { // if for all stores
                        $categoryIds = $data['store_ids'];
                    } else {
                        foreach ($data['store_ids'] as $key => $storeId) {
                            if (in_array($storeId, $articleStoreIds)) {
                                $categoryIds[] = $data['store_ids'][$key];
                            }
                        }
                    }
                    $data['store_ids'] = array_unique($categoryIds);
                }
            }

            $model->addData($data);
            $this->kbTag->setTags($model, $data['tags']);
            $this->kbData->setRating($model);

            try {
                $model->save();
                $Articalid=$model->getArticleId();
                /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                $connection->query("INSERT INTO `test` ( `test_id`,`image`) VALUES ('".$Articalid."','".$img1."')"); */
                $this->messageManager->addSuccess(__('Article was successfully saved'));
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->backendSession->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

                return;
            }
        }
        $this->messageManager->addError(__('Unable to find article to save'));
        $this->_redirect('*/*/');
    }

    /*public function _uploadFile($file){
        $uploader = $this->_fileUploaderFactory->create(array('fileId' => 'image'));
        $uploader->setAllowedExtensions(array('jpg','png','jpeg'));
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        $mediaDirectory = $this->_objectManager
                        ->get('Magento\Framework\Filesystem')
                        ->getDirectoryRead(DirectoryList::MEDIA);

        $_result = $uploader->save($mediaDirectory->getAbsolutePath('/kb'));
        return $_result;
    }*/
}
