<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetSyncDataTest extends UnitTestCase
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

        $expected = array(
            'test.nl' => array(
                'Status'    => 'error',
                'Error_msg' => 'Either domain not found or internal error happened',
            ),
        );

        self::assertEquals($expected, $hostfact->getSyncData(array('test.nl' => array())));
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

        $expected = array(
            'test.nl' => array(
                'Status'    => 'error',
                'Error_msg' => 'Either domain not found or internal error happened',
            ),
        );

        self::assertEquals($expected, $hostfact->getSyncData(array('test.nl' => array())));
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

        $expected = array(
            'test.nl' => array(
                'Status'    => 'error',
                'Error_msg' => 'Either domain not found or internal error happened',
            ),
        );

        self::assertEquals($expected, $hostfact->getSyncData(array('test.nl' => array())));
    }

    public function testSuccess()
    {
        $hostfact = $this->getModule();

        $expected = array();

        $domains = array(
            'test1.nl' => array(),
            'test1.eu' => array(),
            'test1.be' => array(),
            'test2.nl' => array(),
            'test2.eu' => array(),
            'test2.be' => array(),
            'test3.nl' => array(),
            'test3.eu' => array(),
            'test3.be' => array(),
            'test4.nl' => array(),
            'test4.eu' => array(),
            'test4.be' => array(),
        );

        $limit = 10;
        $i     = 0;

        foreach ($domains as $domain => $domainData) {
            $i++;

            if ($limit !== null && $i > $limit) {
                $expected[$domain] = array();

                continue;
            }

            /** @var array $info */
            $info = $hostfact->getDomainInformation($domain);

            $info['Status'] = 'success';

            $expected[$domain] = $info;
        }

        self::assertEquals($expected, $hostfact->getSyncData($domains));
    }
}