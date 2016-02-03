<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use Whois;

class CreateContactTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'public$success',
                'url'        => $wefact::URL_TEST,
            )
        );

        $whois = new Whois;

        self::assertFalse($wefact->createContact($whois));
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

        $whois = new Whois;

        self::assertFalse($wefact->createContact($whois));
    }

    public function testException()
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

        $whois = new Whois;

        self::assertFalse($wefact->createContact($whois));
    }

    public function testSuccess()
    {
        $wefact = $this->getModule();

        $whois = new Whois;

        $whois->ownerSex = 'm';

        self::assertEquals(1, $wefact->createContact($whois));
    }
}