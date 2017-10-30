<?php

namespace diggme\utils;

/**
 * Class Network
 * @package diggme\opensdk
 * @created 2017-04-18 develop@diggme.cn
 * @modified 2017-04-18 develop@diggme.cn
 * @copyright © 2017 www.diggme.cn
 * @contact DP <develop@diggme.cn>
 */
class Network
{

    /**
     * Header container
     * @access private
     * @var array
     */
    private $_header = [];

    /**
     * Query params container
     * @access private
     * @var array
     */
    private $_params = [];

    /**
     * User agent
     * @access private
     * @var string
     */
    private $_userAgent = '';

    /**
     * Reference url
     * @access private
     * @var string
     */
    private $_reference = '';

    /**
     * Cert path
     * @var string
     */
    private $_certPath = '';

    /**
     * Key path
     * @var string
     */
    private $_keyPath = '';

    /**
     * use string
     * @var bool
     */
    private $_useCert = false;

    /**
     * Network constructor.
     */
    public function __construct()
    {
        $this->setHeader('X-Requested-With', 'XMLHttpRequest');
    }

    /**
     * Set http reference url
     * @access public
     * @param string $url
     * @return void
     */
    public function setReference($url)
    {
        if (is_string($url) && !empty($ua)) {
            $this->_reference = $url;
        }
    }

    /**
     * Set http user agent
     * @access public
     * @param string $ua
     * @return void
     */
    public function setUserAgent($ua)
    {
        if (is_string($ua) && !empty($ua)) {
            $this->_userAgent = $ua;
        }
    }

    /**
     * Set query header
     * @access public
     * @param string|array $name
     * @param string $value
     * @return void
     */
    public function setHeader($name, $value = '')
    {
        if (is_string($name)) {
            $this->_header[$this->getHeaderFormatKey($name)] = $value;
        } else if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->_header[$this->getHeaderFormatKey($key)] = $value;
            }
        }
    }

    public function getHeaderFormatKey($key)
    {
        $key = ucwords(str_replace('_', ' ', strtolower($key)));
        $key = str_replace(' ', '-', $key);
        return $key;
    }

    /**
     * Set query params
     * @access public
     * @param string|array $name
     * @param string $value
     * @return void
     */
    public function setParams($name, $value = null)
    {
        if (is_string($name)) {
            if ($value == null) {
                $this->_params = $name;
            } else {
                $this->_params[$name] = $value;
            }
        } else if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->_params[$key] = $value;
            }
        }
    }

    /**
     * Send a get request
     * @access public
     * @param string $url
     * @param array $params [optional]
     * @return bool | string
     */
    public function get($url, $params = [], $encode = false)
    {
        $this->setParams($params);
        return $this->_request('get', $url, $encode);
    }

    /**
     * Send a post request
     * @access public
     * @param string $url
     * @param array $params [optional]
     * @return bool | string
     */
    public function post($url, $params = [])
    {
        $this->setParams($params);
        return $this->_request('post', $url);
    }

    /**
     * Send a put request
     * @access public
     * @param string $url
     * @param array $params [optional]
     * @return bool | string
     */
    public function put($url, $params = [])
    {
        $this->setParams($params);
        return $this->_request('put', $url);
    }

    /**
     * Send a delete request
     * @access public
     * @param string $url
     * @param array $params [optional]
     * @return bool | string
     */
    public function delete($url, $params = [])
    {
        $this->setParams($params);
        return $this->_request('delete', $url);
    }

    public function useCert($certPath, $keyPath)
    {
        $this->_useCert = true;
        $this->_certPath = $certPath;
        $this->_keyPath = $keyPath;
    }

    /**
     * Handle a restful request
     * @access public
     * @param string $method
     * @param string $url
     * @return bool | string
     */
    private function _request($method, $url, $encode = false)
    {
        if (empty($url)) {
            return false;
        }
        $method = in_array($method, array('get', 'post', 'put', 'delete')) ? $method : 'get';
        $ch = curl_init();
        if (!empty($this->_header)) {
            $header = [];
            foreach ($this->_header as $k => $v) {
                $header[] = trim(trim($k, ' '), ':') . ': ' . $v;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if (!empty($this->_userAgent)) {
            curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent);
        }
        if (!empty($this->_refrence)) {
            curl_setopt($ch, CURLOPT_REFERER, $this->_refrence);
        }
        if ($method == 'get' && !empty($this->_params)) {
            $param = http_build_query($this->_params);
            $param = $encode ?: urldecode($param);
            $url = $url . '?' . $param;
        } elseif ($method == 'post') {
            if (!empty($this->_params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_params);
            }
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        } elseif ($method == 'put') {
            if (!empty($this->_params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_params);
            }
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        } elseif ($method == 'delete') {
            if (!empty($this->_params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_params);
            }
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        if ($this->_useCert == true) {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $this->_certPath);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $this->_keyPath);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);


        curl_close($ch);
        return $output;
    }
    
}