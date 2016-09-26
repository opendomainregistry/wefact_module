<?php
namespace Mocks;

class Odr extends \Api_Odr
{
    protected function _execute($url = '', $method = self::DEFAULT_METHOD, array $data = array())
    {
        $this->_result = null;

        return $this;
    }

    public function setError($error)
    {
        $this->_error = $error;

        throw new \Api_Odr_Exception(self::MESSAGE_CURL_ERROR_FOUND);
    }

    public function setResult(array $result)
    {
        $this->_result = $result;

        return $this;
    }

    public function login($apiKey = null, $apiSecret = null)
    {
        if ($apiKey === null) {
            $apiKey = $this->_config['api_key'];
        }

        if ($apiSecret === null) {
            $apiSecret = $this->_config['api_secret'];
        }

        if (strpos($apiSecret, 'secret$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (strpos($apiKey, 'public$success') === 0) {
            return $this->setResult(
                array(
                    'status'   => self::STATUS_SUCCESS,
                    'code'     => 200,
                    'response' => array(
                        'token'      => 'token$success',
                        'as_header'  => 'X-Access-Token',
                        'as_request' => 'access_token',
                    ),
                )
            );
        }

        if (!empty($this->_config['tokenLogin'])) {
            if (empty($this->_config['tokenLogin']) || strpos($this->_config['tokenLogin'], 'token$success') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'token'      => 'token$success',
                            'as_header'  => 'X-Access-Token',
                            'as_request' => 'access_token',
                        ),
                    )
                );
            }
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }

    public function setAutorenew($domain, $state)
    {
        if (!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
            return $this->setResult(
                array(
                    'status'   => self::STATUS_SUCCESS,
                    'code'     => 200,
                    'response' => array(
                        'result' => true,
                    ),
                )
            );
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }

    public function getDomains(array $filters = array())
    {
        if (!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (!empty($this->_config['tokenDomainList']) && strpos($this->_config['tokenDomainList'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (empty($this->_config['tokenDomainList'])) {
            if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            array(
                                'id'              => '1',
                                'name'            => 'test',
                                'api_handle'      => 'test',
                                'tld'             => 'nl',
                                'created'         => date('Y') . '-01-01 00:00:00',
                                'updated'         => date('Y') . '-01-01 00:00:00',
                                'expiration_date' => (date('Y') + 1) . '-01-01 00:00:00',
                            ),
                            array(
                                'id'              => '2',
                                'name'            => 'test',
                                'api_handle'      => 'test',
                                'tld'             => 'eu',
                                'created'         => date('Y') . '-02-01 00:00:00',
                                'updated'         => date('Y') . '-02-01 00:00:00',
                                'expiration_date' => (date('Y') + 2) . '-02-01 00:00:00',
                            ),
                        ),
                    )
                );
            }
        } else {
            if (strpos($this->_config['tokenDomainList'], 'token$successnomessage') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'status' => 'FAILED',
                        ),
                    )
                );
            }

            if (strpos($this->_config['tokenDomainList'], 'token$successinternal') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'status' => 'FAILED',
                            'data'   => array(
                                'message' => 'Someone wanted it!',
                            ),
                        ),
                    )
                );
            }

            if (strpos($this->_config['tokenDomainList'], 'token$success') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            array(
                                'id'              => '1',
                                'name'            => 'test',
                                'api_handle'      => 'test',
                                'tld'             => 'nl',
                                'created'         => date('Y') . '-01-01 00:00:00',
                                'updated'         => date('Y') . '-01-01 00:00:00',
                                'expiration_date' => (date('Y') + 1) . '-01-01 00:00:00',
                            ),
                            array(
                                'id'              => '2',
                                'name'            => 'test',
                                'api_handle'      => 'test',
                                'tld'             => 'eu',
                                'created'         => date('Y') . '-02-01 00:00:00',
                                'updated'         => date('Y') . '-02-01 00:00:00',
                                'expiration_date' => (date('Y') + 2) . '-02-01 00:00:00',
                            ),
                        ),
                    )
                );
            }
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }

    public function checkDomain($domain)
    {
        if (!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (!empty($this->_config['tokenCheckDomain']) && strpos($this->_config['tokenCheckDomain'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (empty($this->_config['tokenCheckDomain'])) {
            if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'is_available' => $domain === 'test.nl',
                        ),
                    )
                );
            }
        } else {
            if (empty($this->_config['tokenCheckDomain']) || strpos($this->_config['tokenCheckDomain'], 'token$successnomessage') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'status' => 'FAILED',
                        ),
                    )
                );
            }

            if (empty($this->_config['tokenCheckDomain']) || strpos($this->_config['tokenCheckDomain'], 'token$successinternal') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'status' => 'FAILED',
                            'data'   => array(
                                'message' => 'Someone wanted it!',
                            ),
                        ),
                    )
                );
            }

            if (empty($this->_config['tokenCheckDomain']) || strpos($this->_config['tokenCheckDomain'], 'token$success') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'is_available' => $domain === 'test.nl',
                        ),
                    )
                );
            }
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }
    
    public function deleteDomain($domain, $deletedAt)
    {
        if (!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (!empty($this->_config['tokenDeleteDomain']) && strpos($this->_config['tokenDeleteDomain'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (empty($this->_config['tokenDeleteDomain'])) {
            if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'domain_id'   => 999999,
                            'deleted_at'  => '2017-02-02T10:07:52.0Z',
                            'domain_name' => 'testing-my-domain-name',
                        ),
                    )
                );
            }
        } else {
            if (strpos($this->_config['tokenDeleteDomain'], 'token$successinternal') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'status' => 'FAILED',
                            'data'   => array(
                                'message' => 'Testing',
                            ),
                        ),
                    )
                );
            }

            if (strpos($this->_config['tokenDeleteDomain'], 'token$successnomessage') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'status' => 'FAILED',
                        ),
                    )
                );
            }

            if (strpos($this->_config['tokenDeleteDomain'], 'token$success') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'domain_id'   => 999999,
                            'deleted_at'  => '2017-02-02T10:07:52.0Z',
                            'domain_name' => 'testing-my-domain-name',
                        ),
                    )
                );
            }
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }
    
    public function getDomainInfo($domain)
    {
        if ((!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) || (!empty($this->_config['tokenInfo']) && strpos($this->_config['tokenInfo'], 'token$throw') === 0)) {
            return $this->setError('Forced error');
        }

        if (!empty($this->_config['tokenDomainInfo']) && strpos($this->_config['tokenDomainInfo'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (!empty($this->_config['tokenInfo']) && strpos($this->_config['tokenInfo'], 'token$success') !== 0) {
            return $this->setResult(
                array(
                    'status'   => self::STATUS_ERROR,
                    'code'     => 404,
                    'response' => array(
                        'message' => 'Forced error',
                        'data'    => array(),
                    ),
                )
            );
        }

        if (empty($this->_config['tokenDomainInfo'])) {
            if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
                list($name, $tld) = explode('.', $domain, 2);

                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'id'              => 3,
                            'domain_id'       => 3,
                            'status'          => 'REGISTERED',
                            'name'            => $name,
                            'domain_name'     => $name,
                            'autorenew'       => 'ON',
                            'tld'             => $tld,
                            'expiration_date' => (date('Y') + 1) . '-01-01',
                            'auth_code'       => $tld === 'be' ? null : 'TEST1221TSET',
                            'contacts'        => array(
                                'REGISTRANT' => 'XXX001',
                                'ONSITE'     => 'XXX001'
                            ),
                            'contacts_map'    => array(
                                'REGISTRANT' => 24,
                                'ONSITE'     => 32
                            ),
                            'nameservers'     => array(
                                'ns1.test.ru',
                                'ns2.test.ru',
                            ),
                        ),
                    )
                );
            }
        } else {
            if (strpos($this->_config['tokenDomainInfo'], 'token$successinternal') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'status' => 'FAILED',
                            'data'   => array(
                                'message' => 'Testing',
                            ),
                        ),
                    )
                );
            }

            if (strpos($this->_config['tokenDomainInfo'], 'token$successnomessage') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'status' => 'FAILED',
                        ),
                    )
                );
            }

            if (strpos($this->_config['tokenDomainInfo'], 'token$successmissingns') === 0) {
                list($name, $tld) = explode('.', $domain, 2);

                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'id'              => 3,
                            'domain_id'       => 3,
                            'status'          => 'REGISTERED',
                            'name'            => $name,
                            'domain_name'     => $name,
                            'autorenew'       => 'ON',
                            'tld'             => $tld,
                            'expiration_date' => (date('Y') + 1) . '-01-01',
                            'auth_code'       => $tld === 'be' ? null : 'TEST1221TSET',
                            'contacts'        => array(
                                'REGISTRANT' => 'XXX001',
                                'ONSITE'     => 'XXX001'
                            ),
                            'contacts_map'    => array(
                                'REGISTRANT' => 24,
                                'ONSITE'     => 32
                            ),
                            'nameservers'     => array(
                                'ns1.test.ru',
                                'ns2.test.ru',
                                '',
                            ),
                        ),
                    )
                );
            }

            if (strpos($this->_config['tokenDomainInfo'], 'token$success') === 0) {
                list($name, $tld) = explode('.', $domain, 2);

                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'id'              => 3,
                            'domain_id'       => 3,
                            'status'          => 'REGISTERED',
                            'name'            => $name,
                            'domain_name'     => $name,
                            'autorenew'       => 'ON',
                            'tld'             => $tld,
                            'expiration_date' => (date('Y') + 1) . '-01-01',
                            'auth_code'       => $tld === 'be' ? null : 'TEST1221TSET',
                            'contacts'        => array(
                                'REGISTRANT' => 'XXX001',
                                'ONSITE'     => 'XXX001'
                            ),
                            'contacts_map'    => array(
                                'REGISTRANT' => 24,
                                'ONSITE'     => 32
                            ),
                            'nameservers'     => array(
                                'ns1.test.ru',
                                'ns2.test.ru',
                            ),
                        ),
                    )
                );
            }
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }

    public function getMe()
    {
        if (!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (!empty($this->_config['tokenGetMe']) && strpos($this->_config['tokenGetMe'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (empty($this->_config['tokenGetMe'])) {
            if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'user_id'   => '1',
                            'balance'   => '0.00',
                            'hitpoints' => 0,
                        ),
                    )
                );
            }
        } else {
            if (empty($this->_config['tokenGetMe']) || strpos($this->_config['tokenGetMe'], 'token$success') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'user_id'   => '1',
                            'balance'   => '0.00',
                            'hitpoints' => 0,
                        ),
                    )
                );
            }
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }
    
    public function getContact($handle)
    {
        if (!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
            return $this->setResult(
                array(
                    'status'   => self::STATUS_SUCCESS,
                    'code'     => 200,
                    'response' => array(
                        'id'                      => $handle,
                        'first_name'              => 'Test',
                        'middle_name'             => 'Testovich',
                        'last_name'               => 'Testov',
                        'full_name'               => 'T Testov',
                        'initials'                => 'T',
                        'state'                   => 'Test State',
                        'city'                    => 'Test City',
                        'postal_code'             => '1900AB',
                        'phone'                   => '+555.1234561122',
                        'fax'                     => null,
                        'email'                   => 'test@gooblesupermegacomp.com',
                        'country'                 => 'RU',
                        'language'                => 'NL',
                        'gender'                  => 'MALE',
                        'address'                 => 'Street of 1000 tests, 1000',
                        'street'                  => 'Street of 1000 tests',
                        'house_number'            => 1000,
                        'company_name'            => 'T Testov',
                        'company_email'           => 'test@gooblesupermegacomp.com',
                        'company_address'         => 'Last Test str, 122',
                        'company_street'          => 'Last Test str',
                        'company_house_number'    => 122,
                        'company_postal_code'     => '4321AB',
                        'company_city'            => 'Test City',
                        'company_phone'           => '+1.1234567890',
                        'company_vatin'           => '123ABC',
                        'organization_legal_form' => 'PERSOON',
                        'created'                 => '2016-02-02 10:07;52',
                        'updated'                 => '2016-02-02 10:07:52'
                    ),
                )
            );
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }

    public function getDomainAuthCode($domain)
    {
        if (!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
            list($name, $tld) = explode('.', $domain, 2);

            return $this->setResult(
                array(
                    'status'   => self::STATUS_SUCCESS,
                    'code'     => 200,
                    'response' => array(
                        'domain_id'   => 4,
                        'domain_name' => $name,
                        'auth_code'   => md5($domain),
                    ),
                )
            );
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }

    public function getContacts(array $filters = array())
    {
        if (!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
            return $this->setResult(
                array(
                    'status'   => self::STATUS_SUCCESS,
                    'code'     => 200,
                    'response' => array(
                        array(
                            'id'   => 8,
                            'name' => 'T Testov',
                        ),
                        array(
                            'id'   => 9,
                            'name' => 'Gooble Super Mega Company, Test Division',
                        ),
                    ),
                )
            );
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }

    public function registerDomain($domain, array $data = array())
    {
        if (!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (!empty($this->_config['tokenRegisterDomain']) && strpos($this->_config['tokenRegisterDomain'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (empty($this->_config['tokenRegisterDomain'])) {
            if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'result' => true,
                        ),
                    )
                );
            }
        } else {
            if (strpos($this->_config['tokenRegisterDomain'], 'token$successnomessage') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'status' => 'FAILED',
                        ),
                    )
                );
            }

            if (strpos($this->_config['tokenRegisterDomain'], 'token$successinternal') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'status' => 'FAILED',
                            'data'   => array(
                                'message' => 'Someone wanted it!',
                            ),
                        ),
                    )
                );
            }

            if (strpos($this->_config['tokenRegisterDomain'], 'token$success') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'result' => true,
                        ),
                    )
                );
            }
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }

    public function updateDomain($domain, array $data = array())
    {
        if (!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (!empty($this->_config['tokenUpdate']) && strpos($this->_config['tokenUpdate'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
            return $this->setResult(
                array(
                    'status'   => self::STATUS_SUCCESS,
                    'code'     => 200,
                    'response' => array(
                        'result' => true,
                    ),
                )
            );
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }

    public function transferDomain($domain, array $data = array())
    {
        if (!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (!empty($this->_config['tokenTransferDomain']) && strpos($this->_config['tokenTransferDomain'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (empty($this->_config['tokenTransferDomain'])) {
            if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'result' => true,
                        ),
                    )
                );
            }
        } else {
            if (strpos($this->_config['tokenTransferDomain'], 'token$successnomessage') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'status' => 'FAILED',
                        ),
                    )
                );
            }

            if (strpos($this->_config['tokenTransferDomain'], 'token$successinternal') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'status' => 'FAILED',
                            'data'   => array(
                                'message' => 'Someone wanted it!',
                            ),
                        ),
                    )
                );
            }

            if (strpos($this->_config['tokenTransferDomain'], 'token$success') === 0) {
                return $this->setResult(
                    array(
                        'status'   => self::STATUS_SUCCESS,
                        'code'     => 200,
                        'response' => array(
                            'result' => true,
                        ),
                    )
                );
            }
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }

    public function createContact(array $data)
    {
        if (!empty($this->_config['tokenCreateContact']) && strpos($this->_config['tokenCreateContact'], 'token$success') === 0) {
            return $this->setResult(
                array(
                    'status'   => self::STATUS_SUCCESS,
                    'code'     => 200,
                    'response' => array(
                        'data' => array(
                            'id' => 1,
                        ),
                    ),
                )
            );
        }

        if (!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
            return $this->setResult(
                array(
                    'status'   => self::STATUS_SUCCESS,
                    'code'     => 200,
                    'response' => array(
                        'data' => array(
                            'id' => 1,
                        ),
                    ),
                )
            );
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }

    public function updateContact($handle, array $data)
    {
        if (!empty($this->_config['token']) && strpos($this->_config['token'], 'token$throw') === 0) {
            return $this->setError('Forced error');
        }

        if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
            return $this->setResult(
                array(
                    'status'   => self::STATUS_SUCCESS,
                    'code'     => 200,
                    'response' => array(
                        'id' => $handle,
                    ),
                )
            );
        }

        return $this->setResult(
            array(
                'status'   => self::STATUS_ERROR,
                'code'     => 404,
                'response' => array(
                    'message' => 'Forced error',
                    'data'    => array(),
                ),
            )
        );
    }
}