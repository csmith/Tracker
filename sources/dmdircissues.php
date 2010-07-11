<?PHP

                chdir('/home/dmdirc/www/bugs/');
                require_once('core.php');


class DMDircIssuesSource implements Source {

	private $data = array();

	public function __construct() {
		mysql_connect('localhost', '', '');
		mysql_select_db('');


		$sql = 'SELECT UNIX_TIMESTAMP(date_modified) AS ts, field_name, old_value, new_value, type, bug_id FROM mantis_bug_history_table INNER JOIN mantis_user_table ON mantis_bug_history_table.user_id = mantis_user_table.id WHERE mantis_user_table.username = \'MD87\' AND date_modified > FROM_UNIXTIME(' . MIN_TIME . ')';

		$res = mysql_query($sql);

		while ($row = mysql_fetch_assoc($res)) {
                        $info = history_localize_item($row['field_name'], $row['type'], $row['old_value'], $row['new_value']);

                        $desc = $info['note'] . (!empty($info['change']) ? ': ' . $info['change'] : '');

			if ($desc == 'Checkin') { $ctime = $row['ts']; continue; }

			if ($desc == 'New Issue') { $ntime = $row['ts']; } else if ($ntime == $row['ts']) { continue; }

			if ($row['ts'] == $ctime) { continue; }

			$link = '<a href="http://bugs.dmdirc.com/view.php?id=' . $row['bug_id'];
                        $link .= '">' . $row['bug_id'] . '</a>';

			if ($desc == 'New Issue') {
				$message = 'Created DMDirc issue ' . $link;
			} else if (substr($desc, 0, 6) == 'Note A') {
				$message = 'Added note to DMDirc issue ' . $link;
			} else {
				continue;
				$message  = 'Altered DMDirc issue ' . $link . ' (' . htmlentities($desc) . ')';
			}

			$this->data[] = new Event($row['ts'], $message); 
		}
	}

	public function getData() {
		return $this->data;
	}
}

$_SOURCES[] =& new DMDircIssuesSource();

?>
