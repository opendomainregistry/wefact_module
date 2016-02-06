<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \opendomainregistry as Module;
use \Database_Model     as Model;

class GetAvailableTldsTest extends UnitTestCase
{
    public function testCheckCached()
    {
        $wefact = new Module;
        $cached = $this->getSecureProperty($wefact, '_availableTlds');

        $expected = array(
            'be',
            'eu',
        );

        $cached->setValue($wefact, $expected);

        self::assertEquals($expected, $wefact->getAvailableTlds());
    }

    public function testNoRegistrarId()
    {
        $wefact = new Module;

        self::assertNull($wefact->getAvailableTlds());
    }

    public function testExpected()
    {
        $wefact = new Module;
        $model  = Model::getInstance();

        $this->getSecureProperty($wefact, '_registrarId')->setValue($wefact, 1);

        $response = $this->getSecureProperty($model, '_response');

        $expected = array(
            'be',
            'eu',
            'com',
        );

        $response->setValue($model, $expected);

        self::assertEquals($expected, $wefact->getAvailableTlds());

        $response->setValue($model, null);
    }
}