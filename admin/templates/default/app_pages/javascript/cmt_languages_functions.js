/**
 * cmt_languages_functions.js
 * Javascript functions to edit and view the language versions of a website
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2013-06-10
 *
 */

cmtPages = {

		
	/**
	 * function initialize()
	 * 
	 * @param void
	 * @return void
	 */
	initialize: function() {
		jQuery(document).ready(this.init.bind(this));
	},
	
	init: function() {
		
		jQuery('#cmtEditLanguageForm input[name=cmtCopyMode]').on('change', function(ev) {
			var radio = ev.currentTarget

			if (radio.value == 'copyNone') {
				jQuery('#copyFromLanguageContainer').hide({
					effect: 'blind',
					duration: 400,
					fade: true
				})
			} else {
				jQuery('#copyFromLanguageContainer').show({
					effect: 'blind',
					duration: 800,
					fade: true
				})
			}
		})

		if (jQuery('#cmtEditLanguageForm input[name=cmtCopyMode]:checked').val() == 'copyNone') {
			jQuery('#copyFromLanguageContainer').hide()
		}
	}	
}

cmtPages.initialize();