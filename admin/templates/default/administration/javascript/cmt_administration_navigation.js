/**
 * cmt_administration_navigation.js
 * Initialisiert das Navigationsmenü nach dem Laden der Seite
 * 
 * @version 2017-04-25
 * @author J.Hahn <info@contentomat.de>
 * 
 */
	cmtNavigation = {
	
		initialize: function() {
			jQuery(document).ready(this.initNavigation.bind(this));
		},
		
		initNavigation: function() {
			//console.info("Start: " + jQuery.cookie('cmtMenuPinned'));
			
			// init navigation menu
			jQuery('#cmtNavigation').extendedAccordion({
				openAllSelector: '.cmt-icon-open-all',
				closeAllSelector: '.cmt-icon-close-all'
			});

			// init main navigation pin status
			if (jQuery.cookie('cmtMenuPinned') == 'true') {
				jQuery('body').addClass('st-menu-pinned');
			}
			
			
			// init main navigation pin toggler
			jQuery('.cmt-icon-pin-menu').on('click.pin-menu', function(ev) {

				jQuery('body').toggleClass('st-menu-pinned');
				if (!jQuery('body').hasClass('st-menu-pinned')) {
					jQuery.cookie('cmtMenuPinned', 'false');
				} else {
					jQuery.cookie('cmtMenuPinned', 'true');
				}
				
				// console.info(jQuery.cookie('cmtMenuPinned'));

			});
		}
	};
	
	cmtNavigation.initialize();