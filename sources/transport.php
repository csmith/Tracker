<?PHP

class TransportSource implements Source {

	private $data = array('last' => 0, 'data' => array());

	public function __construct() {
		$cache = dirname(dirname(__FILE__)) . '/cache/transport.dat';

		if (file_exists($cache)) {
			$this->data = unserialize(file_get_contents($cache));
                        if (filemtime($cache) > strtotime('-1 day')) { return; }
		}

		$url = 'https://oyster.tfl.gov.uk/oyster/security_check';
		$fields = 'j_username=&j_password=';
		$info = $this->do_post_request($url, $fields);

		foreach ($info['data']['wrapper_data'] as $header) {
			if (substr($header, 0, 11) == 'Set-Cookie:') {
				$cookie = preg_replace('/^Set-Cookie: (.*?); Path.*$/', '\1', $header);
				break;
			}
		}

		$url = 'https://oyster.tfl.gov.uk/oyster/ppvStatement.do';
		$fields = '';
		$headers = 'Cookie: ' . $cookie;
		$info = $this->do_post_request($url, $fields, $headers, 'GET');
                $info = $this->do_post_request($url, $fields, $headers, 'GET');
		preg_match('/<table class="journeyhistory">.*?<tr>.*?<\/tr>(.*?)<\/table>/is', $info['response'], $m);
		preg_match_all('/<tr>(.*?)<\/tr>/is', $m[0], $m2);
		$mtime = $this->data['last'];

	  	foreach ($m2[1] as $match) {	
			preg_match_all('/<td.*?>(.*?)<\/td>/is', $match, $m3);

			if (trim($m3[1][0]) != '&nbsp;') {
				$date = explode('/', trim($m3[1][0]));
				$date = $date[1] . '/' . $date[0] . '/' . $date[2];
				$date = strtotime($date);
			}

			$time = strtotime($m3[1][1], $date);
			$mtime = max($mtime, $time);

			if ($time > $this->data['last']) {
				$station = trim($m3[1][2]);
				$enter = 'Entry' == trim($m3[1][3]);
				$message = $enter ? 'Entered <strong>' : 'Departed <strong>';
				$message .= $station . ' Station</strong>';
				$this->data['data'][] = new Event($time, $message);
			}
		}

		$this->data['last'] = $mtime;

		file_put_contents($cache, serialize($this->data));
	}

	public function getData() {
		return $this->data['data'];
	}

	private function do_post_request($url, $data, $optional_headers = null, $method = 'POST') {
 	     $params = array('http' => array(
        	          'method' => $method,
                	  'content' => $data
	               ));
	     if ($optional_headers !== null) {
        	$params['http']['header'] = $optional_headers;
	     }
	     $ctx = stream_context_create($params);
	     $fp = @fopen($url, 'rb', false, $ctx);
	     if (!$fp) {
	        throw new Exception("Problem with $url, $php_errormsg");
	     }
	     $response = @stream_get_contents($fp);
	     if ($response === false) {
	        throw new Exception("Problem reading data from $url, $php_errormsg");
	     }
	     return array('response' => $response, 'data' => stream_get_meta_data($fp));
	}
}

$_SOURCES[] =& new TransportSource();

?>
