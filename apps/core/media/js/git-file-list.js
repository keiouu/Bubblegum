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
            showFolder(_data, 0);
        }
        
        function hideFolder(callback) {
            $(_elem).find("table").animate({ opacity: 0.25 }, 200, callback);
        }
        
        function showFolder(folder, parent) {
            $(folder).data("parent", parent);
            
            // Print a pretty table
            var tbody = $(_elem).html('<table class="table table-striped table-bordered table-condensed"><thead><tr><th>Filename</th></tr></thead><tbody></tbody></table>').find("tbody");
            
            $(folder).children("ul").children("li").each(function() {
                var elem = $(this).clone();
                var isFolder = elem.find("ul").length > 0;
                elem.find("ul").remove();
                if (isFolder) {
                    $('<tr><td><i class="icon-folder-close"></i> <a href="" class="folder">'+elem.html()+"</a></td></tr>").data("sub-set", $(this).clone()).prependTo(tbody);
                } else {
                    tbody.append('<tr><td><i class="icon-file"></i> <a href="" class="file" data-path="'+elem.attr("data-path")+'">'+elem.html()+"</a></td></tr>");
                }
            });
            
            if (parent != 0) {
                $('<tr><td><i class="icon-folder-close"></i> <a href="" class="folder folder-back">..</a></td></tr>').data("sub-set", parent.clone()).prependTo(tbody);
            }
            
            tbody.find("a").click(function(e) {
                e.preventDefault();
                if ($(this).hasClass("folder")) {
                    // Show the folder.
                    var isBack = $(this).hasClass("folder-back");
                    hideFolder(function() {
                        if (isBack)
                            showFolder(parent, $(parent).data("parent"));
                        else
                            showFolder($(this).parent().parent().data("sub-set"), folder);
                    });
                } else {
                    var path = $(this).attr("data-path");
                    // Get the file's code from AJAX
                    $.ajax({
                        url: tp_home_url + "api/project/" + project_id + "/git/show_file/?file=" + path,
                    }).done(function(data) {
                        $(_elem).html('<pre class="prettyprint linenums">'+data+'</pre>');
                        prettyPrint();
                        $(_elem).prepend('<a href="">&laquo; Back</a>').click(function() {
                            showFolder(folder, parent);
                            return false;
                        });
                    });
                }
                
                return false;
            });
        }
        
        return initialize();
    };    
})(jQuery);

$(function() {
    $(".git-file-list").gitlist();
});