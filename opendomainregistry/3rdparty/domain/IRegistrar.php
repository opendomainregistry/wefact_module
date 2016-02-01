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

    public function getVersionInformation();
}