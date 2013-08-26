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

    static public function addDirSeparator($path) {
        $rest = substr($path, -1);
        if(!in_array($rest, array('/', DIRECTORY_SEPARATOR))) {
            $path = $path . DIRECTORY_SEPARATOR;
        }
        return $path;
    }

    static public function existAndCreateDir($rootDir, $arrDir) {
        if($arrDir) {
            foreach($arrDir as $v) {
                $rootDir = self::addDirSeparator($rootDir);
                $dir = $rootDir . $v;
                if(!is_dir($dir)) mkdir($dir, 0755);
                $rootDir = $dir;
            }
        }
        return $rootDir;
    }
}