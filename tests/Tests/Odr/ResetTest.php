<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class ResetTest extends UnitTestCase
{
    public function testLogged()
    {
        $hostfact = $this->getModule();

        $this->getSecureProperty($hostfact, 'AccessToken')->setValue($hostfact, true);

        self::assertTrue($hostfact->reset());
    }

    public function testInvalidFormat()
    {
        $hostfact = $this->getModule();

        $hostfact->User = 'failure';

        self::assertFalse($hostfact->reset());
    }

    public function testInvalidLogin()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$secret',
            )
        );

        self::assertFalse($hostfact->reset());
    }

    public function testException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$throw',
            )
        );

        self::assertFalse($hostfact->reset());
    }

    public function testSuccess()
    {
        $hostfact = $this->getModule();

        self::assertTrue($hostfact->reset());

        self::assertEquals('token$success', $this->getSecureProperty($hostfact, 'AccessToken')->getValue($hostfact));
    }

    public function testSession()
    {
        $hostfact = $this->getModule();

        $_SESSION = array(
            $hostfact::ODR_TOKEN_SESSION => '1',
        );

        $this->getSecureProperty($hostfact, 'AccessToken')->setValue($hostfact, null);

        self::assertTrue($hostfact->reset());

        self::assertEquals('1', $this->getSecureProperty($hostfact, 'AccessToken')->getValue($hostfact));
        self::assertEquals('1', $hostfact->getAccessToken());
    }

    public function testAccessTokenSet()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenGetMe' => 'token$success',
            )
        );

        $_SESSION = array(
            $hostfact::ODR_TOKEN_SESSION => 'B',
        );

        $this->getSecureProperty($hostfact, 'AccessToken')->setValue($hostfact, 'B');

        self::assertTrue($hostfact->reset());

        self::assertArrayHasKey($hostfact::ODR_TOKEN_SESSION, $_SESSION);

        self::assertEquals('B', $this->getSecureProperty($hostfact, 'AccessToken')->getValue($hostfact));
        self::assertEquals('B', $hostfact->getAccessToken());
        self::assertEquals('B', $_SESSION[$hostfact::ODR_TOKEN_SESSION]);
    }

    public function testAccessTokenSetGetMeThrown()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenGetMe' => 'token$thrown',
            )
        );

        $_SESSION = array(
            $hostfact::ODR_TOKEN_SESSION => 'B',
        );

        $this->getSecureProperty($hostfact, 'AccessToken')->setValue($hostfact, 'B');

        self::assertTrue($hostfact->reset());

        self::assertEquals('token$success', $this->getSecureProperty($hostfact, 'AccessToken')->getValue($hostfact));
        self::assertEquals('token$success', $hostfact->getAccessToken());
    }

    public function testAccessTokenSetGetMeError()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenGetMe' => 'token$failure',
            )
        );

        $_SESSION = array(
            $hostfact::ODR_TOKEN_SESSION => 'B',
        );

        $this->getSecureProperty($hostfact, 'AccessToken')->setValue($hostfact, 'B');

        self::assertTrue($hostfact->reset());

        self::assertEquals('token$success', $this->getSecureProperty($hostfact, 'AccessToken')->getValue($hostfact));
        self::assertEquals('token$success', $hostfact->getAccessToken());
    }

    public function testAccessTokenSetGetMeErrorFalse()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenGetMe' => 'token$failure',
            )
        );

        $_SESSION = array(
            $hostfact::ODR_TOKEN_SESSION => 'B',
        );

        $this->getSecureProperty($hostfact, 'AccessToken')->setValue($hostfact, 'B');

        self::assertFalse($hostfact->reset());

        self::assertFalse($this->getSecureProperty($hostfact, 'AccessToken')->getValue($hostfact));
        self::assertFalse($hostfact->getAccessToken());
    }
}