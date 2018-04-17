<?php
/**
 *---------------------------------------------------------------------------
 *
 *                  T E N C E N T   P R O P R I E T A R Y
 *
 *     COPYRIGHT (c)  2008 BY  TENCENT  CORPORATION.  ALL RIGHTS
 *     RESERVED.   NO  PART  OF THIS PROGRAM  OR  PUBLICATION  MAY
 *     BE  REPRODUCED,   TRANSMITTED,   TRANSCRIBED,   STORED  IN  A
 *     RETRIEVAL SYSTEM, OR TRANSLATED INTO ANY LANGUAGE OR COMPUTER
 *     LANGUAGE IN ANY FORM OR BY ANY MEANS, ELECTRONIC, MECHANICAL,
 *     MAGNETIC,  OPTICAL,  CHEMICAL, MANUAL, OR OTHERWISE,  WITHOUT
 *     THE PRIOR WRITTEN PERMISSION OF :
 *
 *                        TENCENT  CORPORATION
 *
 *       Advertising Platform R&D Team, Advertising Platform & Products
 *       Tencent Ltd.
 *---------------------------------------------------------------------------
 */

/**
 * The class for web response
 *
 * @package sdk.src.framework.httpfoudation
 */
class TMWebResponse extends TMResponse
{
    /**
     *
     * 返回的cookie
     * @var array
     */
    protected $cookies     = array();

    /**
     *
     * 返回的状态码
     * @var int
     */
    protected $statusCode  = 200;

    /**
     *
     * 返回的状态文本
     * @var string
     */
    protected $statusText  = 'OK';

    /**
     *
     * 是否只返回http头
     * @var boolean
     */
    protected $headerOnly  = false;

    /**
     *
     * http头信息
     * @var array
     */
    protected $headers     = array();

    /**
     *
     * meta信息
     * @var array
     */
    protected $metas       = array();

    /**
     *
     * http meta信息
     * @var array
     */
    protected $httpMetas   = array();

    /**
     *
     * positoins
     * @var array
     */
    protected $positions   = array('first', '', 'last');

    /**
     *
     * css
     * @var array
     */
    protected $stylesheets = array();

    /**
     *
     * javascript
     * @var array
     */
    protected $javascripts = array();

    /**
     *
     * 是否是ajax请求
     * @var boolean
     */
    protected $isAjax      = false;

    /**
     *
     * 返回是否需要加上监测代码
     * @var boolean
     */
    protected $needTrack   = false;

    /**
     *
     * 所有支持的状态码以及状态文本
     * @var array
     */
    static protected $statusTexts = array(
        '100' => 'Continue',
        '101' => 'Switching Protocols',
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '306' => '(Unused)',
        '307' => 'Temporary Redirect',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Request Entity Too Large',
        '414' => 'Request-URI Too Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Requested Range Not Satisfiable',
        '417' => 'Expectation Failed',
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported',
    );

    /**
     *
     * 单例对象
     * @var TMWebResponse
     */
    private static $instance = null;

    /**
     *
     * 是否发送过返回
     * @var boolean
     */
    protected $hasSendResponse = false;

    /**
     * Get instance function
     *
     * @param array $options
     * @return TMWebResponse
     */
    public static function getInstance($options = array())
    {
        if(self::$instance === null)
        {
            self::$instance = new TMWebResponse($options);
        }

        return self::$instance;
    }

    /**
     * Initializes this TMWebResponse.
     *
     * Available options:
     *
     *    * charset: The charset to use (utf-8 by default)<br>
     *    * content_type: The content type (text/html by default)
     *
     * @param array $options         An array of options
     *
     * @return bool true, if initialization completes successfully, otherwise false
     *
     *
     */
    public function initialize($options = array())
    {
        parent::initialize($options);

        $this->javascripts = array_combine($this->positions, array_fill(0, count($this->positions), array()));
        $this->stylesheets = array_combine($this->positions, array_fill(0, count($this->positions), array()));

        if (!isset($this->options['charset']))
        {
            $this->options['charset'] = 'utf-8';
        }

        $this->options['content_type'] = $this->fixContentType(isset($this->options['content_type']) ? $this->options['content_type'] : 'text/html');
    }

