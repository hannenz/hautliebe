/**
 * cmt_pages_functions.js
 * Javascript-Datei für die Seitenansicht ('app_pages.inc')
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2014-04-10
 *
 */

cmtPages = {

	options: {
		pageTableID: '#cmtPagesTable',
		nodeID: 'root'
	},
		
	/**
	 * function initialize()
	 * 
	 * @param void
	 * @return void
	 */
	initialize: function() {
		jQuery(document).ready(this.initPages.bind(this));
	},
	
	initPages: function() {
		
//		var ajaxUrl = document.URL.replace(/action=.*&/, '') + '&cmtAction=loadNode&cmtNodeID={nodeID}&cmtLanguage=' + this.options.pagesLanguage;

		// remove anchors from URL
		var ajaxUrl = document.URL.replace(/#.*/, '') + '&cmtAction=loadNode&cmtNodeID={nodeID}&cmtLanguage=' + this.options.pagesLanguage;

		// Baumstruktur erstellen
		jQuery(this.options.pageTableID).treeTable({
			indent: 32,
			rootID: this.options.nodeID,
			onStartDrag: function(event, ui) {
				var node = jQuery(event.target).parents('tr')[0];
				jQuery(node).trigger('mouseout');
				
				jQuery('#cmtButtonSavePagesOrder').button('enable')
			},
			onDropOver: function(event, ui) {

			},
			afterLoadNodes: function(nodes, parentNodeID) {
				cmtPage.initAjaxGUIElements(jQuery(parentNodeID).closest('tbody'));
			},
			ajaxURL: ajaxUrl
			
		});
		
		// Button zur Speicherung der Struktur initialisieren
		jQuery('#cmtButtonSavePagesOrder').click(this.savePagesOrder.bind(this));
		jQuery('#cmtButtonSavePagesOrder').button('disable')
		
		// Meldungen initialisieren
		this.initMessages();
		
	},
	
	initMessages: function() {
		
		jQuery('.cmtMessage').click( function(ev) {
			jQuery(this).slideUp(400);
		})
	},
	
	savePagesOrder: function(event) {
		
		var button = event.currentTarget;
		var nodes = jQuery(this.options.pageTableID).treeTable('serializeNodes', this.options.nodeID);

		var serializedNodes = this.serializeForQuerystring(nodes, 'cmtPages');

		jQuery.ajax(button.href, {
			data: serializedNodes + '&cmtNodeID=' + this.options.nodeID,
			dataType: 'json',
			type:'POST',
			complete: this.savePagesOrderComplete.bindScope(this)
		})
		return false;
	},
	
	savePagesOrderComplete: function (jqXHR, status) {
		var data = jQuery.parseJSON(jqXHR.responseText);
		
		if (data.cmtStatus) {
			jQuery('#cmtMessageSaveOrderSuccess').slideDown(400);
			jQuery('#cmtButtonSavePagesOrder').button('disable')
		} else {
			jQuery('#cmtMessageSaveOrderError').slideDown(400);
		}
		
		// Knopf: Hoverstatus entfernen
		jQuery('#cmtButtonSavePagesOrder').removeClass('ui-state-hover');
	},
	
	serializeForQuerystring: function(obj, prefix) {
	    var str = [];
	    
	    for(var p in obj) {

	    	if (prefix) {
	    		var k = prefix + "[" + p + "]";
	    	} else {
	    		var k = p;
	    	}
	    	
	        var v = obj[p];
	        
	        if (typeof v == 'object') {
	        	str.push(this.serializeForQuerystring(v, k))
	        } else {
	        	//str.push(encodeURIComponent(k) + "=" + encodeURIComponent(v));
	        	str.push(k + "=" + v);
	        	
	        }
	    }
	    //console.info(str)
	    return str.join("&");
	}
	
}

cmtPages.initialize();