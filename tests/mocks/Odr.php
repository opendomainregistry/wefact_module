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

        if (empty($this->_config['token']) || strpos($this->_config['token'], 'token$success') === 0) {
            return $this->setResult(
                array(
                    'status'   => self::STATUS_SUCCESS,
                    'code'     => 200,
                    'response' => array(
                        'available' => $domain === 'test.nl',
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