    /**
     * Sets if the response consist of just HTTP headers.
     *
     * @param bool $value
     */
    public function setHeaderOnly($value = true)
    {
        $this->headerOnly = (boolean) $value;
    }

    /**
     * Returns if the response must only consist of HTTP headers.
     *
     * @return bool returns true if, false otherwise
     */
    public function isHeaderOnly()
    {
        return $this->headerOnly;
    }

    /**
     * Sets a cookie.
     *
     * @param    string    $name            HTTP header name
     * @param    string    $value         Value for the cookie
     * @param    string    $expire        Cookie expiration period
     * @param    string    $path            Path
     * @param    string    $domain        Domain name
     * @param    bool        $secure        If secure
     * @param    bool        $httpOnly    If uses only HTTP
     *
     * @throws TMParameterException If fails to set the cookie
     */
    public function setCookie($name, $value, $expire = null, $path = '/', $domain = '', $secure = false, $httpOnly = false)
    {
        if ($expire !== null)
        {
            if (is_numeric($expire))
            {
                $expire = (int) $expire;
            }
            else
            {
                $expire = strtotime($expire);
                if ($expire === false || $expire == -1)
                {
                    throw new TMParameterException('Your expire parameter is not valid.');
                }
            }
        }

        $this->cookies[] = array(
            'name'         => $name,
            'value'        => $value,
            'expire'     => $expire,
            'path'         => $path,
            'domain'     => $domain,
            'secure'     => $secure ? true : false,
            'httpOnly' => $httpOnly,
        );
    }

    /**
     * Sets response status code.
     *
     * @param string $code    HTTP status code
     * @param string $name    HTTP status text
     *
     */
    public function setStatusCode($code, $name = null)
    {
        $this->statusCode = $code;
        $this->statusText = null !== $name ? $name : self::$statusTexts[$code];
    }

    /**
     * Retrieves status code for the current web response.
     *
     * @return string Status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets a HTTP header.
     *
     * @param string    $name         HTTP header name
     * @param string    $value        Value (if null, remove the HTTP header)
     * @param bool        $replace    Replace for the value
     *
     */
    public function setHttpHeader($name, $value, $replace = true)
    {
        $name = $this->normalizeHeaderName($name);

        if (is_null($value))
        {
            unset($this->headers[$name]);

            return;
        }

        if ('Content-Type' == $name)
        {
            if ($replace || !$this->getHttpHeader('Content-Type', null))
            {
                $this->setContentType($value);
            }

            return;
        }

        if (!$replace)
        {
            $current = isset($this->headers[$name]) ? $this->headers[$name] : '';
            $value = ($current ? $current.', ' : '').$value;
        }

        $this->headers[$name] = $value;
    }

    /**
     * Gets HTTP header current value.
     *
     * @param    string $name         HTTP header name
     * @param    string $default    Default value returned if named HTTP header is not found
     *
     * @return array
     */
    public function getHttpHeader($name, $default = null)
    {
        $name = $this->normalizeHeaderName($name);

        return isset($this->headers[$name]) ? $this->headers[$name] : $default;
    }

    /**
     * Checks if response has given HTTP header.
     *
     * @param    string $name    HTTP header name
     *
     * @return bool
     */
    public function hasHttpHeader($name)
    {
        return array_key_exists($this->normalizeHeaderName($name), $this->headers);
    }

    /**
     * Sets response content type.
     *
     * @param string $value    Content type
     *
     */
    public function setContentType($value)
    {
        $this->headers['Content-Type'] = $this->fixContentType($value);
    }

    /**
     * Gets response content type.
     *
     * @return array
     */
    public function getContentType()
    {
        return $this->getHttpHeader('Content-Type', $this->options['content_type']);
    }

    /**
     * Sends HTTP headers and cookies.
     *
     */
    public function sendHttpHeaders()
    {
        // status
        $status = 'HTTP/1.1 '.$this->statusCode.' '.$this->statusText;
        @header($status);

        // headers
        if (!$this->getHttpHeader('Content-Type'))
        {
            $this->setContentType($this->options['content_type']);
        }
        foreach ($this->headers as $name => $value)
        {
            @header($name.': '.$value);
        }

        // cookies
        foreach ($this->cookies as $cookie)
        {
            if (version_compare(phpversion(), '5.2', '>='))
            {
                setcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httpOnly']);
            }
            else
            {
                setcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure']);
            }
        }
    }

