<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class CheckLogoutTest extends UnitTestCase
{
    public function testNo()
    {
        $wefact = $this->getModule();
        $method = $this->getSecureMethod($wefact, '_checkLogout');

        self::assertNull($method->invoke($wefact, false));
    }

    public function testInvalidLogin()
    {
        $wefact = $this->getModule();
        $method = $this->getSecureMethod($wefact, '_checkLogout');

        $token = $this->getSecureProperty($wefact, 'AccessToken');

        $token->setValue($wefact, true);

        self::assertNull($method->invoke($wefact, true));

        self::assertFalse($token->getValue($wefact));
    }
}