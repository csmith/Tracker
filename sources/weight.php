<?PHP

class WeightSource implements Source {

	private $data = array();

	public function __construct() {
		require_once('/home/chris/websites/apps.md87.co.uk/weight/data.php');
		foreach ($data as $wk => $weight) {
			$time = strtotime('+' . $wk . ' weeks', 1190588400);
			$message = 'Recorded my <a href="http://apps.md87.co.uk/weight/">weight</a> as <strong>';
			$message .= $weight . 'kg</strong>';
			$this->data[] = new Event($time, $message);
		}
	}

	public function getData() {
		return $this->data;
	}
}

$_SOURCES[] =& new WeightSource();

?>
