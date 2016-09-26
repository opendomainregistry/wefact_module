<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class SetDomainAutoRenewTest extends UnitTestCase
{
    public function testTrue()
    {
        $wefact = $this->getModule();

        self::assertTrue($wefact->setDomainAutoRenew('test.nl', true));
    }

    public function testFalse()
    {
        $wefact = $this->getModule();

        self::assertTrue($wefact->setDomainAutoRenew('test.nl', false));
    }

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

        self::assertFalse($wefact->setDomainAutoRenew('test.nl', false));
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

        self::assertFalse($wefact->setDomainAutoRenew('test.nl', false));
    }

    public function testException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$thrown',
            )
        );

        self::assertFalse($wefact->setDomainAutoRenew('test.nl', false));
    }
}