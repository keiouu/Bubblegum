/**
 * Graphing Library for Tikapot
 */

function draw_pie_chart(canvas, chartData) {
    var context = canvas.getContext('2d');
    var chartSizePercent = 75;
    var canvasWidth = canvas.width;
    var canvasHeight = canvas.height;
    var centreX = canvasWidth / 2;
    var centreY = canvasHeight / 2;
    var chartRadius = Math.min(canvasWidth, canvasHeight) / 2 * (chartSizePercent / 100);
    var chartStartAngle = -.5 * Math.PI;
    
    // Clear the canvas
    context.clearRect(0, 0, canvasWidth, canvasHeight);
    
    var currentPos = 0;
    for (var slice in chartData) {
        // Setup the slice
        chartData[slice]['startAngle'] = 2 * Math.PI * currentPos;
        chartData[slice]['endAngle'] = 2 * Math.PI * (currentPos + ( chartData[slice]['value'] / 100));
        currentPos += chartData[slice]['value'] / 100;
        
        var startAngle = chartData[slice]['startAngle']  + chartStartAngle;
        var endAngle = chartData[slice]['endAngle']  + chartStartAngle;
        var startX = centreX;
        var startY = centreY;

        // Setup colour gradient
        var sliceGradient = context.createLinearGradient( 0, 0, canvasWidth*.75, canvasHeight*.75 );
        sliceGradient.addColorStop( 0, "rgb(" + chartData[slice]["start_colour"] + ")" );
        sliceGradient.addColorStop( 1, "rgb(" + chartData[slice]["end_colour"] + ")" );
    
        // Draw slice
        context.beginPath();
        context.moveTo( startX, startY );
        context.arc( startX, startY, chartRadius, startAngle, endAngle, false );
        context.lineTo( startX, startY );
        context.closePath();
        context.fillStyle = sliceGradient;
        context.fill();
        
        // Border
        context.lineWidth = 1;
        context.strokeStyle = "#444";
        context.stroke();
    }
    
    // Hover handling
    $(canvas).mousemove(function(mouse_event) {
        $(".graph_tooltip").remove();
        var mouseX = mouse_event.pageX - this.offsetLeft;
        var mouseY = mouse_event.pageY - this.offsetTop;
        var xFromCentre = mouseX - centreX;
        var yFromCentre = mouseY - centreY;
        var distanceFromCentre = Math.sqrt(Math.pow(Math.abs(xFromCentre), 2) + Math.pow(Math.abs(yFromCentre), 2));
        
        if (distanceFromCentre <= chartRadius) {        
            var angle = Math.atan2(yFromCentre, xFromCentre) - chartStartAngle;
            if (angle < 0) {
                angle = 2 * Math.PI + angle;
            }
                          
            for (var slice in chartData) {
                if (angle >= chartData[slice]['startAngle'] && angle <= chartData[slice]['endAngle']) {
                    $(this).css("cursor", "pointer");
                    $("body").append('<div class="graph_tooltip" style="top: '+(mouse_event.pageY - 27)+'px; left: '+(mouse_event.pageX + 12)+'px;">'+chartData[slice]['label']+'</div>')
                    return;
                }
            }
        }
        $(this).css("cursor", "default");
    });
}