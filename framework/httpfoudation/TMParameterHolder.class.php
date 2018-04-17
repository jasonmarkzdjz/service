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
 * TMParameterHolder implements Serializable
 * Parameters, in this case, are used to extend classes with additional data
 * that requires no additional logic to manage.
 *
 * @package sdk.src.framework.httpfoudation
 */
class TMParameterHolder implements Serializable
{
    /**
     *
     * 保存参数的数组
     * @var array
     */
    protected $parameters = array();

    /**
     * The constructor for sfParameterHolder.
     */
    public function __construct()
    {
    }

    /**
     * Clears all parameters associated with this request.
     */
    public function clear()
    {
        $this->parameters = array();
    }

    /**
     * Retrieves a parameter.
     *
     * @param  string $name     A parameter name
     * @param  mixed  $default  A default parameter value
     * @return mixed A parameter value, if the parameter exists, otherwise null
     */
    public function get($name, $default = null)
    {
        if (isset($this->parameters[$name]))
        {
            $value = & $this->parameters[$name];
        }
        else
        {
            $value = TMUtil::getArrayValueForPath($this->parameters, $name, $default);
        }

        return $value;
    }

    /**
     * Retrieves a parameter.
     *
     * @param  string $name     A parameter name
     * @param  mixed  $default  A default parameter value
     *
     * @return mixed A parameter value Ref, if the parameter exists, otherwise null
     */
    public function & getByRef($name, $default = null)
    {
        if (isset($this->parameters[$name]))
        {
            $value = & $this->parameters[$name];
        }
        else
        {
            $value = TMUtil::getArrayValueForPath($this->parameters, $name, $default);
        }

        return $value;
    }

    /**
     * Retrieves an array of parameter names.
     *
     * @return array An indexed array of parameter names
     */
    public function getNames()
    {
        return array_keys($this->parameters);
    }

    /**
     * Retrieves an array of parameters.
     *
     * @return array An associative array of parameters
     */
    public function getAll()
    {
        return $this->parameters;
    }

    /**
     * getAllByRef
     * Retrieves an array of parameters.
     *
     * @return array An associative array of parameters. Ref
     */
    public function & getAllByRef()
    {
        return $this->parameters;
    }

    /**
     * Indicates whether or not a parameter exists.
     *
     * @param  string $name  A parameter name
     *
     * @return bool true, if the parameter exists, otherwise false
     */
    public function has($name)
    {
        if (isset($this->parameters[$name]))
        {
            return true;
        }
        else
        {
            return TMUtil::hasArrayValueForPath($this->parameters, $name);
        }

        return false;
    }

    /**
     * Remove a parameter.
     *
     * @param  string $name     A parameter name
     * @param  mixed  $default  A default parameter value
     *
     * @return string A parameter value, if the parameter was removed, otherwise null
     */
    public function remove($name, $default = null)
    {
        $retval = $default;

        if (array_key_exists($name, $this->parameters))
        {
            $retval = $this->parameters[$name];
            unset($this->parameters[$name]);
        }
        else
        {
            $retval = TMUtil::removeArrayValueForPath($this->parameters, $name, $default);
        }

        return $retval;
    }

    /**
     * Sets a parameter.
     *
     * If a parameter with the name already exists the value will be overridden.
     *
     * @param string $name   A parameter name
     * @param mixed  $value  A parameter value
     */
    public function set($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Sets a parameter by reference.
     *
     * If a parameter with the name already exists the value will be overridden.
     *
     * @param string $name   A parameter name
     * @param mixed $value  A reference to a parameter value
     */
    public function setByRef($name, & $value)
    {
        $this->parameters[$name] =& $value;
    }

    /**
     * Sets an array of parameters.
     *
     * If an existing parameter name matches any of the keys in the supplied
     * array, the associated value will be overridden.
     *
     * @param array $parameters  An associative array of parameters and their associated values
     */
    public function add($parameters)
    {
        if (is_null($parameters))
        {
            return;
        }

        foreach ($parameters as $key => $value)
        {
            $this->parameters[$key] = $value;
        }
    }

    /**
     * Sets an array of parameters by reference.
     *
     * If an existing parameter name matches any of the keys in the supplied
     * array, the associated value will be overridden.
     *
     * @param array $parameters  An associative array of parameters and references to their associated values
     */
    public function addByRef(& $parameters)
    {
        foreach ($parameters as $key => &$value)
        {
            $this->parameters[$key] =& $value;
        }
    }

    /**
     * Serializes the current instance.
     *
     * @return array Objects instance
     */
    public function serialize()
    {
        return serialize($this->parameters);
    }

    /**
     * Unserializes a sfParameterHolder instance.
     *
     * @param string $serialized  A serialized sfParameterHolder instance
     */
    public function unserialize($serialized)
    {
        $this->parameters = unserialize($serialized);
    }
}