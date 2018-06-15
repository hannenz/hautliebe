/**
 * cmt_rightsmanager_functions.js
 * Javascript-Datei für die Rechteverwaltung ('app_rightsmanager.inc')
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2012-06-12
 *
 */

cmtRightsmanager = {
		
	/**
	 * function initialize()
	 * 
	 * @param void
	 * @return void
	 */
	initialize: function() {
		jQuery(document).ready(this.initRightsmanager.bind(this));
	},
	
	/**
	 * function initRightsmanager()
	 * Hier werden alle Methoden aufgerufen, die nach dem Laden der Seite ausgeführt werden müssen.
	 * 
	 * @param void
	 * @return void
	 */
	 initRightsmanager: function() {
		 this.initCheckboxes();
	 },
	 
	 initCheckboxes: function() {
	 
		 jQuery('.cmtCheckboxAccess').each(function(index, el) {
			
			jQuery(el).on('click.cmtrightsmanager', this._checkAllRights.bindScope(this));

		 }.bindScope(this))
	 },
	 
	 _checkAllRights: function(ev) {
		 
		 var accessCheckBox = ev.currentTarget;
		 var accessCheckBoxStatus = jQuery(accessCheckBox).prop('checked');
		 
		 if (typeof accessCheckBoxStatus == 'undefined' ) {
			 accessCheckBoxStatus = false;
		 }
		 var tr = jQuery(accessCheckBox).closest('tr');
		 
		 var checkBoxes = jQuery(tr).find('input[type=checkbox]:not(.cmtCheckboxAccess)');

		 jQuery(checkBoxes).each(function (index, element) {
			 
			 if (!accessCheckBoxStatus) {
				 jQuery(element).prop('checked', false)
				 jQuery(element).button('disable');
			 } else {
				 jQuery(element).prop('checked', true)
				 jQuery(element).button('enable');
			 }
			 
			 //jQuery(element).prop('checked', accessCheckBoxStatus)
			 jQuery(element).button('refresh');
		 })
	 }
}

cmtRightsmanager.initialize();