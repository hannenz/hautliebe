/**
 * cmt_showtable_functions.js
 * Javascript-Datei für den Datenbanktabellenbrowser ('app_showtable.inc')
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2015-06-16
 *
 */

cmtShowtable = {
		
		/**
		 * function initialize()
		 * 
		 * @param void
		 * @return void
		 */
		initialize: function() {
			jQuery(document).ready(this.initShowtable.bind(this));
		},
		
		/**
		 * function initShowtable()
		 * Hier werden alle Methoden aufgerufen, die nach dem Laden der Seite ausgeführt werden müssen.
		 * 
		 * @param void
		 * @return void
		 */
		 initShowtable: function() {
			 
			 this.initPaging();
			 this.initSearchFormButtons();
		 },
		 
		 /**
		  * function initPaging()
		  * Initialisert Dropdownlisten die mit dem Selektor ".cmtSelectPageList" gekennzeichnet sind. Bei Auswahl 
		  * aus einer solchen Liste, wird die ausgewählte Seite neu geladen.
		  * 
		  * @param void
		  * @return void
		  */
		 initPaging: function() {
			 
			 jQuery('.cmtSelectPageList').change(function(ev) {
				
				 document.location.href = jQuery(this).attr('data-url') + '&' +jQuery(this).attr('name') + '=' + this.value
			 })
		 },
		 
		 initSearchFormButtons: function() {
			
			 jQuery('#cmtSearchForm .cmtButtonReset').on('click', function(ev) {
				
				var form = jQuery(ev.currentTarget).closest('#cmtSearchForm');
				
				// searchfields
				jQuery('.cmtSelectSearchField', form).prop('selectedIndex', '0').trigger('change');
				
				// search criteria selects
				jQuery('.cmtSelectSearchCriteria', form).prop('selectedIndex', '0').trigger('change');

				// input fields
				jQuery('.cmtSearchValue', form).val('');

				ev.stopPropagation();
				ev.preventDefault();
				
				jQuery(form).trigger('submit')
			});
		 }
	}
	
	cmtShowtable.initialize();