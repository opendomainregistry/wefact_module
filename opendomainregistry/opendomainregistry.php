<?php
require_once '3rdparty/domain/IRegistrar.php';
require_once '3rdparty/domain/standardfunctions.php';
require_once '3rdparty/domain/opendomainregistry/helpers.php';

class opendomainregistry implements IRegistrar
{
    const URL_LIVE = 'https://api.opendomainregistry.net';
    const URL_TEST = 'http://api.odrregistry.nl';

    public $User;
    public $Password;

    public $Error;
    public $Warning;
    public $Success;

    public $Period = 1;
    public $registrarHandles = array();

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

        // Calls
        try {
            $this->odr->checkDomain($domain);

            $result = $this->odr->getResult();

            // Build response
            if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
                return $this->parseError($result['response']);
            }

            return (bool)$result['response']['available'];
        } catch(Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }
    }

    /**
     * Register a new domain
     * 
     * @param string $domain      The domainname that needs to be registered
     * @param array  $nameservers The nameservers for the new domain
     * @param whois  $whois       The customer information for the domain's whois information
     *
     * @return bool
     */
    public function registerDomain($domain, $nameservers = array(), $whois = null)
    {
        $loggedIn = $this->_checkLogin();

        if (!$loggedIn) {
            return false;
        }

        $tld = substr(stristr($domain, '.'), 1);

        /**
         * Step 1) obtain an owner handle
         */
        $ownerHandle = $this->_obtainHandle($domain, $whois, 'owner', HANDLE_OWNER);

        if (!$ownerHandle) {
            return $ownerHandle;
        }

        $adminHandle = $this->_obtainHandle($domain, $whois, 'admin', HANDLE_ADMIN);

        if (!$adminHandle) {
            return $adminHandle;
        }

        $techHandle = $this->_obtainHandle($domain, $whois, 'tech', HANDLE_TECH);

        if (!$techHandle) {
            return $techHandle;
        }

        /**
         * Step 5) check your own default settings
         */
        $this->_checkPeriod($tld);

        $period = $this->Period * 12;

         /**
         * Step 6) register domain
         */
        $parameters = array(
            'period'             => $period,
            'contact_registrant' => $ownerHandle,
            'contact_tech'       => $techHandle,
            'contact_onsite'     => $adminHandle,
            'auth_code'          => substr(md5(time()), 0, 6),
        );
        
        $parameters = array_merge($parameters, $nameservers);

        // Calls
        try {
            $this->odr->registerDomain($domain, $parameters);

            return $this->_checkResult(true, $loggedIn);
        } catch(Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }
    }
    
    /**
     * Transfer a domain to the given user
     * 
     * @param string $domain      Domain name for transfer
     * @param array  $nameservers The nameservers for the transfered domain
     * @param whois  $whois       The contact information for the new owner, admin, tech and billing contact
     * @param string $authcode    Authorisation code for domain
     *
     * @return bool
     */
    public function transferDomain($domain, $nameservers = array(), $whois = null, $authcode = '')
    {
        $loggedIn = $this->_checkLogin();

        if (!$loggedIn) {
            return false;
        }
        
        $tld = substr(stristr($domain, '.'), 1);

        /**
         * Step 1) obtain an owner handle
         */
        $ownerHandle = $this->_obtainHandle($domain, $whois, 'owner', HANDLE_OWNER);

        if (!$ownerHandle) {
            return $ownerHandle;
        }

        $adminHandle = $this->_obtainHandle($domain, $whois, 'admin', HANDLE_ADMIN);

        if (!$adminHandle) {
            return $adminHandle;
        }

        $techHandle = $this->_obtainHandle($domain, $whois, 'tech', HANDLE_TECH);

        if (!$techHandle) {
            return $techHandle;
        }

        /**
         * Step 5) check your own default settings
         */
        // Determine period for registration in years, based on your TLD
        // $this->Period is also used in WeFact, for determining the renewal date
        $this->_checkPeriod($tld);

        /**
         * Step 6) register domain
         */
        // Start registering the domain, you can use $domain, $ownerHandle, $adminHandle, $techHandle, $nameservers        
        $parameters = array(
            'contact_registrant' => $ownerHandle,
            'contact_tech'       => $techHandle,
            'contact_onsite'     => $adminHandle,
            'auth_code'          => $authcode,
        );
        
        $parameters = array_merge($parameters, $nameservers);

        // Calls
        $this->odr->transferDomain($domain, $parameters);

        return $this->_checkResult(true, $loggedIn);
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
        $loggedIn = $this->_checkLogin();

        if (!$loggedIn) {
            return false;
        }

        $delete_date = time();

        if ($delType === 'end') {
            $this->odr->custom('/domain/info/' . $domain);

            $result = $this->odr->getResult();

            if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
                return $this->parseError($result['response']);
            }

            $delete_date = strtotime('-2 days', strtotime($result['response']['expiration_date']));
        }

        // Date must be in future
        $delete_date = max($delete_date, strtotime('+2 hours'));

        // Overwrite setting
        $parameters = array('deleted_at' => date('c', $delete_date));

        // Calls
        $this->odr->custom('/domain/' . $domain .'/', Api_Odr::METHOD_DELETE, $parameters);

        return $this->_checkResult(true, $loggedIn);
    }

    /**
     * Get all available information of the given domain
     *
     * @param mixed $domain The domain for which the information is requested
     *
     * @return array
     */
    public function getDomainInformation($domain)
    {
        $loggedIn = $this->_checkLogin();

        if (!$loggedIn) {
            return false;
        }

        /**
         * Step 1) update nameservers for domain
         */
        $this->odr->custom('/domain/info/' . $domain);

        $result = $this->odr->getResult();

        // Logout
        $this->_checkLogout($loggedIn);

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        $nameservers = array();

        foreach ($result['response'] as $k => $v) {
            if (strpos($k, 'ns') !== 0) {
                continue;
            }

            $nameservers[] = is_array($v) ? $v['host'] : $v;
        }

        $whois = $this->getContact($result['response']['contact_registrant']);

        $whois->adminHandle = $result['response']['contact_onsite'];
        $whois->techHandle  = $result['response']['contact_tech'];

        $authkey         = $result['response']['auth_code'];
        $expiration_date = substr($result['response']['expiration_date'], 0, 10);

        /**
         * Step 2) provide feedback to WeFact
         */

        // Return array with data
        $response = array(
            'Domain'      => $domain,
            'Information' => array(
                'nameservers'       => $nameservers, // Array with 1, 2 or 3 elements (hostnames)
                'whois'             => $whois, // Whois object
                'expiration_date'   => $expiration_date,
                'registration_date' => '',
                'authkey'           => $authkey,
            ),
        );

        return $response;
    }
    
    /**
     * Get a list of all the domains
     * 
     * @param string $contactHandle The handle of a contact, so the list could be filtered (useful for updating domain whois data)
     *
     * @return array
     */
    public function getDomainList($contactHandle = '')
    {
        $loggedIn = $this->_checkLogin();

        if (!$loggedIn) {
            return false;
        }

        $filter = array(
            'contact' => $contactHandle,
        );

        /**
         * Step 1) query domain
         */
        $this->odr->getDomains($filter);

        $result = $this->odr->getResult();

        // Logout
        $this->_checkLogout($loggedIn);

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        /**
         * Step 2) provide feedback to WeFact
         */
        $domains = array();

        // Loop for all domains:
        foreach ($result['response'] as $domain) {
            // Return array with data, later on....getDomainInformation will add other information
            $response = array(
                'Domain'      => $domain['domain_name'],
                'Information' => array(
                    'nameservers' => array(),
                    'whois'       => null,
                    'expires'     => '',
                    'regdate'     => '',
                    'authkey'     => '',
                )
            );

            $domains[] = $response;
        }

        // When loop is ready, return array
        return $domains;
    }

    /**
     * Change the lock status of the specified domain
     * 
     * @param string $domain The domain to change the lock state for
     * @param bool   $lock   The new lock state
     *
     * @return bool True is the lock state was changed succesfully
     */
    public function lockDomain($domain, $lock = true)
    {
        return $this->parseError('Helaas bezit de API van ODR geen mogelijkheid om domeinnamen te locken. Er wordt niets uitgevoerd.');    
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
        $loggedIn = $this->_checkLogin();

        if (!$loggedIn) {
            return false;
        }

        $this->odr->custom('/domain/' . $domain . '/renew-' . ($autorenew ? 'on' : 'off') .'/', Api_Odr::METHOD_PUT);

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        $this->_checkLogout($loggedIn);

        // Autorenew method always return true if no error happened
        return true;
    }

    /**
     * Get EPP code/token
     * 
     * @param mixed $domain Domain name
     *
     * @return null|string
     */
    public function getToken($domain)
    {
        if (!$this->_checkLogin()) {
            return false;
        }

        $this->odr->custom('/domain/auth-code/' . $domain);

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        return $result['response']['auth_code'];
    }

    /**
     * Check domain information for one or more domains
     *
     * @param mixed $domains Array with list of domains. Key is domain, value must be filled
     *
     * @return mixed $list_domains
     */
    public function getSyncData($domains)
    {
        /**
         * Scenario 2: We must request the info per domain. Take care of script timeout
         */
        $limit   = 10;
        $checked = 0;

        // Check domain one for one
        foreach ($domains as $domain => $value) {
            // Ask registrar for information of domain
            $data = $this->getDomainInformation($domain);

            if ($data === false) {
                $domains[$domain]['Status']    = 'error';
                $domains[$domain]['Error_msg'] = 'Domain not found';

                continue;
            }

            // extend the list_domains array with data from the registrar
            $domains[$domain]['Information']['nameservers']        = $data['Information']['nameservers'];
            $domains[$domain]['Information']['expiration_date']    = (isset($data['Information']['expiration_date'])) ? $data['Information']['expiration_date'] : '';
            $domains[$domain]['Information']['auto_renew']         = '';

            $domains[$domain]['Status'] = 'success';

            // Increment counter
            $checked++;

            // Stop loop after max domains
            if ($limit !== null && $checked > $limit) {
                break;
            }
        }

        // Return list  (domains which aren't completed with data, will be synced by a next cronjob)
        return $domains;
    }

    /**
     * Update the domain Whois data, but only if no handles are used by the registrar
     * 
     * @param mixed $domain
     * @param whois $whois
     *
     * @return bool
     */
    public function updateDomainWhois($domain, $whois)
    {
        $loggedIn = $this->_checkLogin();

        if (!$loggedIn) {
            return false;
        }

        /**
         * Step 1) obtain an owner handle
         */
        $ownerHandle = $this->_obtainHandle($domain, $whois, 'owner', HANDLE_OWNER);

        if (!$ownerHandle) {
            return $ownerHandle;
        }

        $adminHandle = $this->_obtainHandle($domain, $whois, 'admin', HANDLE_ADMIN);

        if (!$adminHandle) {
            return $adminHandle;
        }

        $techHandle = $this->_obtainHandle($domain, $whois, 'tech', HANDLE_TECH);

        if (!$techHandle) {
            return $techHandle;
        }

        $this->odr->custom('/domain/info/' . $domain);

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        $parameters = $result['response'];

        // Overwrite setting
        $parameters['contact_registrant'] = $ownerHandle;
        $parameters['contact_tech']       = $techHandle;
        $parameters['contact_onsite']     = $adminHandle;

        // Calls
        $this->odr->updateDomain($domain, $parameters);

        $result = $this->odr->getResult();

        // Logout
        $this->_checkLogout($loggedIn);

        // Build response
        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        return true;
    }
    
    /**
     * Returns domain whois handles
     *
     * @param mixed $domain Domain name
     *
     * @return array Domain info with handles
     */
    public function getDomainWhois($domain)
    {
        $loggedIn = $this->_checkLogin();

        if (!$loggedIn) {
            return false;
        }

        $this->odr->custom('/domain/info/' . $domain);

        $result = $this->odr->getResult();

        // Logout
        $this->_checkLogout($loggedIn);

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        $contacts = array();

        $contacts['ownerHandle'] = $result['response']['contact_registrant'];
        $contacts['adminHandle'] = $result['response']['contact_onsite'];
        $contacts['techHandle']  = $result['response']['contact_tech'];

        /**
         * Step 2) provide feedback to WeFact
         */
        return $contacts;
    }

    /**
     * Create a new whois contact
     *
     * @param whois  $whois The whois information for the new contact
     * @param string $type  The contact type. This is only used to access the right data in the $whois object
     *
     * @return bool Is new contact was created succesfully or not
     */
    public function createContact($whois, $type = HANDLE_OWNER)
    {
        $loggedIn = $this->_checkLogin();

        if (!$loggedIn) {
            return false;
        }

        $prefix = $this->_getContactPrefix($type);

        // Some help-functions, to obtain more formatted data
        $whois->getParam($prefix, 'StreetName');  // Not only Address, but also StreetName, StreetNumber and StreetNumberAddon are available after calling this function
        $whois->getParam($prefix, 'CountryCode'); // Phone and faxnumber are split. CountryCode, PhoneNumber and FaxNumber available. CountryCode contains for example '+31'. PhoneNumber contains number without leading zero e.g. '123456789'

        $legalForm = $this->_getLegalForm($whois, $prefix);

        /**
         * Step 1) Create the contact
         */
        $parameters = array(
            'first_name'              => $whois->{$prefix . 'Initials'},
            'last_name'               => $whois->{$prefix . 'SurName'},
            'full_name'               => $whois->{$prefix . 'Initials'} . ' ' . $whois->{$prefix . 'SurName'},
            'initials'                => $whois->{$prefix . 'Initials'},
            //birthday
            //state
            'city'                    => $whois->{$prefix . 'City'},
            'postal_code'             => strtoupper(str_replace(' ', '', $whois->{$prefix . 'ZipCode'})),
            'phone'                   => $whois->{$prefix . 'CountryCode'} . '.' . str_replace(' ', '', $whois->{$prefix . 'PhoneNumber'}),
            'email'                   => $whois->{$prefix . 'EmailAddress'},
            'country'                 => $whois->{$prefix . 'Country'},
            'language'                => 'NL',
            'gender'                  => (strtoupper($whois->{$prefix . 'Sex'}) === 'M' ? 'MALE' : (strtoupper($whois->{$prefix . 'Sex'}) === 'F' ? 'FEMALE' : 'NA')),
            'street'                  => $whois->{$prefix . 'StreetName'},
            'house_number'            => $whois->{$prefix . 'StreetNumber'} . ' ' . $whois->{$prefix . 'StreetNumberAddon'},
            //url
            'company_name'            => ($whois->{$prefix . 'CompanyName'}) ?: $whois->{$prefix . 'Initials'} . ' ' . $whois->{$prefix . 'SurName'},// We must give a name here (25-08-2015 ODR)
            'company_email'           => $whois->{$prefix . 'EmailAddress'},
            'company_street'          => $whois->{$prefix . 'StreetName'},
            'company_house_number'    => $whois->{$prefix . 'StreetNumber'} . ' ' . $whois->{$prefix . 'StreetNumberAddon'},
            'company_postal_code'     => strtoupper(str_replace(' ', '', $whois->{$prefix . 'ZipCode'})),
            'company_city'            => $whois->{$prefix . 'City'},
            'company_phone'           => $whois->{$prefix . 'CountryCode'} . '.' . str_replace(' ', '', $whois->{$prefix . 'PhoneNumber'}),
            //company_url
            'company_vatin'           => $whois->{$prefix . 'TaxNumber'},
            'organization_legal_form' => $legalForm,
        );

        // Calls
        $this->odr->createContact($parameters);

        $result = $this->odr->getResult();

        return $this->_checkResult(empty($result['response']['id']) ? false : $result['response']['id'], $loggedIn);
    }

    /**
     * Update the whois information for the given contact person
     *
     * @param string $handle The handle of the contact to be changed
     * @param whois  $whois  The new whois information for the given contact
     * @param mixed  $type   The of contact. This is used to access the right fields in the whois array
     *
     * @return bool
     */
    public function updateContact($handle, $whois, $type = HANDLE_OWNER)
    {
        $loggedIn = $this->_checkLogin();

        if (!$loggedIn) {
            return false;
        }

        $prefix = $this->_getContactPrefix($type);

        // Some help-functions, to obtain more formatted data
        $whois->getParam($prefix, 'StreetName');  // Not only Address, but also StreetName, StreetNumber and StreetNumberAddon are available after calling this function
        $whois->getParam($prefix, 'CountryCode'); // Phone and faxnumber are split. CountryCode, PhoneNumber and FaxNumber available. CountryCode contains for example '+31'. PhoneNumber contains number without leading zero e.g. '123456789'

        /**
         * Step 1) Update the contact, it can depend on the modified data which action you should take.
         */

        /**
         * Step 1) Create the contact
         */
        $sex    = strtoupper($whois->{$prefix . 'Sex'});
        $gender = 'NA';

        if (in_array($sex, array('M', 'F'), true)) {
            $gender = $sex === 'M' ? 'MALE' : 'FEMALE';
        }

        $legalForm = $this->_getLegalForm($whois, $prefix);

        $parameters = array(
            'first_name'              => $whois->{$prefix . 'Initials'},
            'last_name'               => $whois->{$prefix . 'SurName'},
            'full_name'               => $whois->{$prefix . 'Initials'} . ' ' . $whois->{$prefix . 'SurName'},
            'initials'                => $whois->{$prefix . 'Initials'},
            //birthday
            //state
            'city'                    => $whois->{$prefix . 'City'},
            'postal_code'             => strtoupper(str_replace(' ', '', $whois->{$prefix . 'ZipCode'})),
            'phone'                   => $whois->{$prefix . 'CountryCode'} . '.' . str_replace(' ', '', $whois->{$prefix . 'PhoneNumber'}),
            'email'                   => $whois->{$prefix . 'EmailAddress'},
            'country'                 => $whois->{$prefix . 'Country'},
            'language'                => 'NL',
            'gender'                  => $gender,
            'street'                  => $whois->{$prefix . 'StreetName'},
            'house_number'            => $whois->{$prefix . 'StreetNumber'} . ' ' . $whois->{$prefix . 'StreetNumberAddon'},
            //url
            'company_name'            => $whois->{$prefix . 'CompanyName'} ?: $whois->{$prefix . 'Initials'} . ' ' . $whois->{$prefix . 'SurName'},// We must give a name here (25-08-2015 ODR)
            'company_email'           => $whois->{$prefix . 'EmailAddress'},
            'company_street'          => $whois->{$prefix . 'StreetName'},
            'company_house_number'    => $whois->{$prefix . 'StreetNumber'} . ' ' . $whois->{$prefix . 'StreetNumberAddon'},
            'company_postal_code'     => strtoupper(str_replace(' ', '', $whois->{$prefix . 'ZipCode'})),
            'company_city'            => $whois->{$prefix . 'City'},
            'company_phone'           => $whois->{$prefix . 'CountryCode'} . '.' . str_replace(' ', '', $whois->{$prefix . 'PhoneNumber'}),
            //company_url
            'company_vatin'           => $whois->{$prefix . 'TaxNumber'},
            'organization_legal_form' => $legalForm,
        );

        // Calls
        $this->odr->custom('/contact/' . $handle, Api_Odr::METHOD_PUT, $parameters);

        return $this->_checkResult(true, $loggedIn);
    }

    /**
     * Get information availabe of the requested contact
     *
     * @param string $handle The handle of the contact to request
     *
     * @return whois Information available about the requested contact
     */
    public function getContact($handle)
    {
        if (!$handle) {
            return false;
        }

        $loggedIn = $this->_checkLogin();

        if (!$loggedIn) {
            return false;
        }

         /**
         * Step 1) Create the contact
         */
        // Create the contact
        $whois = new whois();

        /**
         * Step 1) Search for contact data
         */
        // Calls
        try {
            $this->odr->getContact($handle);
        } catch (Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }

        $result = $this->odr->getResult();

        // Logout
        $this->_checkLogout($loggedIn);

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        /**
         * Step 2) provide feedback to WeFact
         */
        // The contact is found
        $whois->ownerCompanyName      = ($result['response']['contact']['full_name'] !== $result['response']['contact']['company_name']) ? $result['response']['contact']['company_name'] : '';
        $whois->ownerTaxNumber        = $result['response']['contact']['company_vatin'];
        $whois->ownerCompanyLegalForm = $result['response']['contact']['organization_legal_form'];

        $whois->ownerSex      = ($result['response']['contact']['gender'] === 'FEMALE' ? 'f' : 'm');
        $whois->ownerInitials = $result['response']['contact']['first_name'];
        $whois->ownerSurName  = $result['response']['contact']['last_name'];
        $whois->ownerAddress  = $result['response']['contact']['street'] . ' ' . $result['response']['contact']['house_number'];
        $whois->ownerZipCode  = $result['response']['contact']['postal_code'];
        $whois->ownerCity     = $result['response']['contact']['city'];

        $whois->ownerCountry      = $result['response']['contact']['country'];
        $whois->ownerPhoneNumber  = $result['response']['contact']['phone'];
        $whois->ownerFaxNumber    = '';
        $whois->ownerEmailAddress = $result['response']['contact']['email'];

        return $whois;
    }

    /**
     * Get the handle of a contact
     *
     * @param array $whois The whois information of contact
     * @param string $type The type of person. This is used to access the right fields in the whois object
     *
     * @return string handle of the requested contact; False if the contact could not be found
     */
    public function getContactHandle($whois = array(), $type = HANDLE_OWNER)
    {
        $prefix = $this->_getContactPrefix($type);

        /**
         * Step 1) Search for contact data
         */
        $contacts = $this->getContactList($whois->{$prefix . 'SurName'});
        $toCheck  = array();

        foreach ($contacts as $contact) {
            if ($contact['CompanyName'] === trim($whois->{$prefix . 'Initials'} . ' ' . $whois->{$prefix . 'SurName'})) {
                $toCheck[] = $contact['Handle'];
            }
        }

        $contacts = $toCheck;
        
        foreach ($contacts as $contactId) {
            $contact = $this->getContact($contactId);
            
            if (($whois->{$prefix . 'Initials'} == $contact->ownerInitials || str_replace('.', '', $whois->{$prefix . 'Initials'}) == $contact->ownerInitials) &&
                $whois->{$prefix . 'SurName'} === $contact->ownerSurName &&
                $whois->{$prefix . 'CompanyName'} === $contact->ownerCompanyName &&
                $whois->{$prefix . 'EmailAddress'} === $contact->ownerEmailAddress)
            {
                    return $contactId;
            }
          }

        // No handle is found
        return false;    
    }
    
    /**
     * Get a list of contact handles available
     * 
     * @param string $surname Surname to limit the number of records in the list
     *
     * @return array List of all contact matching the $surname search criteria
     */
    public function getContactList($surname = '')
    {
        $loggedIn = $this->_checkLogin();

        if (!$loggedIn) {
            return false;
        }

        $filter = array(
            'full_name' => $surname,
        );

        /**
         * Step 1) Search for contact data
         */
        // Calls
        $this->odr->getContacts($filter);

        $result = $this->odr->getResult();

        $this->_checkLogout($loggedIn);

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

        /**
         * Step 2) provide feedback to WeFact
         */
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
        $loggedIn = $this->_checkLogin();

        if (!$loggedIn) {
            return false;
        }

        /**
         * Step 1) update nameservers for domain
         */
        $this->odr->custom('/domain/info/' . $domain);

        $result = $this->odr->getResult();

        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            return $this->parseError($result['response']);
        }

        $parameters = array_merge($result['response'], $nameservers);

        // Calls
        $this->odr->updateDomain($domain, $parameters);

        return $this->_checkResult(true, $loggedIn);
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
        require_once '3rdparty/domain/opendomainregistry/version.php';

        return $version;
    }

    public function reset()
    {
        if ($this->AccessToken) {
            return false;
        }
    
        // Configuration array, with user API Keys
        $config = array(
            'api_key'    => $this->User,
            'api_secret' => $this->Password,
            'url'        => $this->Testmode ? self::URL_TEST : self::URL_LIVE,
        );

        // Force correct type of credentials
        if (strpos($this->User, 'public$') !== 0) {
            return $this->parseError('Vul de API key als gebruikersnaam in en de API secret als wachtwoord.');
        }

        // Create new instance of API demo class
        $this->odr = new Api_Odr($config);

        // Login into API
        try {
            $this->odr->login();

            $loginResult = $this->odr->getResult();

            if ($loginResult['status'] === Api_Odr::STATUS_ERROR) {
                return $this->parseError($loginResult['response'], $loginResult['code']);
            }

            $this->AccessToken = $loginResult['response']['access_token'];

            return true;
        } catch(Api_Odr_Exception $e) {
            $this->Error[] = $e->getMessage();

            return false;
        }
    }

    /**
     * "Parses" the error message, prefixing it with "ODR: $code - $message"
     *
     * @param string     $message Error message
     * @param string|int $code    Error code
     *
     * @return bool
     */
    public function parseError($message, $code = '')
    {
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

        // Logout
        $this->_checkLogout($isLogout);

        // Build response
        if ($result['status'] !== Api_Odr::STATUS_SUCCESS) {
            if ($result['data'] && is_array($result['data'])) {
                $error_msg = array();

                foreach ($result['data'] as $_field => $_err) {
                    $error_msg[] = '[' . $_field . '] ' . $_err;
                }

                $result['response'] .= ': ' . implode(', ', $error_msg);
            }

            return $this->parseError($result['response']);
        }

        return $value;
    }

    /**
     * Checks access token and performs logout on request
     *
     * @param bool $isLogout Should logout be performed or not
     *
     * @protected
     */
    protected function _checkLogout($isLogout)
    {
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
                    $prefix = 'onsite';
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
     * @param whois  $whois  Instance of "whois" class
     * @param string $prefix Prefix (owner, admin, tech, etc)
     * @param string $key    Defined variable (HANDLE_OWNER, HANDLE_ADMIN, HANDLE_TECH, etc)
     *
     * @return bool|string
     *
     * @protected
     */
    protected function _obtainHandle($domain, whois $whois, $prefix, $key)
    {
        $handle = '';

        // Check if a registrar-specific ownerhandle for this domain already exists.
        if (isset($whois->{$prefix . 'RegistrarHandles'}[$this->ClassName])) {
            $handle = $whois->{$prefix . 'RegistrarHandles'}[$this->ClassName];
        }
        // If not, check if WHOIS-data for owner contact is available to search or create new handle
        elseif ($whois->{$prefix . 'SurName'} != '') {
            // Search for existing handle, based on WHOIS data
            $handle = $this->getContactHandle($whois, $key);

            // If no existing handle is found, create new handle
            if ($handle == '' && !$handle = $this->createContact($whois, $key)) {
                return false;
            }

            // If a new handle is created or found, store in array. WeFact will store this data, which will result in faster registration next time
            $this->registrarHandles[$prefix] = $handle;
        } else {
            // If no handle can be created, because data is missing, quit function
            return $this->parseError(sprintf("No domain {$prefix} contact given for domain '%s'.", $domain));
        }

        return $handle;
    }

    /**
     * Returns mapped legal form
     *
     * @param whois  $whois  Instance of "whois" class
     * @param string $prefix Prefix (owner, admin, tech, etc)
     *
     * @return string
     *
     * @protected
     */
    protected function _getLegalForm(whois $whois, $prefix)
    {
        if (!$whois->{$prefix . 'CompanyName'}) {
            return 'PERSOON';
        }

        return (!$whois->{$prefix . 'CompanyLegalForm'} || substr($whois->{$prefix . 'CompanyLegalForm'}, 0, 3) === 'BE-') ? 'ANDERS' : $whois->{$prefix . 'CompanyLegalForm'};
    }
}