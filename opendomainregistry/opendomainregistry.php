<?php
require_once '3rdparty/domain/IRegistrar.php';
require_once '3rdparty/domain/standardfunctions.php';
require_once 'helpers.php';

class opendomainregistry implements IRegistrar
{
    const URL_LIVE = 'https://api.opendomainregistry.net';
    const URL_TEST = 'http://api.odrregistry.nl';

    const ODR_TOKEN_SESSION = 'wf_odr_access_token';

    public $User;
    public $Password;

    public $Error;
    public $Warning;
    public $Success;

    public $Period = 1;

    public $registrarHandles       = array();
    public $registrarHandlesMapped = array();

    /**
     * @var null|int
     *
     * @protected
     */
    protected $_registrarId;

    /**
     * @var null|array
     *
     * @protected
     */
    protected $_availableTlds;

    /**
     * @var string
     *
     * @protected
     *
     * @static
     */
    static protected $_versionFile = 'version.php';

    private $ClassName;
    private $AccessToken;

    /** @var null|Api_Odr */
    public $odr;

    public function __construct()
    {
        $this->ClassName   = __CLASS__;
        $this->AccessToken = false;

        $this->Error   = array();
        $this->Warning = array();
        $this->Success = array();

        $this->TldPeriod2  = array('com.au','gr','ma','co.uk','me.uk','org.uk','ro','aero','cl','do','gr','il','co.il','la','mu','ph','pk','to','us');
        $this->TldPeriod3  = array('vc','vg');
        $this->TldPeriod10 = array('tm');

        $pdoStatement = Database_Model::getInstance()->prepare('SELECT * FROM `WeFact_Registrar` WHERE `Class` = :classname');

        $dir = basename(__DIR__);

        $pdoStatement->bindValue(':classname', $dir);
        $pdoStatement->execute();

        $this->_registrarId = $pdoStatement->fetch()->id;
    }

    /**
     * Returns either NULL or array of available TLDs
     * Helpful for domain synchronization, that was ODR will filter only supported TLDs, less time for you, less requests for us
     *
     * @return array|null
     */
    public function getAvailableTlds()
    {
        if ($this->_availableTlds !== null) {
            return $this->_availableTlds;
        }

        if ($this->_registrarId === null) {
            return null;
        }

        $this->_availableTlds = array();

        $pdoStatement = Database_Model::getInstance()->prepare('SELECT `Tld` FROM `WeFact_TopLevelDomain` WHERE `Registrar` = :registrar');

        $pdoStatement->bindValue(':registrar', $this->_registrarId);
        $pdoStatement->execute();

        $this->_availableTlds = (array)$pdoStatement->fetchAll(PDO::FETCH_COLUMN);

        return empty($this->_availableTlds) ? array() : $this->_availableTlds;
    }

    /**
     * Check whether a domain is already registered or not
     *
     * @param string $domain The name of the domain that needs to be checked
     *
     * @return bool True if free, False if not free, False and $this->Error[] in case of error
     */
    public function checkDomain($domain)
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        try {
            $this->odr->checkDomain($domain);
        } catch(Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        if (!empty($result['response']['status']) && $result['response']['status'] === 'FAILED') {
            return $this->parseError($result['response']['data']['message']);
        }

        return (bool)$result['response']['is_available'];
    }

    /**
     * Register a new domain
     *
     * @param string $domain      The domainname that needs to be registered
     * @param array  $nameservers The nameservers for the new domain
     * @param Whois  $whois       The customer information for the domain's whois information
     *
     * @return bool
     *
     * @throws Api_Odr_Exception
     */
    public function registerDomain($domain, $nameservers = array(), $whois = null)
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        $exploded = explode('.', $domain, 2);

        $tld = $exploded[1];

        $ownerHandle = $this->_obtainHandle($domain, $whois, HANDLE_OWNER);

        if (!$ownerHandle) {
            return $ownerHandle;
        }

        $adminHandle = $this->_obtainHandle($domain, $whois, HANDLE_ADMIN);

        if (!$adminHandle) {
            return $adminHandle;
        }

        $techHandle = $this->_obtainHandle($domain, $whois, HANDLE_TECH);

