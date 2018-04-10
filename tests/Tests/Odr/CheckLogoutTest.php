<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class CheckLogoutTest extends UnitTestCase
{
    public function testNo()
    {
        $hostfact = $this->getModule();
        $method = $this->getSecureMethod($hostfact, '_checkLogout');

        self::assertNull($method->invoke($hostfact, false));
    }

    public function testInvalidLogin()
    {
        $hostfact = $this->getModule();
        $method = $this->getSecureMethod($hostfact, '_checkLogout');

        $token = $this->getSecureProperty($hostfact, 'AccessToken');

        $token->setValue($hostfact, true);

        self::assertNull($method->invoke($hostfact, true));

        self::assertTrue($token->getValue($hostfact));
    }
}