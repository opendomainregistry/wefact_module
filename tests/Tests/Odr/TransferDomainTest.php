<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use Whois;

class TransferDomainTest extends UnitTestCase
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

        self::assertFalse($wefact->transferDomain('test.nl'));
    }

    public function testError()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        self::assertFalse($wefact->transferDomain('test.nl'));
    }

    public function testErrorAdmin()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        $whois = new Whois;

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($wefact->transferDomain('test.nl', array(), $whois));
    }

    public function testErrorTech()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        $whois = new Whois;

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($wefact->transferDomain('test.nl', array(), $whois));
    }

    public function testException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenTransferDomain' => 'token$thrown',
            )
        );

        $whois = new Whois;

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($wefact->transferDomain('test.nl', array(), $whois));
    }

    public function testSuccess()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        $whois = new Whois;

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertTrue($wefact->transferDomain('test.nl', array(), $whois));
    }

    public function testSuccessWithNameservers()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
            )
        );

        $whois = new Whois;

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertTrue($wefact->transferDomain('test.nl', array('ns1' => 'ns1.test.ru', 'ns2' => 'ns2.test.ru'), $whois));
    }

    public function testDError()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenTransferDomain' => 'token$failure',
            )
        );

        $whois = new Whois;

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($wefact->transferDomain('test.nl', array(), $whois));
    }

    public function testDException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenTransferDomain' => 'token$thrown',
            )
        );

        $whois = new Whois;

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($wefact->transferDomain('test.nl', array(), $whois));
    }

    public function testDInternal()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenTransferDomain' => 'token$successinternal',
            )
        );

        $whois = new Whois;

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($wefact->transferDomain('test.nl', array(), $whois));

        self::assertEquals(array('ODR: Someone wanted it!'), $wefact->Error);
    }

    public function testDInternalNoMessage()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenTransferDomain' => 'token$successnomessage',
            )
        );

        $whois = new Whois;

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->techRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($wefact->transferDomain('test.nl', array(), $whois));

        self::assertEquals(array('ODR: Incorrect response'), $wefact->Error);
    }
}