<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetDomainInformationTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'public$success',
            )
        );

        self::assertFalse($hostfact->getDomainInformation('test.nl'));
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

        self::assertFalse($hostfact->getDomainInformation('test.nl'));
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

        self::assertFalse($hostfact->getDomainInformation('test.nl'));
    }

    public function testSuccess()
    {
        $hostfact = $this->getModule();

        $whois = $hostfact->getContact(24);

        $whois->adminHandle = 32;
        $whois->techHandle  = null;

        $expected = array(
            'Domain'      => 'test.nl',
            'Information' => array(
                'nameservers'       => array(
                    'ns1.test.ru',
                    'ns2.test.ru',
                ),
                'whois'             => $whois,
                'expiration_date'   => date('Y') + 1 . '-01-01',
                'registration_date' => '',
                'authkey'           => 'TEST1221TSET',
                'auto_renew'        => 'on',
            ),
        );

        self::assertEquals($expected, $hostfact->getDomainInformation('test.nl'));
    }

    public function testSuccessMissingNs()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$successmissingns',
            )
        );

        $whois = $hostfact->getContact(24);

        $whois->adminHandle = 32;
        $whois->techHandle  = null;

        $expected = array(
            'Domain'      => 'test.nl',
            'Information' => array(
                'nameservers'       => array(
                    'ns1.test.ru',
                    'ns2.test.ru',
                ),
                'whois'             => $whois,
                'expiration_date'   => date('Y') + 1 . '-01-01',
                'registration_date' => '',
                'authkey'           => 'TEST1221TSET',
                'auto_renew'        => 'on',
            ),
        );

        self::assertEquals($expected, $hostfact->getDomainInformation('test.nl'));
    }

    public function testSuccessNoNs()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$successnons',
            )
        );

        $whois = $hostfact->getContact(24);

        $whois->adminHandle = 32;
        $whois->techHandle  = null;

        $expected = array(
            'Domain'      => 'test.nl',
            'Information' => array(
                'nameservers'       => array(
                ),
                'whois'             => $whois,
                'expiration_date'   => date('Y') + 1 . '-01-01',
                'registration_date' => '',
                'authkey'           => 'TEST1221TSET',
                'auto_renew'        => 'on',
            ),
        );

        self::assertEquals($expected, $hostfact->getDomainInformation('test.nl'));
    }

    public function testErrorNoContactMap()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$successnocontactmap',
            )
        );

        self::assertFalse($hostfact->getDomainInformation('test.nl'));

        self::assertEquals(array('Domain do not have any contacts attached. Probably issue with missing details or contacts being corrupted. Please, contact support and visit ODR to fix such domains'), $hostfact->Error);
    }

    public function testSuccessOnlyOnsite()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$successonlyonsite',
            )
        );

        $whois = $hostfact->getContact(24);

        $whois->adminHandle = 24;
        $whois->techHandle  = null;

        $expected = array(
            'Domain'      => 'test.nl',
            'Information' => array(
                'nameservers'       => array(
                    'ns1.test.ru',
                    'ns2.test.ru',
                ),
                'whois'             => $whois,
                'expiration_date'   => date('Y') + 1 . '-01-01',
                'registration_date' => '',
                'authkey'           => 'TEST1221TSET',
                'auto_renew'        => 'on',
            ),
        );

        self::assertEquals($expected, $hostfact->getDomainInformation('test.nl'));
    }

    public function testDError()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$failure',
            )
        );

        self::assertFalse($hostfact->getDomainInformation('test.nl'));
    }

    public function testDException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$thrown',
            )
        );

        self::assertFalse($hostfact->getDomainInformation('test.nl'));
    }

    public function testDInternal()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$successinternal',
            )
        );

        self::assertFalse($hostfact->getDomainInformation('test.nl'));

        self::assertEquals(array('ODR: Testing'), $hostfact->Error);
    }

    public function testSuccessButNoExpiration()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$successnoexpiration',
            )
        );

        $whois = $hostfact->getContact(24);

        $whois->adminHandle = 32;
        $whois->techHandle  = null;

        $expected = array(
            'Domain'      => 'testxxx23.nl',
            'Information' => array(
                'nameservers'       => array(
                    'ns1.test.ru',
                    'ns2.test.ru',
                ),
                'whois'             => $whois,
                'expiration_date'   => '',
                'registration_date' => '',
                'authkey'           => 'TEST1221TSET',
                'auto_renew'        => 'on',
            ),
        );

        self::assertEquals($expected, $hostfact->getDomainInformation('testxxx23.nl'));
    }

    public function testDInternalNoMessage()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$successnomessage',
            )
        );

        self::assertFalse($hostfact->getDomainInformation('test.nl'));

        self::assertEquals(array('ODR: Incorrectly formatted response'), $hostfact->Error);
    }
}