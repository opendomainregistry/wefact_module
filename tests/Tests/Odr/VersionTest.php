<?php
namespace Tests\Odr;

use Tests\UnitTestCase;

use \opendomainregistry as Module;

class VersionTest extends UnitTestCase
{
    public function testDefault()
    {
        $wefact = new Module;

        $version = include 'opendomainregistry/version.php';

        self::assertEquals($version, $wefact->getVersionInformation());
        
        self::checkVersionData($version);
    }
    
    public function testCustom()
    {
        $wefact = new Module;

        $versionFile = 'tests/mocks/version_custom.php';
        $version     = include $versionFile;

        $this->getSecureProperty($wefact, '_versionFile')->setValue($wefact, $versionFile);

        self::assertEquals($version, $wefact->getVersionInformation());

        self::checkVersionData($version);
    }
    
    static public function checkVersionData(array $data)
    {
        $fields = array(
            'name',
            'api_version',
            'date',
            'wefact_version',

            'autorenew',
            'handle_support',
            'cancel_direct',
            'cancel_expire',
            'domain_support',
            'ssl_support',
        );

        foreach ($fields as $field) {
            self::assertArrayHasKey($field, $data);
        }
    }
}