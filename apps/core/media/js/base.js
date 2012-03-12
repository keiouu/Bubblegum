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

$(function () {
	init_support();
});
