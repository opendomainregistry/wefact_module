<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class CheckLoginTest extends UnitTestCase
{
    public function testLogged()
    {
        $wefact = $this->getModule();
        $method = $this->getSecureMethod($wefact, '_checkLogin');

        $this->getSecureProperty($wefact, 'AccessToken')->setValue($wefact, true);

        self::assertTrue($method->invoke($wefact));
    }

    public function testInvalidLogin()
    {
        $wefact = $this->getModule();
        $method = $this->getSecureMethod($wefact, '_checkLogin');

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$secret',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($method->invoke($wefact));
    }
}