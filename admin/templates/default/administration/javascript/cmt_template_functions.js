/**
 * cmt_template_functions.js
 * Zentrale Javascript-Datei, die Methoden für Darstellungsfunktionen der Seite regelt, z.B. Buttons oder Formularfelder initalisert.
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2016-10-04
 * 
 *
 */

	cmtPage = {

		// Benachrichtigungen
		messageContainerID: '#cmtMessageContainer',
		messageSelector: '.cmtMessage',
		messageTypeSelector: {
			success: 'cmtMessageSuccess',
			error: 'cmtMessageError',
			warning: 'cmtMessageWarning',
			question: 'cmtMessageQuestion',
			info: 'cmtMessageInfo',		// 2011-10-10: Noch nicht implementiert
		},
		maxMessageContainers: 1,	// Maximale Anzahl der Mitteilungscontainer
		
		// Viewport
		contentID: '#cmtContentContainer',
		footerID: '#cmtFooterContainer',
		
		dialogDefaultWidth: 300,
		
		// Pixel, die von Browserfensterhöhe abgezogen werden. Per Test ermittelter Wert, der 
		// in den verschiedenen Browsern unterschiedlich hoch ist (IE 9: 10px, FF5: 13px, Chrome: 24px (sonst "Wackeleffekt")...)
		viewportHeightOffset: 14,
		
		contentYOffset: 0,
		contentHeight: 0,
		footerHeight: 0,
		workbenchHeight: 0,
		bodyTotalPadding: 0,
		
		// Hilfsvariablen
		currentColumnID: null,			// aktuelle Spalte, die untersucht wird
		resizeElementsNumber: 0,		// Anzahl der Elemente in einer Spalte, deren Höhe angepasst werden soll
		currentDialog: null,			// das ist wahrsscheinlich Quatsch
		dialogContainerID: '#cmtDialogContainer',
		data: {},						// zentrales Datenobjekt zum Zwischenspeichern von Daten der verschiedenen Widgets und Skripte
		overlay: null,

		
		/**
		 * function initialize()
		 * 
		 * @param void
		 * @return void
		 */
		initialize: function() {
			jQuery(document).ready(this.initPage.bind(this));
		},
		
		/**
		 * function initPage()
		 * Hier werden alle Methoden aufgerufen, die nach dem Laden der Seite ausgeführt werden müssen.
		 * 
		 * @param void
		 * @return void
		 */
		 initPage: function() {

			this.initBasics();
			this.initGUIElements();
		},
		
		initGUIElements: function(parentElement) {

			parentElement = this._checkElement(parentElement);

			this.initMainNavigation();
			
			this.initDialogLinks(parentElement);
			this.initRowsToHover(parentElement);
			this.initFileInputs(parentElement);
			this.initCalendars(parentElement);
			this.initRowsToSelect(parentElement);
			this.initForm(parentElement);
			this.initFormMultiSelect(parentElement);
			this.initFormSelect(parentElement);
			this.initFormRadio(parentElement);
			this.initFormCheckbox(parentElement);
			this.initTabs(parentElement);
			this.initLayers(parentElement);
			this.initMessages(parentElement);
			this.initTableHeaders(parentElement);
			this.initAutoComplete(parentElement);
////		//	this.initTooltips(parentElement);
			this.initCodeEditor(parentElement);
			// Fensterhöhenanpassung
////			//this.initViewportResize();
			this.initProgressBars(parentElement);
			this.initButtons(parentElement);	
			this.initSetAction(parentElement);
		},
		
		// Methode wird  nach einem Ajax-Aufruf benötigt, um neue Elemente zu initalisieren.
		// TODO: Prüfen, ob alle init-Funktionen hier überhaupt nötig sind!?
		initAjaxGUIElements: function(parentElement) {

			parentElement = this._checkElement(parentElement);

			this.initDialogLinks(parentElement);
			this.initFileInputs(parentElement);
			this.initCalendars(parentElement);
			this.initForm(parentElement);
			this.initFormMultiSelect(parentElement);
			this.initFormSelect(parentElement);
			this.initFormRadio(parentElement);
			this.initFormCheckbox(parentElement);
			this.initTabs(parentElement);
			this.initLayers(parentElement);
			this.initMessages(parentElement);
			this.initTableHeaders(parentElement);
			this.initAutoComplete(parentElement);
			this.initCodeEditor(parentElement);
			
			this.initButtons(parentElement);	
			this.initSetAction(parentElement);
		},				
		/**
		 * function initBasics()
		 * Führt grundlegende Funktionen aus, erweitert z.B. events und binding
		 */
		initBasics: function() {
			// Funktion "reverse()" zum Umkehren der Reihenfolge gefundener Elemente erstellen (aus jQuery-Forum http://forum.jquery.com/)
			jQuery.fn.reverse = [].reverse;

			// Und auch das muss sein, da §$%&/ jQuery "bind()" nur für Events vorsieht, bzw. "bind()" in älteren IEs nicht funktioniert.
			// von: http://www.robertsosinski.com/2009/04/28/binding-scope-in-javascript/
			Function.prototype.bindScope = function(scope) {
				var _function = this;
  
				return function() {
					return _function.apply(scope, arguments);
				}
			}

			// Erweiterung der Events um eigenen Event "afterShow"
			var oldShow = jQuery.fn.show;
			jQuery.fn.show = function(speed, oldCallback) {
				return $(this).each(function() {
					var obj = jQuery(this);
					var newCallback = function() {
						if (jQuery.isFunction(oldCallback)) {
							oldCallback.apply(obj);
						}

						obj.trigger('afterShow');
					};
					// you can trigger a before show if you want
					obj.trigger('beforeShow');

					// now use the old function to show the element passing the new callback
					oldShow.apply(obj, [speed, newCallback]);
				});
			}
		},

		/**
		 * function initLayers()
		 * Initalisiert alle mit dem Selektor '.cmtLayer' gekennzeichneten Elemente als Herunterklappbereiche
		 * 
		 * @param void
		 * @return void
		 */
		initLayers: function(parentElement) {
			
			parentElement = this._checkElement(parentElement);
			
			jQuery('.cmtLayer:not(.cmtIgnore)', parentElement).each( function (index, layer) {
				
				// Nach Titelzeile suchen und Event "click" hinzufügen 
				jQuery(layer).find('.cmtLayerHandle').each( function(index, handle) {
					
					jQuery(handle).click( function(ev) {
						
						var handle = ev.currentTarget;
						var handleIndicator = jQuery(handle).find('.cmtLayerHandleIndicator');
						var layer = jQuery(handle).closest('.cmtLayer');
						var layerContent = jQuery(ev.currentTarget).closest('.cmtLayer').find('.cmtLayerContent'); 
						
						// Pfeil-Icon anpassen
						jQuery(handleIndicator).toggleClass('ui-icon-triangle-1-s');
						jQuery(handleIndicator).toggleClass('ui-icon-triangle-1-n');
						
						// Inhalt öffnen oder schließen
						jQuery(layerContent).toggle('fast');
						jQuery(layerContent).closest('.cmtLayer').toggleClass('cmtLayerOpen')
					})
				});
			});
		},

		/**
		 * function initTabs()
		 * Initalisiert alle mit dem Selektor '.cmtTabs' gekennzeichneten Elemente als Tabs
		 * 
		 * @param void
		 * @return void
		 */
		initTabs: function(parentElement) {

			var parentElement = this._checkElement(parentElement);
			var activeTabNr = parseInt(jQuery('.cmtTabs:not(.cmtIgnore)', parentElement).index('.cmtActiveTab'));
			
			if (activeTabNr == -1) {
				activeTabNr = 0;
			}
			jQuery('.cmtTabs:not(.cmtIgnore)', parentElement).tabs({
				active: activeTabNr,
				heightStyle: 'content'
			});
		},
		
		/**
		 * function initDialogLinks()
		 * Initialisiert die Links auf der Seite, die eine Aktion mit einem Modalfenster ausführen
		 * 
		 *  @param void
		 *  @return void
		 */
		initDialogLinks: function(parentElement) {

			var parentElement = this._checkElement(parentElement);
	
			jQuery(parentElement).find('.cmtDialog:not(.cmtIgnore)').each(function(index, element) {

					/*
					 * Dialog als Fens ter für externe Inhalte
					 */
					if (jQuery(element).hasClass('cmtDialogLoadContent')) {
						
						var extendedURL = jQuery(element).attr('href') + '&cmtDialog=true';
						jQuery(element).attr('href', extendedURL);

						var elWidth = jQuery(element).data('width');
						if (typeof elWidth == 'undefined') {
							elWidth = '600';
						}
						
						var elMinWidth = jQuery(element).data('min-width');
						if (typeof elWidth == 'undefined') {
							elMinWidth = '600';
						}
						
						jQuery(element).on('click.cmtDialog', function(event) {
							var cmtDialog = jQuery(this.dialogContainerID).dialog({
								modal: true,
								title: jQuery(element).data('dialog-title'),
								autoOpen: false,
								minWidth: elMinWidth,
								width: elWidth,
// TODO: maxWidth!								//maxWidth: jQuery(document).height() - 48,
								//maxHeight: jQuery(document).height() - 112,
								position: ['center', 24],
								buttons: {},
								
								// Workaround wg. JQuery-UI-Bug: http://bugs.jqueryui.com/ticket/4820
								open: function() {
									jQuery(this).css({'max-height': jQuery(window).height() - 112, 'overflow-y': 'auto'});
									jQuery(this).addClass('cmtInProgress');
								},
								
								// Workaround wg. JQuery-UI-Bug: http://bugs.jqueryui.com/ticket/4820
								resizeStart: function() {
									jQuery(this).css({'max-height': jQuery(window).height() - 112, 'overflow-y': 'auto'}); 							
								},
								
								// Dialogfenster leeren beim Schließen
								close: function() {
									jQuery(this).dialog('destroy');
									jQuery(this).empty();
								}
							});
							
						// Inhalte laden
						cmtDialog.load(
							element.href, 
							{},
							function (responseText, textStatus, XMLHttpRequest) {
								
								jQuery(this.dialogContainerID).removeClass('cmtInProgress');
								
								this.initGUIElements(this.dialogContainerID);
								
								// "schließen"-Knopf 
								jQuery('.cmtDialogClose', this.dialogContainerID).on('click.cmtDialog', function(ev) {
									jQuery(this.dialogContainerID).dialog('close');
									// jQuery(this.dialogContainerID).dialog('destroy');
									// jQuery(this.dialogContainerID).empty()
								}.bindScope(this))
								
								// FileSelectoren
								if (jQuery(element).hasClass('cmtFileSelector')) {
									
									cmtPage.setData(
										{
											tableFieldName: jQuery(event.currentTarget).attr('data-field')
										},
										'FileSelector'
									);
	
									this.initFileSelector({});
								}
								
							}.bindScope(this)
			            ).bind(this);
						
						cmtDialog.dialog('open');
						
						this.currentDialog = cmtDialog;
						
						element.blur();
						
						return false;
					}.bindScope(this)); // <= Wichtig: nie die Bindung verlieren!
				
				} else if (!this._hasEvent(element, 'click.cmtDialog')){

					/*
					 * "Normale" Dialoge
					 */

					jQuery(element).on('click.cmtDialog', function(event) {
						
						// Inhalte des Dialogfensters
						var dialogContentContainerID = jQuery(event.currentTarget).attr('data-dialog-content-id');
						var dialogVar = jQuery(event.currentTarget).attr('data-dialog-var');
						var dialogConfirmURL = jQuery(event.currentTarget).attr('data-dialog-confirm-url');
						var submitFormID = jQuery(event.currentTarget).attr('data-dialog-submit-form-id');
						var dialogCancelURL = jQuery(event.currentTarget).attr('data-dialog-cancel-url');
						var dialogWidth = parseInt(jQuery(event.currentTarget).attr('data-dialog-width'));
						
						if (!dialogWidth) {
							dialogWidth = this.dialogDefaultWidth;
						}
						
						var varContainer = jQuery('#' + dialogContentContainerID + ' .cmtDialogContent .cmtDialogVar');
						var tagName = jQuery(varContainer).prop('tagName')
						if (typeof tagName == 'undefined') {
							tagName = '';
						}
						tagName = tagName.toString().toLowerCase();
						
						switch (tagName) {
							
							case 'input':
								var type = jQuery(varContainer).attr('type');
								if (type == 'text' || type == 'hidden') {
									jQuery(varContainer).attr('value', dialogVar);
								}
								break;
								
							case 'select':
								jQuery('option[value=' + dialogVar.replace("'", "\'") + ']', varContainer).attr('selected', true);
								break;
								
							default:
								jQuery(varContainer).html(dialogVar);
								break;
						}

						var dialogContent = jQuery('#' + dialogContentContainerID + ' .cmtDialogContent').html();
						
					
						// Buttons
						var dialogButtonCancelText = jQuery('#' + dialogContentContainerID + ' .cmtButtonCancelText').html();
						var dialogButtonCancelClass = jQuery('#' + dialogContentContainerID + ' .cmtButtonCancelText').attr('data-button-class');
						var dialogButtonConfirmText = jQuery('#' + dialogContentContainerID + ' .cmtButtonConfirmText').html();
						var dialogButtonConfirmClass = jQuery('#' + dialogContentContainerID + ' .cmtButtonConfirmText').attr('data-button-class');

						// Titel
						var dialogTitle = jQuery(element).data('dialog-title');
						if (!dialogTitle) {
							dialogTitle = jQuery('#' + dialogContentContainerID).attr('title');
						}

						/*
						 * Dialog: Confirm
						 */
						if (jQuery(element).hasClass('cmtDialogConfirm')) {

							var cmtDialog = jQuery(this.dialogContainerID).dialog({
								resizable: false,
								//height:140,
								title: dialogTitle,
								modal: true,
								dialogClass: 'cmtDialogTypeConfirm',
								open: function() {

									// Diese Lösung wird leider benötigt, da der Buttoncontainer erst nach dem Aufruf dieses 
									// Events erzeugt wird und es sonst nicht möglich ist in JQuery-Ui 1.8 Dialogbuttons mit
									// Icons zu stylen!?
									setTimeout(function() {
										
										// Button abbrechen
										jQuery('.ui-dialog-buttonpane button.'+dialogButtonCancelClass).
										button({
											icons: {
												primary: dialogButtonCancelClass
											}
										})
										
										// Button bestätigen
										jQuery('.ui-dialog-buttonpane button.'+dialogButtonConfirmClass).
										button({
											icons: {
												primary: dialogButtonConfirmClass
											}
										})
									}, 100);
									
								},
								
								close: function() {
									jQuery(this).dialog('destroy');
									jQuery(this).empty();
								}
							});
							
							jQuery(this.dialogContainerID).dialog('option', 'buttons',
								[	
									{
										text: dialogButtonCancelText,
										class: 'cmtButton ' + dialogButtonCancelClass,
										click: function() {
											
											// reset generally a 'cmtAction' field (usually a hidden input field!) 
											jQuery('#cmtAction').val('');
											
											if (dialogCancelURL) {
												window.location.href = dialogCancelURL;
											} else {
												jQuery( this ).dialog( "close" );
											}
										}
									},
									{
										text: dialogButtonConfirmText,
										class: 'cmtButton ' + dialogButtonConfirmClass,
										click: function() {
											
											if (dialogConfirmURL) {
												window.location.href = dialogConfirmURL;
											} else if (submitFormID) {

												jQuery('#' +submitFormID).submit();
											} else {
												// jQuery( this ).dialog( "close" );
											}
											
											jQuery( this ).dialog( "close" );
										}
									}
								]
							);
							
							cmtDialog.html('<div class="cmtDialogContainerInner">' + dialogContent + '</div>');
							
						} else {
							var cmtDialog = jQuery(this.dialogContainerID).dialog({
								resizable: false,
								width: dialogWidth,
								title: dialogTitle,
								modal: true,
								close: function() {
									jQuery(this).dialog('destroy');
									jQuery(this).empty();
								}
							});
							
							cmtDialog.html('<div class="cmtDialogContainerInner">' + dialogContent + '</div>');
							
							
							// "schließen"-Knopf 
							jQuery('.cmtDialogClose', cmtDialog).on('click.cmtDialog', function(ev) {
								jQuery(this.dialogContainerID).dialog('close');
							}.bindScope(this))
						}
						
						return false;
					}.bindScope(this));
				}

			}.bindScope(this)); // <= Wichtig: nie die Bindung verlieren!

		},

		/**
		 * function showAjaxDialog()
		 * Displays a dialog window with ajax content
		 */
		showAjaxDialog: function(params) {
			params = {
				ajaxURL: null,		// load content viaq ajax
				width: 600,
				minWidth: 600,
				title: '',
				contentContainerID: null,		// ID of HTML element containing the content for the dialog window
				dialogVar: '',					// variable (value) to display in dialog text (use <span class="cmtDialogVar"></span> in text)
				onClose: function() {},		// Javascript function called, when dialog is canceled
			};
			
			// create dialog window
			var cmtDialog = jQuery(this.dialogContainerID).dialog({
				modal: true,
				title: params.title,
				autoOpen: false,
				minWidth: params.minWidth,
				width: params.width,
				position: ['center', 24],
				buttons: {},
				resizeable: false,
/*				
				// Workaround wg. JQuery-UI-Bug: http://bugs.jqueryui.com/ticket/4820
				open: function() {
					jQuery(this).css({'max-height': jQuery(window).height() - 112, 'overflow-y': 'auto'});
					jQuery(this).addClass('cmtInProgress');
				},
				
				// Workaround wg. JQuery-UI-Bug: http://bugs.jqueryui.com/ticket/4820
				resizeStart: function() {
					jQuery(this).css({'max-height': jQuery(window).height() - 112, 'overflow-y': 'auto'}); 							
				},
*/				
				// Dialogfenster leeren beim Schließen
				close: function() {
					jQuery(this).dialog('destroy');
					jQuery(this).empty();
					
					params.onClose;
				}
			});
			
			
			// load dialog content from Ajax URL
				
			/* extend ajaxURL for external content */
			params.ajaxURL += '&cmtDialog=true';
			
			cmtDialog.load(
				params.ajaxURL, 
				{},
				function (responseText, textStatus, XMLHttpRequest) {
					
					jQuery(this.dialogContainerID).removeClass('cmtInProgress');
					
					this.initGUIElements(this.dialogContainerID);
					
					// "schließen"-Knopf 
					jQuery('.cmtDialogClose', this.dialogContainerID).on('click.cmtDialog', function(ev) {
						jQuery(this.dialogContainerID).dialog('close');
					}.bindScope(this))
					
					// FileSelectoren
					if (jQuery(element).hasClass('cmtFileSelector')) {
						cmtPage.setData({
							tableFieldName: jQuery(event.currentTarget).attr('data-field')
						}, 'FileSelector');

						this.initFileSelector({});
					}
				}.bindScope(this)
            ).bind(this);
			
			cmtDialog.dialog('open');
			this.currentDialog = cmtDialog;
	
			return false;
		},
		
		showConfirm: function(params) {
			
			defaultParams = {
				width: 600,
				minWidth: 600,
				title: '',
				contentContainerID: null,		// ID of HTML element containing the content for the dialog window
				dialogVar: '',					// variable (value) to display in dialog text (use <span class="cmtDialogVar"></span> in text)
				confirmURL: '',					// URL called, when dialog is confirmed
				cancelURL: '',					// URL called, when dialog is canceled
				onConfirm: function() {},		// Javascript function called, when dialog is confirmed
				onCancel: function() {},		// Javascript function called, when dialog is canceled
				submitFormID: null				// ID of form that should be submitted after dialog is confirmed
			};
			
			params = jQuery.extend(defaultParams, params);

			// Buttons
			var dialogButtonCancelText = jQuery('#' + params.contentContainerID + ' .cmtButtonCancelText').html();
			var dialogButtonCancelClass = jQuery('#' + params.contentContainerID + ' .cmtButtonCancelText').attr('data-button-class');
			var dialogButtonConfirmText = jQuery('#' + params.contentContainerID + ' .cmtButtonConfirmText').html();
			var dialogButtonConfirmClass = jQuery('#' + params.contentContainerID + ' .cmtButtonConfirmText').attr('data-button-class');
			
			var cmtDialog = jQuery(this.dialogContainerID).dialog({
				resizable: false,
				title: params.title,
				modal: true,
				dialogClass: 'cmtDialogTypeConfirm',
				open: function() {

					// Diese Lösung wird leider benötigt, da der Buttoncontainer erst nach dem Aufruf dieses 
					// Events erzeugt wird und es sonst nicht möglich ist in JQuery-Ui 1.8 Dialogbuttons mit
					// Icons zu stylen!?
					setTimeout(function() {
						
						// Button abbrechen
						jQuery('.ui-dialog-buttonpane button.'+dialogButtonCancelClass).
						button({
							icons: {
								primary: dialogButtonCancelClass
							}
						})
						
						// Button bestätigen
						jQuery('.ui-dialog-buttonpane button.'+dialogButtonConfirmClass).
						button({
							icons: {
								primary: dialogButtonConfirmClass
							}
						})
					}, 100);
					
				},
				
				close: function() {
					jQuery(this).dialog('destroy');
					jQuery(this).empty();
				}
			});
			
			// load dialog content from page
			var varContainer = jQuery('#' + params.contentContainerID + ' .cmtDialogContent .cmtDialogVar');
			var tagName = jQuery(varContainer).prop('tagName')
			if (typeof tagName == 'undefined') {
				tagName = '';
			}
			tagName = tagName.toString().toLowerCase();
			
			switch (tagName) {
				
				case 'input':
					var type = jQuery(varContainer).attr('type');
					if (type == 'text' || type == 'hidden') {
						jQuery(varContainer).attr('value', params.dialogVar);
					}
					break;
					
				case 'select':
					jQuery('option[value=' + params.dialogVar.replace("'", "\'") + ']', varContainer).attr('selected', true);
					break;
					
				default:
					jQuery(varContainer).html(params.dialogVar);
					break;
			}

			var dialogContent = jQuery('#' + params.contentContainerID + ' .cmtDialogContent').html();
				
			jQuery(this.dialogContainerID).dialog('option', 'buttons',
				[	
					{
						text: dialogButtonCancelText,
						class: 'cmtButton ' + dialogButtonCancelClass,
						click: function() {
							
							// reset generally a 'cmtAction' field (usually a hidden input field!) 
							jQuery('#cmtAction').val('');
							
							if (params.cancelURL) {
								window.location.href = dialogCancelURL;
							} else {
								params.onCancel();
								jQuery( this ).dialog( "close" );
							}
						}
					},
					{
						text: dialogButtonConfirmText,
						class: 'cmtButton ' + dialogButtonConfirmClass,
						click: function() {
							
							if (params.confirmURL) {
								window.location.href = params.confirmURL;
							} else if (params.submitFormID) {

								jQuery('#' +params.submitFormID).submit();
							} else {
								params.onConfirm();
								// jQuery( this ).dialog( "close" );
							}
							
							jQuery( this ).dialog( "close" );
						}
					}
				]
			);
			
			cmtDialog.html('<div class="cmtDialogContainerInner">' + dialogContent + '</div>');
			
		},
		
		/**
		 * function showDialog()
		 * Displays a dialog window (alert)
		 * 
		 */
		showDialog: function(params) {

			var defaultParams = {
				ajaxURL: null,		// load content viaq ajax
				width: 600,
				minWidth: 600,
				title: '',
				contentContainerID: null,		// ID of HTML element containing the content for the dialog window
				dialogVar: '',					// variable (value) to display in dialog text (use <span class="cmtDialogVar"></span> in text)
//				confirmURL: '',					// URL called, when dialog is confirmed
//				cancelURL: '',					// URL called, when dialog is canceled
				onConfirm: function() {},		// Javascript function called, when dialog is confirmed
//				onCancel: function() {},		// Javascript function called, when dialog is canceled
				submitFormID: null				// ID of form that should be submitted after dialog is confirmed
			};
			
			jQuery.extend(defaultParams, params);

//			var dialogContentContainerID = jQuery(event.currentTarget).attr('data-dialog-content-id');
//			var dialogVar = jQuery(event.currentTarget).attr('data-dialog-var');
//			var dialogConfirmURL = jQuery(event.currentTarget).attr('data-dialog-confirm-url');
//			var submitFormID = jQuery(event.currentTarget).attr('data-dialog-submit-form-id');
//			var dialogCancelURL = jQuery(event.currentTarget).attr('data-dialog-cancel-url');
//			var dialogWidth = parseInt(jQuery(event.currentTarget).attr('data-dialog-width'));
			
			// load dialog content from page
			var varContainer = jQuery('#' + params.contentContainerID + ' .cmtDialogContent .cmtDialogVar');
			var tagName = jQuery(varContainer).prop('tagName')
			if (typeof tagName == 'undefined') {
				tagName = '';
			}
			tagName = tagName.toString().toLowerCase();
			
			switch (tagName) {
				
				case 'input':
					var type = jQuery(varContainer).attr('type');
					if (type == 'text' || type == 'hidden') {
						jQuery(varContainer).attr('value', dialogVar);
					}
					break;
					
				case 'select':
					jQuery('option[value=' + dialogVar.replace("'", "\'") + ']', varContainer).attr('selected', true);
					break;
					
				default:
					jQuery(varContainer).html(dialogVar);
					break;
			}

			var dialogContent = jQuery('#' + params.contentContainerID + ' .cmtDialogContent').html();

			// buttons
			var dialogButtonCancelText = jQuery('#' + params.contentContainerID + ' .cmtButtonCancelText').html();
			var dialogButtonCancelClass = jQuery('#' + params.contentContainerID + ' .cmtButtonCancelText').attr('data-button-class');
			var dialogButtonConfirmText = jQuery('#' + params.contentContainerID + ' .cmtButtonConfirmText').html();
			var dialogButtonConfirmClass = jQuery('#' + params.contentContainerID + ' .cmtButtonConfirmText').attr('data-button-class');

			// title
			var dialogTitle = jQuery(element).data('dialog-title');
			if (!dialogTitle) {
				dialogTitle = jQuery('#' + dialogContentContainerID).attr('title');
			}

			var cmtDialog = jQuery(this.dialogContainerID).dialog({
				resizable: false,
				width: dialogWidth,
				title: dialogTitle,
				modal: true,
				close: function() {
					jQuery(this).dialog('destroy');
					jQuery(this).empty();
				}
			});
							
			cmtDialog.html('<div class="cmtDialogContainerInner">' + dialogContent + '</div>');
							
			// "schließen"-Knopf 
			jQuery('.cmtDialogClose', cmtDialog).on('click.cmtDialog', function(ev) {
				jQuery(this.dialogContainerID).dialog('close');
			}.bindScope(this))
		},		
		
		
		
// User-Benachrichtigungen: Start
		initMessages: function(parentElement) {

			parentElement= this._checkElement(parentElement);
			
			jQuery('.cmtMessage:not(.cmtIgnore)', parentElement).each(
					function(index, element) {

						jQuery(element).click( function (ev) {
							var messageContainer = ev.currentTarget;
							this.hideMessage(messageContainer);
							
							if (jQuery(messageContainer).hasClass('cmt-system-message')) {
								
								var shownMessages = Cookies.getJSON('cmtSystemMessagesShown');
								
								if (typeof shownMessages != 'object') {
									shownMessages = {};
								}

								shownMessages[jQuery(messageContainer).data('cmt-message-id')] = true;
								
								var pathNameParts = window.location.pathname.split('/');
								pathNameParts.pop()
								var pathName = pathNameParts.join('/') + '/';
								Cookies.set('cmtSystemMessagesShown', shownMessages, {path: pathName});
							}
						}.bindScope(this));
						
					}.bindScope(this) 
			).bind(this);
			
		},
		
		/**
		 * function createMessage()
		 * Ein Container für Meldungen wird initialisiert
		 * 
		 * @param string messageContainerID ID des Containers
		 * @return void
		 */
		createMessage: function(messageType, messageContainerID) {
			

			var newMessage = document.createElement('div');
			
			if (typeof messageContainerID != 'undefined') {
				var mcID = messageContainerID;
			} else {
				var mcID = this.messageContainerID;
			}			

			// Bei Klick zufahren
			jQuery(newMessage).click( function (ev) {
				var messageContainer = ev.currentTarget;
				this.hideMessage(messageContainer);
			}.bindScope(this));

			// CSS-Selektoren
			jQuery(newMessage).addClass('cmtMessage '+this.messageTypeSelector[messageType]);

			// Styles
			jQuery(newMessage).css({
				display: 'none'
			});
			
			// und anhängen
			jQuery(mcID).append(newMessage);

			return newMessage;

		},
		
		/**
		 * function showMessage()
		 * Zeigt eine Meldung je nach Typ im vorher definierten Container an.
		 * 
		 * @param string messageText Text der Meldung 8kann auch HTML sein
		 * @param string messageType Art der Meldung: 'info', 'success', 'error' oder 'warning'
		 * 
		 *  @return void
		 */
		showMessage: function(messageText, messageType, messageContainerID) {
	
			if (!messageType) {
				messageType='info'
			}

			if (typeof messageContainerID != 'undefined') {
				var mcID = messageContainerID;
			} else {
				var mcID = this.messageContainerID;
			}
			var messageContainers = jQuery(mcID).find(this.messageSelector)

			// Sind noch alte Container vorhanden, dann ausblenden!
			if (messageContainers.length >= this.maxMessageContainers) {

				jQuery(mcID).find(this.messageSelector).each( function (index, el) {
					this.hideMessage(el);
				}.bindScope(this));
			}

			refMessage = this.createMessage(messageType, mcID);
			jQuery(refMessage).html(messageText);
			jQuery(refMessage).slideDown(400);
			
		},
		
		/**
		 * function hideMessage()
		 * Versteckt das Meldungsfeld, sofern es ausgeklappt ist.
		 * 
		 * @param void
		 * @return void
		 */
		hideMessage: function(messageContainer) {
			
			jQuery(messageContainer).slideUp( 300, function() {

				var messageContainer = this; //ev.currentTarget;
				jQuery(messageContainer).remove();
			});
			// eine Bindung funktioniert hier nicht, da jQuery.slideUp() in der Callback-funktion nichts zurück gibt.
			// Binden wir hier, geht die Information, welches element geklickt wurde in der Callback-Funktion verloren:
			// "this" ist nicht mehr das geklickte Objekt, sondern cmtPage.

		},
// ==> User-Benachrichtigungen: Ende

// Button-Erstellung: Start
		/**
		 * function initButtons()
		 * Initialisiert die mit dem Selektor ".cmtButton" markierten Knöpfe auf einer Seite.
		 * 
		 * @param parentElement Referenz auf ein DOM-Element, in welchem nach Knöpfen zum Umwandeln gesucht werden soll
		 * @return void
		 */
		initButtons: function(parentElement) {

			parentElement = this._checkElement(parentElement);
		
			jQuery(parentElement).find('.cmtButton:not(.cmtIgnore)').each(function(index, element) {
			
				var iconName = null;
				var linkType = null;
				
				var linkHref = jQuery(element).find('a').andSelf().attr('href');
				var linkTitle = jQuery(element).attr('title');
				
				//var button = element.parent('.cmtButton')
				
				if (typeof element.className != 'undefined') {
					element.className.split(' ');
					var iconName = element.className.match(/cmtButtonIcon[A-Za-z0-9_-]+/);
					var linkType = element.className.match(/cmtButtonType[A-Za-z0-9_-]+/);
					var isButton = element.className.match(/ui-button/);
				}

				var addClasses = '';
				
				// Knöpfe ohne Text markieren
				var hasText = false;
				
				if (jQuery(element).text()) {
					hasText = true;
				} else {
					// jQuery(element).addClass('cmtButtonNoText');
					addClasses += ' cmtButtonNoText'
				}

				// Button deaktiviert?
				var isDisabled = false;
				
				if (jQuery(element).is('.cmtDisabled')) {
					isDisabled = true;
					addClasses += ' cmtDisabled'
					jQuery(element).removeClass('cmtDisabled');
				}
				
				// Knopf erzeugen
				var buttonSelector = jQuery(element).attr('class').match(/(cmtButton[^\s]+)/g);

				if (buttonSelector) {
					
					//jQuery(element).removeClass(buttonSelector[0]);
					
					var newButton = jQuery(element).button({
						icons: {
							primary: 'cmtButtonIcon ' + buttonSelector[0]
						}
					});
				} else {
					jQuery(element).button();
				}
				
				// Zusätzliche CSS-Selektoren hinzufügen
				jQuery(element).button().addClass(addClasses)
				
				// Button ggf. deaktivieren
				if (isDisabled) {
					jQuery(element).button('option', 'disabled', true);
				}

				// Icon hinzufügen
// ??? wird das überhaupt noch genutzt????
				if (iconName) {
					jQuery(element).button('option', {
						icons: {
							primary: iconName
						},
						text: hasText
					})
				}
				
				// Events
				// Event 'mousedown': Selektor 'cmtButtonClicked' hinzufügen
				jQuery(element).bind('mousedown.cmtButton', function(event) {
					jQuery(event.currentTarget).addClass('cmtButtonClicked');
				})

				// Event 'mouseout': Selektor 'cmtButtonClicked' entfernen
				jQuery(element).bind('mouseup.cmtButton', function(event) {
					jQuery(event.currentTarget).removeClass('cmtButtonClicked');
				})

				// Event 'mouseover': zur Sicherheit Selektor 'cmtButtonClicked' entfernen
				jQuery(element).bind('mouseover.cmtButton', function(event) {
					jQuery(event.currentTarget).removeClass('cmtButtonClicked');
				})
				
				// Link-Typ
//				switch(linkType) {
//					
//					case 'cmtButtonTypeModal':
//						var cmtDialog = jQuery('#cmtModalWin').dialog({
//							modal: true,
//							title: element.title,
//							autoOpen: false,
//							width: 600,
//							position: ['center', 24],
//							close: function() {
//								jQuery(this).empty();
//							}
//						});
//						
//						// Inhalte laden
//						cmtDialog.load (
//							element.href, 
//							{},
//							function (responseText, textStatus, XMLHttpRequest) {
//								//jQuery('cmtModalWin').dialog();
//							}
//						break;
//				}
//				
//				jQuery(element).button().click(function(ev) {
//					document.location.href = linkHref
//				});
			})
		},
// ==> Button-Erstellung: Ende
		initViewportResize: function() {

			// Body: Gesamtinnenabstände
			this.bodyTotalPadding = parseInt(jQuery('body').css('paddingTop')) + parseInt(jQuery('body').css('paddingBottom'));

			// Inhalt: Startposition (entspricht Headerhöhe und Abstände bis Inhaltscontainer)
			var contentOffsets = jQuery(this.contentID).offset();
			this.contentYOffset = contentOffsets.top;

			// Höhe Arbeitsfläche
			this.workbenchHeight = jQuery(this.contentID).outerHeight(true);

			// Höhe Seitenfuß
			this.footerHeight = jQuery(this.footerID).outerHeight(true);

			// Event "Fenstergröße wird geändert" registrieren
			jQuery(window).resize(function(ev) {
				this.resizeToViewport();
			}.bindScope(this));

			// anfangs sofort Höhen berechnen
			this.resizeToViewport();		
		},
		
		/**
		 * function resizeToViewport()
		 * Methode passt die Höhe des Inhaltsbereiches der Größe des Browserfensters an.
		 *
		 * @param void Erwartet keine Parameter
		 * @param void Gibt keinen Wert zurück
		 */		
		resizeToViewport: function() {
		
			// Etwas gewurschtelt, da jQuery().innerHeight() in manchen Browsern (FF) nicht zuverlässig funktioniert.
			var viewportHeight = jQuery(window).height() - this.bodyTotalPadding ;
			var contentNewHeight = viewportHeight - this.viewportHeightOffset - this.contentYOffset - this.footerHeight;

			// Nur vergrößern, wenn Arbeitsfläche kleiner als Browserfläche
			if (this.workbenchHeight < viewportHeight) {
				jQuery(this.contentID).css('height', contentNewHeight + 'px');
			} else {
				jQuery(this.contentID).css('height', jQuery(this.contentID) + 'px');
			}

			// Höhe der Elemente in den Spalten anpassen
			this.resizeElementsInColumn(this.contentID);

		},

		/**
		 * function resizeElementsInColum()
		 * Methode sucht nach dem Element innerhalb einer "Spalte", welches mit dem Selektor "resizeHeight" markiert ist
		 * und passt dessen und die Höhen aller Elternelemente (bis zum Spaltenelement)der verfügbaren Höhe an.
		 *
		 * @param string ID der Spalte, die untersucht werden soll (mit führendem "#", also z.B. "#serviceCol")
		 * @param void Gibt keinen Wert zurück
		 */			
		resizeElementsInColumn: function(columnID) {

			this.currentColumnID = columnID;

			var resizeElements = jQuery(columnID).find('.cmtResizeHeight');
			this.resizeElementsNumber = resizeElements.length;

			resizeElements.each(
				function(index, el) {

					// Alle Elternelemente des Elements (inkl. Spalte) suchen
					var resizeElementParents = jQuery(el).parentsUntil(jQuery(this.currentColumnID)).reverse();
					
					// Element der Liste anhängen
					resizeElementParents = resizeElementParents.add(el);

					// Alle gefundenen Elemente in der Höhe anpassen
					resizeElementParents.each(
						function(index, el) {

							var siblings = jQuery(el).siblings();
							var siblingsHeight = 0;
							var elementFloats = jQuery(el).css('float');

							for (var i=0; i < siblings.length; i++) {


								var siblingFloats = jQuery(siblings[i]).css('float');

								if (siblingFloats == 'none' && elementFloats == 'none') {
									siblingsHeight += jQuery(siblings[i]).outerHeight(true);
								}
							}

							var elementPadding = jQuery(el).outerHeight() - jQuery(el).height();
							var colNewHeight = jQuery(el).parent().height() - siblingsHeight - elementPadding;
							jQuery(el).css({'height':  + colNewHeight + 'px'});
						}.bindScope(this)
					)
				}.bindScope(this)
			)
		},

		/**
		 * function initCalendars()
		 * Initalisiert Felder mit Kalenderfunktionen
		 */
		initCalendars: function(elementID) {
			
			elementID = this._checkElement(elementID);
			
			jQuery(elementID).find('.cmtShowCalendar:not(.cmtIgnore)').each(function(index, calendarButton) {
				
				var parentContainer = jQuery(calendarButton).parent();
				var cmtDateFormat = jQuery(calendarButton).attr('data-dateformat');

				// Datumsformat auch für den Datepicker einstellen:
				// datepicker: 'yy' => contentomat: 'yyyy'
				// datepicker: 'y' => contentomat: 'yy'
				var dpDateFormat = cmtDateFormat.replace(/y{4}/, 'yy');
				if (dpDateFormat == cmtDateFormat) {
					dpDateFormat = cmtDateFormat.replace(/y{2}/, 'y');
				}

				// Datepicker initialisieren
				jQuery(parentContainer).datepicker({
					dateFormat: dpDateFormat,
					changeMonth: true,
					changeYear: true,
					showButtonPanel: true,
					closeText: 'X',
					showWeek: true,
					
					// wenn Datepicker angezeigt wird, dann Datum aktualisieren und datepicker ausblenden!
					onSelect: function(dateText, datepicker) {
						var parentContainer = jQuery(datepicker.id).parent();

						jQuery(this).find('.cmtDateYear').val(datepicker.selectedYear);
						jQuery(this).find('.cmtDateMonth').val(('0' + (parseInt(datepicker.selectedMonth) + 1).toString()).substr(-2));
						jQuery(this).find('.cmtDateDay').val(('0' + (parseInt(datepicker.selectedDay).toString())).substr(-2));

						jQuery(this).find('.cmtDate').val(dateText);
						
						jQuery(this).find('.ui-datepicker').toggle();
					},
					
					// Pain in the ass: weil der Datepicker beim Monatswechsel komplett neu erstelt wird, muss
					// auch der individuelle Knopf jedes Mal neu hinzugefügt werden.
					// Dieser Krampf hier muss sein, da der "Schließen"-Button bei Inline-Datepickern IMMER ausgeblendet wird
					onChangeMonthYear: function(year, month, datepicker) {
						
						var parentContainer = jQuery(datepicker.input);

						if (jQuery(parentContainer).find('cmtButtonBack').length == 0) {

							setTimeout(function() {
								var buttonPane = jQuery(datepicker.input).find('.ui-datepicker-buttonpane');  
								var addButton = jQuery('<button class="cmtButton cmtButtonBack" type="button">' + $.datepicker.regional['de'].closeText + '</button>');  
	
								addButton.unbind('click').bind('click', function (event) {  
									jQuery(this).parentsUntil('.ui-datepicker').parent().toggle();
								});
	
								var ref = jQuery(addButton).appendTo( buttonPane );

								// Eigenen Wrapper einfügen um content-o-mat Layout zu ermöglichen
								jQuery(parentContainer).find('.ui-datepicker').wrapInner('<div class="cmtDatepickerContentWrapper clearfix ui-corner-all" />');
								
								// weitere Layoutanpassungen
								jQuery(parentContainer).find('.ui-datepicker-header').removeClass('ui-corner-all').addClass('ui-corner-top');
								jQuery(parentContainer).find('.ui-datepicker-buttonpane .ui-priority-secondary').removeClass('ui-priority-secondary').addClass('ui-corner-top');

								jQuery(parentContainer).find('.ui-datepicker-buttonpane button').addClass('cmtButton');
								cmtPage.initButtons(jQuery(buttonPane));
							},5);
						}
					}
				})
				
				// ==> Ende: Datepicker initialisieren
				
				// Eigener Effekt "beforeShow": Bei jedem Aktualisieren muss auch das Datum angepasst werden
				jQuery(parentContainer).parent().find('.ui-datepicker').bind('beforeShow', function(event) {

					var parentContainer = jQuery(event.target).parent();
					var cmtDateFormat = jQuery(event.target).parent().find('.cmtShowCalendar').attr('data-dateformat');

					// Datum aus Feldern ermitteln
					var year = jQuery(parentContainer).find('.cmtDateYear').val();
					var month = jQuery(parentContainer).find('.cmtDateMonth').val();
					var day = jQuery(parentContainer).find('.cmtDateDay').val();
					
					// Datum prüfen und event. korrigieren
					var currentDate = new Date();
					
					if (year == undefined || parseInt(year) == 0) {
						year = currentDate.getFullYear();
					}

					if (month == undefined || parseInt(month) == 0) {
						month = ('0' + (currentDate.getMonth()).toString()).substr(-2);
					} else {
						month = ('0' + (parseInt(month) - 1).toString()).substr(-2);
					}

					if (day == undefined || parseInt(day) == 0) {
						day = ('0' + currentDate.getDate().toString() ).substr(-2);
					}

					var storedDate = cmtDateFormat;

					storedDate = storedDate.replace(/yyyy/, year);
					storedDate = storedDate.replace(/mm/, month);
					storedDate = storedDate.replace(/dd/, day);

					var parsedDate = new Date(year, month, day);
					jQuery(parentContainer).datepicker('setDate', parsedDate)

					// Anfangs ebenfalls den zusätzlichen Knopf hinzufügen => sucks!
					setTimeout(function() {

						if (jQuery(parentContainer).find('.cmtButtonBack').length == 0) {
							var buttonPane = jQuery(parentContainer).find('.ui-datepicker-buttonpane');  
							var addButton = jQuery('<button class="cmtButtonBack" type="button">' + $.datepicker.regional['de'].closeText + '</button>');  
							addButton.unbind('click').bind('click', function (event) {  
								jQuery(this).parentsUntil('.ui-datepicker').parent().toggle();
							});  
		
							addButton.appendTo( buttonPane ); 
						}
						
						jQuery(buttonPane).find('button').addClass('cmtButton');
						cmtPage.initButtons(jQuery(buttonPane));
					},5);
					
					// Eigenen Wrapper einfügen um content-o-mat Layout zu ermöglichen
					jQuery(parentContainer).find('.ui-datepicker').wrapInner('<div class="cmtDatepickerContentWrapper clearfix ui-corner-all" />');
					
					// weitere Layoutanpassungen
					jQuery(parentContainer).find('.ui-datepicker-header').removeClass('ui-corner-all').addClass('ui-corner-top');
					jQuery(parentContainer).find('.ui-datepicker-buttonpane .ui-priority-secondary').removeClass('ui-priority-secondary').addClass('ui-corner-top');
					
				})
				// ==> Ende"beforeShow"
				
				// Sprache einstellen
				jQuery.datepicker.setDefaults(jQuery.datepicker.regional['']);
				jQuery(parentContainer).datepicker('option', jQuery.datepicker.regional['de']);

				// datepicker bei Mausklick auf Grafik anzeigen
				jQuery(calendarButton).click(function (event) {
					jQuery(this).parent().find('.ui-datepicker').toggle();
				});

				// Individuelle Anpassungen des DOM für den content-o-maten
				jQuery(parentContainer)
				.find('select')
				.addClass('cmtIgnore');
				
				// datepicker verbergen: das darf erst ganz zum Schluss geschehen, sonst wird Knopf nicht angehängt
				var dp = jQuery(this).parent().find('.ui-datepicker')
				dp.hide();
			})
		},
		
		/**
		 * function initFileSelector()
		 * Initialisiert ein CMT-Dateiauswahlfenster.
		 * 
		 * @param object Objekt mit benötigten Parametern
		 * @return void
		 */
		initFileSelector: function(params) {
			
			var defaultParams = {
				breadcrumbContainerID: '#cmtFileSelectorBreadcrumbs',
				directoryContainerID: '#cmtFileSelectorDirectoryContent',
				classNameFile: '.cmtFileSelectorFile',
				classNameDirectory: '.cmtFileSelectorDirectory',
				classNameBreadcrumbLink: '.cmtBreadcrumbLink',
				dialogContainerID: this.dialogContainerID,
				cancelButtonID: '#cmtFileSelectorCancel',
				saveButtonID: '#cmtFileSelectorSave',
				selectedPathID: '#cmtFileSelectorPath'
			};
			
			params = jQuery.extend(defaultParams, params);
			
			// Daten zentral in cmtPage.data speichern
			cmtPage.setData(params, 'FileSelector');

			// Schließen-/Abbrechen-Knopf initalisieren
			jQuery(params.cancelButtonID).unbind('click');
			jQuery(params.cancelButtonID).click( function(event) {
				
				var params = cmtPage.getData('FileSelector'); 

				jQuery(params.dialogContainerID).dialog('close');
			}.bindScope(this));
		
			// Übernehmen-Knopf initalisieren
			jQuery(params.saveButtonID).unbind('click');
			jQuery(params.saveButtonID).click( function(event) {

				var params = cmtPage.getData('FileSelector'); 

				jQuery('*[name=' + params.tableFieldName + ']').val(jQuery(params.selectedPathID).val());
				jQuery(params.dialogContainerID).dialog('close');
			}.bindScope(this))
			
			// Dateilinks in Liste auswählbar machen
			this.initFileSelectorContent();

		},
		
		/**
		 * function initFileSelectorContent()
		 * Initalisiert den Inhalt des FileSelectors: Macht Ordner- und Dateilinks klickbar
		 * 
		 * @param void
		 * 
		 * @return void
		 */
		initFileSelectorContent: function() {
		
			var params = cmtPage.getData('FileSelector');
	
			// Dateilinks in Liste auswählbar machen
			jQuery(params.dialogContainerID + ' ' + params.classNameFile).each( function(index, element) {

				jQuery(element).click( function(event) {
					
					var params = cmtPage.getData('FileSelector');
				
					// Pfad in Inputfeld übernehmen
					jQuery(params.selectedPathID).val(unescape(jQuery(event.currentTarget).attr('data-filepath')));

				}.bindScope(this));
				
				jQuery(element).attr('href', 'Javascript:void(0)');
			}.bindScope(this))
			
			// Ordner- und Breadcrumblinks in Liste auswählbar machen
			jQuery(
				params.dialogContainerID + ' ' + params.classNameDirectory + ', ' +
				params.dialogContainerID + ' ' + params.classNameBreadcrumbLink
			).each( function(index, element) {
				
				jQuery(element).click( function(event) {
					
					var params = cmtPage.getData('FileSelector');
					
					// Pfad in Inputfeld übernehmen
					jQuery(params.selectedPathID).val(unescape(jQuery(event.currentTarget).attr('data-filepath')));

					// Inhalte laden
					jQuery.getJSON(
						jQuery(element).attr('data-url'),
						function (XMLHttpRequest) {
						
							// neuen Inhalt einfügen
							this.insertFileSelectorContent(XMLHttpRequest)
							
							// Links in Inhalten mit Funktionen versehen
							this.initFileSelectorContent();
							
							// Reihen-Hover-Effekt einstellen.
							this.initRowsToHover(params.dialogContainerID);
						}.bindScope(this)
		            )
					return false;
					
				}.bindScope(this));
			}.bindScope(this));

			// Tabellenkopf festnageln
/*
			var tableRow = jQuery('tbody tr', '#cmtFileSelectorContentContainer').first();
			var headRow = jQuery('thead tr', '#cmtFileSelectorContentContainer').first();
			
			//cmtFileSelectorContentContainer
			jQuery('td', tableRow).each(function (index, td) {
				jQuery(jQuery('td', headRow)[index]).width(jQuery(td).width());
			})
			
			jQuery(headRow).css({
				position: 'fixed',
				marginTop: '-' + jQuery(headRow).height() + 'px'
			});

			jQuery('table', '#cmtFileSelectorContentContainer').css('marginTop', jQuery(headRow).height());
*/
			
		},
		
		/**
		 * function insertFileSelectorContent()
		 * Fügt die per Ajax-request als JSON-Objekt erhaltenen Inhalte in die entsprechenden HTML-Container ein.
		 * 
		 * @param object requestData Object mit den Eigenschaften 'breadcrumbContent', 'directoryContent' und 'fileContent',
		 * die das Einzufügende HTML als String enthalten.
		 * 
		 *  @return void
		 */
		insertFileSelectorContent: function(requestData) {
			
			var params = cmtPage.getData('FileSelector');
			
			jQuery(params.breadcrumbContainerID).html(requestData.breadcrumbContent);
			jQuery(params.directoryContainerID).html(
				requestData.directoryContent 
				+ requestData.fileContent 
				//+ requestData.javascriptContent
			);
			
		},
		
		/**
		 * OUTDATED: function initRowsToHover()
		 * Richtet einen Hover-Effekt für alle Elemente ein, die mit dem CSS-Slektor ".cmtHover" gekennzeichnet sind. 
		 * Bei Mouseover wird eine CSS-Klasse ".cmtHovered" hinzugefügt.
		 * 
		 * @param string ID des Elements, z.B. '#tableContainer', in welchem nach den zu hovernden Kindelementen gesucht werden soll.
		 * @return void
		 */
		initRowsToHover: function(elementID) {
return;			
//			elementID = this._checkElement(elementID);
//
//			if (this._hasEvent(elementID, 'mouseover.cmtPage')) {
//				return
//			}
//			
//			jQuery(elementID).on('mouseover.cmtPage', '.cmtHover:not(.cmtIgnore)', function(event) {
//				jQuery(event.currentTarget).addClass('cmtHovered');
//			});
//			
//			jQuery(elementID).on('mouseout.cmtPage', '.cmtHover:not(.cmtIgnore)', function(event) {
//				jQuery(event.currentTarget).removeClass('cmtHovered');
//			});
		},

		/**
		 * function initRowsToSelect()
		 * Alle Elemente ein, die mit dem CSS-Slektor ".cmtSelectable" gekennzeichnet sind, werden auswählbar (z.B. Tabellenzeilen) 
		 * Bei Klick wird eine CSS-Klasse ".cmtSelected" und nach Checkboxen oder Radiobuttons gesucht, deren Wert geändert wird.
		 * 
		 * @param string ID des Elements, z.B. '#tableContainer', in welchem nach den zu hovernden Kindelementen gesucht werden soll.
		 * @return void
		 */
		initRowsToSelect: function(elementID) {
		
			elementID = this._checkElement(elementID);
			
			if (this._hasEvent(elementID, 'click.cmtPage')) {
				return
			}
			
			// Datenreihen initialisieren
			jQuery(elementID).on('click.cmtPage', '.cmtSelectable:not(.cmtIgnore)', function(event) {

				var parents = jQuery(event.target).andSelf().parentsUntil(jQuery(event.currentTarget), '.cmtSelectableDontSelect');

				if (!parents.length) {
				
					jQuery(event.currentTarget).toggleClass('cmtSelected');
					
					// Wert einer optionalen Checkbox oder eines Radiobutton anpassen
					var status = jQuery(event.currentTarget).find('.cmtSelectableStatus');
					status.prop('checked', !status[0].checked);
				}

			});
			
			// "alle auswählen"-Buttons suchen und initialisieren
			jQuery(document).on('click.cmtPage', '.cmtSelectableSelectAll', function(event) {

				jQuery('.cmtSelectable').removeClass('cmtSelected').addClass('cmtSelected');
				
				// Wert einer optionalen Checkbox oder eines Radiobutton anpassen
				jQuery('.cmtSelectable').find('.cmtSelectableStatus').prop('checked', true);
				
				return false;
			})
			
			// "auswahl entfernen"-Buttons suchen und initialisieren
			jQuery(document).on('click.cmtPage', '.cmtSelectableUnselectAll', function(event) {

				jQuery('.cmtSelectable').removeClass('cmtSelected');
				
				// Wert einer optionalen Checkbox oder eines Radiobutton anpassen
				jQuery('.cmtSelectable').find('.cmtSelectableStatus').prop('checked', false);
				
				return false;
			})

		},
		
		selectRow: function(row) {
			
			if (!jQuery(row).hasClass('cmtSelected')) {
				jQuery(row).addClass('cmtSelected');
			}
			
			// Wert einer optionalen Checkbox oder eines Radiobutton anpassen
			jQuery(row).find('.cmtSelectableStatus').prop('checked', true);

		},

		unselectRow: function(row) {
			
			if (jQuery(row).hasClass('cmtSelected')) {
				jQuery(row).removeClass('cmtSelected');
			}
			
			// Wert einer optionalen Checkbox oder eines Radiobutton anpassen
			jQuery(row).find('.cmtSelectableStatus').prop('checked', true);
		},
		
		initSetAction: function(elementID) {

			elementID = this._checkElement(elementID);

			//jQuery(document).on('click.cmtPage', '.cmtSetAction:not(.cmtIgnore)', function(event) {
			jQuery('.cmtSetAction:not(.cmtIgnore)', elementID).click(function(event) {
				var element = event.currentTarget;
			
				var actionType = jQuery(element).attr('data-action-type');
				var actionTargetID = jQuery(element).attr('data-action-target-id');

				jQuery('#'+actionTargetID).attr('value', actionType);
				return true;

			});
		},
		
		/**
		 * function initMultiSelect()
		 * initalisiert alle Select-Felder, die mit dem CSS-Selektor '.cmtFormSelectMultiple' 
		 * gekennzeichnet sind. Diese Felder werden durch das ui.multiselect-plugin ersetzt
		 * 
		 * @param void
		 * @return void
		 */
		initFormMultiSelect: function(elementID) {
			
			elementID = this._checkElement(elementID);
			
			// choose either the full version
			//jQuery(".cmtFormSelectMultiple").multiselect();
			jQuery("select[multiple=multiple]:not(.cmtIgnore)", elementID).multiselect();
			// or disable some features
			//$(".multiselect").multiselect({sortable: false, searchable: false});
			
		},

		/**
		 * function initFormSelect()
		 * initalisiert alle Select-Felder, die NICHT mit dem CSS-Selektor '.cmtFormSelectMultiple' 
		 * gekennzeichnet sind.
		 * 
		 * @param mixed Selektorname ('.cmtSelect'), ID ('#cmtSeletMe') oder Referenz auf das Element in welchem die Select-felder getauscht werden sollen.
		 * @return void
		 */
		initFormSelect: function(elementID) {
			
			elementID = this._checkElement(elementID);
			
			jQuery(elementID).find('select:not([multiple=multiple]):not(.cmtIgnore)').selectmenu({
				style:'dropdown'
			});
		},
		
		/**
		 * function initFormRadio()
		 * initalisiert Radioknöpfe (input type="radio") ACHTUNG: Die Radiobuttons müssen in einem Parent-Container 
		 * stecken, in welchem KEINE anderen input-Felder sind!
		 * gekennzeichnet sind.
		 * 
		 * @param string ID des Elements, z.B. '#tableContainer', in welchem gesucht werden soll.

		 * @return void
		 */		
		initFormRadio: function(elementID) {

			elementID = this._checkElement(elementID);
			
			jQuery('input[type=radio]:not(.cmtIgnore)', elementID).each( function(index, element) {
				
				var relatedLabel = jQuery(element).siblings('label');
				
				// Falls es kein zugehöriges Label gibt, muss eines erstellt werden
				if (relatedLabel.length == 0) {
	
					// Falls die Checkbox keine ID hat, muss eine erstellt und Checkbox und neuem Label zugeordnet werden
					if (element.id == '') {
						var time = new Date();
						jQuery(element).prop('id', 'cmtLabel_' + time.getTime());
					}
					
					var elementID = element.id;
					
					jQuery('<label for="' + elementID + '"></label>').insertAfter(element)
				}
				
				jQuery(element)
				.parent()
				.buttonset()
				.addClass('cmtButtonTypeRadio')
				.find('label')
				.removeClass('ui-corner-left')
				.removeClass('ui-corner-right');
			})

		},
		
		/**
		 * function initFormCheckbox()
		 * initalisiert Checkboxen (input type="checkbox") ACHTUNG: Die Checkboxen müssen in einem Parent-Container 
		 * stecken, in welchem KEINE anderen input-Felder sind!
		 * gekennzeichnet sind.
		 * 
		 * @param string ID des Elements, z.B. '#tableContainer', in welchem gesucht werden soll.

		 * @return void
		 */		
		initFormCheckbox: function(elementID) {

			elementID = this._checkElement(elementID);
			
			jQuery('input[type=checkbox]:not(.cmtIgnore)', elementID).each( function(index, element) {
				
				

				//jQuery(element).parent().addClass('cmtButtonTypeCheckbox')
				
				var relatedLabel = jQuery(element).siblings('label');
				
				// Falls es kein zugehöriges Label gibt, muss eines erstellt werden
				if (relatedLabel.length == 0) {

					// Falls die Checkbox keine ID hat, muss eine erstellt und Checkbox und neuem Label zugeordnet werden
					if (element.id == '') {
						var time = new Date();
						jQuery(element).prop('id', 'cmtLabel_' + time.getTime());
					}
					
					var elementID = element.id;
					
					var relatedLabel = jQuery('<label for="' + elementID + '"></label>').insertAfter(element)
				}
				
				//alert(jQuery(element).prop('checked'));
				jQuery(element)
				.parent()
				.addClass('cmtButtonTypeCheckbox')
				.buttonset()
				
//				jQuery(element).on('click', function(ev) {
//					jQuery(ev.currentTarget).parent().buttonset('refresh');
//				})
				
//				jQuery(element)
//				.parent()
//				.buttonset()
//				.addClass('cmtButtonTypeCheckbox')
//				.find('label')
//				.removeClass('ui-corner-left')
//				.removeClass('ui-corner-right');

			})
			
//			jQuery('input[type=checkbox]:not(.cmtIgnore)', elementID).each( function(index, element) {
//				
//				// unchecking f****** butonset manually
//				var checked = jQuery(element).prop('checked')
//				var relatedLabel = jQuery(element).siblings('label');
////				console.info(checked);
//				if (!checked) {
//					console.info(relatedLabel)
//					jQuery(relatedLabel).attr('aria-pressed', 'false')
//					jQuery(relatedLabel).removeClass('ui-state-active')
//					jQuery(relatedLabel).addClass('FUCK')
//				} else {
////					jQuery(relatedLabel).attr('aria-pressed', 'true')
////					jQuery(relatedLabel).removeClass('ui-state-active')
//				}
//			})
			
			
		},

		/**
		 * function initFileInputs()
		 * Initialisiert Dateiuploadfelder
		 * 

		 * @param void
		 * @return void
		 */
		initFileInputs: function(elementID) {
			
			elementID = this._checkElement(elementID);
			
			jQuery('.cmtFormCustomFileInput:not(.cmtIgnore)', elementID).each(function(index, el) {
				jQuery(el).customFileInput({
					button_position : 'right',
					optionalID: 'cmtFileInput_'+index
				})
			});
//			jQuery.customFileInput({
//				classes: '.cmtFormCustomFileInput:not(.cmtIgnore)',
//				button_position : 'right'
//			})
		},
		
		/**
		 * function initTableHeaders()
		 * Initialisert Tabellenkopfzeilen. Die Zeilen werden auch angezeigt, wenn sie eigentlich aus dem Browserfenster herausscrollen
		 * 
		 * @param string ID des Elements, z.B. '#tableContainer', in welchem gesucht werden soll.

		 * @return void
		 */
		initTableHeaders: function(elementID) {

			var elementID = this._checkElement(elementID);

			var tables = jQuery('table.cmtTable:not(.cmtIgnore)', elementID);

			if (tables.length) {
				jQuery(tables).each(function(index, element) {
				       
					var wrapper = jQuery(element).wrap('<div class="cmtTableHeaderWrapper" style="position:relative"></div>');
					
					var headerRow = jQuery("tr:first", element);
					var clonedHeaderRow = jQuery(headerRow).html();
					
					var top = 0;
					var left = parseInt(jQuery(element).css('left'));

					jQuery(wrapper).after('<table class="cmtTableFloatingHeader" style="position: fixed; z-index: 98; top: ' + top + 'px; left: ' + left + 'px; visibility: hidden;"><tr>' + clonedHeaderRow + '</tr></table>');
	
					jQuery(headerRow).addClass('cmtTableFloatingHeaderSource')
				
				});
				
				this._updateTableHeaders();
				jQuery('#cmtMainContent').scroll(this._updateTableHeaders);
				jQuery(window).resize(this._updateTableHeaders);
			}
		},

		/**
		 * function initTableHeaders()
		 * Hilfsfunktion: wird beim Scrollen und beim Ändern der Fenstergröße aufgerufen und blendet die Spaltenüberschriften ein oder aus.
		 * 
		 * @param void
		 * @return void
		 */
		_updateTableHeaders: function() {
			jQuery("div.cmtTableHeaderWrapper").each(function(index, element) {

				var sourceHeader = jQuery(".cmtTableFloatingHeaderSource", element);
				var clonedHeader = jQuery(".cmtTableFloatingHeader", element);
				var clonedHeaderCells = jQuery(".cmtTableFloatingHeader td", element);
	
				var offset = jQuery(element).offset();
				var offsetTop = offset.top - parseInt(jQuery('#cmtMainHeader').outerHeight());
//				console.info('ey: ' + offset.top + ' <=> wy ' + scrollTop)
				
				// Tabellenbreite und -position aktualisieren
				var sourceHeaderPosition = jQuery(sourceHeader).offset();

				// Spaltenbreiten aktualisieren
				jQuery(sourceHeader).find('td').each(function(index, cell) {
					jQuery(clonedHeaderCells[index]).width(jQuery(cell).width())
				});
				jQuery(clonedHeader).width(jQuery(sourceHeader).width())
				
				// Position aktualisieren
				if (offsetTop < 0) {
					jQuery(clonedHeader).css("visibility", "visible");
				} else {
					jQuery(clonedHeader).css({
						visibility: 'hidden',
					});
				}
			})
		},
		
		/**
		 * function initCodeEditor()
		 * Initialisert Texteditoren auf der Seite. Wandelt normale Textfelder in einen Texteditor auf Basis von ACE um:
		 * http://ace.ajax.org/
		 * 
		 * @param string ID des Elements, z.B. '#editorContainer', in welchem gesucht werden soll.

		 * @return void
		 */
		initCodeEditor: function(elementID) {
		
			elementID = this._checkElement(elementID);

			var editors = jQuery('.cmtEditor:not(.cmtIgnore)', elementID);

			if (editors.length) {
				
				// Editor: fullscreenmodus ermöglichen
				var dom = ace.require("ace/lib/dom")
				var commands = ace.require("ace/commands/default_commands").commands
				
				commands.push({
					name: "Toggle Fullscreen",
					bindKey: "F11",
					exec: function(editor) {
//					dom.toggleCssClass(document.body, "cmtEditorFullScreen")
					dom.toggleCssClass(editor.container, "cmtEditorFullscreen")
					editor.resize()
					}
				})
				
				// Editoren zuweisen
				jQuery(editors).each(function(index, element) {
					
					// Formularfeld verstecken
					var textArea = jQuery('textarea#' + jQuery(element).data('field'), elementID);
					jQuery(textArea).hide();
					
					// Editor starten
					var editor = ace.edit(element.id);
					
					var language = jQuery(element).data('language');
					var theme = jQuery(element).data('theme');
					
					if (!language) {
						language = 'text'
					}
					
					if (!theme) {
						theme = 'chrome'
					}

					editor.setTheme("ace/theme/" + theme);
					editor.getSession().setMode("ace/mode/" + language);
	
					// Formular-Event registrieren: Bei Speicherung die Inhalte des Editors übernehmen
					var form = jQuery(textArea).closest('form');

					jQuery(form).on('submit', function(ev) {

						var val = editor.getValue(); //.replace(/\</g, '&lt;').replace(/\>/g, '&gt;');
						jQuery(textArea).val(val);

					})
					
				})
			}
//			var editor = ace.edit("editor1");
//			editor.setTheme("ace/theme/textmate");
//			editor.getSession().setMode("ace/mode/html");
		},
		
		/**
		 * function initAutocomplete()
		 * initialisiert Autocomplete (besser "Autosuggest") -Felder.
		 * 
		 * @param string ID des Elements, z.B. '#tableContainer', in welchem gesucht werden soll.
		 * @return void
		 */
		initAutoComplete: function(elementID) {

			elementID = this._checkElement(elementID);

			// Standardeigenschaften für alle Autocomplete-Varianten
			var autocompleteBasics = {
				minLength: 2,		
				create: function() {
					var ac = jQuery(this).autocomplete('widget')
					jQuery(ac).removeClass( "ui-corner-all" ).addClass( "ui-corner-bottom" ).addClass('cmtAutocompleteList');
					jQuery(ac).find('a').removeClass( "ui-corner-all" );
				}
			};
			
			/*
			 *  Autocompletefelder suchen und zuweisen
			 */
			jQuery('.cmtAutocomplete:not(.cmtIgnore)', elementID).each(function(index, element) {

				var autocompleteData = new Object();

				// Trennzeichen definiert?
				if (typeof jQuery(element).attr('data-separator') != 'undefined') {
					var separator = jQuery(element).attr('data-separator');
				} else {
					var separator = ',';
				}
				//var regExp = new RegExp('\\' + separator + '\\s?');
				var regExp = new RegExp('\\' + separator);
				
				// Allgemein: Autocomplete mit Mehrfachauswahl
				if (typeof jQuery(element).attr('data-multiple') != 'undefined' && jQuery(element).attr('data-multiple') == "1") { 

					// Standard-Eigenschaften für Mehrfachauswahlen
					jQuery.extend(autocompleteData, {
						focus: function() {
							// prevent value inserted on focus
							return false;
						},
						select: function( event, ui ) {

//							var terms = this.value.split(/,\s*/);
							var terms = this.value.split(regExp);
							
							// remove the current input
							terms.pop();
							// add the selected item
							terms.push( ui.item.value );
							// add placeholder to get the comma-and-space at the end
							terms.push( "" );

							//this.value = terms.join( ", " );
							this.value = terms.join( separator );
							return false;
						}
					});
				} else {
					
					// etwas merkwürdiges Verhalten: einmal einem .autocomplete() zugewiesen, übernehmen alle anderen, folgenden
					// Felder mit .autocomplete() auch die vorherige, modifizierte select() funktion. Daher muss das hier wieder
					// zur Sicherheit gelöscht werden.
					jQuery.extend(autocompleteData, {
						focus: function() {},
						select: function() {}
					});
				}
				
				
				// 1. Diverse Autocompletes über Ajax-Request
				if (typeof jQuery(element).attr('data-url') != 'undefined') {

					jQuery.extend(autocompleteData, {
						source: function( request, response ) {
							
							// Für Einfach- wie Mehrfachauswahl gleich: Wert ermitteln
							var lastTerm = jQuery.trim(request.term.split( regExp ).pop());

							// Woher Feldnamen beziehen?
							if (jQuery('#'+jQuery(this.element).attr('data-field-id')).length) {
								
								// Feldnamen aus anderem Auswahlfeld
								var field = jQuery('#'+jQuery(this.element).attr('data-field-id')).prop('value');
							} else if (jQuery(this.element).attr('data-field-name')) {
								
								// Feldname wir separat in Attribut "data-field-name" definiert
								var field = jQuery(this.element).attr('data-field-name');
							} else {
								
								// Ansonsten: Name des aktiven Feldes senden
								var field = jQuery(this.element).attr('name');
							}
							
							jQuery.ajax({
								url: jQuery(this.element).attr('data-url'),
								dataType: "json",
								data: {
									action: jQuery(this.element).attr('data-action'),
									suggestSearchField: field,
									suggestSearchValue: lastTerm
								},
								success: function( data ) {
									response(data);
								}
							});
						}
					});
				}
				
				// 2. Autocomplete über Liste
				if (typeof jQuery(element).attr('data-list') != 'undefined') {
					
					var autocompleteValues = jQuery(element).attr('data-list').split( regExp );
				
					jQuery.extend(autocompleteData, {
						source: autocompleteValues
					});
					
					// Mehrfachauswahl?
					if (typeof jQuery(element).attr('data-multiple') != 'undefined' && jQuery(element).attr('data-multiple') == "1") {

						jQuery.extend(autocompleteData, {
							//minLength: 0,
							source: function (request, response) {

								// delegate back to autocomplete, but extract the last term
//								var lastTerm = request.term.split(/,\s*/).pop()

								var lastTerm = request.term.split( regExp ).pop()

								response (jQuery.ui.autocomplete.filter(autocompleteValues, lastTerm));
							}
						})
					}
				}
				
				// Individuelle Eigenschaften mit Standardeigenschaften verbinden
				autocompleteData = jQuery.extend(autocompleteBasics, autocompleteData);
				
				// Autocomplete zuweisen => fertig
				jQuery(element)	
				.bind( "keydown", function( event ) {
					// don't navigate away from the field on tab when selecting an item
					if ( event.keyCode === $.ui.keyCode.TAB &&
							$( this ).data( "autocomplete" ).menu.active ) {
						event.preventDefault();
					}
				})
				.autocomplete(autocompleteData);

			});
		 },

		/**
		 * function initForm()
		 * Initialisiert gekennzeichnete Formulare: beim Absenden wird ein Overlay eingeblendet, damit das Formular nicht zweimal abgesendet werden kann.
		 * 
		 * @param string ID des Elements, z.B. '#tableContainer', in welchem gesucht werden soll.
		 * @return void
		 */
		initForm: function(elementID) {

			elementID = this._checkElement(elementID);
			jQuery('.cmtForm:not(.cmtIgnore)', elementID).each( function (index, form) {
				jQuery(form).on('submit', function(ev) {
					this.showOverlay();
					jQuery(document.body).css('cursor', 'wait');
				}.bindScope(this));
			}.bindScope(this))
		 },
		 
		 initMainNavigation: function() {
			jQuery('.cmtMainNavigationToggler').on('click.cmtNavigation', function(ev) {
				jQuery('body').toggleClass('st-menu-open');
				ev.stopPropagation();
			}.bindScope(this));
			
			jQuery('body').on('click.cmtNavigation', function(ev) {
				
				var el = ev.target;
				
				if (jQuery('body').hasClass('st-menu-open') && !jQuery(el).closest('#cmtMainNavigation').length) {
					jQuery('body').removeClass('st-menu-open');
				};
			});
			
			jQuery('#cmtMainNavigation').extendedAccordion();
		 },
		
		 
		 /**
		  * function initProgressBars()
		  * Inits progress bars on an page.
		  * 
		  * @param elementID ID of parent element in which the progress bars should be initialized
		  * @return void 
		  */
		 initProgressBars: function(elementID) {

			var parentElement = this._checkElement(elementID);

			jQuery('.cmtProgressbar:not(.cmtIgnore)', parentElement).each(function (index, bar) {
				
				var barLabel = jQuery('.cmtLabel', bar);
				var label = null;

				if (jQuery(bar).data('label-value')) {
					
					var label = function () {
						
						var entity = '';
						if (jQuery(bar).data('label-value-entity')) {
							entity = jQuery(bar).data('label-value-entity');
						}
						barLabel.text(jQuery(bar).progressbar('value') + entity);
						
						// check label width and bar width
//						var labelOffsetPosition = jQuery(barLabel).offset()
//						var labelOffset = jQuery(barLabel).width() + labelOffsetPosition.left;
//						var indicatorWidth = jQuery('.ui-progressbar-value', bar).width();

//						if (indicatorWidth > labelOffset) {
//							jQuery(bar).addClass('cmtInvertLabel');
//						}
						
						if (jQuery(bar).progressbar('value') > 50) {
							jQuery(bar).addClass('cmtInvertLabel');
						} else {
							jQuery(bar).removeClass('cmtInvertLabel');
						}
//						console.info(jQuery(bar).progressbar('value'));
					}
				}

				jQuery(bar).progressbar({
					create: label,
					change: label
				});
				
			});
		},
		 
		 
		/**
		 * function showOverlay()
		 * Zeigt ein Overlay an.
		 * 
		 * @param void
		 * @return void
		 */
		showOverlay: function() {
			if (this.overlay) {
				return;
			}
			this.overlay = jQuery('<div class="ui-widget-overlay"> </div>');
			jQuery(this.overlay).appendTo("body");
		},

		/**
		 * function hideOverlay()
		 * Verbirgt ein Overlay.
		 * 
		 * @param void
		 * @return void
		 */
		hideOverlay: function() {
			if (!this.overlay) {
				return;
			}
			jQuery(this.overlay).remove();
			this.overlay = null;
		},
		
		showDialogOverlay: function(dialogID) {
			
			var dialogContentHeight = jQuery(dialogID).height();
			
			jQuery(dialogID).css({
				position: 'relative',
				overflow: 'hidden'
			})
			
			jQuery(dialogID).prepend('<div class="cmtDialogOverlay cmtInProgress" style="position: absolute; width: 100%; height: ' + dialogContentHeight + 'px; z-index: 999999;"></div>')
		},
		
		hideDialogOverlay: function(dialogID) {
			if (typeof dialogID == 'undefined') {
				dialogID = document;
			}

			jQuery(dialogID).find('.cmtDialogOverlay').remove();
		},

		
		initTooltips: function(parentElement) {
			
			parentElement = this._checkElement(parentElement);

		},
		
		_checkElement: function(elementID) {
			if (jQuery(elementID).length == 0) {
				return document
			} else {
				return jQuery(elementID);
			}
		},
		
		_hasEvent: function(element, event) {
			
			var parts = event.split('.');
			var eventName = parts[0];
			var namespace = parts[1];
			
			var elementEvents = jQuery._data(element, 'events');
			if (!elementEvents) {
				return false
			}
			
			var elementEvent = elementEvents[eventName];

			if (!namespace && !jQuery.isEmptyObject(elementEvent)) {
				
				return true;
			} else if (namespace && !jQuery.isEmptyObject(elementEvent)) {
				
				for (i in elementEvent) {
					if (elementEvent[i].namespace == namespace) {
						return true;
					}
				}
				
				return false
			}

			return false;
		},
		
		/**
		 * function setData()
		 * Speichert Daten in einem zentralen Objekt "data" des cmtPage-Objektes. 
		 * Kann zu Zwischenspeicherung für Daten in anderen Objekten genutzt werden.
		 * 
		 * @param object params Objekt mit zu speichernden Daten Eigenschaftenname/Wert
		 * @param string nameSpace Optionaler aber dringend zu empfehlender Parameter: Wenn 
		 * hier z.B. "FileSelector" angegeben wird, wird das Datenobjekt unter 
		 * cmtPage.data.FileSelector gespeichert.
		 * 
		 * @return void
		 */
		setData: function(params, nameSpace) {
			if (typeof nameSpace != 'undefined') {
				var addData ={}
				addData[nameSpace] = params;
				cmtPage.data = jQuery.extend(true, cmtPage.data, addData);
			} else {
				cmtPage.data = jQuery.extend(cmtPage.data, params);
			}
		},
		
		/**
		 * function getData()
		 * Liefert die in cmtPage.data gespeicherten Daten zurück.
		 * 
		 * @param string param1 Name des Namespaces oder der Variable, die zurückgegeben werden soll.
		 * @param string param2 Name der Variable in einem mit param1 angegebenen Namespace.
		 * 
		 * @return mixed gefundene Variable
		 */
		getData: function(param1, param2) {
			
			if (arguments.length > 1) {
				if (typeof cmtPage.data[param1] != 'undefined' && typeof cmtPage.data[param1][param2] != 'undefined') {
					return cmtPage.data[param1][param2];
				} else {
					return undefined;
				}
			} else {
				if (typeof cmtPage.data[param1] != 'undefined') {
					return cmtPage.data[param1];
				} else {
					return undefined;
				}
			}
		},

		/**
		 * function isInProgress()
		 * Add the progress indicator from an object.
		 * 
		 * @param Object el Element that should be "in progress"
		 * @return void
		 * 
		 */
		isInProgress: function(el) {

			el = jQuery(el);
			
			if (!el.length) {
				return;
			}
			jQuery(el).addClass('cmt-in-progress');

			var tag = jQuery(el).prop('tagName');

			switch(tag.toLowerCase()) {
				case 'button':
					jQuery(el).attr('disabled', true);
					break;
			}
			
		},
		
		/**
		 * function isRelaxed()
		 * Remove the progress indicator from an object.
		 * 
		 * @param Object el Element that is "in progress"
		 * @return void
		 * 
		 */
		isRelaxed: function(el) {
			
			el = jQuery(el);
			
			if (!el.length) {
				return;
			}
			
			jQuery(el).removeClass('cmt-in-progress');
			
			var tag = jQuery(el).prop('tagName');
			
			switch(tag.toLowerCase()) {
				case 'button':
					jQuery(el).attr('disabled', false);
					break;
			}
		},
		
	}
	
	cmtPage.initialize()