<?PHP

class DMDircSvnSource implements Source {

	private $data;

	public function __construct() {
		$cache = dirname(dirname(__FILE__)) . '/cache/dmdircsvn.dat';

		if (file_exists($cache)) {
			$this->data = unserialize(file_get_contents($cache));
		} else {
			$this->data = array('data' => array(), 'processed' => 0);
		}

		$repo = '/home/dmdirc/repo';
		$name = 'DMDirc'; 
		$titl = '<strong>' . $name . '</strong>';

		$revs = count(glob($repo . '/db/revs/*'));
		$done = max(1, (int) $this->data['processed']);

		for ($i = $done; $i < $revs; $i++) {
			$auth = trim(`svnlook author $repo -r $i`);

			if ($auth == 'chris87') {
				$date = strtotime(substr(`svnlook date $repo -r $i`, 0, 19));
				$revh = '<strong>' . $i . '</strong>';
				$mesg = '<code>' . htmlentities(trim(`svnlook log $repo -r $i`)) . '</code>';
				$mesg = 'Committed revision ' . $revh . ' to ' . $titl . ' with log message ' . $mesg;
				$this->data['data'][] = new Event($date, $mesg);
			}
		}

		$this->data['processed'] = $revs;

		file_put_contents($cache, serialize($this->data));
		unset($this->data['processed']);
	}

	public function getData() {
		return $this->data['data'];
	}
}

$_SOURCES[] =& new DMDircSvnSource();

?>
