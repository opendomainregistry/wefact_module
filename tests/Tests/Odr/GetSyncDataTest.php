<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class GetSyncDataTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'public$success',
                'url'        => $wefact::URL_TEST,
            )
        );

        $expected = array(
            'test.nl' => array(
                'Status'    => 'error',
                'Error_msg' => 'Either domain not found or internal error happened',
            ),
        );

        self::assertEquals($expected, $wefact->getSyncData(array('test.nl' => array())));
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

        $expected = array(
            'test.nl' => array(
                'Status'    => 'error',
                'Error_msg' => 'Either domain not found or internal error happened',
            ),
        );

        self::assertEquals($expected, $wefact->getSyncData(array('test.nl' => array())));
    }

    public function testException()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$success',
                'api_secret' => 'secret$success',
                'token'      => 'token$thrown',
                'url'        => $wefact::URL_TEST,
            )
        );

        $expected = array(
            'test.nl' => array(
                'Status'    => 'error',
                'Error_msg' => 'Either domain not found or internal error happened',
            ),
        );

        self::assertEquals($expected, $wefact->getSyncData(array('test.nl' => array())));
    }

    public function testSuccess()
    {
        $wefact = $this->getModule();

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
            $info = $wefact->getDomainInformation($domain);

            $info['Status'] = 'success';

            $expected[$domain] = $info;
        }

        self::assertEquals($expected, $wefact->getSyncData($domains));
    }
}