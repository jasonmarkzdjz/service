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
 * The basic class for web request and console request
 *
 * @package sdk.src.framework.httpfoudation
 */
abstract class TMRequest
{
    /**
     * Process validation and execution for only GET requests.
     *
     */
    const GET = 2;

    /**
     * Skip validation and execution for any request method.
     *
     */
    const NONE = 1;

    /**
     * Process validation and execution for only POST requests.
     *
     */
    const POST = 4;

    /**
     * Process validation and execution for only PUT requests.
     *
     */
    const PUT = 5;

    /**
     * Process validation and execution for only DELETE requests.
     *
     */
    const DELETE = 6;

    /**
     * Process validation and execution for only HEAD requests.
     *
     */
    const HEAD = 7;

    /**
     *
     * 记录错误的数组
     * @var array
     */
    protected    $errors                    = array();

    /**
     *
     * 记录方法的数组
     * @var array
     */
    protected    $method                    = null;

    /**
     *
     * 参数容器
     * @var TMParameterHolder
     */
    protected    $parameterHolder           = null;

    /**
     *
     * 配置容器
     * @var array
     */
    protected    $config                    = null;

    /**
     *
     * 属性容器
     * @var array
     */
    protected    $attributeHolder           = null;

    /**
     * construct
     * Class constructor.
     *
     * @param    array                         $parameters    An associative array of initialization parameters
     * @param    array                         $attributes    An associative array of initialization attributes
     *
     */
    public function __construct($parameters = array(), $attributes = array())
    {
        $this->initialize($parameters, $attributes);
    }

    /**
     * Initializes this Request.
     *
     * @param array $parameters An associative array of initialization parameters
     * @param array $attributes An associative array of initialization attributes
     *
     */
    public function initialize($parameters = array(), $attributes = array())
    {
        // initialize parameter and attribute holders
        $this->parameterHolder = new TMParameterHolder();
        $this->attributeHolder = new TMParameterHolder();

        $this->parameterHolder->add($parameters);
        $this->attributeHolder->add($attributes);
    }

    /**
     * Extracts parameter values from the request.
     *
     * @param    array $names    An indexed array of parameter names to extract
     *
     * @return array An associative array of parameters and their values. If
     *                             a specified parameter doesn't exist an empty string will
     *                             be returned for its value
     */
    public function extractParameters(array $names)
    {
        $array = array();

        $parameters = $this->parameterHolder->getAll();
        foreach ($parameters as $key => $value)
        {
            if (in_array($key, $names))
            {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * Retrieves an error message.
     *
     * @param    string $name    An error name
     *
     * @return string An error message, if the error exists, otherwise null
     */
    public function getError($name)
    {
        return isset($this->errors[$name]) ? $this->errors[$name] : null;
    }

    /**
     * Retrieves an array of error names.
     *
     * @return array An indexed array of error names
     */
    public function getErrorNames()
    {
        return array_keys($this->errors);
    }

    /**
     * Retrieves an array of errors.
     *
     * @return array An associative array of errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Retrieves this request's method.
     *
     * @return int One of the following constants:
     *                         - TMRequest::GET
     *                         - TMRequest::POST
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Indicates whether or not an error exists.
     *
     * @param    string $name    An error name
     *
     * @return bool true, if the error exists, otherwise false
     */
    public function hasError($name)
    {
        return array_key_exists($name, $this->errors);
    }

    /**
     * Indicates whether or not any errors exist.
     *
     * @return bool true, if any error exist, otherwise false
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Removes an error.
     *
     * @param    string $name    An error name
     *
     * @return string An error message, if the error was removed, otherwise null
     */
    public function removeError($name)
    {
        $retval = null;

        if (isset($this->errors[$name]))
        {
            $retval = $this->errors[$name];

            unset($this->errors[$name]);
        }

        return $retval;
    }

    /**
     * Sets an error.
     *
     * @param string $name         An error name
     * @param string $message    An error message
     *
     */
    public function setError($name, $message)
    {
        $this->errors[$name] = $message;
    }

    /**
     * Sets an array of errors
     *
     * If an existing error name matches any of the keys in the supplied
     * array, the associated message will be overridden.
     *
     * @param array $errors An associative array of errors and their associated messages
     *
     */
    public function setErrors($errors)
    {
        $this->errors = array_merge($this->errors, $errors);
    }

    /**
     * Sets the request method.
     *
     * @param int $methodCode    One of the following constants:
     *                         - TMRequest::GET
     *                         - TMRequest::POST
     *                         - TMRequest::PUT
     *                         - TMRequest::DELETE
     *                         - TMRequest::HEAD     *
     * @throws TMParameterException - If the specified request method is invalid
     */
    public function setMethod($methodCode)
    {
        $available_methods = array(self::GET, self::POST, self::PUT, self::DELETE, self::HEAD, self::NONE);
        if (in_array($methodCode, $available_methods))
        {
            $this->method = $methodCode;

            return;
        }

        // invalid method type
        throw new TMParameterException(sprintf('Invalid request method: %s.', $methodCode));
    }

    /**
     * Retrieves the parameters for the current request.
     *
     * @return sfParameterHolder The parameter holder
     */
    public function getParameterHolder()
    {
        return $this->parameterHolder;
    }

    /**
     * Retrieves the attributes holder (not request parameters).
     *
     * @return sfParameterHolder The attribute holder
     */
    public function getAttributeHolder()
    {
        return $this->attributeHolder;
    }

    /**
     * Retrieves an attribute from the current request.
     *
     * @param    string $name         Attribute name
     * @param    string $default    Default attribute value
     *
     * @return mixed An attribute value
     */
    public function getAttribute($name, $default = null)
    {
        return $this->attributeHolder->get($name, $default);
    }

    /**
     * Indicates whether or not an attribute exist for the current request.
     *
     * @param    string $name    Attribute name
     *
     * @return bool true, if the attribute exists otherwise false
     */
    public function hasAttribute($name)
    {
        return $this->attributeHolder->has($name);
    }

    /**
     * Sets an attribute for the request.
     *
     * @param string $name     Attribute name
     * @param string $value    Value for the attribute
     *
     */
    public function setAttribute($name, $value)
    {
        $this->attributeHolder->set($name, $value);
    }

    /**
     * Retrieves a paramater for the current request.
     *
     * @param string $name         Parameter name
     * @param string $default    Parameter default value
     *
     */
    public function getParameter($name, $default = null)
    {
        return $this->parameterHolder->get($name, $default);
    }

    /**
     * Retrieves a paramater for the current request.Ref
     *
     * @param string $name         Parameter name
     * @param string $default    Parameter default value
     *
     */
    public function getParameterByRef($name, $default = null)
    {
        return $this->parameterHolder->getByRef($name, $default);
    }

    /**
     * Indicates whether or not a parameter exist for the current request.
     *
     * @param    string $name    Parameter name
     *
     * @return bool true, if the paramater exists otherwise false
     */
    public function hasParameter($name)
    {
        return $this->parameterHolder->has($name);
    }

    /**
     * Sets a parameter for the current request.
     *
     * @param string $name     Parameter name
     * @param string $value    Parameter value
     *
     */
    public function setParameter($name, $value)
    {
        $this->parameterHolder->set($name, $value);
    }

}