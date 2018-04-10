<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class LockDomainTest extends UnitTestCase
{
    public function testTrue()
    {
        $hostfact = $this->getModule();

        self::assertFalse($hostfact->lockDomain('test.nl', true));
    }

    public function testFalse()
    {
        $hostfact = $this->getModule();

        self::assertFalse($hostfact->lockDomain('test.nl', false));
    }

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

        self::assertFalse($hostfact->lockDomain('test.nl', false));
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

        self::assertFalse($hostfact->lockDomain('test.nl', false));
    }
}