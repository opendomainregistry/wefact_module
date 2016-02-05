<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetDomainWhoisTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'public$success',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->getDomainWhois('test.nl'));
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

        self::assertFalse($wefact->getDomainWhois('test.nl'));
    }

    public function testException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$thrown',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->getDomainWhois('test.nl'));
    }

    public function testSuccess()
    {
        $wefact = $this->getModule();

        $expected = array(
            'ownerHandle' => 24,
            'adminHandle' => 32,
            'techHandle'  => null,
        );

        self::assertEquals($expected, $wefact->getDomainWhois('test.nl'));
    }
}