/**
 * functions for tablebrowser_overview => edit table
 * 
 * @version 2014-04-17
 * @author J.Hahn <info@contentomat.de>
 */

cmtTableBrowserOverview = {
	
	init: function() {
		jQuery(document).ready(function() {
			this.initCollationSelection();
		}.bind(this))

	},

	initCollationSelection: function() {
		var self = this;
		
		jQuery('#cmt_charset').change(function(ev) {
			
			self.showCollations(jQuery(ev.currentTarget).val())
		})
		
		jQuery('#cmt_charset').trigger('change');
	},
	
	/**
	 * function showCollations()
	 * Disables all non valid colaltions in the collation select form field for a given charset.
	 * 
	 * @param string charset Name of the character set
	 * @return void
	 */
	showCollations: function(charset) {

		jQuery('#cmt_collation option').each(function(index, option) {
			
			if (!option.value.match(charset)) {
				jQuery(option).prop('disabled', 'disabled');
				jQuery(option).prop('selected', false);
				//jQuery(option).hide();
			} else {
				jQuery(option).removeAttr('disabled');
				//jQuery(option).show();
			}
		})
		
		jQuery('#cmt_collation option:not([disabled])').first().attr('selected', true);
		jQuery('#cmt_collation').selectmenu('destroy');
		
		jQuery('#cmt_collation').selectmenu({
			style:'dropdown'
		});
	 }
		
		
}
 
cmtTableBrowserOverview.init();