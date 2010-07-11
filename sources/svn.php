<?PHP

class SvnSource implements Source {

	private $data;

	public function __construct() {
		$cache = dirname(dirname(__FILE__)) . '/cache/svn.dat';

		if (file_exists($cache)) {
			$this->data = unserialize(file_get_contents($cache));
		} else {
			$this->data = array('data' => array(), 'processed' => array());
		}

		foreach (glob('/home/chris/svn/*') as $repo) {
			$name = basename($repo);
			$titl = '<strong>' . $name . '</strong>';

			$revs = count(glob($repo . '/db/revs/*'));
			$done = max(1, (int) $this->data['processed'][$name]);

			for ($i = $done; $i < $revs; $i++) {
                                $date = strtotime(substr(`svnlook date $repo -r $i`, 0, 19));
				$revh = '<strong>' . $i . '</strong>';
				$mesg = '<code>' . htmlentities(trim(`svnlook log $repo -r $i`)) . '</code>';
				$mesg = 'Committed revision ' . $revh . ' to ' . $titl . ' with log message ' . $mesg;
				$this->data['data'][] = new Event($date, $mesg);
			}

			$this->data['processed'][$name] = $revs;
		}

		file_put_contents($cache, serialize($this->data));
		unset($this->data['processed']);
	}

	public function getData() {
		return $this->data['data'];
	}
}

$_SOURCES[] =& new SvnSource();

?>
