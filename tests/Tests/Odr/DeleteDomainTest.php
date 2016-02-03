<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

class DeleteDomainTest extends UnitTestCase
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

        self::assertFalse($wefact->deleteDomain('test.nl'));
    }
}