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
                'token'      => 'public$success',
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

    public function testErrorDomainListNoMessage()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'          => 'public$success',
                'api_secret'       => 'secret$success',
                'token'            => 'token$success',
                'tokenCheckDomain' => 'token$successnomessage',
            )
        );

        self::assertFalse($wefact->checkDomain('test.nl'));

        self::assertEquals(array('ODR: Incorrect response'), $wefact->Error);
    }

    public function testErrorDomainListInternal()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'          => 'public$success',
                'api_secret'       => 'secret$success',
                'token'            => 'token$success',
                'tokenCheckDomain' => 'token$successinternal',
            )
        );

        self::assertFalse($wefact->checkDomain('test.nl'));

        self::assertEquals(array('ODR: Someone wanted it!'), $wefact->Error);
    }
}