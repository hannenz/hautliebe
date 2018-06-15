/**
 * Funktionsbibliothek / Klasse cmtGallery
 * Stellt Methoden für die Darstellung der Bildergalerie-Applikation dar.
 * 
 * @author J.Hahn <info@buero-hahn.de>
 * @version 2012-12-14
 */

	var cmtGallery = {
		
		// eigene Klassenvariablen
		currentDialog: null,
		galleryContentID: null,
		currentCategoryID: 0,
		
		/**
		 * Konstruktor
		 * 
		 * @param selector
		 * @param options
		 */
		initialize: function(selector, options) {
			
			// Standardzeugs
//			this.setOptions(options);
//		    this.jqueryObject = jQuery(selector);
		    
		    this.galleryContentID = '#galleryCategoryContent';
		    //this.messageContainerID = '#galleryCategoryContent';
		    
			jQuery(document).ready(this.initGallery.bind(this));
		},
		
		/**
		 * function initGallery()
		 * Führt alle Initalisiuerungen für die Galerie durch
		 * 
		 *  @param void
		 *  @return void
		 */
		initGallery: function() {

			// Funktionslinks initialisieren
			//this.initLinks();
			this.initPageLinks();
			//this.initEditItemToogle();
			this.initShowImageLinks();
			this.initSortableItems();
			this.initDroppableItems();

		},
		
		/**
		 * function initLinks()
		 * Initialisiert die Links auf der Seite, die eine Aktion mit einem Modalfenster ausf�hren
		 * 
		 *  @param void
		 *  @return void
		 */
		initLinks: function() {
			
			jQuery(document).find('.galleryButton').each(function(index, element) {

				jQuery(element).click(function(event) {
					var cmtDialog = jQuery('#cmtModalWin').dialog({
						modal: true,
						title: element.title,
						autoOpen: false,
						width: 600,
						position: ['center', 24],
						close: function() {
							jQuery(this).empty();
						}
					});
			
					// Inhalte laden
					cmtDialog.load (
						element.href, 
						{},
						function (responseText, textStatus, XMLHttpRequest) {
							//jQuery('cmtModalWin').dialog();
						}
		            );
					
					cmtDialog.dialog('open');
					
					this.currentDialog = cmtDialog;
					
					element.blur();

					return false;
				}.bind(this)); // <= Wichtig: nie die Bindung verlieren!
			}.bind(this)); // <= Wichtig: nie die Bindung verlieren!
		},
		
		/**
		 * function initPageLinks()
		 * Initialisiert verschiedene
		 */
		initPageLinks: function() {
			
			// "Änderungen speichern"
			if (jQuery('.gallerySaveCategory')) {
				jQuery('.gallerySaveCategory').click(this.saveCategory.bind(this));
				jQuery('.gallerySaveCategory').attr('href', 'Javascript:void(0);')
			}
		},
		
		/**
		 * function initEditItemToggle()
		 * Initalisiert den Mouseover/ -out-Effekt, welcher die Bearbeitungsoptionen eines Bildes oder einer Galerie anzeigt
		 * 
		 * @param void
		 * @return void
		 */
		initEditItemToogle: function() {
			
			jQuery('.galleryItemContainer').mouseenter(function(event) {
				
				var el = jQuery(event.currentTarget).find('.galleryEditItemContainer');
				el.fadeTo(200, 1)
				
			});
			
			jQuery('.galleryItemContainer').mouseleave(function(event) {

				var el = jQuery(event.currentTarget).find('.galleryEditItemContainer')
				el.fadeTo(50, 0, function(el) {
					jQuery(this).clearQueue();
				})
			})
		},
		
		/**
		 * function initShowImageLinks()
		 * Initalisiert die Links, die die Gro�ansicht eines Bildes in einem Modalfenster aufrufen.
		 * 
		 *  @param void
		 *  @return void
		 */
		initShowImageLinks: function() {
			
			jQuery('.galleryImageLink').click(function(event) {

				// Wenn ein Event an das Objekt gebunden ist, dann referenziert "this" auf das Objekt!
				// Die Event-Daten werden als erstes Argument der Methode �bergeben.
				
				var link = event.currentTarget;
				
				var url = link.href;
				var rel = link.rel;
				var sizeParts = rel.match(/([0-9]*)\s?x\s?([0-9]*)/);
				
				var xOffset = 24;
				var yOffset = 24;
				
				var viewPortHeight = jQuery(window).height();
				var viewPortWidth = jQuery(window).width();
				
				// Querformat
				if (sizeParts[1] >= viewPortWidth) {
					var modalWidth = viewPortWidth  - xOffset * 2
				} else {
					var modalWidth = sizeParts[1]
				}

				if (sizeParts[2] >= viewPortHeight) {
					var modalHeight = viewPortHeight  - yOffset * 2
				} else {
					var modalHeight = sizeParts[2]
				}
				
				var cmtDialog = jQuery('#cmtModalWin').dialog({
					modal: true,
					title: link.title,
					width: modalWidth,
					height: modalHeight,
					autoOpen: false,
					close: function(event, ui) {
						jQuery(this).empty();
						jQuery(this).dialog('destroy');
					}
				});
		
				cmtDialog.load (
					url, 
					{},
					function (responseText, textStatus, XMLHttpRequest) {
						
					}
	            )

				cmtDialog.dialog('open');
				link.blur();
				return false;
			}.bind(this));	// <= hier die Bindung!		
		},
		
		/**
		 * function initForm()
		 * initialisiert ein Formular, so dass es im gerade geöffneten Modalfenster angezeigt wird.
		 * 
		 * @param formID ID des Formulars
		 * @return boolean Immer false, damit das Formular nicht abschickt und so die ganze Seite neu geladen wird.
		 * 
		 */
		initForm: function(formID) {
			
			var form = jQuery(formID);
			//var cmtDialog = this.currentDialog;
			
			// Wenn Formular abgeschickt wird
			form.submit(function(ev) {

				var form = jQuery(ev.currentTarget);

				this.currentDialog.load (
					form.attr('action'), 
					{
						type: form.attr('method'),
						data: form.serialize()
					},
					function (responseText, textStatus, XMLHttpRequest) {
						
					}
				)
				
				// damit das Formular nicht abgeschickt wird, wenn der Knopf gedr�ckt wird
				return false;
				
			}.bind(this));
			
			// Wenn Dialog abgebrochen wird
			form.find('.cmtButtonCancel').click(function(event) {
				this.currentDialog.dialog('close');
			}.bind(this));
		},
		
		/**
		 * function initSortableItems()
		 * Initialisert die Elemente/ Bilder, die sortiertbar sein sollen. Derzeit nur Bilde rin Kategorieansicht.
		 * 
		 * @param void
		 * @return void
		 */
		initSortableItems: function() {
			jQuery(this.galleryContentID).sortable({
				items: '.gallerySortableItem',
				placeholder: "gallerySortablePlaceHolder",
				revert: 300,
				scroll: true,
				opacity: 0.5
			});
			
			jQuery(this.galleryContentID).disableSelection();
		},

		initDroppableItems: function() {
			jQuery(this.galleryContentID + ' .galleryDroppableItem').droppable({
				
				drop: function(ev,ui) {
			
					var imageID = cmtGallery.getNumberFromID(ui.draggable.attr('id'));
					var categoryID = cmtGallery.getNumberFromID(jQuery(this).attr('id'));

					// Normalen Platzhaler durch "In Arbeit"-Platzhalter ersetzen
					var placeHolder = jQuery(cmtGallery.galleryContentID).find('.gallerySortablePlaceHolder');
					var placeHolderClone = placeHolder.clone().removeClass('gallerySortablePlaceHolder').addClass('galleryActivePlaceHolder');
					
					jQuery(placeHolder).after(placeHolderClone);
					jQuery(cmtGallery.galleryContentID).sortable('option', 'revert', false);

					// Original-Thumbnail verstecken
					jQuery(ui.draggable).hide();
					
					jQuery.ajax({
						url: document.URL+'&galleryAction=moveImage',
						data: jQuery.param({'galleryImageID': imageID, 'galleryCategoryID': categoryID}),
						type: 'POST',
						dataType: 'json',
						
						success: function(data, textStatus, jqXHR) {

							if (data.imageMoved) {
								
								// Anzahl der Bilder in Labels der Sidebar aktualisieren
								jQuery('.galleryCategoryContainer').each(function(index, element) {
									
									var elementID = jQuery(element).attr('id');
									var categoryID = cmtGallery.getNumberFromID(elementID);
									
									// Anzahl der Bilder > 0
									if (data.imagesInCategories[categoryID]) {
										jQuery('#galleryCategory_' + categoryID + ' .infoLabel').html(parseInt(data.imagesInCategories[categoryID]));
										jQuery('#galleryCategory_' + categoryID).removeClass('galleryCategoryContainerEmpty');
									} else {
										jQuery('#galleryCategory_' + categoryID + ' .infoLabel').html('0');
										jQuery('#galleryCategory_' + categoryID).addClass('galleryCategoryContainerEmpty');
									}
									
									if (categoryID == cmtGallery.currentCategoryID) {
										jQuery('#galleryImagesInCategory').text(parseInt(data.imagesInCategories[categoryID]));
									}

								});
								
								// Bild aus aktueller Kategorieübersicht löschen
								jQuery(ui.draggable).remove();
								jQuery(cmtGallery.galleryContentID).find('.galleryActivePlaceHolder').fadeOut('fast', function(el) {
									jQuery(this).remove();
								});
								
								cmtPage.showMessage(cmtMessages['success']['imageMoved'], 'success');
								
							} else {
								cmtPage.showMessage(cmtMessages['error']['imageMoved'], 'error');
							}
						}
					});
				},
				
				hoverClass: 'galleryDroppableItemHover',
				activeClass: 'galleryDroppableItemActive'
			});
		},
		
		getNumberFromID: function(id) {
	
			var result = id.split('_');
			return result[1];
		},
		
		saveCategory: function(ev) {
			var imagesOrder = jQuery(this.galleryContentID).sortable('toArray');
			var imagesOrderHelper = {};

			// Bilder-Datenbank-IDs aus Element-IDs extrahieren
			jQuery.each(imagesOrder, function(key, value) {
				idParts = value.split('_');
				imagesOrderHelper['galleryImageIDs['+idParts[1]+']'] = parseInt(key) + 1;
				//imagesOrder[key] = idParts[1];
			});
			
			jQuery.ajax({
				url: document.URL+'&galleryAction=saveImagesOrder',
				data: jQuery.param(imagesOrderHelper),
				type: 'POST',
				dataType: 'json',
				success: function(data, textStatus, jqXHR) {
					cmtPage.showMessage(cmtMessages[data.galleryMessageType][data.galleryMessage], data.galleryMessageType);
				}
				
			})
//			console.info(jQuery.param(imagesOrderHelper));
		},
		
		// TODO: Muss noch erweitert werden!
/*
		setDialogType: function(params) {
			
			switch (params.type) {

				case 'confirm':
					// Abbrechen-Knopf
					if (params.buttonCancelLabel) {
						var cancelLabel = params.buttonCancelLabel;
						
						if (params.buttonCancelAction) {
							var cancelAction = params.buttonCancelAction;
						} else {
							var cancelAction = function(ev) {this.currentDialog.dialog('close')}.bind(this);
						}
					}
					
					// OK-Knopf
					if (params.buttonConfirmLabel) {
						var confirmLabel = params.buttonConfirmLabel;
						
						if (params.buttonConfirmAction) {
							var confirmAction = params.buttonConfirmAction;
						} else {
							var confirmAction = function(ev) {this.currentDialog.dialog('close')}.bind(this);
						}
					}
					
					this.currentDialog.dialog({
						buttons: [{
							text: cancelLabel,
							click: cancelAction
						},{
							text: confirmLabel,
							click: confirmAction
						}]
					})
					
					break;
					
				default:
				case 'dialog':
					
					// OK-Knopf
					if (params.buttonConfirmLabel) {
						var confirmLabel = params.buttonConfirmLabel;
						
						if (params.buttonConfirmAction) {
							var confirmAction = params.buttonConfirmAction;
						} else {
							var confirmAction = function(ev) {this.currentDialog.dialog('close')}.bind(this);
						}
					}
					
					this.currentDialog.dialog({
						buttons: [{
							text: confirmLabel,
							click: confirmAction
						}]
					})					
					break;
					
			}
		},
*/		
		updateContent: function(url) {

			jQuery(this.galleryContentID).load (
				url, 
				{},
				function (responseText, textStatus, XMLHttpRequest) {}
			);
		},
/*		
		closeDialog: function() {
			this.currentDialog.dialog('close');
		},
		
		submitFormInDialog: function(formID) {
			document.getElementById(formID).submit();
		},
*/		
		setCurrentCategoryID: function(categoryID) {
			var cid = parseInt(categoryID);
			this.currentCategoryID = cid;
		} 
		
	}
	
	// Galerie starten
	cmtGallery.initialize();
	
