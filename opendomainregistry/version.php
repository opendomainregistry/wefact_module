<?php
$version = array();

$version['name']             = 'Open Domain Registry';
$version['api_version']      = '3.2';
$version['date']             = '2018-04-10';
$version['wefact_version']   = '4.0';
$version['hostfact_version'] = '4.0';

$version['dev_logo']    = '//www.opendomainregistry.net/assets/img/frontend/logotype.png';
$version['dev_author']  = 'Open Domain Registry';
$version['dev_website'] = 'https://www.opendomainregistry.net';
$version['dev_email']   = 'support@opendomainregistry.net';
$version['dev_phone']   = '+31 (0) 165 318788';

$version['autorenew']      = true;
$version['handle_support'] = true;
$version['cancel_direct']  = true;
$version['cancel_expire']  = true;
$version['domain_support'] = true;
$version['ssl_support']    = false;

$version['dns_management_support'] = false;
$version['dns_templates_support']  = false;
$version['dns_records_support']    = false;

return $version;

/**
 * Changelog
 *
 * 4.0 - Migration to HostFact
 * 3.9 - Added state/region to contacts
 * 3.8 - Update in ODR API
 * 3.7 - Nieuwe API URL
 * 3.6 - Nieuwe ODR API
 */