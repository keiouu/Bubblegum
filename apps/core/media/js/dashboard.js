$(function() {
    $(".draggables .inner .draggable").each(function() {
        var obj = $(this);
        var inAnim = false;
        obj.drag("draginit", function(i, e){
            obj.addClass("in-drag");
        });
        obj.drag(function(i, e){
            obj.offset({left: e.offsetX, top: e.offsetY});
        });
        obj.drag("dragend", function(i, e){
            // Is the mouse in the center of a container?
            obj.hide();
            var pointObj = $.elementFromPoint(e.startX + e.deltaX, e.startY + e.deltaY);
            obj.show();
            
            if ($(pointObj).is(".draggable, .draggable *, .draggables .inner")) {
                obj.removeClass("in-drag");
            
                // Find Container
                var container = $(pointObj);
                if (container.is(".draggable")) {
                    container = container.parent();
                } else if (container.is(".draggable *")) {
                    container = container.parent().parent();
                }
                
                // Clip to container
                obj.appendTo(container);
                obj.css("left", "0");
                obj.css("top", "0");
            } else {
                // Send back to its container if it isnt in one now
                inAnim = true;
                obj.animate({
                    top: "-="+e.deltaY,
                    left: "-="+e.deltaX
                }, e.deltaY+e.deltaX, function () {
                    inAnim = false;
                    obj.removeClass("in-drag");
                });
            }
        });
        obj.mouseup(function() {
            if (!inAnim)
                obj.removeClass("in-drag");
        });
    });
});