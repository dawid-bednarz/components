<?php

namespace daweb\mixin;

/**
 * Description of MixinTrait
 *
 * @author daWeb
 */
trait MixinTrait {

    /**
     * mixins methods for __call()
     * @var array
     */
    public $_methods = [];

    /**
     * mixins properties for __get()
     * @var array
     */
    public $_properties = [];
    protected $_mixin;

    public function mixins() {
        return [];
    }

    public function mixinAttach($name, $params) {
        return $this->_mixin->attach($name, $params);
    }

    public function mixinDetech($name) {
        return $this->_mixin->detech($name);
    }

    public function hasMixin($name) {
        return isset($this->_methods[$name]) || isset($this->_properties[$name]);
    }

    /**
     * retrun name key of the mixin in _properties or false
     * @param string $name
     * @return string || false
     */
    private function searchProperty($name) {

        $properties = $this->_properties;

        while (current($properties)) {

            $key = key($this->_properties);

            if (key_exists($name, $properties[$key])) {
                return $key;
            }
            next($properties);
        }
        return false;
    }

    /**
     * search only mixin properties
     * @param string $name
     * @return boolean
     */
    public function issetProperty($name) {

        if ($this->searchProperty($name)) {
            return true;
        }
        return false;
    }

    /**
     * return mixed property, when prop exists is saved to cache
     * @param string $name
     * @return mixed
     */
    public function __get($name) {

        if (!isset($this->_properties['cache'][$name])) {

            $key = $this->searchProperty($name);

            if ($key) {
                $this->_properties['cache'][$name] = $this->_properties[$key][$name];
                return $this->_properties[$key][$name];
            }
        } else {
            return $this->_properties['cache'][$name];
        }
        throw new \Exception("Property $name not exists in " . get_class($this));
    }

    public function __set($name, $val) {

        $key = $this->searchProperty($name);

        if ($key) {
            $this->_properties[$key][$name] = $val;
            $this->_properties['cache'][$name] = $val;
        } else {
            throw new \Exception("Property $name not exists in " . get_class($this));
        }
    }

    public function __construct() {

        $this->_mixin = new Mixin($this);

        if ($this->_mixin->has()) {
            $this->_mixin->make();
        }
    }

    /**
     * check exists method called in _methods properties.
     * _methods is result of the mixin object
     * @param type $method
     * @param type $args
     * @return mix
     * @throws BadMethodCallException
     */
    function __call($method, $args) {

        $methods = $this->_methods;

        while (current($methods)) {

            $key = key($this->_methods);

            if (in_array($method, $methods[$key])) { //check isset mixin method
                $class = $this->_methods[$key]['class'];
                end($methods); //end loop
            }
            next($methods);
        }

        if (isset($class)) {
            return call_user_func_array([$class, $method], $args);
        } else {
            
            $key = $this->searchProperty($method);
            
            if ($key && is_callable($this->_properties[$key][$method])) { //maybe property is a callback
                return call_user_func_array($this->$method, $args);
            }

            throw new BadMethodCallException;
        }
    }

}
