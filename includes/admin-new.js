$(function(){
	window.$$ = {
		d : $(document),
		w : $(window),
		b : $("body"),
		c : $(".container")
	};

	function doneResizing(){
		$$.b.removeClass("resizing");
	};

	$$.w.bind('resize', function() {
		$$.b.addClass("resizing");

	    if ($$.resizeTimer) clearTimeout($$.resizeTimer);
	    $$.resizeTimer = setTimeout(doneResizing, 100);
	});

	function updateWidth(){
//		.animate({ "width" : Math.floor(($$.w.width()-20) / 100) * 100 },1000);
	}
//	updateWidth();
//	$$.w.resize(updateWidth);
});