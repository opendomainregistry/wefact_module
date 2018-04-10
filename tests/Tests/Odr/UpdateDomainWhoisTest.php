<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use Whois;

class UpdateDomainWhoisTest extends UnitTestCase
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

        $whois = new Whois;

        self::assertFalse($hostfact->updateDomainWhois('test.nl', $whois));
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

        $whois = new Whois;

        self::assertFalse($hostfact->updateDomainWhois('test.nl', $whois));
    }

    public function testInfoError()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenInfo'  => 'token$failure',
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

        self::assertFalse($hostfact->updateDomainWhois('test.nl', $whois));
    }

    public function testInfoException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$success',
                'tokenInfo'  => 'token$thrown',
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

        self::assertFalse($hostfact->updateDomainWhois('test.nl', $whois));
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

        self::assertFalse($hostfact->updateDomainWhois('test.nl', $whois));
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

        self::assertFalse($hostfact->updateDomainWhois('test.nl', $whois));
    }

    public function testException()
    {
        $hostfact = $this->getModule();

        $hostfact->odr->setConfig(
            array(
                'api_key'     => 'public$success',
                'api_secret'  => 'secret$success',
                'token'       => 'token$success',
                'tokenUpdate' => 'token$thrown',
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

        self::assertFalse($hostfact->updateDomainWhois('test.nl', $whois));
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

        self::assertTrue($hostfact->updateDomainWhois('test.nl', $whois));
    }
}