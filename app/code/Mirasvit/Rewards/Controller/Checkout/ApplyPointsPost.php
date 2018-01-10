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
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Controller\Checkout;

use Magento\Framework\Controller\ResultFactory;

class ApplyPointsPost extends \Mirasvit\Rewards\Controller\Checkout
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function execute()
    {
		$points_amount=$_REQUEST['points_amount'];
		$removepoints=$_REQUEST['remove-points'];
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if($points_amount % 500 == 0 || $removepoints == 1){
			$response = $this->processRequest();
			if ($this->getRequest()->isXmlHttpRequest()) {
				echo json_encode($response);
				exit;
			}
			if ($response['success']) {
				$this->messageManager->addSuccess($response['message']);
			} elseif ($response['message']) {
				$this->messageManager->addError($response['message']);
			}
		}else{
			$this->messageManager->addError('Spend reward points in multiple of 500 only');
			$error_mess=array('error'=>'true','message'=>'Spend reward points in multiple of 500 only');
			echo json_encode($error_mess);
			exit;
		}
        return $this->_goBack();
    }
}
