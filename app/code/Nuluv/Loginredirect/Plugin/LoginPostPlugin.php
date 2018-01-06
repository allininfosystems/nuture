<?php

/**
 *
 */
namespace Nuluv\Loginredirect\Plugin;
use Magento\Framework\App\Response\RedirectInterface as RedirectInterface;

/**
 *
 */
class LoginPostPlugin
{

    /**
     * Change redirect after login to home instead of dashboard.
     *
     * @param \Magento\Customer\Controller\Account $subject
     * @param \Magento\Framework\Controller\Result\Redirect $result
     */
	 public function __construct(
       RedirectInterface $redirect
   )
   {
       $this->_redirect = $redirect;
   }
    public function afterExecute(
        \Magento\Customer\Controller\Account\LoginPost $subject,
        $result)
    {
		
        $result->setUrl($this->_redirect->getRefererUrl()); // Change this to what you want
        return $result;
    }

}