<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use Whois;

class UpdateContactTest extends UnitTestCase
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

        $whois = new Whois;

        self::assertFalse($wefact->updateContact(1, $whois));
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

        $whois = new Whois;

        self::assertFalse($wefact->updateContact(1, $whois));
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

        $whois = new Whois;

        self::assertFalse($wefact->updateContact(1, $whois));
    }

    public function testSuccess()
    {
        $wefact = $this->getModule();

        $whois = new Whois;

        $whois->ownerSex = 'm';

        self::assertTrue($wefact->updateContact(1, $whois));
    }
}