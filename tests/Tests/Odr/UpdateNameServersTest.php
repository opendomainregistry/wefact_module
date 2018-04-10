<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class UpdateNameServersTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        self::assertFalse($hostfact->updateNameServers('test.nl'));
    }

    public function testInfoError()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenInfo'  => 'token$failure',
            )
        );

        self::assertFalse($hostfact->updateNameServers('test.nl'));
    }

    public function testInfoException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenInfo'  => 'token$thrown',
            )
        );

        self::assertFalse($hostfact->updateNameServers('test.nl'));
    }

    public function testError()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$failure',
            )
        );

        self::assertFalse($hostfact->updateNameServers('test.nl'));
    }

    public function testException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'     => 'public$success',
                'api_secret'  => 'secret$success',
                'token'       => 'token$success',
                'tokenUpdate' => 'token$thrown',
            )
        );

        self::assertFalse($hostfact->updateNameServers('test.nl'));
    }

    public function testSuccess()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        self::assertTrue($hostfact->updateNameServers('test.nl'));
    }
}