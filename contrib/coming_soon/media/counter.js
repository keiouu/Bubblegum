var counter_days = 0, counter_hours = 0, counter_minutes = 0, counter_seconds = 0;

function run_counter() {
	counter_seconds--;
	if (counter_seconds <= 0 && counter_minutes > 0) {
		counter_seconds = 60;
		counter_minutes--;
	}
	if (counter_seconds <= 0) {
		counter_seconds = 0;
	}
	if (counter_minutes <= 0 && counter_hours > 0) {
		counter_minutes = 60;
		counter_hours--;
	}
	if (counter_hours <= 0 && counter_days > 0) {
		counter_hours = 24;
		counter_days--;
	}
	if (counter_days <= 0) {
		counter_days = 0;
	}
	
	$("#days").html(counter_days);
	$("#hours").html(counter_hours);
	$("#minutes").html(counter_minutes);
	$("#seconds").html(counter_seconds);
}

$(function() {
	var days = $("#days").html();
	var hours = $("#hours").html();
	var minutes = $("#minutes").html();
	var seconds = $("#seconds").html();
	counter_days = parseInt(days);
	counter_hours = parseInt(hours);
	counter_minutes = parseInt(minutes);
	counter_seconds = parseInt(seconds);
	if (!isNaN(counter_days) && !isNaN(counter_hours) && !isNaN(counter_minutes) && !isNaN(counter_seconds)) {
		t = setInterval('run_counter()', 1000);
	}
});
