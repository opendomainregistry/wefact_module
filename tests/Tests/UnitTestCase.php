<?php
namespace Tests;

use \opendomainregistry as Module;

use Mocks;

abstract class UnitTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var bool
     *
     * @private
     */
    private $_isLoaded = false;

    /**
     * @var array
     *
     * @protected
     *
     * @static
     */
    static protected $_isAvailable = array();

    /**
     * Setup the test
     *
     * @return null
     *
     * @protected
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_isLoaded = true;

        $_SESSION['wf_odr_access_token'] = null;
    }

    /**
     * Tear the test down
     *
     * @return null
     *
     * @protected
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Check if the test case is setup properly
     *
     * @return null
     *
     * @throws \PHPUnit_Framework_IncompleteTestError
     */
    public function __destruct()
    {
        if (!$this->_isLoaded) {
            throw new \PHPUnit_Framework_IncompleteTestError('Please run parent::setUp()');
        }
    }

    /**
     * Checks if a particular extension is loaded and if not, marks the test as skipped
     *
     * @param string $extension Extension to check
     *
     * @return bool
     *
     * @static
     */
    static public function checkExtension($extension)
    {
        if (!extension_loaded($extension)) {
            self::markTestSkipped("Warning: Extension '{$extension}' is not loaded");

            return false;
        }

        return true;
    }

    /**
     * Returns accessible protected or private method for testing
     *
     * @param string $className  Target class name
     * @param string $methodName Target method name
     *
     * @return \ReflectionMethod
     *
     * @throws \ReflectionException If method not exists
     */
    public function getSecureMethod($className, $methodName)
    {
        $reflection = new \ReflectionClass($className);

        $method = $reflection->getMethod($methodName);

        $method->setAccessible(true);

        return $method;
    }

    /**
     * Returns accessible protected or private property for testing
     *
     * @param string $className    Target class name
     * @param string $propertyName Target property name
     *
     * @return \ReflectionProperty
     *
     * @throws \ReflectionException If method not exists
     */
    public function getSecureProperty($className, $propertyName)
    {
        $reflection = new \ReflectionClass($className);

        $property = $reflection->getProperty($propertyName);

        $property->setAccessible(true);

        return $property;
    }

    public function getModule()
    {
        $module = new Module;

        $module->User     = 'public$success';
        $module->Password = 'secret$success';
        $module->Testmode = true;

        $module->odr = new Mocks\Odr(
            array(
                'api_key'    => $module->User,
                'api_secret' => $module->Password,
                'url'        => $module->Testmode ? Module::URL_TEST : Module::URL_LIVE,
            )
        );

        return $module;
    }
}