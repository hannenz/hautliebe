/**
 * cmt_fieldbrowser_functions.js
 * Javascript-Funktionen für die Tabellenbrowseranwendung
 * 
 * @package app_tablebrowser
 * @author J.Hahn <info@contentomat.de>
 * @version 2012-04-22
 * 
 */

	cmtFieldbrowser = {
	
		initialize: function() {
			jQuery(document).ready(this.initNavigation.bind(this));
		},
		
		initFieldbrowser: function() {
			
			// Detailansicht
			jQuery('.cmtContentArea').hide();
			
			var selectContentArea = jQuery('#cmtSelectFieldtype')
		}
	}
	
	cmtFieldbrowser.initialize();


/*
 var cmtFieldhandler = Class.create();
 
 cmtFieldhandler.prototype = {
 
 	initialize: function(){
 		this.relationNr = Cmt.vars.relationNr;

 	},
 	
 	addRelation: function(url){

		var newRelationNr = ++Cmt.vars.relationNr;

		// 1. neuen Außen-Container erzeugen
		var newRelationContainer = new Element('div', {
			id: 'relationOuterContainer_' + newRelationNr,
			className: 'fieldTypeRelationOuterContainer'
		});
		
		$(Cmt.vars.relationContainerID).insert(newRelationContainer, { position: 'bottom' })

		Element.ajaxUpdate( newRelationContainer, url, {
			options: {
				method: 'post'
			},
			parameters: {
				action: 'ajaxAddRelation',
				ajaxNewRelationNr: Cmt.vars.relationNr
			},
			//insertion: 'bottom',
			showMessage: Cmt.vars.messages.createRelation
		});	
 	},
	
	removeRelation: function(relationNr) {
		var relationContainerID = Cmt.vars.relationOuterContainerIDPrefix + relationNr.toString();
		if ($(relationContainerID)) {
			$(relationContainerID).remove();
		
			Cmt.vars.relationNr -= 1;	
		} else {
			return false;
		}
		this.updateRelationContainers();
	},
	
	updateRelationContainers: function() {
		//var relationContainers = $(Cmt.vars.relationContainerID).select('div#:contains('+Cmt.vars.relationOuterContainerIDPrefix+')');
		var relationContainers = $(Cmt.vars.relationContainerID).select('div[id^='+Cmt.vars.relationOuterContainerIDPrefix+']');
		var newIDCounter = 1;

		relationContainers.each(
			function(el, index) {
				
				var newID = $(el).id.toString().replace(/_[0-9]+/, '_'+this.newIDCounter);
				$(el).writeAttribute('id', newID);
				
				var elElements = $(el).select('*[id^='+Cmt.vars.relationIDSearchPrefix+']');
				var elContents = $(el).select(Cmt.vars.relationNrSelector);
				var elRows = $(el).select('*[class^='+Cmt.vars.relationFormatSelectorPrefix+']');
				var elFormFields = $(el).select('*[name^='+Cmt.vars.relationFormFieldNamePrefix+']');

				// 1. Alle IDs innerhalb der Relationscontainer aktualisieren
				elElements.each(
					function(el, index) {
						var newID = $(el).id.toString().replace(/_[0-9]+/, '_'+this.newIDCounter);
						$(el).writeAttribute('id', newID);
					}
					, {newIDCounter: this.newIDCounter}
				)

				// 2. Angezeigte Relationsnummern aktualisieren
				elContents.each(
					function(el, index) {
						el.update(this.newIDCounter)
					}
					, {newIDCounter: this.newIDCounter}
				)

				// 3. Formularfeldname aktualisieren
				elFormFields.each(
					function(el, index) {
						el.name = el.name.toString().replace(/[0-9]+/, this.newIDCounter)
					}
					, {newIDCounter: this.newIDCounter}	
				)
				
				// 4. Containerformatierungen ändern
				elRows.each(
					function(el, index) {
						el.className = el.className.toString().replace(/[0-9]+/, (this.newIDCounter-1)%2)
					}
					, {newIDCounter: this.newIDCounter}	
				)
				
				// hochzählen
				this.newIDCounter++;

			}, {newIDCounter: newIDCounter}
		);
	}
 }
 
 CmtField = new cmtFieldhandler(); 
*/