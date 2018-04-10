<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class ConvertNameserversTest extends UnitTestCase
{
    public function testPairing()
    {
        $hostfact = $this->getModule();
        $domain = 'test.eu';

        $data = array(
            array(
                'input' => array(
                    'ns1'   => 'ns1.' . $domain,
                    'ns1ip' => '1.2.3.4',
                    'ns2'   => 'ns2.' . $domain,
                    'ns2ip' => '2.3.4.5',
                ),

                'expected' => array(
                    'ns1' => array(
                        'host' => 'ns1.' . $domain,
                        'ip'   => '1.2.3.4',
                    ),
                    'ns2' => array(
                        'host' => 'ns2.' . $domain,
                        'ip'   => '2.3.4.5',
                    ),
                ),
            ),

            array(
                'input' => array(
                    'ns1'    => 'ns1.' . $domain,
                    'ns1ip'  => '1.2.3.4',
                    'ns2'    => 'ns2.' . $domain,
                    'ns2ip'  => '2.3.4.5',
                    'ns3'    => 'ns3.' . $domain,
                    'ns3ip'  => '3.4.5.6',
                    'ns4'    => 'ns4.' . $domain,
                    'ns4ip'  => '4.5.6.7',
                    'ns5'    => 'ns5.' . $domain,
                    'ns5ip'  => '5.6.7.8',
                    'ns6'    => 'ns6.' . $domain,
                    'ns6ip'  => '6.7.8.9',
                    'ns7'    => 'ns7.' . $domain,
                    'ns7ip'  => '7.8.9.0',
                    'ns8'    => 'ns8.' . $domain,
                    'ns8ip'  => '8.9.0.1',
                    'ns9'    => 'ns9.' . $domain,
                    'ns9ip'  => '9.0.1.2',
                    'ns10'   => 'ns10.' . $domain,
                    'ns10ip' => '1.1.2.3',
                ),

                'expected' => array(
                    'ns1' => array(
                        'host' => 'ns1.' . $domain,
                        'ip'   => '1.2.3.4',
                    ),
                    'ns2' => array(
                        'host' => 'ns2.' . $domain,
                        'ip'   => '2.3.4.5',
                    ),
                    'ns3' => array(
                        'host' => 'ns3.' . $domain,
                        'ip'   => '3.4.5.6',
                    ),
                    'ns4' => array(
                        'host' => 'ns4.' . $domain,
                        'ip'   => '4.5.6.7',
                    ),
                    'ns5' => array(
                        'host' => 'ns5.' . $domain,
                        'ip'   => '5.6.7.8',
                    ),
                    'ns6' => array(
                        'host' => 'ns6.' . $domain,
                        'ip'   => '6.7.8.9',
                    ),
                    'ns7' => array(
                        'host' => 'ns7.' . $domain,
                        'ip'   => '7.8.9.0',
                    ),
                    'ns8' => array(
                        'host' => 'ns8.' . $domain,
                        'ip'   => '8.9.0.1',
                    ),
                ),
            ),

            array(
                'input' => array(
                    'ns1'   => 'ns1.fr.fr',
                    'ns1ip' => '1.2.3.4',
                    'ns2'   => 'ns2.' . $domain,
                    'ns2ip' => '2.3.4.5',
                ),

                'expected' => array(
                    'ns1' => array(
                        'host' => 'ns1.fr.fr',
                        'ip'   => null,
                    ),
                    'ns2' => array(
                        'host' => 'ns2.' . $domain,
                        'ip'   => '2.3.4.5',
                    ),
                ),
            ),
        );

        foreach ($data as $d) {
            self::assertEquals($d['expected'], $hostfact->convertNameservers('test.eu', $d['input']));
        }
    }
}