    /**
     * Send content for the current web response.
     *
     */
    public function sendContent()
    {
        if (!$this->headerOnly)
        {
            parent::sendContent();
        }
    }

    /**
     * Sends the HTTP headers and the content.
     */
    public function send()
    {
        if($this->hasSendResponse === false){
            $this->sendHttpHeaders();
            $this->sendContent();
            $this->setHasSendResponse();
        }
    }

    /**
     * Retrieves a normalized Header.
     *
     * @param    string $name    Header name
     *
     * @return string Normalized header
     */
    protected function normalizeHeaderName($name)
    {
        return $name;//preg_replace('/\-(.)/is', "'-'.strtoupper('\\1')", strtr(ucfirst(strtolower($name)), '_', '-'));
//        return preg_replace_callback('/\-(.)/e', function($r)use($lang){ return $lang[$r[1]]; }, strtr(ucfirst(strtolower($name)), '_', '-'));
//        return preg_replace_callback('/\-(.)/e', array($this, '-'.strtoupper('\\1')), strtr(ucfirst(strtolower($name)), '_', '-'));
    }

    /**
     * Adds vary to a http header.
     *
     * @param string $header    HTTP header
     */
    public function addVaryHttpHeader($header)
    {
        $vary = $this->getHttpHeader('Vary');
        $currentHeaders = array();
        if ($vary)
        {
            $currentHeaders = split('/\s*,\s*/', $vary);
        }
        $header = $this->normalizeHeaderName($header);

        if (!in_array($header, $currentHeaders))
        {
            $currentHeaders[] = $header;
            $this->setHttpHeader('Vary', implode(', ', $currentHeaders));
        }
    }

    /**
     * Adds an control cache http header.
     *
     * @param string $name     HTTP header
     * @param string $value    Value for the http header
     */
    public function addCacheControlHttpHeader($name, $value = null)
    {
        $cacheControl = $this->getHttpHeader('Cache-Control');
        $currentHeaders = array();
        if ($cacheControl)
        {
            foreach (split('/\s*,\s*/', $cacheControl) as $tmp)
            {
                $tmp = explode('=', $tmp);
                $currentHeaders[$tmp[0]] = isset($tmp[1]) ? $tmp[1] : null;
            }
        }
        $currentHeaders[strtr(strtolower($name), '_', '-')] = $value;

        $headers = array();
        foreach ($currentHeaders as $key => $value)
        {
            $headers[] = $key.(null !== $value ? '='.$value : '');
        }

        $this->setHttpHeader('Cache-Control', implode(', ', $headers));
    }

    /**
     * Retrieves meta headers for the current web response.
     *
     * @return string Meta headers
     */
    public function getHttpMetas()
    {
        return $this->httpMetas;
    }

    /**
     * Adds a HTTP meta header.
     *
     * @param string    $key            Key to replace
     * @param string    $value        HTTP meta header value (if null, remove the HTTP meta)
     * @param bool        $replace    Replace or not
     */
    public function addHttpMeta($key, $value, $replace = true)
    {
        $key = $this->normalizeHeaderName($key);

        // set HTTP header
        $this->setHttpHeader($key, $value, $replace);

        if (is_null($value))
        {
            unset($this->httpMetas[$key]);

            return;
        }

        if ('Content-Type' == $key)
        {
            $value = $this->getContentType();
        }
        elseif (!$replace)
        {
            $current = isset($this->httpMetas[$key]) ? $this->httpMetas[$key] : '';
            $value = ($current ? $current.', ' : '').$value;
        }

        $this->httpMetas[$key] = $value;
    }

    /**
     * Retrieves all meta headers.
     *
     * @return array List of meta headers
     */
    public function getMetas()
    {
        return $this->metas;
    }

