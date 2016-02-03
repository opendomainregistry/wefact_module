<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class DeleteDomainTest extends UnitTestCase
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

        self::assertFalse($wefact->deleteDomain('test.nl'));
    }

    public function testEndInfoError()
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

        self::assertFalse($wefact->deleteDomain('test.nl', 'end'));
    }

    public function testEndInfoException()
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

        self::assertFalse($wefact->deleteDomain('test.nl', 'end'));
    }

    public function testEndInfoSuccess()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenInfo'  => 'token$success',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertTrue($wefact->deleteDomain('test.nl', 'end'));
    }

    public function testOtherError()
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

        self::assertFalse($wefact->deleteDomain('test.nl', 'fastest'));
    }

    public function testOtherException()
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

        self::assertFalse($wefact->deleteDomain('test.nl', 'fastest'));
    }

    public function testOtherSuccess()
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

        self::assertTrue($wefact->deleteDomain('test.nl', 'fastest'));
    }
}