{% set_app "framework" %}
<style type="text/css">
	.debug_panel {
		width: 100%;
		margin: 0;
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		text-align: left !important;
	}
	
	.debug_panel * {
		margin: 0;
	}
	
	.debug_panel .console_warning {
		color: orange;
	}
	
	.debug_panel .console_error {
		color: red;
	}

	.debug_pill {
		/* Adapted from Bootstrap */
		display: inline-block;
		padding: 4px 10px 4px;
		margin-bottom: 0;
		font-size: 13px;
		line-height: 18px;
		color: #333;
		text-align: center;
		text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
		vertical-align: middle;
		background-color: whiteSmoke;
		cursor: pointer;
		background-image: linear-gradient(top, white, #E6E6E6);
		background-repeat: repeat-x;
		border-color: #E6E6E6 #E6E6E6 #BFBFBF;
		border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
		border: 1px solid #CCC;
		border-bottom-color: #BBB;
		border-radius: 4px;
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
	}
	
	.debug_tab {
		display: none;
		background-color: rgba(0,0,0,0.90);
		color: #EEE;
		font-family: Arial, sans-serif;
		font-size: 13px;
		border-top: 1px solid green;
		position: relative;
		height: 250px;
		overflow-y: scroll;
	}
	
	.debug_tab * {
		width: auto;
		color: #FFF;
	}
</style>
<script type="text/javascript">
function debug_hideTabs() {
	document.getElementById('console-tab').style.display='none';
	document.getElementById('profile-tab').style.display='none';
	document.getElementById('db-tab').style.display='none';
}

function debug_toggleTab(name) {
	var elem = document.getElementById(name + '-tab');
	var show = !(elem.style.display == 'block');
	debug_hideTabs();
	if (show)
		elem.style.display='block';
}
</script>

<div style="height: 30px;"></div> {% comment %} Clear the buttons.. {% endcomment %}
<div class="debug_panel">
	<div style="padding: 10px 10px;">
		<div class="debug_pill" onclick="debug_toggleTab('profile');">{% local_i18n "debug_profiler" %}</div>
		<div class="debug_pill" onclick="debug_toggleTab('console');">{% local_i18n "debug_console" %} {{debug_info_count}}</div>
		<div class="debug_pill" onclick="debug_toggleTab('db');">{% local_i18n "debug_db" %}</div>
	</div>
	<div id="profile-tab" class="debug_tab">
		<div style="padding: 20px 30px;">
			<h3 style="color: #EEE;">{% local_i18n "debug_profiler" %}</h3>
			<table>
				<thead>
					<tr>
						<th>Function</th>
						<th>Call Count</th>
						<th>Average Cost</th>
						<th>Total Cost</th>
					</tr>
				</thead>
				<tbody>
				<?php
				require_once(home_dir . "framework/profiler.php");
				$blocks = Profiler::get_blocks();
				foreach ($blocks as $block_set => $block) {
					print '<tr>
								<td>'.$block_set.'</td>
								<td>' . Profiler::get_call_count($block_set) . '</td>
								<td>' . Profiler::get_average($block_set) . '</td>
								<td>' . Profiler::get_total($block_set) . '</td>
							 </tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<div id="console-tab" class="debug_tab">
		<div style="padding: 20px 30px;"><h3 style="color: #EEE;">{% local_i18n "debug_console" %}</h3>
			<p>{{debug_info}}</p>
		</div>
	</div>
	<div id="db-tab" class="debug_tab">
		<div style="padding: 20px 30px;"><h3 style="color: #EEE;">{% local_i18n "debug_db" %}</h3>
			<p style="margin: 3px; color: #EEE;">{% local_i18n "debug_dbqueries" %} {{db_queries}}</p>
			<p>{{db_info}}</p>
		</div>
	</div>
</div>
