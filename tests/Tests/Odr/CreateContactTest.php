<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use Whois;

class CreateContactTest extends UnitTestCase
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

        self::assertFalse($hostfact->createContact($whois));
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

        self::assertFalse($hostfact->createContact($whois));
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

        self::assertFalse($hostfact->createContact($whois));
    }

    public function testSuccess()
    {
        $hostfact = $this->getModule();

        $whois = new Whois;

        $whois->ownerSex = 'm';

        self::assertEquals(1, $hostfact->createContact($whois));
    }
}