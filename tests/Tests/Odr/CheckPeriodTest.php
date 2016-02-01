<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \opendomainregistry as Module;

class CheckPeriodTest extends UnitTestCase
{
    public function testDefault()
    {
        $wefact = new Module;

        $wefact->Period = 99;

        self::assertEquals(99, $wefact->Period);

        $method = $this->getSecureMethod($wefact, '_checkPeriod');

        self::assertEquals(1, $method->invoke($wefact, 'notexistingtldforsure'));
    }

    public function test2Years()
    {
        $wefact = new Module;

        $method = $this->getSecureMethod($wefact, '_checkPeriod');

        $tlds = array(
            'to',
            'pk',
            'us',
        );

        foreach ($tlds as $tld) {
            self::assertEquals(2, $method->invoke($wefact, $tld));
        }

        self::assertEquals(1, $method->invoke($wefact, 'notexistingtldforsure'));
    }

    public function test3Years()
    {
        $wefact = new Module;

        $method = $this->getSecureMethod($wefact, '_checkPeriod');

        $tlds = array(
            'vc',
            'vg',
        );

        foreach ($tlds as $tld) {
            self::assertEquals(3, $method->invoke($wefact, $tld));
        }

        self::assertEquals(1, $method->invoke($wefact, 'notexistingtldforsure'));
    }

    public function test10Years()
    {
        $wefact = new Module;

        $method = $this->getSecureMethod($wefact, '_checkPeriod');

        $tlds = array(
            'tm',
        );

        foreach ($tlds as $tld) {
            self::assertEquals(10, $method->invoke($wefact, $tld));
        }

        self::assertEquals(1, $method->invoke($wefact, 'notexistingtldforsure'));
    }
}