<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \opendomainregistry as Module;
use \Database_Model     as Model;

class GetAvailableTldsTest extends UnitTestCase
{
    public function testCheckCached()
    {
        $hostfact = new Module;
        $cached = $this->getSecureProperty($hostfact, '_availableTlds');

        $expected = array(
            'be',
            'eu',
        );

        $cached->setValue($hostfact, $expected);

        self::assertEquals($expected, $hostfact->getAvailableTlds());
    }

    public function testNoRegistrarId()
    {
        $hostfact = new Module;

        self::assertNull($hostfact->getAvailableTlds());
    }

    public function testExpected()
    {
        $hostfact = new Module;
        $model  = Model::getInstance();

        $this->getSecureProperty($hostfact, '_registrarId')->setValue($hostfact, 1);

        $response = $this->getSecureProperty($model, '_response');

        $expected = array(
            'be',
            'eu',
            'com',
        );

        $response->setValue($model, $expected);

        self::assertEquals($expected, $hostfact->getAvailableTlds());

        $response->setValue($model, null);
    }
}