<?php

namespace Nuluv\Forgotpass\Controller\Magento\Customer\Account;


class ForgotPasswordPost extends \Magento\Customer\Controller\Account\ForgotPasswordPost
{
	
	protected function getSuccessMessage($email)
    {
        return __(
            'We are sending you an email with instructions to reset your password. If you don’t receive an email check your junk folder.',
            $this->escaper->escapeHtml($email)
        );
    }
}
	
	