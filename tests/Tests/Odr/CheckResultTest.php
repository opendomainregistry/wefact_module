<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \Api_Odr as Odr;

class CheckResultTest extends UnitTestCase
{
    public function testGood()
    {
        $wefact = $this->getModule();
        $method = $this->getSecureMethod($wefact, '_checkResult');

        $wefact->odr->setResult(
            array(
                'status' => Odr::STATUS_SUCCESS,
            )
        );

        self::assertTrue($method->invoke($wefact, true));
    }

    public function testGoodArray()
    {
        $wefact = $this->getModule();
        $method = $this->getSecureMethod($wefact, '_checkResult');

        $wefact->odr->setResult(
            array(
                'status' => Odr::STATUS_SUCCESS,
            )
        );

        $expected = array(
            'Test' => true,
            'Die'  => 'false',
        );

        self::assertEquals($expected, $method->invoke($wefact, $expected));
    }

    public function testError()
    {
        $wefact = $this->getModule();
        $method = $this->getSecureMethod($wefact, '_checkResult');

        $wefact->odr->setResult(
            array(
                'status'   => Odr::STATUS_ERROR,
                'response' => array(
                    'message' => 'Test',
                ),
            )
        );

        self::assertFalse($method->invoke($wefact, true));
    }

    public function testErrorArray()
    {
        $wefact = $this->getModule();
        $method = $this->getSecureMethod($wefact, '_checkResult');

        $wefact->odr->setResult(
            array(
                'status'   => Odr::STATUS_ERROR,
                'response' => array(
                    'message' => 'Test',
                    'data'    => array(
                        'test' => 'False',
                    ),
                ),
            )
        );

        self::assertFalse($method->invoke($wefact, true));
    }
}