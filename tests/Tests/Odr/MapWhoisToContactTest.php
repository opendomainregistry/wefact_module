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

        $custs = array(
            'state' => 'Noord-Brabant',
        );

        $whois->{$prefix . 'customvalues'} = $custs;

        $expected = array(
            'first_name'              => null,
            'last_name'               => null,
            'full_name'               => '',
            'initials'                => null,
            'state'                   => 'Noord-Brabant',
            'city'                    => null,
            'postal_code'             => '',
            'phone'                   => '.',
            'email'                   => null,
            'country'                 => null,
            'language'                => 'NL',
            'gender'                  => 'NA',
            'street'                  => null,
            'house_number'            => ' ',
            'company_name'            => '',
            'company_email'           => null,
            'company_street'          => null,
            'company_house_number'    => ' ',
            'company_postal_code'     => '',
            'company_city'            => null,
            'company_phone'           => '.',
            'company_vatin'           => null,
            'organization_legal_form' => 'PERSOON',
            'type'                    => 'REGISTRANT',
        );

        self::assertEquals($expected, $wefact->mapWhoisToContact($whois, HANDLE_OWNER));
    }

    public function testRegion()
    {
        $wefact = $this->getModule();
        $whois  = new Whois;

        $prefix = 'owner';

        $custs = array(
            'region' => 'Noord-Brabant2',
        );

        $whois->{$prefix . 'customvalues'} = $custs;

        $expected = array(
            'first_name'              => null,
            'last_name'               => null,
            'full_name'               => '',
            'initials'                => null,
            'state'                   => 'Noord-Brabant2',
            'city'                    => null,
            'postal_code'             => '',
            'phone'                   => '.',
            'email'                   => null,
            'country'                 => null,
            'language'                => 'NL',
            'gender'                  => 'NA',
            'street'                  => null,
            'house_number'            => ' ',
            'company_name'            => '',
            'company_email'           => null,
            'company_street'          => null,
            'company_house_number'    => ' ',
            'company_postal_code'     => '',
            'company_city'            => null,
            'company_phone'           => '.',
            'company_vatin'           => null,
            'organization_legal_form' => 'PERSOON',
            'type'                    => 'REGISTRANT',
        );

        self::assertEquals($expected, $wefact->mapWhoisToContact($whois, HANDLE_OWNER));
    }

    public function testBoth()
    {
        $wefact = $this->getModule();
        $whois  = new Whois;

        $prefix = 'owner';

        $custs = array(
            'state'  => 'Noord-Brabant',
            'region' => 'Noord-Brabant2',
        );

        $whois->{$prefix . 'customvalues'} = $custs;

        $expected = array(
            'first_name'              => null,
            'last_name'               => null,
            'full_name'               => '',
            'initials'                => null,
            'state'                   => 'Noord-Brabant',
            'city'                    => null,
            'postal_code'             => '',
            'phone'                   => '.',
            'email'                   => null,
            'country'                 => null,
            'language'                => 'NL',
            'gender'                  => 'NA',
            'street'                  => null,
            'house_number'            => ' ',
            'company_name'            => '',
            'company_email'           => null,
            'company_street'          => null,
            'company_house_number'    => ' ',
            'company_postal_code'     => '',
            'company_city'            => null,
            'company_phone'           => '.',
            'company_vatin'           => null,
            'organization_legal_form' => 'PERSOON',
            'type'                    => 'REGISTRANT',
        );

        self::assertEquals($expected, $wefact->mapWhoisToContact($whois, HANDLE_OWNER));
    }

    public function testBothCustom()
    {
        $wefact = $this->getModule();
        $whois  = new Whois;

        $prefix = 'owner';

        $custs = array(
            'state'  => 'Noord-Brabant',
            'region' => 'Noord-Brabant2',
        );

        $whois->{$prefix . 'custom'} = $custs;

        $expected = array(
            'first_name'              => null,
            'last_name'               => null,
            'full_name'               => '',
            'initials'                => null,
            'state'                   => 'Noord-Brabant',
            'city'                    => null,
            'postal_code'             => '',
            'phone'                   => '.',
            'email'                   => null,
            'country'                 => null,
            'language'                => 'NL',
            'gender'                  => 'NA',
            'street'                  => null,
            'house_number'            => ' ',
            'company_name'            => '',
            'company_email'           => null,
            'company_street'          => null,
            'company_house_number'    => ' ',
            'company_postal_code'     => '',
            'company_city'            => null,
            'company_phone'           => '.',
            'company_vatin'           => null,
            'organization_legal_form' => 'PERSOON',
            'type'                    => 'REGISTRANT',
        );

        self::assertEquals($expected, $wefact->mapWhoisToContact($whois, HANDLE_OWNER));
    }
}