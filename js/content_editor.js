// JavaScript Document

/**
 * Functions used by content editor plugin
**/
var PageContent = {
	AddEditHandlers: function() {
		// add form validation on submit
		$('page-form').observe("submit", function(event){
			Event.stop(event);
			new Biscuit.Ajax.FormValidator('page-form');
		});
	},
	RestripePageList: function(list_id) {
		var curr_num = 1;
		jQuery('#'+list_id+' dd').each(function() {
			jQuery(this).removeClass('stripe-even');
			jQuery(this).removeClass('stripe-odd');
			if (curr_num%2 == 0) {
				jQuery(this).addClass('stripe-even');
			} else {
				jQuery(this).addClass('stripe-odd');
			}
			curr_num++;
		});
	},
	HighlightPageList: function(list_id) {
		jQuery('#'+list_id+' > dd').each(function() {
			new Effect.Highlight(this.id,{startcolor: '#93f586', afterFinish: function(obj) {
				$(obj.element).setStyle({backgroundColor: ''});
			}});
		});
	}
};
