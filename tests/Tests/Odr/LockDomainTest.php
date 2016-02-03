<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class LockDomainTest extends UnitTestCase
{
    public function testTrue()
    {
        $wefact = $this->getModule();

        self::assertFalse($wefact->lockDomain('test.nl', true));
    }

    public function testFalse()
    {
        $wefact = $this->getModule();

        self::assertFalse($wefact->lockDomain('test.nl', false));
    }

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

        self::assertFalse($wefact->lockDomain('test.nl', false));
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

        self::assertFalse($wefact->lockDomain('test.nl', false));
    }
}