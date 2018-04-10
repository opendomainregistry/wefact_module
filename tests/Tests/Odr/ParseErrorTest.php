<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \opendomainregistry as Module;

class ParseErrorTest extends UnitTestCase
{
    public function testParseData()
    {
        $hostfact = $this->getModule();

        $data = array(
            array(
                'input' => '',
                'expected' => 'ODR: Unknown error happened, sorry about that',
            ),
            array(
                'input' => false,
                'expected' => 'ODR: Unknown error happened, sorry about that',
            ),
            array(
                'input' => null,
                'expected' => 'ODR: Unknown error happened, sorry about that',
            ),
            array(
                'input' => '0',
                'expected' => 'ODR: Unknown error happened, sorry about that',
            ),
            array(
                'input' => 'Testing',
                'expected' => 'ODR: Testing',
            ),
            array(
                'input' => new \Exception('Exception Message'),
                'expected' => 'ODR: Exception Message',
            ),
            array(
                'input' => array(
                    'message' => 'Testing',
                ),
                'expected' => 'ODR: Testing',
            ),
            array(
                'input' => array(),
                'expected' => 'ODR: Unknown error happened, sorry about that',
            ),

            array(
                'input'    => false,
                'code'     => 100,
                'expected' => 'ODR: 100 - Unknown error happened, sorry about that',
            ),
            array(
                'input'    => '',
                'code'     => 100,
                'expected' => 'ODR: 100 - Unknown error happened, sorry about that',
            ),
            array(
                'input'    => null,
                'code'     => 100,
                'expected' => 'ODR: 100 - Unknown error happened, sorry about that',
            ),
            array(
                'input'    => '0',
                'code'     => 100,
                'expected' => 'ODR: 100 - Unknown error happened, sorry about that',
            ),
            array(
                'input'    => 'Testing',
                'code'     => 100,
                'expected' => 'ODR: 100 - Testing',
            ),
            array(
                'input'    => new \Exception('Exception Message'),
                'code'     => 100,
                'expected' => 'ODR: 100 - Exception Message',
            ),
            array(
                'input'    => array(
                    'message' => 'Testing',
                ),
                'code'     => 100,
                'expected' => 'ODR: 100 - Testing',
            ),
            array(
                'input'    => array(),
                'code'     => 100,
                'expected' => 'ODR: 100 - Unknown error happened, sorry about that',
            ),
        );

        foreach ($data as $d) {
            self::assertFalse($hostfact->parseError($d['input'], empty($d['code']) ? null : $d['code']));

            self::assertEquals(array($d['expected']), $hostfact->Error);

            $hostfact->Error = array();
        }
    }
}