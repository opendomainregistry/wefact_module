<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \opendomainregistry as Module;

use \Whois;

class ObtainHandleTest extends UnitTestCase
{
    public function testWhoisIsNull()
    {
        $wefact = new Module;
        $method = $this->getSecureMethod($wefact, '_obtainHandle');

        self::assertFalse($method->invoke($wefact, 'test.nl', null, 'owner', HANDLE_OWNER));
    }

    public function testHandlesExistsButString()
    {
        $wefact = new Module;
        $method = $this->getSecureMethod($wefact, '_obtainHandle');

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array('opendomainregistry' => 'AAA');
        $whois->adminRegistrarHandles = array('opendomainregistry' => 'BBB');
        $whois->techRegistrarHandles  = array('opendomainregistry' => 'CCC');

        self::assertFalse($method->invoke($wefact, 'test.nl', $whois, HANDLE_OWNER));
        self::assertFalse($method->invoke($wefact, 'test.nl', $whois, HANDLE_ADMIN));
        self::assertFalse($method->invoke($wefact, 'test.nl', $whois, HANDLE_TECH));
    }

    public function testHandlesExists()
    {
        $wefact = new Module;
        $method = $this->getSecureMethod($wefact, '_obtainHandle');

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array('opendomainregistry' => '500');
        $whois->adminRegistrarHandles = array('opendomainregistry' => '600');
        $whois->techRegistrarHandles  = array('opendomainregistry' => '700');

        self::assertEquals('500', $method->invoke($wefact, 'test.nl', $whois, HANDLE_OWNER));
        self::assertEquals('600', $method->invoke($wefact, 'test.nl', $whois, HANDLE_ADMIN));
        self::assertEquals('700', $method->invoke($wefact, 'test.nl', $whois, HANDLE_TECH));
    }

    public function testSurNameError()
    {
        $wefact = new Module;
        $method = $this->getSecureMethod($wefact, '_obtainHandle');

        $whois = new Whois;

        $whois->ownerSurName      = 'Testov';
        $whois->ownerCompanyName  = 'T Testov';
        $whois->ownerInitials     = 'T';
        $whois->ownerEmailAddress = 'test@gooblesupermegacomp.com';

        self::assertFalse($method->invoke($wefact, 'test.nl', $whois, HANDLE_OWNER));
    }
}