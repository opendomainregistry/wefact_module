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

    public function testHandlesExists()
    {
        $wefact = new Module;
        $method = $this->getSecureMethod($wefact, '_obtainHandle');

        $whois = new Whois;

        $whois->ownerRegistrarHandles = array('opendomainregistry' => 'AAA');
        $whois->adminRegistrarHandles = array('opendomainregistry' => 'BBB');
        $whois->techRegistrarHandles  = array('opendomainregistry' => 'CCC');

        self::assertEquals('AAA', $method->invoke($wefact, 'test.nl', $whois, HANDLE_OWNER));
        self::assertEquals('BBB', $method->invoke($wefact, 'test.nl', $whois, HANDLE_ADMIN));
        self::assertEquals('CCC', $method->invoke($wefact, 'test.nl', $whois, HANDLE_TECH));
    }
}