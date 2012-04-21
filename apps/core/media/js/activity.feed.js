function update_activity_feed() {
	$.ajax({
	  url: tp_home_url + "api/activity_feed/",
	  success: function(data) {
		 $("#activity_feed").html('<li class="nav-header"><i class="icon-fire"></i> Activity Feed</li>' + data);
	  }
	});
}

$(function () {
	setTimeout("update_activity_feed()", 500);
	setInterval("update_activity_feed()", 10000);
});