    /**
     * Adds a meta header.
     *
     * @param string    $key            Name of the header
     * @param string    $value        Meta header value (if null, remove the meta)
     * @param bool        $replace    true if it's replaceable
     * @param bool        $escape     true for escaping the header
     */
    public function addMeta($key, $value, $replace = true, $escape = true)
    {
        $key = strtolower($key);

        if (is_null($value))
        {
            unset($this->metas[$key]);

            return;
        }

        if ($escape)
        {
            $value = htmlspecialchars($value, ENT_QUOTES, $this->options['charset']);
        }

        $current = isset($this->metas[$key]) ? $this->metas[$key] : null;
        if ($replace || !$current)
        {
            $this->metas[$key] = $value;
        }
    }

    /**
     * Retrieves title for the current web response.
     *
     * @return string Title
     */
    public function getTitle()
    {
        return isset($this->metas['title']) ? $this->metas['title'] : '';
    }

    /**
     * Sets title for the current web response.
     *
     * @param string    $title     Title name
     * @param bool        $escape    true, for escaping the title
     */
    public function setTitle($title, $escape = true)
    {
        $this->addMeta('title', $title, true, $escape);
    }

    /**
     * Returns the available position names for stylesheets and javascripts in order.
     *
     * @return array An array of position names
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * Retrieves stylesheets for the current web response.
     *
     * @param    string    $position
     *
     * @return string Stylesheets
     */
    public function getStylesheets($position = '')
    {
        if ($position == 'ALL')
        {
            return $this->stylesheets;
        }

        $this->validatePosition($position);

        return isset($this->stylesheets[$position]) ? $this->stylesheets[$position] : array();
    }

    /**
     * Adds a stylesheet to the current web response.
     *
     * @param string $css             Stylesheet
     * @param string $position    Position
     * @param string $options     Stylesheet options
     */
    public function addStylesheet($css, $position = '', $options = array())
    {
        $this->validatePosition($position);

        $this->stylesheets[$position][$css] = $options;
    }

    /**
     * Removes a stylesheet from the current web response.
     *
     * @param string $css             Stylesheet
     * @param string $position    Position
     */
    public function removeStylesheet($css, $position = '')
    {
        $this->validatePosition($position);

        unset($this->stylesheets[$position][$css]);
    }

    /**
     * Retrieves javascript code from the current web response.
     *
     * @param    string $position    Position
     *
     * @return string Javascript code
     */
    public function getJavascripts($position = '')
    {
        if ($position == 'ALL')
        {
            return $this->javascripts;
        }

        $this->validatePosition($position);

        return isset($this->javascripts[$position]) ? $this->javascripts[$position] : array();
    }

    /**
     * Adds javascript code to the current web response.
     *
     * @param string $js                Javascript code
     * @param string $position    Position
     * @param string $options     Javascript options
     */
    public function addJavascript($js, $position = '', $options = array())
    {
        $this->validatePosition($position);

        $this->javascripts[$position][$js] = $options;
    }

    /**
     * Removes javascript code from the current web response.
     *
     * @param string $js                Javascript code
     * @param string $position    Position
     */
    public function removeJavascript($js, $position = '')
    {
        $this->validatePosition($position);

        unset($this->javascripts[$position][$js]);
    }

    /**
     * Retrieves cookies from the current web response.
     *
     * @return array Cookies
     */
    public function getCookies()
    {
        $cookies = array();
        foreach ($this->cookies as $cookie)
        {
            $cookies[$cookie['name']] = $cookie;
        }

        return $cookies;
    }

    /**
     * Retrieves HTTP headers from the current web response.
     *
     * @return string HTTP headers
     */
    public function getHttpHeaders()
    {
        return $this->headers;
    }

    /**
     * Cleans HTTP headers from the current web response.
     */
    public function clearHttpHeaders()
    {
        $this->headers = array();
    }

    /**
     * Copies all properties from a given TMWebResponse object to the current one.
     *
     * @param TMWebResponse $response    An TMWebResponse instance
     */
    public function copyProperties(TMWebResponse $response)
    {
        $this->options         = $response->getOptions();
        $this->headers         = $response->getHttpHeaders();
        $this->metas             = $response->getMetas();
        $this->httpMetas     = $response->getHttpMetas();
        $this->stylesheets = $response->getStylesheets('ALL');
        $this->javascripts = $response->getJavascripts('ALL');
    }

