<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class UpdateNameServersTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->updateNameServers('test.nl'));
    }

    public function testInfoError()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenInfo'  => 'token$failure',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->updateNameServers('test.nl'));
    }

    public function testInfoException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenInfo'  => 'token$thrown',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->updateNameServers('test.nl'));
    }

    public function testError()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$failure',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->updateNameServers('test.nl'));
    }

    public function testException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'     => 'public$success',
                'api_secret'  => 'secret$success',
                'token'       => 'token$success',
                'tokenUpdate' => 'token$thrown',
                'url'         => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->updateNameServers('test.nl'));
    }

    public function testSuccess()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertTrue($wefact->updateNameServers('test.nl'));
    }
}