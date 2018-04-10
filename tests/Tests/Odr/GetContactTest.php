<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetContactTest extends UnitTestCase
{
    public function testNoHandle()
    {
        $hostfact = $this->getModule();

        self::assertFalse($hostfact->getContact(0));
    }

    public function testNotLoggedIn()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'public$success',
            )
        );

        self::assertFalse($hostfact->getContact(1));
    }

    public function testError()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'public$failure',
            )
        );

        self::assertFalse($hostfact->getContact(1));
    }

    public function testException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$thrown',
            )
        );

        self::assertFalse($hostfact->getContact(1));
    }
}