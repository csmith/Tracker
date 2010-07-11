<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
 <head>
  <title>Chris tracking</title>
  <script src="res/prototype.js" type="text/javascript"></script>
  <script type="text/javascript">
   function doFilter() {
    var params = new Hash();
    params.set('dummy', 'true');
    params.set('period', document.getElementById('period').value);

    var elements = document.getElementsByTagName('input');
    for (var i = 0; i < elements.length; i++) {
     var element = elements[i];
     if (element.type == 'checkbox' && element.checked) {
      params.set(element.id, 'true');
     }
    }

    params.set('sort', document.getElementById('sort').value);

    new Ajax.Updater('content', 'content.php', {parameters:params});
    return false;
   }

   Ajax.Responders.register({
    onCreate: function(request) {
     document.getElementById('loading').style.display = 'block';
    },
    onComplete: function(request) {
     document.getElementById('loading').style.display = 'none';
    }
   });
  </script>
  <style type="text/css">
   li.WikiSource { list-style-image: url('res/wiki.ico'); }
   li.SvnSource, li.DMDircSvnSource { list-style-image: url('res/svn.ico'); }
   li.WeightSource { list-style-image: url('res/weight.png'); }
   li.TransportSource { list-style-image: url('res/transport.ico'); }
   li.AudioscrobblerSource { list-style-image: url('res/lastfm.ico'); }
   li.AudioscrobblerSource li { list-style-image: none; list-style-type: decimal; }
   li.DMDircIssuesSource { list-style-image: url('res/dmdirc.ico'); }
   li { margin-bottom: 6px; }
   li ul li { margin-bottom: 2px; }
   #content {
    position: absolute;
    top: 20px;
    right: 250px;
    left: 20px;
    bottom: 20px;
    overflow: auto;
    border: 1px solid #aaa;
    padding: 10px;
   }

   #right {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 210px;
   }

   #filter, #timespan, #sortbox {
    border: 1px solid #aaa;
    padding: 10px;
    margin-bottom: 20px;
   }

   input[type="checkbox"] {
    margin-right: 10px;
   }

   h2 {
    margin: 0px;
   }

   select {
    width: 100%;
    margin-top: 10px;
   }

   #loading {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100px;
    padding: 5px;
    margin-top: -0.75em;
    margin-left: -50px;
    background-color: #900;
    color: white;
    text-align: center;
    border: 0px;
    z-index: 100;
   }
  </style>
 </head>
 <body>
  <div id="loading" style="display: none;">
   Loading...
  </div>
  <div id="content">
<?PHP require_once('content.php'); ?>
  </div>
  <div id="right">
   <form action="/tracker/" method="POST" onsubmit="return doFilter();">
    <input type="hidden" name="dummy" value="true">
  <div id="filter">
   <h2>Filter</h2>
    <ul id="filterlist">
<?PHP

foreach ($_SOURCES as $source) {
	$name = get_class($source);
        $nicename = preg_replace('/([a-z])([A-Z])/', '\1 \2', substr($name, 0, -6));
	$checked = '';

	if (empty($_POST) || isset($_POST[$name])) {
		$checked = ' checked="checked"';
	}

	echo '<li class="', $name,' "><label><input type="checkbox" id="', $name, '" name="', $name, '"', $checked, '>';
	echo $nicename, '</label>';
}

?>
    </ul>
  </div>
  <div id="timespan">
   <h2>Timespan</h2>
   <select id="period" name="period">
<?PHP

 foreach (array('day', 'week', 'month', 'year') as $timespan) {
  echo '<option value="', $timespan, '"';
  if ((isset($_POST['timespan']) && $_POST['timespan'] == $timespan)
   || (!isset($_POST['timespan']) && $timespan == 'week')) {
   echo ' selected="selected"';
  }
  echo '>', ucfirst($timespan), '</option>';
 }

?>
   </select>
  </div>
  <div id="sortbox">
   <h2>Sort order</h2>
   <select name="sort" id="sort">
<?PHP

 foreach (array('forwards', 'backwards') as $timespan) {
  echo '<option value="', $timespan, '"';
  if ((isset($_POST['sort']) && $_POST['sort'] == $timespan)
   || (!isset($_POST['sort']) && $timespan == 'backwards')) {
   echo ' selected="selected"';
  }
  echo '>', ucfirst($timespan), '</option>';
 }

?>
   </select>
  </div>
    <input type="submit" value="Apply" id="filterbutton">
   </form>
  </div>
 </body>
</html>
