{% set_app "framework" %}

<div style="height: 30px;"></div>
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
						<th>Average Time</th>
						<th>Total Time</th>
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
			<p style="padding: 5px 0 10px 0;">{% local_i18n "debug_version" %} {{tikapot_version}}</p>
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
