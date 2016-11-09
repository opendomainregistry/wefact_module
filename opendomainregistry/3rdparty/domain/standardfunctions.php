<?php
if (!defined('HANDLE_OWNER')) {
    define('HANDLE_OWNER', 'OWNER');
}

if (!defined('HANDLE_ADMIN')) {
    define('HANDLE_ADMIN', 'ADMIN');
}

if (!defined('HANDLE_TECH')) {
    define('HANDLE_TECH', 'TECH');
}

if (!class_exists('Whois')) {
    class Whois
    {
        protected $_data = array();

        public function __construct()
        {
        }

        /**
         * @param string $key
         *
         * @return mixed
         */
        public function __get($key)
        {
            return empty($this->_data[$key]) ? null : $this->_data[$key];
        }

        public function __set($key, $value)
        {
            $this->_data[$key] = $value;
        }

        public function __isset($key)
        {
            return isset($this->_data[$key]);
        }

        public function getParam($prefix, $param)
        {
            return empty($this->_data[$prefix . $param]) ? null : $this->_data[$prefix . $param];
        }
    }
}