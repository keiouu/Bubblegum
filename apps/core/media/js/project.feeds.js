var feed_milestone = "";

function json_to_table(data, headings) {
	var html = '', page = 1, items = 0;
	$.each(data, function(key, val) {
		html += '<tr data-page="'+page+'"';
		if (page > 1)
			html += ' style="display: none;"';
		html += '>';
		if (key == "error") {
				html += '<td colspan="'+headings.length+'">' + val + '</td>';
		} else {
			$.each(val, function(key2, val2) {
				if (headings.indexOf(key2) > -1)
					html += '<td>' + val2 + '</td>';
			});
		}
		html += '</tr>';
		items++;
		if (items == 10) { // Maximum 10 items per page
			items = 0;
			page++;
		}
	});
	return html;
}

function get_pages(link) {
	var trs = link.find("tr");
	var pages = 1;
	trs.each(function () {
		var page = parseInt($(this).attr("data-page"));
		if (page > pages)
			pages = page;
	});
	return pages;
}

function advance_page(obj, link, pages, page) {
	link.find("tr").hide();
	link.find("tr[data-page="+page+"]").show();
	obj.find("li").removeClass("active");
	obj.find("li[data-page="+page+"]").addClass("active");
	
	if (page >= pages)
		obj.find(".next").addClass('disabled');
	else
		obj.find(".next").removeClass('disabled');
	
	if (page == 1)
		obj.find(".prev").addClass('disabled');
	else
		obj.find(".prev").removeClass('disabled');
}

function update_pagination(obj) {
	var page = 1;
	var link = $("#" + obj.attr("data-link"));
	if (link) {
		obj.find(".pages").html("");
		var pages = get_pages(link);
		for (var i = 1; i <= pages; i++) {
			obj.find(".pages").append('<li data-page="'+i+'"><a href="#">'+i+'</a></li>');
		}
		obj.find(".pages li:first").addClass("active");
		if (pages > 1) {
			obj.find(".next").removeClass('disabled').click(function() {
				if (page < pages) {
					page++;
					advance_page(obj, link, pages, page);
				}
				return false;
			});
			obj.find(".prev").addClass('disabled').click(function() {
				if (page > 1) {
					page--;
					advance_page(obj, link, pages, page);
				}
				return false;
			});
			obj.find(".pages li").click(function() {
				page = $(this).attr("data-page");
				advance_page(obj, link, pages, page);
				return false;
			});
			obj.show();
		} else {
			obj.hide();
		}
	}
}

function update_milestone_feed() {
	if ($("#milestone_feed").length > 0) {
		$.getJSON(tp_home_url + "api/project/" + project_id + "/milestones/", function(data) {
			$("#milestone_feed").html(json_to_table(data, ["name", "progress"])).find("tr td:first-child").each(function() {
				$(this).wrapInner('<a href="#" />').click(function() {
					feed_milestone = $(this).find("a").html();
					$("#milestoneCollapse").collapse('hide');
					update_feeds();
					return false;
				});
			});
			update_pagination($(".pagination[data-link=milestone_feed]"));
		});
	}
}

function update_tasks_feed() {
	if ($("#tasks_feed").length > 0) {
		var url = tp_home_url + "api/project/" + project_id + "/tasks/?own_tasks_only=1";
		if (feed_milestone.length > 0)
			url += '&milestone=' + feed_milestone;
		$.getJSON(url, function(data) {
			$("#tasks_feed").html(json_to_table(data, ["milestone", "name", "type", "priority", "status", "progress"])).find("tr td:nth-child(2)").each(function() {
				$(this).wrapInner('<a href="#" />').click(function() {
					task_view($(this).find("a").html())
					return false;
				});
			});
			if ($("#tasks_feed tr").length == 0) {
				$("#tasks_feed").append('<tr><td colspan="6">No tasks!</td></tr>');
			}
			update_pagination($(".pagination[data-link=tasks_feed]"));
		});
	}
}

function update_all_tasks_feed() {
	if ($("#all_tasks_feed").length > 0) {
		var url = tp_home_url + "api/project/" + project_id + "/tasks/";
		if (feed_milestone.length > 0)
			url += '?milestone=' + feed_milestone;
		$.getJSON(url, function(data) {
			$("#all_tasks_feed").html(json_to_table(data, ["milestone", "name", "type", "priority", "status", "progress", "assignees"])).find("tr td:nth-child(2)").each(function() {
				$(this).wrapInner('<a href="#" />').click(function() {
					task_view($(this).find("a").html())
					return false;
				});
			});
			if ($("#all_tasks_feed tr").length == 0) {
				$("#all_tasks_feed").append('<tr><td colspan="7">No tasks!</td></tr>');
			}
			update_pagination($(".pagination[data-link=all_tasks_feed]"));
		});
	}
}

function update_feeds() {
	update_milestone_feed();
	update_tasks_feed();
	update_all_tasks_feed();
}

$(function () {
	update_feeds();
});
