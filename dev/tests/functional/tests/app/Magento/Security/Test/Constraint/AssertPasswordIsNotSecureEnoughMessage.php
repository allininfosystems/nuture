<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Security\Test\Constraint;

use Magento\Customer\Test\Page\CustomerAccountCreate;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertPasswordIsNotSecureEnoughMessage
 */
class AssertPasswordIsNotSecureEnoughMessage extends AbstractConstraint
{
    /**
     * Assert that appropriate message is displayed on "Create New Customer Account" page(frontend) if password is not
     * secure enough.
     *
     * @param CustomerAccountCreate $registerPage
     * @return void
     */
    public function processAssert(CustomerAccountCreate $registerPage)
    {
        $expectedErrorMessage = 'Password has be minimum of 8 characters and has to include a numeric,' .
            'uppercase and special characters without any space.';
        $errorMessage = $registerPage->getRegisterForm()->getPasswordError();
        \PHPUnit_Framework_Assert::assertEquals(
            $expectedErrorMessage,
            $errorMessage,
            'The messages are not equal.'
        );
    }

    /**
     * Assert that displayed error message is correct
     *
     * @return string
     */
    public function toString()
    {
        return 'Password insecure message is present on customer registration page.';
    }
}
