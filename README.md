components
==========

require PHP 5.4 >=

### Install 
in your composer.json
```
{
   require: {
     "dawid-daweb/components" "dev-master"
   }
}
```
### Mixin 
is the simple way on add additional properties and methods to your object.
Simple example:
```php
<?php
 use daweb\mixin\MixinTrait;
                
        class object { // your object 

            use MixinTrait; // use trait or implemments MixinInterface and create your own logic managment
        }

        $object = new Object;

        $object->mixinAttach('additional_params', [ //attach your additional properties
            'prop' => 'Hello ',
            'prop2' => 'World',
            'callback' => function($name) {return $name;}
        ]);

        echo $object->prop; //Hello
        echo $object->callback('Beautiful'); //Beautiful
        echo $object->prop2; //World
```
When we need mix some class
```php
          class MyObject { //for mixin with Object

            private $prop;

            public function getProp() {
                return $this->prop;
            }
        }

        $object->mixinAttach('additional_methods', [//add all public methods to $object
            'class' => 'MyObject', //if you need use constructor pass new MyObject
            'prop' => 'work'
        ]);
        echo $object->getProp2(); //work
```

Helper methods
```php
        $object->hasMixin('additional_params'); 
        $object->mixinDetech('additional_params');
        $object->issetProperty('prop'); //check whether $objec has mixed property
```
