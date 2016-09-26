<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \Whois;

class GetContactHandleTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $wefact = $this->getModule();
        $whois  = new Whois;

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'public$success',
            )
        );

        self::assertFalse($wefact->getContactHandle($whois, HANDLE_OWNER));
        self::assertFalse($wefact->getContactHandle($whois, HANDLE_ADMIN));
        self::assertFalse($wefact->getContactHandle($whois, HANDLE_TECH));
    }

    public function testError()
    {
        $wefact = $this->getModule();
        $whois  = new Whois;

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$failure',
            )
        );

        self::assertFalse($wefact->getContactHandle($whois, HANDLE_OWNER));
        self::assertFalse($wefact->getContactHandle($whois, HANDLE_ADMIN));
        self::assertFalse($wefact->getContactHandle($whois, HANDLE_TECH));
    }

    public function testException()
    {
        $wefact = $this->getModule();
        $whois  = new Whois;

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$thrown',
            )
        );

        self::assertFalse($wefact->getContactHandle($whois, HANDLE_OWNER));
        self::assertFalse($wefact->getContactHandle($whois, HANDLE_ADMIN));
        self::assertFalse($wefact->getContactHandle($whois, HANDLE_TECH));
    }

    public function testSuccess()
    {
        $wefact = $this->getModule();
        $whois  = new Whois;

        $whois->ownerSurName      = 'Testov';
        $whois->ownerCompanyName  = 'T Testov';
        $whois->ownerInitials     = 'T';
        $whois->ownerEmailAddress = 'test@gooblesupermegacomp.com';

        self::assertEquals(8, $wefact->getContactHandle($whois, HANDLE_OWNER));

        $whois->ownerSurName      = 'Testov';
        $whois->ownerCompanyName  = 'Test Testov';
        $whois->ownerInitials     = 'Test';
        $whois->ownerEmailAddress = 'test@gooblesupermegacomp.com';

        self::assertFalse($wefact->getContactHandle($whois, HANDLE_OWNER));
    }
}