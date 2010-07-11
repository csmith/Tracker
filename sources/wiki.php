<?PHP

class WikiSource implements Source {

	private $data = array();

	public function __construct() {
		mysql_connect('localhost', '', '');
		mysql_select_db('');

		$sql = 'SELECT tag AS page, UNIX_TIMESTAMP(time) AS ts FROM wikka_pages WHERE user = \'ChrisSmith\'';
		$res = mysql_query($sql);

		while ($row = mysql_fetch_assoc($res)) {
			$message  = 'Edited the <a href="http://wiki.MD87.co.uk/' . $row['page'];
			$message .= '">' . $row['page'] . '</a> page on my wiki';
			$this->data[] = new Event($row['ts'], $message); 
		}
	}

	public function getData() {
		return $this->data;
	}
}

$_SOURCES[] =& new WikiSource();

?>
