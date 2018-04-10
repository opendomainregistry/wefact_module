<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetDomainWhoisTest extends UnitTestCase
{
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

        self::assertFalse($hostfact->getDomainWhois('test.nl'));
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

        self::assertFalse($hostfact->getDomainWhois('test.nl'));
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

        self::assertFalse($hostfact->getDomainWhois('test.nl'));
    }

    public function testSuccess()
    {
        $hostfact = $this->getModule();

        $expected = array(
            'ownerHandle' => 24,
            'adminHandle' => 32,
            'techHandle'  => null,
        );

        self::assertEquals($expected, $hostfact->getDomainWhois('test.nl'));
    }
}