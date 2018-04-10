<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \opendomainregistry as Module;

class GetContactPrefixTest extends UnitTestCase
{
    public function testDefault()
    {
        $hostfact = new Module;
        $method = $this->getSecureMethod($hostfact, '_getContactPrefix');

        self::assertEquals('', $method->invoke($hostfact, 'test'));
    }

    public function testOwner()
    {
        $hostfact = new Module;
        $method = $this->getSecureMethod($hostfact, '_getContactPrefix');

        self::assertEquals('owner', $method->invoke($hostfact, HANDLE_OWNER));
    }

    public function testAdmin()
    {
        $hostfact = new Module;
        $method = $this->getSecureMethod($hostfact, '_getContactPrefix');

        self::assertEquals('admin', $method->invoke($hostfact, HANDLE_ADMIN));
    }

    public function testTech()
    {
        $hostfact = new Module;
        $method = $this->getSecureMethod($hostfact, '_getContactPrefix');

        self::assertEquals('tech', $method->invoke($hostfact, HANDLE_TECH));
    }
}