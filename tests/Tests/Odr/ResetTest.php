<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class ResetTest extends UnitTestCase
{
    public function testLogged()
    {
        $wefact = $this->getModule();

        $this->getSecureProperty($wefact, 'AccessToken')->setValue($wefact, true);

        self::assertTrue($wefact->reset());
    }

    public function testInvalidFormat()
    {
        $wefact = $this->getModule();

        $wefact->User = 'failure';

        self::assertFalse($wefact->reset());
    }

    public function testInvalidLogin()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$secret',
            )
        );

        self::assertFalse($wefact->reset());
    }

    public function testException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$throw',
            )
        );

        self::assertFalse($wefact->reset());
    }

    public function testSuccess()
    {
        $wefact = $this->getModule();

        self::assertTrue($wefact->reset());

        self::assertEquals('token$success', $this->getSecureProperty($wefact, 'AccessToken')->getValue($wefact));
    }

    public function testSession()
    {
        $wefact = $this->getModule();

        $_SESSION = array(
            $wefact::ODR_TOKEN_SESSION => '1',
        );

        $this->getSecureProperty($wefact, 'AccessToken')->setValue($wefact, null);

        self::assertTrue($wefact->reset());

        self::assertEquals('1', $this->getSecureProperty($wefact, 'AccessToken')->getValue($wefact));
        self::assertEquals('1', $wefact->getAccessToken());
    }

    public function testAccessTokenSet()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenGetMe' => 'token$success',
            )
        );

        $_SESSION = array(
            $wefact::ODR_TOKEN_SESSION => 'B',
        );

        $this->getSecureProperty($wefact, 'AccessToken')->setValue($wefact, 'B');

        self::assertTrue($wefact->reset());

        self::assertArrayHasKey($wefact::ODR_TOKEN_SESSION, $_SESSION);

        self::assertEquals('B', $this->getSecureProperty($wefact, 'AccessToken')->getValue($wefact));
        self::assertEquals('B', $wefact->getAccessToken());
        self::assertEquals('B', $_SESSION[$wefact::ODR_TOKEN_SESSION]);
    }

    public function testAccessTokenSetGetMeThrown()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenGetMe' => 'token$thrown',
            )
        );

        $_SESSION = array(
            $wefact::ODR_TOKEN_SESSION => 'B',
        );

        $this->getSecureProperty($wefact, 'AccessToken')->setValue($wefact, 'B');

        self::assertTrue($wefact->reset());

        self::assertEquals('token$success', $this->getSecureProperty($wefact, 'AccessToken')->getValue($wefact));
        self::assertEquals('token$success', $wefact->getAccessToken());
    }

    public function testAccessTokenSetGetMeError()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenGetMe' => 'token$failure',
            )
        );

        $_SESSION = array(
            $wefact::ODR_TOKEN_SESSION => 'B',
        );

        $this->getSecureProperty($wefact, 'AccessToken')->setValue($wefact, 'B');

        self::assertTrue($wefact->reset());

        self::assertEquals('token$success', $this->getSecureProperty($wefact, 'AccessToken')->getValue($wefact));
        self::assertEquals('token$success', $wefact->getAccessToken());
    }

    public function testAccessTokenSetGetMeErrorFalse()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenGetMe' => 'token$failure',
            )
        );

        $_SESSION = array(
            $wefact::ODR_TOKEN_SESSION => 'B',
        );

        $this->getSecureProperty($wefact, 'AccessToken')->setValue($wefact, 'B');

        self::assertFalse($wefact->reset());

        self::assertFalse($this->getSecureProperty($wefact, 'AccessToken')->getValue($wefact));
        self::assertFalse($wefact->getAccessToken());
    }
}