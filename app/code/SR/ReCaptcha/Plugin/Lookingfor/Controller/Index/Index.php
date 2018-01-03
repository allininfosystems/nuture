<?php

namespace SR\ReCaptcha\Plugin\Lookingfor\Controller\Index;

class Index
{
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \SR\ReCaptcha\Helper\Data $dataHelper
     */
    protected $dataHelper;

    /**
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \SR\ReCaptcha\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \SR\ReCaptcha\Helper\Data $dataHelper
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
        $this->dataHelper = $dataHelper;
    }

    public function aroundExecute(
        \Nuluv\Lookingfor\Controller\Index\Index $subject,
        \Closure $proceed
    ) {
		//echo $subject->getRequest()->getPost('g-recaptcha-response');
		//die('testing');
		/* $secretKey = $this->dataHelper->getSecretKey();
		echo $secretKey;
		echo '<br/>';
		echo $_SERVER['REMOTE_ADDR'];
		die('ttt'); */
        if($this->dataHelper->isEnabled()) {
            $recaptcha_response_field = $subject->getRequest()->getPost('g-recaptcha-response');
            if($recaptcha_response_field) {
                $secretKey = $this->dataHelper->getSecretKey();
                $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$recaptcha_response_field."&remoteip=".$_SERVER['REMOTE_ADDR']);
                $result = json_decode($response, true);
				echo '<pre>';print_r($result);die('ssss');
                if(isset($result['success']) && ($result['success'])) {
                    return $proceed();
                } else {
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $this->messageManager->addError(
                        __('There was an error with the recaptcha code, please try again.')
                    );
                    $resultRedirect->setPath('lookingfor/index');
                    return $resultRedirect;
                }
            } /* else {
                $this->messageManager->addError(
                    __('There was an error with the recaptcha code, please try again.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('lookingfor/index/index');
                return $resultRedirect;
            } */
        }

        return $proceed();
    }
}