<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetDomainListTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'token$failure',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->getDomainList());
    }

    public function testError()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$failure',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->getDomainList());
    }

    public function testSuccess()
    {
        $wefact = $this->getModule();

        $expected = array(
            array(
                'Domain'      => 'test.nl',
                'Information' => array(
                    'nameservers' => array(),
                    'whois'       => null,
                    'expires'     => (date('Y') + 1) . '-01-01',
                    'regdate'     => '',
                    'authkey'     => '',
                ),
            ),
            array(
                'Domain'      => 'test.eu',
                'Information' => array(
                    'nameservers' => array(),
                    'whois'       => null,
                    'expires'     => (date('Y') + 2) . '-02-01',
                    'regdate'     => '',
                    'authkey'     => '',
                ),
            ),
        );

        self::assertEquals($expected, $wefact->getDomainList('test'));
    }
}