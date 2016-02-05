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
    protected $_bind = array();

    static public function getInstance()
    {
        return new self;
    }

    public function bindValue($bind, $value)
    {
        $this->_bind[$bind] = $value;
    }

    public function execute()
    {
        $this->_bind = array();

        return new MockResponse;
    }

    public function fetch()
    {
        return $this->execute();
    }

    public function fetchAll($mode = \PDO::FETCH_ASSOC)
    {
        return $this->execute();
    }

    public function prepare($sql)
    {
        return $this;
    }
}

class MockResponse
{
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