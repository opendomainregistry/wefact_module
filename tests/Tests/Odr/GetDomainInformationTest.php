<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetDomainInformationTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'public$success',
            )
        );

        self::assertFalse($wefact->getDomainInformation('test.nl'));
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

        self::assertFalse($wefact->getDomainInformation('test.nl'));
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

        self::assertFalse($wefact->getDomainInformation('test.nl'));
    }

    public function testSuccess()
    {
        $wefact = $this->getModule();

        $whois = $wefact->getContact(24);

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

        self::assertEquals($expected, $wefact->getDomainInformation('test.nl'));
    }

    public function testSuccessMissingNs()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$successmissingns',
            )
        );

        $whois = $wefact->getContact(24);

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

        self::assertEquals($expected, $wefact->getDomainInformation('test.nl'));
    }

    public function testSuccessNoNs()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$successnons',
            )
        );

        $whois = $wefact->getContact(24);

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

        self::assertEquals($expected, $wefact->getDomainInformation('test.nl'));
    }

    public function testErrorNoContactMap()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$successnocontactmap',
            )
        );

        self::assertFalse($wefact->getDomainInformation('test.nl'));

        self::assertEquals(array('Domain do not have any contacts attached. Probably issue with missing details or contacts being corrupted. Please, contact support and visit ODR to fix such domains'), $wefact->Error);
    }

    public function testSuccessOnlyOnsite()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$successonlyonsite',
            )
        );

        $whois = $wefact->getContact(24);

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

        self::assertEquals($expected, $wefact->getDomainInformation('test.nl'));
    }

    public function testDError()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$failure',
            )
        );

        self::assertFalse($wefact->getDomainInformation('test.nl'));
    }

    public function testDException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$thrown',
            )
        );

        self::assertFalse($wefact->getDomainInformation('test.nl'));
    }

    public function testDInternal()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$successinternal',
            )
        );

        self::assertFalse($wefact->getDomainInformation('test.nl'));

        self::assertEquals(array('ODR: Testing'), $wefact->Error);
    }

    public function testDInternalNoMessage()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'         => 'public$success',
                'api_secret'      => 'secret$success',
                'token'           => 'token$success',
                'tokenDomainInfo' => 'token$successnomessage',
            )
        );

        self::assertFalse($wefact->getDomainInformation('test.nl'));

        self::assertEquals(array('ODR: Incorrectly formatted response'), $wefact->Error);
    }
}