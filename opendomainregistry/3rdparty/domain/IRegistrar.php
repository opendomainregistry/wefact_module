<?php
interface IRegistrar
{
    public function checkDomain($domain);

    public function registerDomain($domain, $nameservers = array(), $whois = array());

    public function transferDomain($domain, $nameservers = array(), $whois = array(), $authcode = '');

    public function deleteDomain($domain, $delType = 'end');

    public function getDomainInformation($domain);

    public function getDomainList($contactHandle = '');

    public function lockDomain($domain, $lock = true);

    public function setDomainAutoRenew($domain, $bool = true);

    public function updateDomainWhois($domain, $whois);

    public function getDomainWhois($domain);

    public function getToken($domain);

    public function createContact($whois, $type = '');

    public function updateContact($handle, $whois, $type = '');

    public function getContact($handle);

    public function getContactList($surname = '');

    public function updateNameServers($domain, $nameservers = array());

    static public function getVersionInformation();
}

class Database_Model
{
    /**
     * @var array
     *
     * @protected
     */
    protected $_bind = array();

    /**
     * @var null|MockResponse|mixed
     *
     * @protected
     */
    protected $_response;

    /**
     * @var null|Database_Model
     *
     * @protected
     *
     * @static
     */
    static protected $_instance;

    /**
     * Returns instance of model
     *
     * @return Database_Model|null
     *
     * @static
     */
    static public function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * Binds value for request
     * Not usable in this "version", but suppose to act like PDO's prepared statements
     *
     * @param $bind
     * @param $value
     */
    public function bindValue($bind, $value)
    {
        $this->_bind[$bind] = $value;
    }

    /**
     * Executes the query and clears any set binds
     *
     * @return null|MockResponse|mixed
     */
    public function execute()
    {
        $this->_bind = array();

        return $this->_response ?: new MockResponse;
    }

    /**
     * Changes next response
     * Notice: You can pass anything here, method doesn't wrap your value into other class/array/whatever
     *
     * @param null|mixed $response
     *
     * @return $this
     */
    public function setResponse($response = null)
    {
        $this->_response = $response;

        return $this;
    }

    public function fetch()
    {
        return $this->execute();
    }

    /**
     * @param int $mode
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetchAll($mode = \PDO::FETCH_ASSOC)
    {
        return $this->execute();
    }

    /**
     * @param string $sql
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function prepare($sql)
    {
        return $this;
    }
}

class MockResponse
{
    /**
     * @var array
     *
     * @protected
     */
    protected $_data = array();

    public function __get($key)
    {
        return empty($this->_data[$key]) ? null : $this->_data[$key];
    }

    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }
}