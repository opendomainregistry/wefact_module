<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class TransferDomainTest extends UnitTestCase
{
    public function testNotLoggedIn()
    {
        $wefact = $this->getModule();

        $wefact->odr->setConfig(
            array(
                'api_key'    => 'public$failure',
                'api_secret' => 'secret$success',
                'token'      => 'token$failure',
                'url'        => $wefact::URL_TEST,
            )
        );

        self::assertFalse($wefact->transferDomain('test.nl'));
    }
}