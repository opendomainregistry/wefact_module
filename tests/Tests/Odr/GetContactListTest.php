<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetContactListTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        self::assertFalse($hostfact->getContactList());
    }

    public function testError()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$failure',
            )
        );

        self::assertFalse($hostfact->getContactList());
    }

    public function testException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$thrown',
            )
        );

        self::assertFalse($hostfact->getContactList());
    }

    public function testSuccess()
    {
        $hostfact = $this->getModule();

        $expected = array(
            array(
                'Handle'      => 8,
                'CompanyName' => 'T Testov',
            ),
            array(
                'Handle'      => 9,
                'CompanyName' => 'Gooble Super Mega Company, Test Division',
            ),
        );

        self::assertEquals($expected, $hostfact->getContactList('test'));
    }
}