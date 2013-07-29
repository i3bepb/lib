<?php

namespace i3bepb;
class Func {
	protected static $rootDir = null;

    static public function rootDir() {
		if(!$this->rootDir) {
			if(php_sapi_name() !== 'cli') {
				static::rootDir = realpath($_SERVER['DOCUMENT_ROOT'] . '/../');
			} else {
				// @todo: definition root dir
				static::rootDir = 'c:\webserver\home\app';
			}
		}
		return static::rootDir;
	}
}