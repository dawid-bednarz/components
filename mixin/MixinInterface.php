<?php

namespace daweb\mixin;
/**
 * 
 *
 * @author daWeb
 */
interface MixinInterface {

    public function mixins();

    public function hasMixin($name);

    public function mixinAttach($name, $params);

    public function mixinDetech($name);
}
