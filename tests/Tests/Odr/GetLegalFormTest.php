<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \opendomainregistry as Module;
use \Whois;

class GetLegalFormTest extends UnitTestCase
{
    public function testDefault()
    {
        $wefact = new Module;
        $whois  = new Whois;

        $method = $this->getSecureMethod($wefact, '_getLegalForm');

        self::assertEquals('PERSOON', $method->invoke($wefact, $whois, 'owner'));
        self::assertEquals('PERSOON', $method->invoke($wefact, $whois, 'admin'));
        self::assertEquals('PERSOON', $method->invoke($wefact, $whois, 'tech'));
    }

    public function testCustom()
    {
        $wefact = new Module;
        $whois  = new Whois;

        $whois->ownerCompanyName      = 'Archer';
        $whois->ownerCompanyLegalForm = 'Sterling';

        $whois->adminCompanyName      = 'Archer';
        $whois->adminCompanyLegalForm = 'Malory';

        $whois->techCompanyName      = 'Archer';
        $whois->techCompanyLegalForm = 'Lana';

        $method = $this->getSecureMethod($wefact, '_getLegalForm');

        self::assertEquals('Sterling', $method->invoke($wefact, $whois, 'owner'));
        self::assertEquals('Malory',   $method->invoke($wefact, $whois, 'admin'));
        self::assertEquals('Lana',     $method->invoke($wefact, $whois, 'tech'));

        $whois->techCompanyLegalForm = 'PERSOON';

        self::assertEquals('PERSOON', $method->invoke($wefact, $whois, 'tech'));
    }

    public function testAndersEmpty()
    {
        $wefact = new Module;
        $whois  = new Whois;

        $whois->ownerCompanyName = 'Archer';
        $whois->adminCompanyName = 'Archer';
        $whois->techCompanyName  = 'Archer';

        $method = $this->getSecureMethod($wefact, '_getLegalForm');

        self::assertEquals('ANDERS', $method->invoke($wefact, $whois, 'owner'));
        self::assertEquals('ANDERS', $method->invoke($wefact, $whois, 'admin'));
        self::assertEquals('ANDERS', $method->invoke($wefact, $whois, 'tech'));
    }

    public function testAndersBe()
    {
        $wefact = new Module;
        $whois  = new Whois;

        $whois->ownerCompanyName      = 'Archer';
        $whois->ownerCompanyLegalForm = 'BE-ANDERS';

        $whois->adminCompanyName      = 'Archer';
        $whois->adminCompanyLegalForm = 'BE-TESTING';

        $whois->techCompanyName      = 'Archer';
        $whois->techCompanyLegalForm = 'BE-SOMETHINGCOMPLETELYDIFFERENT';

        $method = $this->getSecureMethod($wefact, '_getLegalForm');

        self::assertEquals('ANDERS', $method->invoke($wefact, $whois, 'owner'));
        self::assertEquals('ANDERS', $method->invoke($wefact, $whois, 'admin'));
        self::assertEquals('ANDERS', $method->invoke($wefact, $whois, 'tech'));
    }
}