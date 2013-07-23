<?php
namespace i3bepb;
class develop {
    static public function log($text) {
        $f = realpath($_SERVER['DOCUMENT_ROOT'] . '/../') . '/mylog.txt';
        if(!$f) exit('not exist mylog.txt');
        $fp = fopen($f, 'a') or die('err my log');
        fputs($fp, $text . "\r\n");
        fclose($fp);
    }

    static public function debug($var, $output = true) {
        $o = '';
        if($output) $o .= '<pre>';
        if(is_bool($var)) {
            $o .= $var ? 'TRUE' : 'FALSE';
        } elseif(!$var && is_scalar($var)) {
            $o .= var_export($var, true);
        } else {
            $o .= print_r($var, true);
        }
        if($output) $o .= '</pre>';

        if($output) {
            exit($o);
        } else {
            self::log($o);
        }
    }
}
