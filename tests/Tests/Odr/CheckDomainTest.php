<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class CheckDomainTest extends UnitTestCase
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

        self::assertFalse($hostfact->checkDomain('test.nl'));
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

        self::assertFalse($hostfact->checkDomain('test.nl'));
    }

    public function testException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'public$success',
                'token'      => 'token$thrown',
            )
        );

        self::assertFalse($hostfact->checkDomain('test.nl'));
    }

    public function testTrue()
    {
        $hostfact = $this->getModule();

        self::assertTrue($hostfact->checkDomain('test.nl'));
    }

    public function testFalse()
    {
        $hostfact = $this->getModule();

        self::assertFalse($hostfact->checkDomain('test.eu'));
    }

    public function testErrorDomainListNoMessage()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'          => 'public$success',
                'api_secret'       => 'secret$success',
                'token'            => 'token$success',
                'tokenCheckDomain' => 'token$successnomessage',
            )
        );

        self::assertFalse($hostfact->checkDomain('test.nl'));

        self::assertEquals(array('ODR: Incorrectly formatted response'), $hostfact->Error);
    }

    public function testErrorDomainListInternal()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'          => 'public$success',
                'api_secret'       => 'secret$success',
                'token'            => 'token$success',
                'tokenCheckDomain' => 'token$successinternal',
            )
        );

        self::assertFalse($hostfact->checkDomain('test.nl'));

        self::assertEquals(array('ODR: Someone wanted it!'), $hostfact->Error);
    }
}