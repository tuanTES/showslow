<?
if (!array_key_exists('url', $_GET) || filter_var($_GET['url'], FILTER_VALIDATE_URL) === false) {
	?><html>
<head>
<title>Error - no URL specified</title>
</head>
<body>
<h1>Error - no URL specified</h1>
<p><a href="../">Go back</a> and pick the URL</p>
</body></html>
<?
	return;
}

?><html>
<head>
<title>Show Slow: Details for <?=htmlentities($_GET['url'])?></title>
<script src="http://api.simile-widgets.org/timeplot/1.1/timeplot-api.js" type="text/javascript"></script>
<script src="details.js" type="text/javascript"></script>
<style>
.yslow1 {
	color: #55009D;
}

.yslow2 {
	color: #2175D9;
}
</style>
</head>
<body onload="onLoad('<?=urlencode($_GET['url'])?>', dataversion);" onresize="onResize();">
<a href="http://code.google.com/p/showslow/"><img src="../showslow_icon.png" style="float: right; margin-left: 1em; border: 0"/></a>
<div style="float: right">powered by <a href="http://code.google.com/p/showslow/">showslow</a></div>
<h1><a title="Click here to go to home page" href="../">Show Slow</a>: Details for <a href="<?=htmlentities($_GET['url'])?>"><?=htmlentities($_GET['url'])?></a></h1>
<?
require_once('../config.php');
db_connect();

$query = sprintf("SELECT y.timestamp, y.w, y.o, y.i FROM yslow2 y, urls WHERE urls.url = '%s' AND y.url_id = urls.id AND y.timestamp > DATE_SUB(now(),INTERVAL 3 MONTH) ORDER BY `timestamp` DESC",
	mysql_real_escape_string($_GET['url'])
);
$result = mysql_query($query);

if (!$result) {
	error_log(mysql_error());
}

$row = mysql_fetch_assoc($result);

if (!$row) {
	?>No data is available yet<?
} else {
	?><h2>Current <a href="http://developer.yahoo.com/yslow/">YSlow</a> grade: <?=yslowPrettyScore($row['o'])?> (<i><?=htmlentities($row['o'])?></i>)</h2>
	<script>dataversion = '<?=urlencode($row['timestamp'])?>'; </script>

	<img src="http://chart.apis.google.com/chart?chs=225x125&cht=gom&chd=t:<?=urlencode($row['o'])?>&chl=<?=urlencode(yslowPrettyScore($row['o']).' ('.$row['o'].')')?>" alt="<?=yslowPrettyScore($row['o'])?> (<?=htmlentities($row['o'])?>)" title="Current YSlow grade: <?=yslowPrettyScore($row['o'])?> (<?=htmlentities($row['o'])?>)"/>

	<h2>YSlow grade over time</h2>
	<div id="my-timeplot" style="height: 250px;"></div>

	<div style="fint-size: 0.2em">
	<span class="yslow1">YSlow1 Grade</span> (0-100);
	<span class="yslow2">YSlow2 Grade</span> (0-100); 
	<span style="color: #D0A825">Page Size</span> (in bytes)
	</div>

	<h2>Measurements history (<a href="data.php?url=<?=urlencode($_GET['url'])?>">csv</a>)</h3>
	<table border="1" cellpadding="5" cellspacing="0">
	<tr><th>Time</th><th>Page Size</th><th>YSlow grade</th><th>profile</th></tr>
<?
	do {
		?><tr>
		<td><?=$row['timestamp']?></td>
		<td align="right"><?=($row['i'] == 'yslow1' ? $row['w'] * 1024 : $row['w'])?> bytes</td>
		<td align="right"><?=yslowPrettyScore($row['o'])?> (<i><?=htmlentities($row['o'])?></i>)</td>
		<td align="right"<? if ($row['i'] == 'ydefault') { echo ' class="yslow2"'; } else if ($row['i'] == 'yslow1') { echo ' class="yslow1"'; } ?>"><?=$row['i']?></td>
		</tr>
<?
	} while ($row = mysql_fetch_assoc($result));

	mysql_free_result($result);
?>
	</table>
<?
}
?>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-817839-17");
pageTracker._trackPageview();
} catch(err) {}</script>
</body></html>