        if (!$techHandle) {
            return $techHandle;
        }

        $this->_checkPeriod($tld);

        $period = $this->Period * 12;

        $parameters = array(
            'period'             => $period,
            'contact_registrant' => $ownerHandle,
            'contact_tech'       => $techHandle,
            'contact_onsite'     => $adminHandle,
            'auth_code'          => substr(md5(time()), 0, 6),
        );

        $parameters = array_merge($parameters, $this->convertNameservers($domain, $nameservers));

        try {
            $this->odr->registerDomain($domain, $parameters);
        } catch(Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        if (!empty($result['response']['status']) && $result['response']['status'] === 'FAILED') {
            return $this->parseError($result['response']['data']['message']);
        }

        return $this->_checkResult(true);
    }

    /**
     * Transfer a domain to the given user
     *
     * @param string $domain      Domain name for transfer
     * @param array  $nameservers The nameservers for the transfered domain
     * @param Whois  $whois       The contact information for the new owner, admin, tech and billing contact
     * @param string $authcode    Authorisation code for domain
     *
     * @return bool
     *
     * @throws Api_Odr_Exception
     */
    public function transferDomain($domain, $nameservers = array(), $whois = null, $authcode = '')
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        $tld = substr(stristr($domain, '.'), 1);

        $ownerHandle = $this->_obtainHandle($domain, $whois, HANDLE_OWNER);

        if (!$ownerHandle) {
            return $ownerHandle;
        }

        $adminHandle = $this->_obtainHandle($domain, $whois, HANDLE_ADMIN);

        if (!$adminHandle) {
            return $adminHandle;
        }

        $techHandle = $this->_obtainHandle($domain, $whois, HANDLE_TECH);

        if (!$techHandle) {
            return $techHandle;
        }

        $this->_checkPeriod($tld);

        $parameters = array(
            'contact_registrant' => $ownerHandle,
            'contact_tech'       => $techHandle,
            'contact_onsite'     => $adminHandle,
            'auth_code'          => $authcode,
        );

        $parameters = array_merge($parameters, $this->convertNameservers($domain, $nameservers));

        try {
            $this->odr->transferDomain($domain, $parameters);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        if (!empty($result['response']['status']) && $result['response']['status'] === 'FAILED') {
            return $this->parseError($result['response']['data']['message']);
        }

        return $this->_checkResult(true);
    }

    /**
     * Delete a domain
     *
     * @param string $domain  The name of the domain that you want to delete
     * @param string $delType end|now
     *
     * @return bool Was domain successfully removed or not
     */
    public function deleteDomain($domain, $delType = 'end')
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        $deleteDate = time();

        if ($delType === 'end') {
            try {
                $this->odr->getDomainInfo($domain);
            } catch (Api_Odr_Exception $e) {
                $this->Error[] = $e->getMessage();

                return false;
            }

            $result = $this->odr->getResult();

            if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
                return $this->parseError($result['response']);
            }

