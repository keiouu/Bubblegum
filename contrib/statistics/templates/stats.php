{% extends "contrib/admin/templates/base.php" %}

<?php
require_once(home_dir . "contrib/statistics/models.php");
$stats = Statistics_Visit::generate();

$pie_colours = array(
    "0, 125, 255",
    "25, 200, 50",
    "50, 200, 255",
    "255, 50, 70",
    "255, 200, 0",
);

$pie_colour_stops = array(
    "10, 65, 125",
    "10, 123, 20",
    "0, 200, 255",
    "255, 30, 40",
    "255, 100, 0",
);
?>

{% block head %}
<link href="{{home_url}}contrib/statistics/media/graph.css" rel="stylesheet">
<script type="text/javascript" src="{{home_url}}contrib/statistics/media/graph.js"></script>
{% endblock %}

{% block body %}
<h1>Statistics</h1><br />
<div class="well span4">
	<h2>Browser Statistics</h2>
	<div class="pie_chart">
		<canvas id="browser_chart" width="200" height="200"></canvas>
		<table class="legend">
		<?php
		$i = 0;
		foreach ($stats['browsers'] as $browser => $count) {
			print '<tr class="entry"><td class="colour">'.$pie_colours[$i].'</td><td class="stop_colour">'.$pie_colour_stops[$i].'</td><td class="legend">' . $browser . '</td><td class="value">' . Statistics_Visit::as_percentage($stats['browsers'], $browser) . '</td></tr>';
			$i = ($i + 1) % count($pie_colours);
		}
		?>
		</table>
	</div>
</div>
<div class="well span4">
	<h2>OS Statistics</h2>
	<div class="pie_chart">
		<canvas id="os_chart" width="200" height="200"></canvas>
		<table class="legend">
		<?php
		$i = 0;
		foreach ($stats['os'] as $os => $count) {
			print '<tr class="entry"><td class="colour">'.$pie_colours[$i].'</td><td class="stop_colour">'.$pie_colour_stops[$i].'</td><td class="legend">' . $os . '</td><td class="value">' . Statistics_Visit::as_percentage($stats['os'], $os) . '</td></tr>';
			$i = ($i + 1) % count($pie_colours);
		}
		?>
		</table>
	</div>
</div>
<div class="well span4">
	<h2>Javascript Enabled</h2>
	<div class="pie_chart">
		<canvas id="js_chart" width="200" height="200"></canvas>
		<table class="legend">
		<?php
		print '<tr class="entry"><td class="colour">'.$pie_colours[0].'</td><td class="stop_colour">'.$pie_colour_stops[0].'</td><td class="legend">Enabled</td><td class="value">' . Statistics_Visit::as_percentage($stats['js'], "enabled")  . '</td></tr>';
		print '<tr class="entry"><td class="colour">'.$pie_colours[1].'</td><td class="stop_colour">'.$pie_colour_stops[1].'</td><td class="legend">Disabled</td><td class="value">' . Statistics_Visit::as_percentage($stats['js'], "disabled") . '</td></tr>';
		?>
		</table>
	</div>
</div>
<div class="well span4">
	<h2>Cookies Enabled</h2>
	<div class="pie_chart">
		<canvas id="cookies_chart" width="200" height="200"></canvas>
		<table class="legend">
		<?php
		print '<tr class="entry"><td class="colour">'.$pie_colours[0].'</td><td class="stop_colour">'.$pie_colour_stops[0].'</td><td class="legend">Enabled</td><td class="value">' . Statistics_Visit::as_percentage($stats['cookies'], "enabled")  . '</td></tr>';
		print '<tr class="entry"><td class="colour">'.$pie_colours[1].'</td><td class="stop_colour">'.$pie_colour_stops[1].'</td><td class="legend">Disabled</td><td class="value">' . Statistics_Visit::as_percentage($stats['cookies'], "disabled") . '</td></tr>';
		?>
		</table>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		$(".pie_chart").each(function(i, elem) {
			var canvas = $(elem).find("canvas");
			var data = new Array();
			$(elem).find(".entry").each(function(j, entry) {
				var colourTD = $(entry).find(".colour");
				
				data[j] = [];
				data[j]["label"] = $(entry).find(".legend").html();
				data[j]["value"] = $(entry).find(".value").html();
				data[j]["start_colour"] = colourTD.html();
				data[j]["end_colour"] = $(entry).find(".stop_colour").html();
				
				colourTD.html("&nbsp;");
				colourTD.css("background-color", "rgb("+data[j]["start_colour"]+")");
			});
			draw_pie_chart(canvas[0], data);
		});
	});
</script>
{% endblock %}
