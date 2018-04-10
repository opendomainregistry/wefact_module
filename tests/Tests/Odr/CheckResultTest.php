<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \Api_Odr as Odr;

class CheckResultTest extends UnitTestCase
{
    public function testGood()
    {
        $hostfact = $this->getModule();
        $method = $this->getSecureMethod($hostfact, '_checkResult');

        $hostfact->odr->setResult(
            array(
                'status' => Odr::STATUS_SUCCESS,
            )
        );

        self::assertTrue($method->invoke($hostfact, true));
    }

    public function testGoodArray()
    {
        $hostfact = $this->getModule();
        $method = $this->getSecureMethod($hostfact, '_checkResult');

        $hostfact->odr->setResult(
            array(
                'status' => Odr::STATUS_SUCCESS,
            )
        );

        $expected = array(
            'Test' => true,
            'Die'  => 'false',
        );

        self::assertEquals($expected, $method->invoke($hostfact, $expected));
    }

    public function testError()
    {
        $hostfact = $this->getModule();
        $method = $this->getSecureMethod($hostfact, '_checkResult');

        $hostfact->odr->setResult(
            array(
                'status'   => Odr::STATUS_ERROR,
                'response' => array(
                    'message' => 'Test',
                ),
            )
        );

        self::assertFalse($method->invoke($hostfact, true));
    }

    public function testErrorArray()
    {
        $hostfact = $this->getModule();
        $method = $this->getSecureMethod($hostfact, '_checkResult');

        $hostfact->odr->setResult(
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

        self::assertFalse($method->invoke($hostfact, true));
    }

    public function testErrorLogout()
    {
        $hostfact = $this->getModule();
        $method = $this->getSecureMethod($hostfact, '_checkResult');

        $hostfact->odr->setResult(
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

        self::assertFalse($method->invoke($hostfact, true, true));
    }

    public function testErrorFailedResponse()
    {
        $hostfact = $this->getModule();
        $method = $this->getSecureMethod($hostfact, '_checkResult');

        $hostfact->odr->setResult(
            array(
                'status'   => Odr::STATUS_SUCCESS,
                'response' => array(
                    'status' => 'FAILED',
                ),
            )
        );

        self::assertFalse($method->invoke($hostfact, true, true));

        self::assertEquals(array('ODR: Incorrectly formatted response'), $hostfact->Error);
    }

    public function testErrorFailedResponseWithMessage()
    {
        $hostfact = $this->getModule();
        $method = $this->getSecureMethod($hostfact, '_checkResult');

        $hostfact->odr->setResult(
            array(
                'status'   => Odr::STATUS_SUCCESS,
                'response' => array(
                    'status' => 'FAILED',
                    'data'    => array(
                        'message' => 'False',
                    ),
                ),
            )
        );

        self::assertFalse($method->invoke($hostfact, true, true));

        self::assertEquals(array('ODR: False'), $hostfact->Error);
    }
}