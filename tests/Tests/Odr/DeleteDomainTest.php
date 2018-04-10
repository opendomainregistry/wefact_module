<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class DeleteDomainTest extends UnitTestCase
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

        self::assertFalse($hostfact->deleteDomain('test.nl'));
    }

    public function testEndInfoError()
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

        self::assertFalse($hostfact->deleteDomain('test.nl', 'end'));
    }

    public function testEndInfoException()
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

        self::assertFalse($hostfact->deleteDomain('test.nl', 'end'));
    }

    public function testEndInfoSuccess()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenInfo'  => 'token$success',
            )
        );

        self::assertTrue($hostfact->deleteDomain('test.nl', 'end'));
    }

    public function testOtherError()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$failure',
            )
        );

        self::assertFalse($hostfact->deleteDomain('test.nl', 'fastest'));
    }

    public function testOtherException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$thrown',
            )
        );

        self::assertFalse($hostfact->deleteDomain('test.nl', 'fastest'));
    }

    public function testOtherSuccess()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        self::assertTrue($hostfact->deleteDomain('test.nl', 'fastest'));
    }

    public function testDError()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'           => 'public$success',
                'api_secret'        => 'secret$success',
                'token'             => 'token$success',
                'tokenDeleteDomain' => 'token$failure',
            )
        );

        self::assertFalse($hostfact->deleteDomain('test.nl', 'fastest'));
    }

    public function testDException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'           => 'public$success',
                'api_secret'        => 'secret$success',
                'token'             => 'token$success',
                'tokenDeleteDomain' => 'token$thrown',
            )
        );

        self::assertFalse($hostfact->deleteDomain('test.nl', 'fastest'));
    }

    public function testDInternal()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'           => 'public$success',
                'api_secret'        => 'secret$success',
                'token'             => 'token$success',
                'tokenDeleteDomain' => 'token$successinternal',
            )
        );

        self::assertFalse($hostfact->deleteDomain('test.nl', 'fastest'));

        self::assertEquals(array('ODR: Testing'), $hostfact->Error);
    }

    public function testDInternalNoMessage()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'           => 'public$success',
                'api_secret'        => 'secret$success',
                'token'             => 'token$success',
                'tokenDeleteDomain' => 'token$successnomessage',
            )
        );

        self::assertFalse($hostfact->deleteDomain('test.nl', 'fastest'));

        self::assertEquals(array('ODR: Incorrectly formatted response'), $hostfact->Error);
    }
}