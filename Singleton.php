<?php
namespace i3bepb;
abstract class Singleton {
    static protected $_instance = null;

    static public function getInstance($param = false) {
        if(null === static::$_instance) {
            $className = get_called_class();
            if($param) static::$_instance = new $className($param);
            else static::$_instance = new $className();
        }
        return static::$_instance;
    }

    final private function __clone() {}
}