<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class CheckDomainTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'token$failure',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->checkDomain('test.nl'));
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

        self::assertFalse($wefact->checkDomain('test.nl'));
    }

    public function testException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'public$success',
                'token'      => 'token$thrown',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->checkDomain('test.nl'));
    }

    public function testTrue()
    {
        $wefact = $this->getModule();

        self::assertTrue($wefact->checkDomain('test.nl'));
    }

    public function testFalse()
    {
        $wefact = $this->getModule();

        self::assertFalse($wefact->checkDomain('test.eu'));
    }
}