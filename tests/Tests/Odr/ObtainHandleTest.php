<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \opendomainregistry as Module;

use \Whois;

class ObtainHandleTest extends UnitTestCase
{
    public function testWhoisIsNull()
    {
        $hostfact = new Module;
        $method = $this->getSecureMethod($hostfact, '_obtainHandle');

        self::assertFalse($method->invoke($hostfact, 'test.nl', null, 'owner', HANDLE_OWNER));
    }

    public function testHandlesExistsButString()
    {
        $hostfact = new Module;
        $method = $this->getSecureMethod($hostfact, '_obtainHandle');

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array('opendomainregistry' => 'AAA');
        $whois->adminRegistrarHandles = array('opendomainregistry' => 'BBB');
        $whois->techRegistrarHandles  = array('opendomainregistry' => 'CCC');

        self::assertFalse($method->invoke($hostfact, 'test.nl', $whois, HANDLE_OWNER));
        self::assertFalse($method->invoke($hostfact, 'test.nl', $whois, HANDLE_ADMIN));
        self::assertFalse($method->invoke($hostfact, 'test.nl', $whois, HANDLE_TECH));
    }

    public function testHandlesExists()
    {
        $hostfact = new Module;
        $method = $this->getSecureMethod($hostfact, '_obtainHandle');

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array('opendomainregistry' => '500');
        $whois->adminRegistrarHandles = array('opendomainregistry' => '600');
        $whois->techRegistrarHandles  = array('opendomainregistry' => '700');

        self::assertEquals('500', $method->invoke($hostfact, 'test.nl', $whois, HANDLE_OWNER));
        self::assertEquals('600', $method->invoke($hostfact, 'test.nl', $whois, HANDLE_ADMIN));
        self::assertEquals('700', $method->invoke($hostfact, 'test.nl', $whois, HANDLE_TECH));
    }

    public function testSurNameError()
    {
        $hostfact = new Module;
        $method = $this->getSecureMethod($hostfact, '_obtainHandle');

        $whois = new Whois;

        $whois->ownerSurName      = 'Testov';
        $whois->ownerCompanyName  = 'T Testov';
        $whois->ownerInitials     = 'T';
        $whois->ownerEmailAddress = 'test@gooblesupermegacomp.com';

        self::assertFalse($method->invoke($hostfact, 'test.nl', $whois, HANDLE_OWNER));
    }
}