<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use Whois;

class MapWhoisToContactTest extends UnitTestCase
{
    public function testState()
    {
        $wefact = $this->getModule();
        $whois  = new Whois;

        $prefix = 'owner';

        $whois->{$prefix . 'State'} = 'Noord-Brabant';

        $expected = array(
            'first_name'              => null,
            'last_name'               => null,
            'full_name'               => '',
            'initials'                => null,
            'state'                   => $whois->{$prefix . 'State'},
            'city'                    => null,
            'postal_code'             => '',
            'phone'                   => '.',
            'email'                   => null,
            'country'                 => null,
            'language'                => 'NL',
            'gender'                  => 'NA',
            'street'                  => null,
            'house_number'            => '',
            'company_name'            => '',
            'company_email'           => null,
            'company_street'          => null,
            'company_house_number'    => '',
            'company_postal_code'     => '',
            'company_city'            => null,
            'company_phone'           => '.',
            'company_vatin'           => null,
            'organization_legal_form' => 'PERSOON',
        );

        self::assertEquals($expected, $wefact->mapWhoisToContact($whois, HANDLE_OWNER));
    }

    public function testRegion()
    {
        $wefact = $this->getModule();
        $whois  = new Whois;

        $prefix = 'owner';

        $whois->{$prefix . 'Region'} = 'Noord-Brabant';

        $expected = array(
            'first_name'              => null,
            'last_name'               => null,
            'full_name'               => '',
            'initials'                => null,
            'state'                   => $whois->{$prefix . 'Region'},
            'city'                    => null,
            'postal_code'             => '',
            'phone'                   => '.',
            'email'                   => null,
            'country'                 => null,
            'language'                => 'NL',
            'gender'                  => 'NA',
            'street'                  => null,
            'house_number'            => '',
            'company_name'            => '',
            'company_email'           => null,
            'company_street'          => null,
            'company_house_number'    => '',
            'company_postal_code'     => '',
            'company_city'            => null,
            'company_phone'           => '.',
            'company_vatin'           => null,
            'organization_legal_form' => 'PERSOON',
        );

        self::assertEquals($expected, $wefact->mapWhoisToContact($whois, HANDLE_OWNER));
    }

    public function testBoth()
    {
        $wefact = $this->getModule();
        $whois  = new Whois;

        $prefix = 'owner';

        $whois->{$prefix . 'State'}  = 'Noord-Brabant';
        $whois->{$prefix . 'Region'} = 'Noord-Brabant2';

        $expected = array(
            'first_name'              => null,
            'last_name'               => null,
            'full_name'               => '',
            'initials'                => null,
            'state'                   => $whois->{$prefix . 'State'},
            'city'                    => null,
            'postal_code'             => '',
            'phone'                   => '.',
            'email'                   => null,
            'country'                 => null,
            'language'                => 'NL',
            'gender'                  => 'NA',
            'street'                  => null,
            'house_number'            => '',
            'company_name'            => '',
            'company_email'           => null,
            'company_street'          => null,
            'company_house_number'    => '',
            'company_postal_code'     => '',
            'company_city'            => null,
            'company_phone'           => '.',
            'company_vatin'           => null,
            'organization_legal_form' => 'PERSOON',
        );

        self::assertEquals($expected, $wefact->mapWhoisToContact($whois, HANDLE_OWNER));
    }
}