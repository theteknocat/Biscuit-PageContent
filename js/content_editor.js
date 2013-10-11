// JavaScript Document

/**
 * Functions used by content editor plugin
**/
var PageContent = {
	RestripePageList: function(list_id) {
		var curr_num = 1;
		$('#'+list_id+' dd').each(function() {
			$(this).removeClass('stripe-even');
			$(this).removeClass('stripe-odd');
			if (curr_num%2 == 0) {
				$(this).addClass('stripe-even');
			} else {
				$(this).addClass('stripe-odd');
			}
			curr_num++;
		});
	},
	HighlightPageList: function(list_id) {
		$('#'+list_id+' > dd').each(function() {
			$(this).effect('highlight',{color: '#93f586'},1000,function() {
				$(this).css({'background': ''});
			});
		});
	}
};
