/**
 * Converts a <ul> into a file list
 */
(function($){
    $.gitlist = $.gitlist || { };

    $.gitlist.widget = {
        options: {
        }
    };

    $.fn.gitlist = function(options) {
        var options = $.extend({}, $.gitlist.widget.options, options);
        this.each(function(){ $(this).data('gitlist', new GitListWidget($(this), options)); });
        return this;
    };
    
    function GitListWidget(root, options) {
        var _self = this;
        var _elem = root;
        var _data = {};

        function initialize() {
            // Clone the original
            _data = $(_elem).clone();
            
            // Print a pretty table
            var tbody = $(_elem).html('<table class="table table-striped table-bordered table-condensed"><thead><tr><th>Filename</th></tr></thead><tbody></tbody></table>').find("tbody");
            console.log(_data);
            $(_data).children("ul").children("li").each(function() {
                var elem = $(this).clone();
                var isFolder = elem.find("ul").length > 0;
                elem.find("ul").remove();
                elem.data("isFolder", isFolder);
                
                if (isFolder) {
                    tbody.prepend('<tr><td><i class="icon-folder-close"></i> <a href="">'+elem.html()+"</a></td></tr>");
                } else {
                    tbody.append('<tr><td><i class="icon-file"></i> <a href="">'+elem.html()+"</a></td></tr>");
                }
            });
        }
        
        return initialize();
    };    
})(jQuery);

$(function() {
    $(".git-file-list").gitlist();
});