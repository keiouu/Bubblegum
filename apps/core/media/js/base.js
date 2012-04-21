function init_support() {
	$(".supportLink").click(function() {
		var url = $(this).attr("href");
		$.ajax({
		  url: url,
		  success: function(data) {
			 var body = $.trim(data);
          $('<div class="modal" id="myModal"><div class="modal-header"><a class="close" data-dismiss="modal">×</a><h3>Support</h3></div><div class="modal-body">'+body+'</div><div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Okay</a></div></div>').modal();
		  }
		});
		return false;
	});
}

function show_messages() {
	$('.messages').show();
	$('.alert').show();
}

function hide_messages() {
	$('.alert').alert('close');
}

$(function () {
	init_support();
	
	// Messages
	if ($('.alert').length > 0)
		show_messages();
	setTimeout("hide_messages();", 2000 * $('.alert').length);
});
