<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use Whois;

class UpdateContactTest extends UnitTestCase
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

        $whois = new Whois;

        self::assertFalse($hostfact->updateContact(1, $whois));
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

        $whois = new Whois;

        self::assertFalse($hostfact->updateContact(1, $whois));
    }

    public function testException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$thrown',
            )
        );

        $whois = new Whois;

        self::assertFalse($hostfact->updateContact(1, $whois));
    }

    public function testSuccess()
    {
        $hostfact = $this->getModule();

        $whois = new Whois;

        $whois->ownerSex = 'm';

        self::assertTrue($hostfact->updateContact(1, $whois));
    }
}