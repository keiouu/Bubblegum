{% extends "contrib/admin/templates/base.php" %}

{% block sidebar_menu %}
<div class="well">
  <h5>{% i18n "admin_menu" %}</h4>
  <button class="btn btn-primary" onClick="parent.location='<?php echo $request->fullPath; ?>add/'">{% i18n "admin_new" %} <?php echo $request->model; ?></button>
  <?php
  // Extra actions
  foreach ($request->admin->get_actions() as $action) {
  	if ($action->is_global()) {
		print $action->render($request, $request->dataset);
	}
  }
  ?>
  <button class="btn btn-danger" id="button_delete" data-toggle="modal" data-target="#delete-confirm-modal" data-backdrop="static">{% i18n "admin_delete_all" %}</button>
  <p>{% i18n "admin_max_items" %}</p>
  <select name="max_count" class="pagination_limit">
  	<?php
  	for ($i = 1; $i <= 1000; $i *= 2) {
  		print '<option value="'.$i.'"';
  		if ((isset($request->get['limit']) && $request->get['limit'] == $i) || (!isset($request->get['limit']) && $i == 25))
  			print ' selected="selected"';
  		print '>'.$i.'</option>';
  		if ($i == 1)
  			$i = 5;
  		if ($i == 10)
  			$i = 12.5;
  		if ($i == 100)
  			$i = 125;
  	}
  	?>
  </select>
</div>
<?php if (count($request->admin->get_filters()) > 0) { ?>
<div class="well filters">
	<h5>{% i18n "admin_filters" %}</h5>
	<?php
	require_once(home_dir . "framework/utils.php");
	foreach ($request->admin->get_filters() as $filter) {
		print $filter->render($request);
	}
	?>
</div>
<?php } ?>
{% endblock %}
            
{% block body %}
<div class="main-unit">
	<table class="table table-bordered table-striped sortable-table">
		<thead>
			<tr>
				<th></th>
				<?php
				require_once(home_dir . "framework/utils.php");
				foreach ($request->dataset->get_headings() as $_heading) {
					$heading = starts_with($_heading, "call_") ? substr($_heading, strlen("call_")) : $_heading;
					if (starts_with($_heading, "call_")) {
						print '<th>' . ($heading === "id" ? "#" : prettify($heading)) . '</th>';
					} else {
						$arrow = "arrowDown";
						if (isset($request->get['*'.$heading]))
							$arrow = $request->get['*'.$heading] == "desc" ? 'arrowUp' : 'arrowDown';
						print '<th class="sortable" data-orderby="'.$heading.'">' . ($heading === "id" ? "#" : prettify($heading)) . '<span class="arrow '.$arrow.'"></span></th>';
					}
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php
				$linked_headings = $request->dataset->get_linked_headings();
				$dataset = $request->dataset->get_page($request->current_page, $request->pagination_limit);
				if (count($dataset) == 0) {
					print '<tr><td colspan="'.(count($request->dataset->get_headings()) + 1).'">{% i18n "admin_nodata" %}</td></tr>';
				} else {
					foreach ($dataset as $data) {
						print '<tr>';
						print '<td class="select-col"><input class="delete_box" type="checkbox" name="delete_'.$data->pk.'" value="'.$data->pk.'"></td>';
						foreach ($request->dataset->get_headings() as $heading) {
							$linked = in_array($heading, $linked_headings);
							print '<td>';
							if ($linked)
								print '<a href="'.$request->model_url.'edit/'.$data->pk.'/">';
							print $request->dataset->get_value($data, $heading);
							if ($linked)
								print '</a>';
							print '</td>';
						}
						print '</tr>';
					}
				}
			?>
		</tbody>
	</table>
	<div class="pagination">
		<ul>
			<?php
			// The previous button
			if ($request->current_page == 1) {
				$url = $request->fullPath . "?" . $request->query_string();
				print '<li class="prev disabled"><a href="'.$url.'" onClick="return false;">&larr; {% i18n "admin_prev" %}</a></li>';
			} else {
				$url = $request->create_url($request->fullPath, $request->query_string(), "page=" . ($request->current_page - 1));
				print '<li class="prev"><a href="'.$url.'">&larr; {% i18n "admin_prev" %}</a></li>';
			}
			
			// The middle buttons
			$count = $request->dataset->get_pages($request->pagination_limit);
			for ($i = 1; $i <= $count; $i++) {
				$url = $request->create_url($request->fullPath, $request->query_string(), "page=" . $i);
				print '<li class="'.($i == $request->current_page ? 'active' : '').'"><a href="'.$url.'">'.$i.'</a></li>';
			}
			
			// The next button
			if ($count <= $request->current_page) {
				$url = $request->fullPath . "?" . $request->query_string();
				print '<li class="next disabled"><a href="'.$url.'" onClick="return false;">{% i18n "admin_next" %} &rarr;</a></li>';
			} else {
				$url = $request->create_url($request->fullPath, $request->query_string(), "page=" . ($request->current_page + 1));
				print '<li class="next"><a href="'.$url.'">{% i18n "admin_next" %} &rarr;</a></li>';
			}
			?>
		</ul>
	</div>
</div>
{% include "contrib/admin/templates/includes/delete-modal.php" %}
<script type="text/javascript">
$(function () {
	function goURL(header, value) {
		var url = "{{page_url}}?";
		var found = false;
		if (document.location.search.length > 0) {
			var url_params = document.location.search.substring(1);
			var args = url_params.split("&");
			for (i = 0; i < args.length; i++) {
				if (i > 0)
					url += '&';
				var arg = args[i].split("=");
				var arg_name = arg[0];
				var arg_value = arg[1];
				url += arg_name + '=';
				if (arg_name == header) {
					found = true;
					url += value;
				} else {
					url += arg_value;
				}
			}
			if (!found && args.length > 0)
				url += '&';
		}
		if (!found) {
			url += header + '=' + value;
		}
		window.location = url;
	}

	$(".pagination_limit").change(function() {
		goURL("limit", $(this).val());
	});

	$(".filters select").change(function() {
		goURL("_" + $(this).attr("name"), $(this).val());
	});

	$(".sortable-table th.sortable").click(function() {
		var dasc = "desc";
		if ($(this).find("span.arrowUp").length > 0)
			dasc = "asc";
		goURL("*" + $(this).attr("data-orderby"), dasc);
	});
	
	$("#button_delete").hide();
	
	$('.delete_box').change(function() {
		if ($('.delete_box:checked').length > 0) {
			$("#button_delete").show();
		} else {
			$("#button_delete").hide();
		}
	});
	
	$("#do-delete").click(function() {
		var data_ids = "";
		$('.delete_box:checked').each(function() {
			if (data_ids.length > 0)
				data_ids += ",";
			data_ids += $(this).val();
		});
		goURL("delete", data_ids);
	});
});
</script>
{% endblock %}
