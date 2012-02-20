function update_activity_feed() {
	$.ajax({
	  url: tp_home_url + "api/activity_feed/",
	  success: function(data) {
		 $("#activity_feed").html(data);
	  }
	});
}

$(function () {
	update_activity_feed();
});