            $deleteDate = strtotime('-2 days', strtotime($result['response']['expiration_date']));
        }

        $deleteDate = max($deleteDate, strtotime('+2 hours'));

        try {
            $this->odr->deleteDomain($domain, date('c', $deleteDate));
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        if (!empty($result['response']['status']) && $result['response']['status'] === 'FAILED') {
            return $this->parseError($result['response']['data']['message']);
        }

        return $this->_checkResult(true);
    }

    /**
     * Get all available information of the given domain
     *
     * @param mixed $domain The domain for which the information is requested
     *
     * @return array|bool
     */
    public function getDomainInformation($domain)
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        try {
            $this->odr->getDomainInfo($domain);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        if (!empty($result['response']['status']) && $result['response']['status'] === 'FAILED') {
            return $this->parseError($result['response']['data']['message']);
        }

        $response = $result['response'];

        $nameservers = array();

        foreach ($response['nameservers'] as $ns) {
            if (empty($ns)) {
                continue;
            }

            $nameservers[] = is_array($ns) ? $ns['host'] : $ns;
        }

        $whois = $this->getContact($response['contacts_map']['REGISTRANT']);

        $whois->adminHandle = $response['contacts_map']['ONSITE'];
        $whois->techHandle  = !empty($response['contacts_map']['TECH']) ? $response['contacts_map']['TECH'] : null;

        $authkey        = empty($response['auth_code']) ? null : $response['auth_code'];
        $expirationDate = date('Y-m-d', strtotime((empty($response['expiration_date']) ? $response['expiration_at'] : $response['expiration_date'])));

        return array(
            'Domain'      => $domain,
            'Information' => array(
                'nameservers'       => $nameservers,
                'whois'             => $whois,
                'expiration_date'   => $expirationDate,
                'registration_date' => '',
                'authkey'           => $authkey,
                'auto_renew'        => empty($response['autorenew']) ? '' : strtolower($response['autorenew']),
            ),
        );
    }

    /**
     * Get a list of all the domains
     *
     * @param string $contactHandle The handle of a contact, so the list could be filtered (useful for updating domain whois data)
     *
     * @return array|bool
     *
     * @throws Api_Odr_Exception
     */
    public function getDomainList($contactHandle = '')
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        $filter = array();

        if ($contactHandle) {
            $filter['contact'] = $contactHandle;
        }

        $tlds = $this->getAvailableTlds();

        if (!empty($tlds)) {
            $filter['tlds'] = implode(',', $tlds);
        }

        $this->odr->getDomains($filter);

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        if (!empty($result['response']['status']) && $result['response']['status'] === 'FAILED') {
            return $this->parseError($result['response']['data']['message']);
        }

        $domains = array();

        foreach ($result['response'] as $domain) {
            $domains[] = array(
                'Domain'      => $domain['api_handle'] .'.'. $domain['tld'],
                'Information' => array(
                    'nameservers' => array(),
                    'whois'       => null,
                    'expires'     => date('Y-m-d', strtotime((!empty($domain['expiration_date']) ? $domain['expiration_date'] : $domain['expiration_at']))),
                    'regdate'     => '',
                    'authkey'     => '',
                ),
            );
        }

        return $domains;
    }

    /**
     * Change the lock status of the specified domain
     * Warning! Open Domain Registry does NOT support locking domains, because we're using authorization codes
     * We might add this feature in the future, though
     *
     * @param string $domain The domain to change the lock state for
     * @param bool   $isLock New lock state
     *
     * @return bool True is the lock state was changed succesfully
     */
    public function lockDomain($domain, $isLock = true)
    {
        return $this->parseError('Helaas bezit de API van ODR geen mogelijkheid om domeinnamen te locken. Er wordt niets uitgevoerd.');

        if (!$this->_checkLogin()) {
            return false;
        }

        $this->odr->lockDomain($domain, $isLock);

        return $this->_checkResult(true);
    }

    /**
     * Change the autorenew state of the given domain. When autorenew is enabled, the domain will be extended
     *
     * @param string $domain    The domain name to change the autorenew setting for
     * @param bool   $autorenew The new autorenew value (True = On|False = Off)
     *
     * @return bool True when the setting is succesfully changed, False otherwise
     */
    public function setDomainAutoRenew($domain, $autorenew = true)
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        try {
            $this->odr->setAutorenew($domain, $autorenew);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']['message'], $result['code']);
        }

        // Autorenew method always return true if no error happened
        return true;
    }

    /**
     * Get EPP code/token
     *
     * @param mixed $domain Domain name
     *
     * @return null|string|bool
     */
    public function getToken($domain)
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        try {
            $this->odr->getDomainAuthCode($domain);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        return empty($result['response']['auth_code']) ? null : $result['response']['auth_code'];
    }

    /**
     * Check domain information for one or more domains
     *
     * @param array $domains Array with list of domains. Key is domain, value must be filled
     *
     * @return array
     */
    public function getSyncData($domains)
    {
        $limit   = 10;
        $checked = 0;

        foreach ($domains as $domain => &$value) {
            $data = $this->getDomainInformation($domain);

            if ($data === false) {
                $value['Status']    = 'error';
                $value['Error_msg'] = 'Either domain not found or internal error happened';

                continue;
            }

            $checked++;

            if ($limit !== null && $checked > $limit) {
                break;
            }

            /** @var array $data  Because PHPStorm */
            /** @var array $value Because PHPStorm */

            $value = $data;

            $value['Information']['nameservers']     = $data['Information']['nameservers'];
            $value['Information']['expiration_date'] = (isset($data['Information']['expiration_date'])) ? $data['Information']['expiration_date'] : '';
            $value['Information']['auto_renew']      = empty($data['Information']['auto_renew']) ? '' : $data['Information']['auto_renew'];

            $value['Status'] = 'success';
        }

        unset($value);

        // Return list  (domains which aren't completed with data, will be synced by a next cronjob)
        return $domains;
    }

    /**
     * Update the domain Whois data, but only if no handles are used by the registrar
     *
     * @param mixed $domain
     * @param Whois $whois
     *
     * @return bool
     *
     * @throws Api_Odr_Exception
     */
    public function updateDomainWhois($domain, $whois)
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        $ownerHandle = $this->_obtainHandle($domain, $whois, HANDLE_OWNER);

        if (!$ownerHandle) {
            return false;
        }

        $adminHandle = $this->_obtainHandle($domain, $whois, HANDLE_ADMIN);

        if (!$adminHandle) {
            return false;
        }

        $techHandle = $this->_obtainHandle($domain, $whois, HANDLE_TECH);

        if (!$techHandle) {
            return false;
        }

        try {
            $this->odr->getDomainInfo($domain);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        $response = $result['response'];

        $response['contact_registrant'] = $ownerHandle;
        $response['contact_tech']       = $techHandle;
        $response['contact_onsite']     = $adminHandle;

        try {
            $this->odr->updateDomain($domain, $response);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        return $this->_checkResult(true);
    }

    /**
     * Returns domain whois handles
     *
     * @param mixed $domain Domain name
     *
     * @return array|bool Domain info with handles
     *
     * @throws Api_Odr_Exception
     */
    public function getDomainWhois($domain)
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        try {
            $this->odr->getDomainInfo($domain);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        $response = $result['response'];

        $contacts = array(
            'ownerHandle' => $response['contacts_map']['REGISTRANT'],
            'adminHandle' => $response['contacts_map']['ONSITE'],
            'techHandle'  => empty($response['contacts_map']['TECH']) ? null : $response['contacts_map']['TECH'],
        );

        return $contacts;
    }

    /**
     * Create a new whois contact
     *
     * @param Whois  $whois The whois information for the new contact
     * @param string $type  The contact type. This is only used to access the right data in the $whois object
     *
     * @return bool Is new contact was created succesfully or not
     *
     * @throws Api_Odr_Exception
     */
    public function createContact($whois, $type = HANDLE_OWNER)
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        $contact = $this->mapWhoisToContact($whois, $type);

        try {
            $this->odr->createContact($contact);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $result = $this->odr->getResult();

        return $this->_checkResult(empty($result['response']['data']['id']) ? false : $result['response']['data']['id']);
    }

    /**
     * Update the whois information for the given contact person
     *
     * @param string $handle The handle of the contact to be changed
     * @param Whois  $whois  The new whois information for the given contact
     * @param mixed  $type   The of contact. This is used to access the right fields in the whois array
     *
     * @return bool
     */
    public function updateContact($handle, $whois, $type = HANDLE_OWNER)
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        $contact = $this->mapWhoisToContact($whois, $type);

        try {
            $this->odr->updateContact($handle, $contact);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        return $this->_checkResult(true);
    }

    /**
     * Get information available of the requested contact
     *
     * @param string $handle The handle of the contact to request
     *
     * @return Whois|bool Information available about the requested contact
     */
    public function getContact($handle)
    {
        if (!$handle || !$this->_checkLogin()) {
            return false;
        }

        $whois = new Whois;

        try {
            $this->odr->getContact($handle);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        $response = $result['response'];

        $whois->ownerCompanyName      = $response['full_name'];
        $whois->ownerTaxNumber        = empty($response['company_vatin']) ? null : $response['company_vatin'];
        $whois->ownerCompanyLegalForm = $response['organization_legal_form'];

        $whois->ownerSex      = $response['gender'] === 'FEMALE' ? 'f' : 'm';
        $whois->ownerInitials = $response['initials'];
        $whois->ownerSurName  = $response['last_name'];
        $whois->ownerAddress  = $response['street'] . ' ' . $response['house_number'];
        $whois->ownerZipCode  = $response['postal_code'];
        $whois->ownerCity     = $response['city'];

        $whois->ownerCountry      = $response['country'];
        $whois->ownerState        = $response['state'];
        $whois->ownerRegion       = $response['state'];
        $whois->ownerPhoneNumber  = $response['phone'];
        $whois->ownerFaxNumber    = $response['fax'];
        $whois->ownerEmailAddress = $response['email'];

        return $whois;
    }

    /**
     * Get the handle of a contact
     *
     * @param Whois  $whois The whois information of contact
     * @param string $type  The type of person. This is used to access the right fields in the whois object
     *
     * @return string handle of the requested contact; False if the contact could not be found
     *
     * @throws Api_Odr_Exception
     */
    public function getContactHandle(Whois $whois, $type = HANDLE_OWNER)
    {
        $prefix = $this->_getContactPrefix($type);

        $contacts = $this->getContactList($whois->{$prefix . 'SurName'});
        $toCheck  = array();

        if (empty($contacts)) {
            return false;
        }

        foreach ($contacts as $contact) {
            if (strpos($contact['CompanyName'], trim($whois->{$prefix . 'Initials'} . ' ' . $whois->{$prefix . 'SurName'})) !== false) {
                $toCheck[] = $contact['Handle'];
            }
        }

        $contacts = $toCheck;

        foreach ($contacts as $contactId) {
            $contact = $this->getContact($contactId);

            if (
                $whois->{$prefix . 'SurName'} === $contact->{$prefix . 'SurName'} &&
                $whois->{$prefix . 'CompanyName'} === $contact->{$prefix . 'CompanyName'} &&
                $whois->{$prefix . 'EmailAddress'} === $contact->{$prefix . 'EmailAddress'} &&
                ($whois->{$prefix . 'Initials'} === $contact->{$prefix . 'Initials'} || str_replace('.', '', $whois->{$prefix . 'Initials'}) === $contact->{$prefix . 'Initials'})
            )
            {
                return $contactId;
            }
        }

        return false;
    }

    /**
     * Get a list of contact handles available
     *
     * @param string $surname Surname to limit the number of records in the list
     *
     * @return array|bool List of all contact matching the $surname search criteria
     *
     * @throws Api_Odr_Exception
     */
    public function getContactList($surname = '')
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        $filter = array();

        if ($surname) {
            $filter['full_name'] = $surname;
        }

        try {
            $this->odr->getContacts($filter);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        $contacts = array();

        foreach ($result['response'] as $contact) {
            $contacts[] = array(
                'Handle'      => $contact['id'],
                'CompanyName' => $contact['name'],
            );
        }

        return $contacts;
    }

    /**
     * Update the nameservers for the given domain
     *
     * @param string $domain      The domain to be changed
     * @param array  $nameservers The new set of nameservers
     *
     * @return bool Was update successful or not
     */
    public function updateNameServers($domain, $nameservers = array())
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        try {
            $this->odr->getDomainInfo($domain);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $info = $this->odr->getResult();

        if ($info['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($info['response']);
        }

        $info = $info['response'];

        $parameters = array(
            'domain_name' => $info['domain_name'],
            'auth_code'   => $info['auth_code'],
        );

        foreach ($info['contacts_map'] as $contactType => $contactId) {
            $parameters['contact_' . strtolower($contactType)] = $contactId;
        }

        foreach (range(1, 8) as $r) {
            if (empty($nameservers["ns{$r}"])) {
                continue;
            }

            $ns = array(
                'host' => $nameservers["ns{$r}"],
                'ip'   => null,
            );

            if (!empty($nameservers["ns{$r}ip"]) && strpos($ns['host'], $domain)) {
                $ns['ip'] = $nameservers["ns{$r}ip"];
            }

            $parameters["ns{$r}"] = $ns;
        }

        try {
            $this->odr->updateDomain($domain, $parameters);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        return $this->_checkResult(true);
    }

    /**
     * Get class version information
     *
     * @return array
     *
     * @static
     */
    static public function getVersionInformation()
    {
        /** @var array $version */
        return include self::$_versionFile;
    }

    /**
     * Resets the session, if access token exists, tries to use it
     *
     * @return bool
     */
    public function reset()
    {
        if (!$this->AccessToken && !empty($_SESSION[self::ODR_TOKEN_SESSION])) {
            $this->AccessToken = $_SESSION[self::ODR_TOKEN_SESSION];
        }

        if (strpos($this->User, 'public$') !== 0) {
            return $this->parseError('Vul de API key als gebruikersnaam in en de API secret als wachtwoord.');
        }

        if (!$this->odr) {
            $config = array(
                'api_key'    => $this->User,
                'api_secret' => $this->Password,
                'url'        => $this->Testmode ? self::URL_TEST : self::URL_LIVE,
            );

            $this->odr = new Api_Odr($config);
        }

        if ($this->AccessToken && is_string($this->AccessToken)) {
            try {
                $this->odr->setHeader('X-Access-Token', $this->AccessToken);

                $this->odr->getMe();
            } catch (Api_Odr_Exception $e) {
                if (!empty($_SESSION[self::ODR_TOKEN_SESSION])) {
                    unset($_SESSION[self::ODR_TOKEN_SESSION]);
                }

                $this->AccessToken = false;

                $this->odr->setHeader('X-Access-Token', null);
            }

            if ($this->AccessToken) {
                return true;
            }
        }

        try {
            $this->odr->login();

            $loginResult = $this->odr->getResult();

            if ($loginResult['status'] === Api_Odr::STATUS_ERROR) {
                return $this->parseError($loginResult['response'], $loginResult['code']);
            }

            $this->AccessToken = $loginResult['response']['token'];

            $_SESSION[self::ODR_TOKEN_SESSION] = $this->AccessToken;
        } catch(Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Returns current access token or FALSE, in case access token not defined
     *
     * @return bool|string
     */
    public function getAccessToken()
    {
        return $this->AccessToken;
    }

    /**
     * "Parses" the error message, prefixing it with "ODR: $code - $message"
     *
     * @param string|array $message Error message
     * @param string|int   $code    Error code
     *
     * @return bool
     */
    public function parseError($message, $code = '')
    {
        if (is_array($message)) {
            $message = $message['message'];
        }

        $this->Error[] = 'ODR: ' . ($code ? $code . ' - ' : '') . $message;

        return false;
    }

    /**
     * Checks the period for correct value
     *
     * @param string $tld
     *
     * @return int
     *
     * @protected
     */
    protected function _checkPeriod($tld)
    {
        $this->Period = 1;

        if (in_array($tld, $this->TldPeriod2, true)) {
            $this->Period = 2;
        } elseif (in_array($tld, $this->TldPeriod3, true)) {
            $this->Period = 3;
        } elseif (in_array($tld, $this->TldPeriod10, true)) {
            $this->Period = 10;
        }

        return $this->Period;
    }

    /**
     * Checks the result of the last request and returns {$value}
     * Can also perform logout on request
     *
     * @param mixed $value    Return value
     * @param bool  $isLogout Should logout be performed or not
     *
     * @return mixed
     *
     * @protected
     */
    protected function _checkResult($value, $isLogout = false)
    {
        $result = $this->odr->getResult();

        if ($isLogout) {
            $this->_checkLogout($isLogout);
        }

        if (!empty($result['response']['status']) && $result['response']['status'] === 'FAILED') {
            return $this->parseError($result['response']['data']['message']);
        }

        if ($result['status'] === Api_Odr::STATUS_SUCCESS) {
            return $value;
        }

        if (empty($result['response']['data']) || !is_array($result['response']['data'])) {
            return $this->parseError($result['response']['message']);
        }

        $error = array();

        foreach ($result['response']['data'] as $fField => $fErr) {
            $error[] = '[' . $fField . '] ' . $fErr;
        }

        $result['response']['message'] .= ': ' . implode(', ', $error);

        return $this->parseError($result['response']['message']);
    }

    /**
     * Checks access token and performs logout on request
     * Temporary disabled, to keep access token in session, should speed things up significantly
     *
     * @param bool $isLogout Should logout be performed or not
     *
     * @protected
     */
    protected function _checkLogout($isLogout)
    {
        return;

        if (!$isLogout) {
            return;
        }

        $this->odr->custom('/user/logout');

        $this->AccessToken = false;
    }

    /**
     * Maps WeFact contact types to ODR contact types
     *
     * @param string $type Contact type
     *
     * @return string
     *
     * @protected
     */
    protected function _getContactPrefix($type)
    {
        $prefix = '';

        // Determine which contact type should be found
        switch ($type) {
            case HANDLE_OWNER:
                    $prefix = 'owner';
                break;
            case HANDLE_ADMIN:
                    $prefix = 'admin';
                break;
            case HANDLE_TECH:
                    $prefix = 'tech';
                break;
            default:
                break;
        }

        return $prefix;
    }

    /**
     * Checks login
     *
     * @return bool
     *
     * @protected
     */
    protected function _checkLogin()
    {
        if ($this->AccessToken) {
            return true;
        }

        return $this->reset();
    }

    /**
     * Obtains single handle for domain
     *
     * @param string $domain Domain name
     * @param Whois  $whois  Instance of "whois" class
     * @param string $key    Defined variable (HANDLE_OWNER, HANDLE_ADMIN, HANDLE_TECH, etc)
     *
     * @return bool|string
     *
     * @throws Api_Odr_Exception
     *
     * @protected
     */
    protected function _obtainHandle($domain, $whois, $key)
    {
        $prefix = $this->_getContactPrefix($key);

        // Check if a registrar-specific handle for this domain already exists
        if ($whois !== null && (isset($whois->{$prefix . 'RegistrarHandles'}[$this->ClassName]) || !empty($this->registrarHandlesMapped[$whois->{$prefix . 'WeFactHandle'}]))) {
            $handle = empty($whois->{$prefix . 'RegistrarHandles'}[$this->ClassName]) ? $this->registrarHandlesMapped[$whois->{$prefix . 'WeFactHandle'}] : $whois->{$prefix . 'RegistrarHandles'}[$this->ClassName];

            if (empty($whois->{$prefix . 'RegistrarHandles'}[$this->ClassName])) {
                $whois->{$prefix . 'RegistrarHandles'}[$this->ClassName] = $this->registrarHandlesMapped[$whois->{$prefix . 'WeFactHandle'}];
            }
        }
        // If not, check if WHOIS-data for contact is available to search or create new handle
        elseif ($whois !== null && $whois->{$prefix . 'SurName'} != '') {
            // Search for existing handle, based on WHOIS data
            $handle = $this->getContactHandle($whois, $key);

            // If no existing handle is found, create new handle
            if (!$handle && !$handle = $this->createContact($whois, $key)) {
                return false;
            }

            // If a new handle is created or found, store in array. WeFact will store this data, which will result in faster registration next time
            $this->registrarHandles[$prefix]                                  = $handle;
            $this->registrarHandlesMapped[$whois->{$prefix . 'WeFactHandle'}] = $handle;
        } else {
            // If no handle can be created, because data is missing, quit function
            return $this->parseError(sprintf("No domain {$prefix} contact given for domain '%s'.", $domain));
        }

        return $handle;
    }

    /**
     * Returns mapped legal form
     *
     * @param Whois  $whois  Instance of "whois" class
     * @param string $prefix Prefix (owner, admin, tech, etc)
     *
     * @return string
     *
     * @protected
     */
    protected function _getLegalForm(Whois $whois, $prefix)
    {
        if (!$whois->{$prefix . 'CompanyName'}) {
            return 'PERSOON';
        }

        return (!$whois->{$prefix . 'CompanyLegalForm'} || substr($whois->{$prefix . 'CompanyLegalForm'}, 0, 3) === 'BE-') ? 'ANDERS' : $whois->{$prefix . 'CompanyLegalForm'};
    }

    /**
     * Maps WeFact Whois object to ODR contact format
     *
     * @param Whois $whois Whois object
     * @param mixed $type  Contact Type
     *
     * @return array
     */
    public function mapWhoisToContact(Whois $whois, $type)
    {
        $prefix = $this->_getContactPrefix($type);

        // Some help-functions, to obtain more formatted data
        $whois->getParam($prefix, 'StreetName');  // Not only Address, but also StreetName, StreetNumber and StreetNumberAddon are available after calling this function
        $whois->getParam($prefix, 'CountryCode'); // Phone and faxnumber are split. CountryCode, PhoneNumber and FaxNumber available. CountryCode contains for example '+31'. PhoneNumber contains number without leading zero e.g. '123456789'

        $legalForm = $this->_getLegalForm($whois, $prefix);

        $gender = 'NA';

        if (in_array(strtoupper($whois->{$prefix . 'Sex'}), array('F', 'M'), true)) {
            $gender = strtoupper($whois->{$prefix . 'Sex'}) === 'F' ? 'FEMALE' : 'MALE';
        }

        return array(
            'first_name'              => $whois->{$prefix . 'Initials'},
            'last_name'               => $whois->{$prefix . 'SurName'},
            'full_name'               => trim($whois->{$prefix . 'Initials'} . ' ' . $whois->{$prefix . 'SurName'}),
            'initials'                => $whois->{$prefix . 'Initials'},
            //birthday
            'state'                   => isset($whois->{$prefix . 'State'}) ? $whois->{$prefix . 'State'} : $whois->{$prefix . 'Region'},
            'city'                    => $whois->{$prefix . 'City'},
            'postal_code'             => strtoupper(str_replace(' ', '', $whois->{$prefix . 'ZipCode'})),
            'phone'                   => $whois->{$prefix . 'CountryCode'} . '.' . str_replace(' ', '', $whois->{$prefix . 'PhoneNumber'}),
            'email'                   => $whois->{$prefix . 'EmailAddress'},
            'country'                 => $whois->{$prefix . 'Country'},
            'language'                => 'NL',
            'gender'                  => $gender,
            'street'                  => $whois->{$prefix . 'StreetName'},
            'house_number'            => trim($whois->{$prefix . 'StreetNumber'} . ' ' . $whois->{$prefix . 'StreetNumberAddon'}),
            //url
            'company_name'            => $whois->{$prefix . 'CompanyName'} ?: trim($whois->{$prefix . 'Initials'} . ' ' . $whois->{$prefix . 'SurName'}),
            'company_email'           => $whois->{$prefix . 'EmailAddress'},
            'company_street'          => $whois->{$prefix . 'StreetName'},
            'company_house_number'    => trim($whois->{$prefix . 'StreetNumber'} . ' ' . $whois->{$prefix . 'StreetNumberAddon'}),
            'company_postal_code'     => strtoupper(str_replace(' ', '', $whois->{$prefix . 'ZipCode'})),
            'company_city'            => $whois->{$prefix . 'City'},
            'company_phone'           => $whois->{$prefix . 'CountryCode'} . '.' . str_replace(' ', '', $whois->{$prefix . 'PhoneNumber'}),
            //company_url
            'company_vatin'           => $whois->{$prefix . 'TaxNumber'},
            'organization_legal_form' => $legalForm,
        );
    }

    /**
     * Converts nameservers from WeFact format ("ns1" for host, "ns1ip" for IP) to array format (array("host" for host, "ip" for IP))
     *
     * @param string $domain
     * @param array  $nameservers
     *
     * @return array
     */
    public function convertNameservers($domain, array $nameservers)
    {
        $parameters = array();

        foreach (range(1, 8) as $r) {
            if (empty($nameservers["ns{$r}"])) {
                continue;
            }

            $ns = array(
                'host' => $nameservers["ns{$r}"],
                'ip'   => null,
            );

            if (!empty($nameservers["ns{$r}ip"]) && strpos($ns['host'], $domain)) {
                $ns['ip'] = $nameservers["ns{$r}ip"];
            }

            $parameters["ns{$r}"] = $ns;
        }

        return $parameters;
    }
}