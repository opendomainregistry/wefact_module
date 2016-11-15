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
                'token'      => 'token$success',
            )
        );

        self::assertFalse($wefact->getDomainList());
    }

    public function testThrown()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainList' => 'token$thrown',
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
            )
        );

        self::assertFalse($wefact->getDomainList());
    }

    public function testErrorDomainList()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainList' => 'token$failure',
            )
        );

        self::assertFalse($wefact->getDomainList());
    }

    public function testErrorDomainListNoMessage()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainList' => 'token$successnomessage',
            )
        );

        self::assertFalse($wefact->getDomainList());

        self::assertEquals(array('ODR: Incorrectly formatted response'), $wefact->Error);
    }

    public function testErrorDomainListInternal()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainList' => 'token$successinternal',
            )
        );

        self::assertFalse($wefact->getDomainList());

        self::assertEquals(array('ODR: Someone wanted it!'), $wefact->Error);
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

    public function testSuccessTldFilter()
    {
        $wefact = $this->getModule();

        $availableTlds = $this->getSecureProperty($wefact, '_availableTlds');

        $availableTlds->setValue($wefact, array('be', 'eu', 'nl'));

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