    /**
     * Merges all properties from a given TMWebResponse object to the current one.
     *
     * @param TMWebResponse $response    An TMWebResponse instance
     */
    public function merge(TMWebResponse $response)
    {
        foreach ($this->getPositions() as $position)
        {
            $this->javascripts[$position] = array_merge($this->getJavascripts($position), $response->getJavascripts($position));
            $this->stylesheets[$position] = array_merge($this->getStylesheets($position), $response->getStylesheets($position));
        }
    }

    /**
     * (non-PHPdoc)
     * @see TMResponse::serialize()
     */
    public function serialize()
    {
        return serialize(array($this->content, $this->statusCode, $this->statusText, $this->options, $this->cookies, $this->headerOnly, $this->headers, $this->metas, $this->httpMetas, $this->stylesheets, $this->javascripts));
    }

    /**
     * (non-PHPdoc)
     * @param mixed $serialized 序列化对象
     * @see TMResponse::unserialize()
     */
    public function unserialize($serialized)
    {
        list($this->content, $this->statusCode, $this->statusText, $this->options, $this->cookies, $this->headerOnly, $this->headers, $this->metas, $this->httpMetas, $this->stylesheets, $this->javascripts) = unserialize($serialized);
    }

    /**
     * Validate a position name.
     *
     * @param    string $position
     *
     * @throws TMParameterException if the position is not available
     */
    protected function validatePosition($position)
    {
        if (!in_array($position, $this->positions, true))
        {
            throw new TMParameterException(sprintf('The position "%s" does not exist (available positions: %s).', $position, implode(', ', $this->positions)));
        }
    }

    /**
     * Fixes the content type by adding the charset for text content types.
     *
     * @param string $contentType    The content type
     *
     * @return string The content type with the charset if needed
     */
    protected function fixContentType($contentType)
    {
        // add charset if needed (only on text content)
        if (false === stripos($contentType, 'charset') && (0 === stripos($contentType, 'text/') || strlen($contentType) - 3 === strripos($contentType, 'xml')))
        {
            $contentType .= '; charset='.$this->options['charset'];
        }

        return $contentType;
    }

    /**
     * Set the response is ajax
     *
     * @access public
     */
    public function setAjax()
    {
        $this->isAjax = true;
    }

    /**
     * Get the response's ajax status
     *
     * @access public
     * @return boolean $isAjax
     */
    public function getAjaxStatus()
    {
        return $this->isAjax;
    }

    /**
     * Set the response need track
     *
     * @access public
     */
    public function setNeedTrack()
    {
        $this->needTrack = true;
    }

    /**
     * Get the response's track need status
     *
     * @access public
     * @return boolean $needTrack
     */
    public function getNeedTrackStatus()
    {
        return $this->needTrack;
    }

    /**
     * Generate send alert back string
     *
     * @param string $alert             the alert message
     * @param string $url               default is TMConfig::Domain
     * @return string $content
     */
    public function getAlertBackString($alert = "", $url="")
    {
        if (!empty($alert))
        {
            $alertstr = "alert('" . $alert . "');\n";
        }
        else
        {
            $alertstr = "";
        }

        if (empty ($url))
        {
            $gotoStr = "window.history.back();\n";
        }
        else
        {
            $gotoStr = "window.location.href='" . $url . "'\n";
        }

        $content = "\t<script language=javascript>\n\t<!--\n";
        if (!empty($alertstr))
        {
            $content .= $alertstr;
        }

        if($url != "NONE")
        {
            $content .= $gotoStr;
        }
        $content .= "\t-->\n\t</script>\n";

        return $content;
    }

    /**
     * 实现链接跳转，需要多1次请求
     *
     * @param  string $url
     * @param  int $delay    跳转延时
     * @param  int $statusCode   跳转编码
     */
    public function redirect($url, $delay = 0, $statusCode = 302)
    {
        // redirect
        $this->clearHttpHeaders();
        $this->setStatusCode($statusCode);
        $this->setHttpHeader('Location', $url);
        $this->setContent(sprintf('<html><head><meta http-equiv="refresh" content="%d;url=%s"/></head></html>', $delay, htmlspecialchars($url, ENT_QUOTES)));
        $this->send();
    }

    /**
     *
     * 设置是否已经发送过返回值
     */
    protected function setHasSendResponse()
    {
        $this->hasSendResponse = true;
    }
}