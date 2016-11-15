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
            )
        );

        self::assertTrue($wefact->deleteDomain('test.nl', 'fastest'));
    }

    public function testDError()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'           => 'public$success',
                'api_secret'        => 'secret$success',
                'token'             => 'token$success',
                'tokenDeleteDomain' => 'token$failure',
            )
        );

        self::assertFalse($wefact->deleteDomain('test.nl', 'fastest'));
    }

    public function testDException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'           => 'public$success',
                'api_secret'        => 'secret$success',
                'token'             => 'token$success',
                'tokenDeleteDomain' => 'token$thrown',
            )
        );

        self::assertFalse($wefact->deleteDomain('test.nl', 'fastest'));
    }

    public function testDInternal()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'           => 'public$success',
                'api_secret'        => 'secret$success',
                'token'             => 'token$success',
                'tokenDeleteDomain' => 'token$successinternal',
            )
        );

        self::assertFalse($wefact->deleteDomain('test.nl', 'fastest'));

        self::assertEquals(array('ODR: Testing'), $wefact->Error);
    }

    public function testDInternalNoMessage()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'           => 'public$success',
                'api_secret'        => 'secret$success',
                'token'             => 'token$success',
                'tokenDeleteDomain' => 'token$successnomessage',
            )
        );

        self::assertFalse($wefact->deleteDomain('test.nl', 'fastest'));

        self::assertEquals(array('ODR: Incorrectly formatted response'), $wefact->Error);
    }
}