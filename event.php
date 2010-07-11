<?PHP

class Event {

	private $data;
	private $time;

	public function __construct($time, $data) {
		$this->time = $time;
		$this->data = $data;
	}

	public function getTime() {
		return $this->time;
	}

	public function getData() {
		return $this->data;
	}

}

?>
