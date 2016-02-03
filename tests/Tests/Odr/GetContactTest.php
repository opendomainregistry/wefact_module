<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetContactTest extends UnitTestCase
{
    public function testNoHandle()
    {
        $wefact = $this->getModule();

        self::assertFalse($wefact->getContact(0));
    }

    public function testNotLoggedIn()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'public$success',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->getContact(1));
    }

    public function testError()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'public$failure',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->getContact(1));
    }

    public function testException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$thrown',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->getContact(1));
    }
}