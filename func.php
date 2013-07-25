<?php

namespace i3bepb;
class Func {
	protected static $rootDir = null;

    static public function rootDir() {
		if(!$this->rootDir) {
			if(php_sapi_name() !== 'cli') {
				$this->rootDir = realpath($_SERVER['DOCUMENT_ROOT'] . '/../');
			} else {
				// @todo: definition root dir
				$this->rootDir = 'c:\webserver\home\app';
			}
		}
		return $this->rootDir;
	}
}