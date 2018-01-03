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

use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Mirasvit\Kb\Controller\Adminhtml\Article
{
    /**
     *
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('article_id');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select article(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = $this->articleFactory->create()
                        ->load($id);
                    $model->delete();
                }
                $this->messageManager->addSuccess(
                    __('Total of %1 record(s) were successfully deleted', count($ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
