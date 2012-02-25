var number = 150;
var speed = 1;
var jump = 1;
var jump_variant_ceil = 2;

var particles = Array();

jQuery.fn.intersects = function(obj) {
	var mpos = $(this).offset();
	var opos = obj.offset();
	var or = opos.left + obj.outerWidth();
	var ob = opos.top + obj.outerHeight();
	return (mpos.left + 10) >= opos.left && (mpos.left - 10) <= or && (mpos.top + 10) >= opos.top && (mpos.top - 10) <= ob;
}

function Particle(i) {
	this.i = i;
}

Particle.prototype.setup = function() {
	$("#particles").append('<div id="particle_'+this.i+'" class="particle"></div>');
	this.obj = $("#particles #particle_" + this.i);
	this.window_width = $(window).width(); 
	this.window_height = $(window).height();
	this.x = Math.floor(Math.random() * this.window_width);
	this.y = Math.floor(Math.random() * this.window_height);
	this.obj.css("left", this.x + "px");
	this.obj.css("top", this.y + "px");
	this.r = Math.floor(Math.random() * 20);
	this.g = Math.floor(Math.random() * 20);
	this.b = Math.floor(Math.random() * 20);
	this.a = Math.random();
	this.obj.css("background-color", "rgba(" + this.r + "," + this.g + "," + this.b + "," + this.a + ")");
};

Particle.prototype.tick = function() {
	// Reset the window height, in case it changes
	this.window_width = $(window).width(); 
	this.window_height = $(window).height();
	
	var x_definer = "left";
	var y_definer = "top";
	if (Math.random() < 0.1) {
		x_definer = "right";
		y_definer = "bottom";
	}
	var my_jump = jump + (Math.random() * jump_variant_ceil);
	this.x = (this.x + my_jump) % (this.window_width - 15);
	this.y = (this.y + my_jump) % (this.window_height - 15);
	this.obj.css(x_definer, this.x + "px");
	this.obj.css(y_definer, this.y + "px");
	this.obj.show();
	var obj = this.obj;
	$.each($(".pair, #content h1, #content p"), function() {
		if (obj.intersects($(this))) {
			obj.hide();
			return false;
		}
	});
};


function fun_loop() {
	for (i in particles) {
		particles[i].tick();
	}
}

$(function() {
	for (var i = 0; i < number; i++) {
		particles[i] = new Particle(i);
		particles[i].setup();
	}

	t = setInterval('fun_loop()', speed);
});
