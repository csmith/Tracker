<?PHP

class AudioscrobblerSource implements Source {

	private $data = array('last' => 0, 'data' => array());

	public function __construct() {
		$cache = dirname(dirname(__FILE__)) . '/cache/lastfm.dat';

		if (file_exists($cache)) {
			$this->data = unserialize(file_get_contents($cache));
                        if (filemtime($cache) > strtotime('-1 day')) { return; }
		}

		$data = file_get_contents('http://ws.audioscrobbler.com/1.0/user/MD87/weeklychartlist.xml');
		$data = new SimpleXMLElement($data);
		$mtime = $data['last'];

		foreach($data->chart as $chart) {
			$from = (int) $chart['from'];
			$to = (int) $chart['to'];

			if ($to > $this->data['last']) {
				$mtime = $to;

				$newdata = "http://ws.audioscrobbler.com/1.0/user/MD87/weeklytrackchart.xml?from=$from&to=$to";
				echo "Retrieving $newdata<br>"; flush();
				$newdata = new SimpleXMLElement(file_get_contents($newdata));
				$top = array();
				foreach ($newdata->track as $track) {
					$title = (String) $track->name;
					$artist = (String) $track->artist;
					$link = (String) $track->url;
					$count = (int) $track->playcount;
					$message  = '<li><a href="' . $link. '">' . $title .'</a> by ';
					$message .= $artist . ' (' . $count . ' play';
					$message .= ($count == 1 ? '' : 's') . ')';
					$top[] = $message;
					if (count($top) == 5) { break; }
				}

				$message = 'Top tracks this week: <ul>' . implode(', ', $top) . '</ul>';
				$this->data['data'][] = new Event(strtotime('23:59:59', $to), $message);

                                $newdata = "http://ws.audioscrobbler.com/1.0/user/MD87/weeklyartistchart.xml?from=$from&to=$to";
                                echo "Retrieving $newdata<br>"; flush();
                                $newdata = new SimpleXMLElement(file_get_contents($newdata));
                                $top = array();
                                foreach ($newdata->artist as $track) {
                                        $artist = (String) $track->name;
                                        $link = (String) $track->url;
					$count = (int) $track->playcount;
                                        $message  = '<li><a href="' . $link. '">' . $artist.'</a> ';
                                        $message .= ' (' . $count . ' play';
                                        $message .= ($count == 1 ? '' : 's') . ')';
                                        $top[] = $message;
                                        if (count($top) == 3) { break; }
                                }

                                $message = 'Top artists this week: <ul>' . implode(', ', $top) . '</ul>';
                                $this->data['data'][] = new Event(strtotime('23:59:59', $to), $message);

			}
		}

		$this->data['last'] = $mtime;

		file_put_contents($cache, serialize($this->data));
	}

	public function getData() {
		return $this->data['data'];
	}

}

$_SOURCES[] =& new AudioscrobblerSource();

?>
