<?php

namespace Nuluv\Forgotpass\Controller\Magento\Customer\Account;

class ForgotPasswordPost extends \Magento\Customer\Controller\Account\ForgotPasswordPost
{
	
	protected function getSuccessMessage($email)
    {
        return __(
            'If there is an account associated with %1 you will receive an email with a link to reset your password.',
            $this->escaper->escapeHtml($email)
        );
    }
}
	
	