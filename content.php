<?PHP

$period = isset($_POST['period']) ? $_POST['period'] : 'week';

define('MIN_TIME', strtotime('-1 ' . $period));
define('MAX_TIME', time());

if (!empty($_POST)) {
	$_FILTER = $_POST;
}

require_once('tracker.php');

$lastdate = '';

foreach ($_EVENTS as $event) {
        $date = date('l, F \t\h\e j<\s\u\p>S</\s\u\p>, Y', $event['event']->getTime());

        if ($date != $lastdate) {
                if ($lastdate != '') {
                        echo '</ul>';
                }

                echo '<h2>', $date, '</h2><ul>';
                $lastdate = $date;
        }

        echo '<li class="', get_class($event['source']), '">', $event['event']->getData();
}

if ($lastdate == '') {
 echo '<h2>Error</h2>';
 echo '<p>No events matching that filter/time period</p>';
}

?>
