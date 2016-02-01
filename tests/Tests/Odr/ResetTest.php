<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class ResetTest extends UnitTestCase
{
    public function testLogged()
    {
        $wefact = $this->getModule();

        $this->getSecureProperty($wefact, 'AccessToken')->setValue($wefact, true);

        self::assertFalse($wefact->reset());
    }

    public function testInvalidFormat()
    {
        $wefact = $this->getModule();

        $wefact->User = 'failure';

        self::assertFalse($wefact->reset());
    }

    public function testInvalidLogin()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$secret',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->reset());
    }

    public function testException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$throw',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->reset());
    }

    public function testSuccess()
    {
        $wefact = $this->getModule();

        self::assertTrue($wefact->reset());

        self::assertEquals('token$success', $this->getSecureProperty($wefact, 'AccessToken')->getValue($wefact));
    }
}