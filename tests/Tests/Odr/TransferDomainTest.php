<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use Whois;

class TransferDomainTest extends UnitTestCase
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

        self::assertFalse($hostfact->transferDomain('test.nl'));
    }

    public function testError()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        self::assertFalse($hostfact->transferDomain('test.nl'));
    }

    public function testErrorAdmin()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($hostfact->transferDomain('test.nl', array(), $whois));
    }

    public function testErrorTech()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($hostfact->transferDomain('test.nl', array(), $whois));
    }

    public function testException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenTransferDomain' => 'token$thrown',
            )
        );

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($hostfact->transferDomain('test.nl', array(), $whois));
    }

    public function testSuccess()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertTrue($hostfact->transferDomain('test.nl', array(), $whois));
    }

    public function testSuccessWithNameservers()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertTrue($hostfact->transferDomain('test.nl', array('ns1' => 'ns1.test.ru', 'ns2' => 'ns2.test.ru'), $whois));
    }

    public function testDError()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenTransferDomain' => 'token$failure',
            )
        );

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($hostfact->transferDomain('test.nl', array(), $whois));
    }

    public function testDException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenTransferDomain' => 'token$thrown',
            )
        );

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($hostfact->transferDomain('test.nl', array(), $whois));
    }

    public function testDInternal()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenTransferDomain' => 'token$successinternal',
            )
        );

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($hostfact->transferDomain('test.nl', array(), $whois));

        self::assertEquals(array('ODR: Someone wanted it!'), $hostfact->Error);
    }

    public function testDInternalNoMessage()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenTransferDomain' => 'token$successnomessage',
            )
        );

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($hostfact->transferDomain('test.nl', array(), $whois));

        self::assertEquals(array('ODR: Incorrectly formatted response'), $hostfact->Error);
    }
}