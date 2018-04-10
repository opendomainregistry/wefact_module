<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class CheckLoginTest extends UnitTestCase
{
    public function testLogged()
    {
        $hostfact = $this->getModule();
        $method = $this->getSecureMethod($hostfact, '_checkLogin');

        $this->getSecureProperty($hostfact, 'AccessToken')->setValue($hostfact, true);

        self::assertTrue($method->invoke($hostfact));
    }

    public function testInvalidLogin()
    {
        $hostfact = $this->getModule();
        $method = $this->getSecureMethod($hostfact, '_checkLogin');

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$secret',
            )
        );

        self::assertFalse($method->invoke($hostfact));
    }
}