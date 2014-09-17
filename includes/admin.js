$(function(){
//	jQuery.timeago.settings.strings.seconds = "moments";
	$('span#last-updated').timeago();
	$(".item-list").disableSelection();
	$(".item-list").sortable({
		distance: 10,
		placeholder: 'item-placeholder'
	});
	$(".item-list li").each(function(){
		$li = $(this);
		$li.find("li.delete a").click(function(){
			return false;
		});
	});
});