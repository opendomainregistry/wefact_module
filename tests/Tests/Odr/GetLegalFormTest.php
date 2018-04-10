<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \opendomainregistry as Module;
use \Whois;

class GetLegalFormTest extends UnitTestCase
{
    public function testDefault()
    {
        $hostfact = new Module;
        $whois  = new Whois;

        $method = $this->getSecureMethod($hostfact, '_getLegalForm');

        self::assertEquals('PERSOON', $method->invoke($hostfact, $whois, 'owner'));
        self::assertEquals('PERSOON', $method->invoke($hostfact, $whois, 'admin'));
        self::assertEquals('PERSOON', $method->invoke($hostfact, $whois, 'tech'));
    }

    public function testCustom()
    {
        $hostfact = new Module;
        $whois  = new Whois;

        $whois->ownerCompanyName      = 'Archer';
        $whois->ownerCompanyLegalForm = 'Sterling';

        $whois->adminCompanyName      = 'Archer';
        $whois->adminCompanyLegalForm = 'Malory';

        $whois->techCompanyName      = 'Archer';
        $whois->techCompanyLegalForm = 'Lana';

        $method = $this->getSecureMethod($hostfact, '_getLegalForm');

        self::assertEquals('Sterling', $method->invoke($hostfact, $whois, 'owner'));
        self::assertEquals('Malory',   $method->invoke($hostfact, $whois, 'admin'));
        self::assertEquals('Lana',     $method->invoke($hostfact, $whois, 'tech'));

        $whois->techCompanyLegalForm = 'PERSOON';

        self::assertEquals('PERSOON', $method->invoke($hostfact, $whois, 'tech'));
    }

    public function testAndersEmpty()
    {
        $hostfact = new Module;
        $whois  = new Whois;

        $whois->ownerCompanyName = 'Archer';
        $whois->adminCompanyName = 'Archer';
        $whois->techCompanyName  = 'Archer';

        $method = $this->getSecureMethod($hostfact, '_getLegalForm');

        self::assertEquals('ANDERS', $method->invoke($hostfact, $whois, 'owner'));
        self::assertEquals('ANDERS', $method->invoke($hostfact, $whois, 'admin'));
        self::assertEquals('ANDERS', $method->invoke($hostfact, $whois, 'tech'));
    }

    public function testAndersBe()
    {
        $hostfact = new Module;
        $whois  = new Whois;

        $whois->ownerCompanyName      = 'Archer';
        $whois->ownerCompanyLegalForm = 'BE-ANDERS';

        $whois->adminCompanyName      = 'Archer';
        $whois->adminCompanyLegalForm = 'BE-TESTING';

        $whois->techCompanyName      = 'Archer';
        $whois->techCompanyLegalForm = 'BE-SOMETHINGCOMPLETELYDIFFERENT';

        $method = $this->getSecureMethod($hostfact, '_getLegalForm');

        self::assertEquals('ANDERS', $method->invoke($hostfact, $whois, 'owner'));
        self::assertEquals('ANDERS', $method->invoke($hostfact, $whois, 'admin'));
        self::assertEquals('ANDERS', $method->invoke($hostfact, $whois, 'tech'));
    }
}