<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \opendomainregistry as Module;

class CheckPeriodTest extends UnitTestCase
{
    public function testDefault()
    {
        $hostfact = new Module;

        $hostfact->Period = 99;

        self::assertEquals(99, $hostfact->Period);

        $method = $this->getSecureMethod($hostfact, '_checkPeriod');

        self::assertEquals(1, $method->invoke($hostfact, 'notexistingtldforsure'));
    }

    public function test2Years()
    {
        $hostfact = new Module;

        $method = $this->getSecureMethod($hostfact, '_checkPeriod');

        $tlds = array(
            'to',
            'pk',
            'us',
        );

        foreach ($tlds as $tld) {
            self::assertEquals(2, $method->invoke($hostfact, $tld));
        }

        self::assertEquals(1, $method->invoke($hostfact, 'notexistingtldforsure'));
    }

    public function test3Years()
    {
        $hostfact = new Module;

        $method = $this->getSecureMethod($hostfact, '_checkPeriod');

        $tlds = array(
            'vc',
            'vg',
        );

        foreach ($tlds as $tld) {
            self::assertEquals(3, $method->invoke($hostfact, $tld));
        }

        self::assertEquals(1, $method->invoke($hostfact, 'notexistingtldforsure'));
    }

    public function test10Years()
    {
        $hostfact = new Module;

        $method = $this->getSecureMethod($hostfact, '_checkPeriod');

        $tlds = array(
            'tm',
        );

        foreach ($tlds as $tld) {
            self::assertEquals(10, $method->invoke($hostfact, $tld));
        }

        self::assertEquals(1, $method->invoke($hostfact, 'notexistingtldforsure'));
    }
}