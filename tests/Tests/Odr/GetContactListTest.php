<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetContactListTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        self::assertFalse($wefact->getContactList());
    }

    public function testError()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$failure',
            )
        );

        self::assertFalse($wefact->getContactList());
    }

    public function testException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$thrown',
            )
        );

        self::assertFalse($wefact->getContactList());
    }

    public function testSuccess()
    {
        $wefact = $this->getModule();

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

        self::assertEquals($expected, $wefact->getContactList('test'));
    }
}