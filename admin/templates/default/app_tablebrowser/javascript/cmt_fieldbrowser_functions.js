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
	
		currentContentAreaID: '',
		contentAreasContainerID: '#cmtContentAreasContainer',
		
		
		initialize: function() {
			jQuery(document).ready(this.initFieldbrowser.bind(this));
		},
		
		initFieldbrowser: function() {
			
			/*
			 *  Detailansicht
			 */
			
			// 1. Inhalte verbergen
			jQuery('.cmtContentArea').hide();
			
			// 2. ausgewählte Inhalte anzeigen
			var selectContentArea = jQuery('#cmtSelectFieldtype');
			this.currentContentAreaID = '#' + jQuery(selectContentArea).prop('value') + '_ContentArea';
			
			jQuery(this.currentContentAreaID).show();
			
			// 3. Selectfeld initialisieren
			// Animationseffekt findet in einem Rutsch statt
			selectContentArea.change( function(ev) {
				var selectField = ev.target;
				
				var oldArea = this.currentContentAreaID;
				this.currentContentAreaID = '#' + jQuery(selectContentArea).prop('value') + '_ContentArea';
				
				// dem Außencontainer wieder aktuelle Höhe zuweisen.
				jQuery(this.contentAreasContainerID).height(parseInt(jQuery(oldArea).height()));
				
				// alten Bereich ausblenden
				jQuery(oldArea).hide('fade', 200,  function() {
					
					// Höhe des Inhaltecontainers anpassen
					jQuery(this.contentAreasContainerID).animate({
							height: parseInt(jQuery(this.currentContentAreaID).height())
						},
						300,
						function(ev) {
							// neue Inhalte anzeigen
							jQuery(this.currentContentAreaID).fadeIn(300, function(ev) {
								
								// Höhe des Außencontainers wieder auf "auto", da Tabs unterschiedliche Höhen haben können.
								jQuery(this.contentAreasContainerID).css({height: 'auto'});
							}.bind(this))
						}.bind(this)
					);
				}.bind(this))
			}.bind(this));
			
			//jQuery(this.contentAreasContainerID).height(parseInt(jQuery(this.currentContentAreaID).height()));
			
			// Ajax-Selectfelder initialisieren
			this.initAjaxSelectField();
			
			// Relations initialisieren
			this.initRelations();
		},
		
		/**
		 * function initRelation()
		 * Initialisiert die Relationfeld-Formularelemente
		 * 
		 * @param object ev jQuery Event-Objekt
		 */
		initRelations: function(parentElement) {

			if (typeof parentElement == 'undefined') {
				parentElement = document
			}
			
			var relationsNr = jQuery('.cmtFieldTypeRelation').length;
			jQuery('.cmtAddRelationButton').prop('data-relations-nr', relationsNr);
			
			jQuery('.cmtAddRelationButton', parentElement).click(this.addRelation.bind(this));
			jQuery('.cmtRemoveRelationButton', parentElement).click(this.removeRelation.bind(this));
		},

		/**
		 * function addRelation()
		 * Fügt ein Relationfeld-Formularelement hinzu
		 * 
		 * @param object ev jQuery Event-Objekt
		 */
		addRelation: function(ev) {
			
			var self = ev.currentTarget;
			var relationsNr = jQuery(self).prop('data-relations-nr');
			var newRelationNr = relationsNr +1;

			self.blur();
			
			// Inhalte per Ajax-request updaten
			jQuery.ajax({
				url: '',
				data: {
					action: 'ajaxAddRelation',
					ajaxNewRelationNr: newRelationNr
				},
				dataType: 'html',
				type: 'POST',
				success: function(data) {
					
					jQuery('#relationContainer').append(data);
					var newContainer = jQuery('#relationContainer').children().last();
					
					// Select-Felder tauschen
					cmtPage.initFormSelect(newContainer);

					// Ajax-Selectfelder initialisieren
					this.initAjaxSelectField(newContainer);
					
					// Buttons tauschen
					cmtPage.initButtons(newContainer);
					
					// "Relation hinzufügen"-Button aktualisieren
					this.initRelations(jQuery(newContainer).children().last());

					// "Relation hinzufügen"-Button aktualisieren
//					var relationNr = jQuery('#relation_ContentArea .cmtAddRelationButton').prop('data-relations-nr');
//					jQuery('#relation_ContentArea .cmtAddRelationButton').prop('data-relations-nr', ++relationNr);
					
				}.bind(this)
			})
			
			
		},
		
		/**
		 * function removeRelation()
		 * Entfernt ein relationfeld-Formularelement
		 * 
		 * @param object ev jQuery Event-Objekt
		 */
		removeRelation: function(ev) {

			var self = ev.currentTarget;
			var relationNr = jQuery(self).attr('data-relation-nr');
			var newRelationNr = relationNr -1

			var followingSiblings = jQuery('#cmtRelationContainer_' + relationNr).nextAll();
			
			// Nummer der Relation in der Überschrift aktualisieren
			jQuery(followingSiblings)
				.find('.cmtContentRelationNr')
				.each( function(index, element) {
					var currentRelationNr = jQuery(element).html();
					jQuery(element).html(currentRelationNr - 1)
				});

			// Selektor der folgenden Reihen aktualisiern
			jQuery(followingSiblings)
				.find('.cmtEditEntryRow')
				.each( function(index, element) {
					
					if (jQuery(element).hasClass('cmtEditEntryRow0')) {
						jQuery(element).removeClass('cmtEditEntryRow0');
						jQuery(element).addClass('cmtEditEntryRow1');
					} else {
						jQuery(element).removeClass('cmtEditEntryRow1');
						jQuery(element).addClass('cmtEditEntryRow0');
					}
				});

				
			
			// Relation-Formular entfernen
			jQuery('#cmtRelationContainer_' + relationNr).remove();
			
			// "Relation hinzufügen"-Button aktualisieren
			var relationsNr = jQuery('.cmtFieldTypeRelation').length;
			jQuery('#relation_ContentArea .cmtAddRelationButton').prop('data-relations-nr', relationsNr);
		},
		
		/**
		 * function initAjaxSelectField()
		 * Initialisiert ein Select-feld, welches per Ajax weitere Select-Felder aktualisiert.
		 * 
		 * @param mixed parentElement Referenz oder ID oder Selektor des Startelements ab welchem gesucht werden soll.
		 */
		initAjaxSelectField: function(parentElement) {
			
			if (typeof parentElement == 'undefined') {
				parentElement = document
			}
			
			jQuery('.cmtFieldHandlerUpdateSelect', parentElement).change(this.updateAjaxSelectFieldTarget.bind(this));
		},
		
		/**
		 * function updateAjaxSelectFieldTarget()
		 * Initialisiert ein Formularfeld, das per Ajax mit Inhalten befüllt wurde
		 * 
		 * @param object ev JQuery Event-Objekt.
		 */
		updateAjaxSelectFieldTarget: function(ev) {
			
			var self = ev.currentTarget;
			var updateTargetIDs = jQuery(self).attr('data-update-target-id').split(',');
			var fieldName = jQuery(self).attr('data-update-field-name').split(',');
			
			for (i=0; i < updateTargetIDs.length; i++) {


				var targetElement = '#' + updateTargetIDs[i];
				
				jQuery(targetElement).html('');
				jQuery(targetElement).addClass('cmtInProgress');
				
				jQuery(targetElement).load(
					'',
					{
						action: 'ajaxUpdateField',
						ajaxTableName: self.value,
						ajaxFieldName: fieldName[i],
					},
					
					function() {
						
						// Selectfelder austauschen
						cmtPage.initFormSelect(this);
						
						jQuery(this).removeClass('cmtInProgress');
					}
				);
			}
		}
	}
	
	cmtFieldbrowser.initialize();