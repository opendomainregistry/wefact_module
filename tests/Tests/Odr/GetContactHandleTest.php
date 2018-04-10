<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \Whois;

class GetContactHandleTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $hostfact = $this->getModule();
        $whois  = new Whois;

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'public$success',
            )
        );

        self::assertFalse($hostfact->getContactHandle($whois, HANDLE_OWNER));
        self::assertFalse($hostfact->getContactHandle($whois, HANDLE_ADMIN));
        self::assertFalse($hostfact->getContactHandle($whois, HANDLE_TECH));
    }

    public function testError()
    {
        $hostfact = $this->getModule();
        $whois  = new Whois;

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$failure',
            )
        );

        self::assertFalse($hostfact->getContactHandle($whois, HANDLE_OWNER));
        self::assertFalse($hostfact->getContactHandle($whois, HANDLE_ADMIN));
        self::assertFalse($hostfact->getContactHandle($whois, HANDLE_TECH));
    }

    public function testException()
    {
        $hostfact = $this->getModule();
        $whois  = new Whois;

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$thrown',
            )
        );

        self::assertFalse($hostfact->getContactHandle($whois, HANDLE_OWNER));
        self::assertFalse($hostfact->getContactHandle($whois, HANDLE_ADMIN));
        self::assertFalse($hostfact->getContactHandle($whois, HANDLE_TECH));
    }

    public function testSuccess()
    {
        $hostfact = $this->getModule();
        $whois  = new Whois;

        $whois->ownerSurName      = 'Testov';
        $whois->ownerCompanyName  = 'T Testov';
        $whois->ownerInitials     = 'T';
        $whois->ownerEmailAddress = 'test@gooblesupermegacomp.com';

        self::assertEquals(8, $hostfact->getContactHandle($whois, HANDLE_OWNER));

        $whois->ownerSurName      = 'Testov';
        $whois->ownerCompanyName  = 'Test Testov';
        $whois->ownerInitials     = 'Test';
        $whois->ownerEmailAddress = 'test@gooblesupermegacomp.com';

        self::assertFalse($hostfact->getContactHandle($whois, HANDLE_OWNER));
    }
}