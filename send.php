<?php
namespace i3bepb;
class Send extends \i3bepb\Singleton {
    static protected $_instance = null;
    protected $ch = null, $methodSend = 'socket';

    public function __construct($methodSend = false) {
        if($methodSend) $this->setMethodSend($methodSend);
    }
    public function __destruct() {
        if($this->methodSend == 'curl') curl_close($this->ch);
    }
    public function formateDataForSend(array $data) {
        return http_build_query($data);
    }
    public function setMethodSend($methodSend) {
        $this->methodSend = $methodSend;
    }
    private function sendCurl($opt) {
        // @todo:: обработка ошибок
        if($this->ch === null) {
            $this->ch = curl_init();
            curl_setopt($this->ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1); // чтобы делал все переходы
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); // чтобы возвращал в переменную, а не делал echo
        }
        curl_setopt($this->ch, CURLOPT_TIMEOUT, ($opt['timeout'] ? $opt['timeout'] : 30));
        curl_setopt($this->ch, CURLOPT_URL, $opt['url']);
        curl_setopt($this->ch, CURLOPT_USERAGENT, ($opt['userAgent'] ? $opt['userAgent'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0'));
        if($opt['cookie']) curl_setopt($this->ch, CURLOPT_COOKIE, $opt['cookie']);
        if($opt['method'] == 'post') {
            curl_setopt($this->ch, CURLOPT_POST, 1);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->formateDataForSend($opt['data']));
        }
        if(!empty($opt['user']) && !empty($opt['password'])) {
            curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($this->ch,CURLOPT_USERPWD, $opt['user'] . ":" . $opt['password']);
        }
        $r = curl_exec($this->ch);
        return $r;
    }
    private function sendSteam($opt) {
        // @todo: обработка ошибок
        $url = parse_url($opt['url']);
        if($opt['method'] == 'post') $fData = $this->formateDataForSend($opt['data']);
        $arr = array(
            $url['scheme'] => array(
                'method' => ($opt['method'] == 'post' ? 'POST' : 'GET'),
                'header' => "Content-type: application/x-www-form-urlencoded\r\nAccept: */*\r\n"
                    . 'User-agent:' . ($opt['userAgent'] ? $opt['userAgent'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0') . "\r\n"
                    . ($opt['method'] == 'post' ? 'Content-length:' . strlen($fData) . "\r\n" : '') . ($opt['accept-encoding'] ? 'accept-encoding: ' . $opt['accept-encoding'] . "\r\n" : '')
                    . ($opt['cookie'] ? 'cookie: ' . $opt['cookie'] . "\r\n" : '') . "Connection:close"
            )
        );
        if($opt['method'] == 'post') $arr[$url['scheme']]['content'] = $fData;

        $r = file_get_contents($opt['url'], false, stream_context_create($arr));
        return $r;
    }
    private function sendSocket($opt) {
        $url = parse_url($opt['url']);
        $request = ($opt['method'] == 'post' ? 'POST' : 'GET') . ' ' . ($url['scheme'] == 'https' ? 'ssl://' : '')
            . $url['path'] . ($url['query'] ? '?' . $url['query'] : '')
            . ($url['fragment'] ? '#' . $url['fragment'] : '') . " HTTP/1.1\r\n";
        $request .= 'Accept: ' . ($opt['accept'] ? $opt['accept'] : ' text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8') . "\r\n";
        $request .= "Аccept-language: " . ($opt['accept-language'] ? $opt['accept-language'] : 'en-US,en;q=0.5') . "\r\n";
        $request .= "Content-type: application/x-www-form-urlencoded\r\n";
        $request .= 'User-Agent: ' . ($opt['userAgent'] ? $opt['userAgent'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0') . "\r\n";
        // $request .= "X-Requested-With: XMLHttpRequest\r\n"; // для имитации ajax запроса
        $request .= 'Host: ' . $url['host'] . "\r\n";
        $request .= ($opt['accept-encoding'] ? 'accept-encoding: ' . $opt['accept-encoding'] . "\r\n" : '');
        $request .= ($opt['cookie'] ? 'cookie: ' . $opt['cookie'] . "\r\n" : '');
        $request .= "Connection: Close\r\n";
        $request .= "\r\n";
        if($opt['method'] == 'post') {
            $request .= $this->formateDataForSend($opt['data']) . "\r\n";
        }
        $request .= "\r\n";

        $socket = fsockopen($url['host'], ($url['port'] ? $url['port'] : 80), $errno, $errstr, ($opt['timeout'] ? $opt['timeout'] : 30));
        if(!$socket) {
            // @todo: обработка ошибок
            // echo "$errstr ($errno)<br>\n";
        } else {
            fwrite($socket, $request);
            $r = '';
            while(!feof($socket)) {
                $r .= fgets($socket, 4096);
            }
            fclose($socket);
            return $r;
        }
    }
    /**
     * Отправка запроса через curl, steam, socket
     *
     * @param array $opt массив с конфигурацией (url, method, data, timeout, cookie)
     * @return string ответ/страница
     */
    public function send($opt) {
        if($this->methodSend == 'curl') {
            return $this->sendCurl($opt);
        } elseif($this->methodSend == 'steam') {
            return $this->sendSteam($opt);
        } else { // socket
            return $this->sendSocket($opt);
        }
    }
}