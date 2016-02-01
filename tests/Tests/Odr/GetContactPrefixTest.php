<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \opendomainregistry as Module;

class GetContactPrefixTest extends UnitTestCase
{
    public function testDefault()
    {
        $wefact = new Module;
        $method = $this->getSecureMethod($wefact, '_getContactPrefix');

        self::assertEquals('', $method->invoke($wefact, 'test'));
    }

    public function testOwner()
    {
        $wefact = new Module;
        $method = $this->getSecureMethod($wefact, '_getContactPrefix');

        self::assertEquals('owner', $method->invoke($wefact, HANDLE_OWNER));
    }

    public function testAdmin()
    {
        $wefact = new Module;
        $method = $this->getSecureMethod($wefact, '_getContactPrefix');

        self::assertEquals('admin', $method->invoke($wefact, HANDLE_ADMIN));
    }

    public function testTech()
    {
        $wefact = new Module;
        $method = $this->getSecureMethod($wefact, '_getContactPrefix');

        self::assertEquals('tech', $method->invoke($wefact, HANDLE_TECH));
    }
}