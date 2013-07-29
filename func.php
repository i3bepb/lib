<?php

namespace i3bepb;
class Func {
	protected static $rootDir = null;

    static public function rootDir() {
		if(!self::$rootDir) {
			if(php_sapi_name() !== 'cli') {
				self::$rootDir = realpath($_SERVER['DOCUMENT_ROOT'] . '/../');
			} else {
				// @todo: definition root dir
                self::$rootDir = 'c:\webserver\home\app';
			}
		}
		return self::$rootDir;
	}
}