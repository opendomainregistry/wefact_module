<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use Whois;

class RegisterDomainTest extends UnitTestCase
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

        self::assertFalse($wefact->registerDomain('test.nl'));
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

        self::assertFalse($wefact->registerDomain('test.nl'));
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

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($wefact->registerDomain('test.nl', array(), $whois, true));
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

        $whois = $this->getDefaultWhois();

        $whois->ownerRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        $whois->adminRegistrarHandles = array(
            'opendomainregistry' => 1,
        );

        self::assertFalse($wefact->registerDomain('test.nl', array(), $whois));
    }

    public function testException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenRegisterDomain' => 'token$thrown',
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

        self::assertFalse($wefact->registerDomain('test.nl', array(), $whois));
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

        self::assertTrue($wefact->registerDomain('test.nl', array(), $whois));
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

        self::assertTrue($wefact->registerDomain('test.nl', array('ns1' => 'ns1.test.ru', 'ns2' => 'ns2.test.ru'), $whois));
    }

    public function testDError()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenRegisterDomain' => 'token$failure',
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
        self::assertFalse($wefact->registerDomain('test.nl', array(), $whois));
    }

    public function testDException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenRegisterDomain' => 'token$thrown',
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

        self::assertFalse($wefact->registerDomain('test.nl', array(), $whois));
    }

    public function testDInternal()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'             => 'public$success',
                'api_secret'          => 'secret$success',
                'token'               => 'token$success',
                'tokenRegisterDomain' => 'token$successinternal',
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

        self::assertFalse($wefact->registerDomain('test.nl', array(), $whois));

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
                'tokenRegisterDomain' => 'token$successnomessage',
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

        self::assertFalse($wefact->registerDomain('test.nl', array(), $whois));

        self::assertEquals(array('ODR: Incorrectly formatted response'), $wefact->Error);
    }
}