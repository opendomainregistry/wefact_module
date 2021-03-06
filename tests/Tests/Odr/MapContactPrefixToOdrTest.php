<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class MapContactPrefixToOdrTest extends UnitTestCase
{
    public function testPairing()
    {
        $hostfact = $this->getModule();

        $data = array(
            'owner'    => 'REGISTRANT',
            'tech'     => 'TECH',
            'admin'    => 'ONSITE',
            'reseller' => 'RESELLER',
            '1'        => '1',
            'zone'     => 'ZONE',
            'unmapped' => 'UNMAPPED',
        );

        foreach ($data as $input => $expected) {
            self::assertEquals($expected, $hostfact->mapContactPrefixToOdr($input));
        }
    }
}