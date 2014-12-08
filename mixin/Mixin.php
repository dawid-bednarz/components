<?php
namespace daweb\mixin;
/**
 * useful funcionality merging some properties and function with different object
 *
 * @author daWeb
 */
class Mixin {

    /**
     * owner mixin
     * @var object 
     */
    private $owner;

    /**
     * count mixin owner for cache
     * @var integer
     */
    private $count;

    /**
     * mixins owner result of the mixins method
     * @var array
     */
    public $mixins;

    public function __construct($object) {

        $this->owner = $object;
    }

    /**
     * check whether object implements MixinInterface or use MixinTrait
     * @return boolean
     * @throws UnexpectedValueException
     */
    public function has() {

        if ($this->owner instanceof MixinInterface == false &&  !isset(class_uses($this->owner)[__NAMESPACE__.'\MixinTrait'])) {
            throw New \Exception(get_class($this->owner) . ' not implementation  MixinInterface');
        }
        $this->mixins = $this->owner->mixins();

        if (!is_array($this->mixins)) {
            throw New \UnexpectedValueException('Except type array');
        }
        $this->count = count($this->mixins);

        if ($this->count) {
            return true;
        }
        return false;
    }

    /**
     * $key is a name mixins
     * $args is a parameters wichih be mix
     * @param string $key
     * @param array $args
     */
    private function mix($key, $args) {
        /* it is only class ? */
        $isClass = isset($args['class']) && is_string($args['class']) && class_exists($args['class']);

        if ($isClass) {
            /* only one save all methods mixin classes */
            $class = is_object($args['class']) ? $args['class'] : new $args['class'];
            $this->owner->_methods[$key] = array_merge(['class' => $class], get_class_methods($class));
        }
        foreach ($args as $attr => $val) {

            if ($isClass) {
                $class->$attr = $val;
            } else {

                $this->owner->_properties[$key][$attr] = $val;
            }
        }
    }

    /**
     * mix mixins with $owner
     * @throws UnexpectedValueException
     */
    public function make() {

        for ($i = 0; $i < $this->count; $i++) {

            $key = key($this->mixins);
            $current = current($this->mixins);

            if (array_filter(array_keys($current), 'is_string')) { // check is associative array
                $this->mix($key, $current);
            } else {
                throw new \UnexpectedValueException('Value in mixins method must be type of associative array');
            }
            next($this->mixins);
        }
    }

    /**
     * attach some mixins after initialize class.
     * return false when overwrite is false and mixin exists in _methods param
     * @param string $name
     * @param array $params
     * @param boolean $overwrite
     * @return boolean
     */
    public function attach($name, $params, $overwrite = true) {

        if (!$overwrite) {
            if (isset($this->owner->_methods[$name]))
                return false;
        }
        $this->mix($name, $params);
        return true;
    }

    /**
     * detech some mixn
     * return true when mixin exists and is unset, false when mixin not exists
     * @param string $name
     * @return boolean
     */
    public function detech($name) {

        if (isset($this->owner->_methods[$name])) {
            unset($this->owner->_methods[$name]);
            return true;
        }
        if (isset($this->owner->_properties[$name])) {
            unset($this->owner->_properties[$name], $this->owner->_properties['cache']);
            return true;
        }
        return false;
    }
}
