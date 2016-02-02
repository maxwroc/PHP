// Menu scroller
$(document).ready(function(){
	$(window).scroll(function () {
		var offset = $(document).scrollTop()+"px";
		$("#menu").parent().animate({top:offset},{duration:1000,queue:false});
	});
});