<?php
namespace i3bepb;

class Func {
	protected static $rootDir = null;

    static private function lookRootDir($dir) {
        $find = false;
        $rootFolders = array('www', 'public');
        if(file_exists($dir) && is_dir($dir)) {
            self::$rootDir = dirname($dir);
            $arr = scandir(self::$rootDir);
            foreach($rootFolders as $v) {
                if(in_array($v, $arr)) {
                    $find = true;
                    break;
                }
            }
            if(!$find) self::lookRootDir(self::$rootDir);
        } else {
            throw new \Exception("Can not be determined root dir");
        }
    }

    static public function rootDir() {
		if(!self::$rootDir) {
			if(php_sapi_name() !== 'cli') {
				self::$rootDir = realpath($_SERVER['DOCUMENT_ROOT'] . '/../');
			} else {
                self::lookRootDir(__DIR__);
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