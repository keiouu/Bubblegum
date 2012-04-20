$(function () {
	// Track line numbers properly for GIT
	$(".git-commit-file-changes pre .list p").each(function() {
		var elem = $(this);
		elem.find("code").addClass("prettyprint");
		elem.prepend('<span class="line-numbers"><span class="left-line-number"> </span> <span class="right-line-number"> </span></span>');
		var classList = $(this).attr('class').split(/\s+/);
		$.each(classList, function(index, item){
			item = $.trim(item);
			if (item.indexOf('L') != 0) {
				elem.find('.left-line-number').html(item.slice(1));
			}
			
			if (item.indexOf('R') != 0) {
				elem.find('.right-line-number').html(item.slice(1));
			}
		});
	});
	prettyPrint();
});
