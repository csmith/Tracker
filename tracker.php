<?PHP

require_once('event.php');
require_once('source.php');

if (!is_dir(dirname(__FILE__) . '/cache')) {
	mkdir(dirname(__FILE__) . '/cache');
}

$_SOURCES = array();

foreach (glob('sources/*.php') as $source) {
	set_time_limit(60);
	require_once($source);
}

$_EVENTS = array();

foreach ($_SOURCES as $source) {
	if (!isset($_FILTER) || $_FILTER[get_class($source)]) {
		foreach ($source->getData() as $event) {
			if ($event->getTime() > MIN_TIME && $event->getTime() < MAX_TIME) {
				$_EVENTS[] = array('source' => $source, 'event' => $event);
			}
		}
	}
}

function sortEvents($a, $b) {
	return ($_POST['sort'] == 'forwards' ? 1 : -1) * ($a['event']->getTime() - $b['event']->getTime());
}

usort($_EVENTS, 'sortEvents');

?>
