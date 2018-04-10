<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetDomainListTest extends UnitTestCase
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

        self::assertFalse($hostfact->getDomainList());
    }

    public function testThrown()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainList' => 'token$thrown',
            )
        );

        self::assertFalse($hostfact->getDomainList());
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

        self::assertFalse($hostfact->getDomainList());
    }

    public function testErrorDomainList()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainList' => 'token$failure',
            )
        );

        self::assertFalse($hostfact->getDomainList());
    }

    public function testErrorDomainListNoMessage()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainList' => 'token$successnomessage',
            )
        );

        self::assertFalse($hostfact->getDomainList());

        self::assertEquals(array('ODR: Incorrectly formatted response'), $hostfact->Error);
    }

    public function testErrorDomainListInternal()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainList' => 'token$successinternal',
            )
        );

        self::assertFalse($hostfact->getDomainList());

        self::assertEquals(array('ODR: Someone wanted it!'), $hostfact->Error);
    }

    public function testSuccess()
    {
        $hostfact = $this->getModule();

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

        self::assertEquals($expected, $hostfact->getDomainList('test'));
    }

    public function testSuccessTldFilter()
    {
        $hostfact = $this->getModule();

        $availableTlds = $this->getSecureProperty($hostfact, '_availableTlds');

        $availableTlds->setValue($hostfact, array('be', 'eu', 'nl'));

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

        self::assertEquals($expected, $hostfact->getDomainList('test'));
    }
}