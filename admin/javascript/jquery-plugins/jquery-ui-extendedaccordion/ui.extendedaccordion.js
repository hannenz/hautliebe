/**
 * widget ui.extendedaccordion.js
 * jQuery UI Widget: creates an accordion menu with functionalities out of an element.
 * 
 * @version 2014-11-10
 * @author J.Hahn <info@contento-mat.de>
 * 
 */
(function(jQuery) {

	jQuery.widget('cmt.extendedAccordion', {
 

		//These options will be used as defaults
		options: {
			accordionSelector: '.cmtExtendedAccordion',
			elementSelector: '.cmtExtendedAccordionElement',
			elementID: 'cmtExtendedAccordionElement_',
			handleSelector: '.cmtExtendedAccordionHandle',
			handleID: 'cmtExtendedAccordionHandle_',
			contentSelector: '.cmtExtendedAccordionContent',
			contentID: 'cmtExtendedAccordionContent_',
			pinSelector: '.cmtExtendedAccordionPin',
			menuID: 'cmtMainNavigation',
			elementOpenClass: 'cmtExtendedAccordionElementIsOpen',
			elementPinnedClass: 'cmtExtendedAccordionElementIsPinned',
			

			openEffect: 'blind',
			openEffectSpeed: 300,
			openEffectOptions: {direction: 'vertical'},
			openEffectCallback: function() {},
			
			closeEffect: 'blind',
			closeEffectSpeed: 300,
			closeEffectOptions: {direction: 'vertical'},
			closeEffectCallback: function() {},			

			openAllSelector: '.cmt-icon-open-all',
			closeAllSelector: '.cmt-icon-close-all',
			
			openElementWhenInit: 0,
			pinElementsWhenInit: []

		},
		
		currentElement: null,
		pinnedElements: [],
		closeAllNextClick: true,
		cookiePrefix: '',
 

		/**
		 * function _create()
		 * Methode initalisiert das Widget/ Menü
		 * 
		 * @param void
		 * @return void
		 */
		_init: function(d) {

			// Cookie-Präfix erstellen
			if (this.element.id) {
				this.cookeName = this.element.id + '_';
			} else {
				this.cookeName = 'extendedAccordion_';	
			}
			
			// Cookie für angepinnte Elemente auslesen und entserialisieren
			var pE = this._unserializeArray(jQuery.cookie(this.cookiePrefix + 'pinnedElements'));
			if (typeof pE == 'object') {
				this.pinnedElements = pE;
			}
			
			// Accordion-Menüelemente ('elements') initialisieren
			this.elements = jQuery(this.element).find(this.options.elementSelector);
			this.elements.each(this.initElement.bind(this));
			
			// Navigationssteuerung initialisieren
			jQuery(this.options.openAllSelector).click( function(ev) {
				this.openAllElements();
			}.bind(this));
			
			jQuery(this.options.closeAllSelector).click( function(ev) {
				this.closeAllElements();
			}.bind(this));
			
			// ggf. erstes Fenster öffnen, wenn ein currentElement im cookie gespeichert ist oder
			// wenn kein Element gepinnt und ein Default-Element (Index) angegeben ist.
			if (!this.currentElement) {
				this.currentElement = jQuery.cookie(this.cookiePrefix + 'currentElement');
			}
	
//			if (!this.currentElement && jQuery(this.element).find('.' + this.options.elementPinnedClass).length == 0) {
//				this.currentElement = this.elements[this.options.openElementWhenInit];
//			}
			
			if (this.currentElement) {
				this.openElement(this.currentElement);
			}

		},
		

		/**
		 * function initHandles()
		 * Initialisiert die für das Auf- und Zuklappen verantwortliche Schaltfläche des Menüs.
		 * 
		 * @param HTML-Element el Referenz auf das HTML-Element welches als "Toggler" (Handle) des Menüs dient.
		 * @return void 
		 */
		initElement: function(index, el) {
			
			// ggf. dem Element ID zuweisen
			if (typeof el.id == 'undefined' || !el.id) {
				jQuery(el).attr('id', this.options.elementID + index);
			}
			
			var content = this.findContent(el);
			
			// Inhalt verstecken, wenn nicht geöffnet
			if (content && !jQuery(el).hasClass(this.options.elementOpenClass)) {
				jQuery(content).hide();
			}

			// Ist element angepinnt?
			if (this.pinnedElements.indexOf(el.id) != -1) {
				this.pinElement(el);
			}
		
			// Klick-Event für Handle
			jQuery(el).find(this.options.handleSelector).click(function(ev) {
				this.toggleElement(ev);
			}.bind(this));
			
			// Klick-Event für Pin
			jQuery(el).find(this.options.pinSelector).click(function(ev) {
				ev.stopPropagation();
				this.togglePinElement(ev);
			}.bind(this));
		},

		/**
		 * function toggleElement()
		 * Event-Aufruf: Klappt ein Menü je nach vorherigem Zustand auf oder zu.
		 * 
		 * @param Object ev Das von MooTools übergebene Event-Objekt. Diese Methode wird vom Event des Toogle-Elements (-knopfes) aufgerufen
		 * @return void 
		 */
		toggleElement: function(ev) {

			var handle = ev.currentTarget;
			var element = this.findElement(handle);
			
			if (this.closeAllNextClick) {
				this.closeAllElements();
				this.currentElement = element;
				this.closeAllNextClick = false;
			}
			
			if (jQuery(element).hasClass(this.options.elementPinnedClass)) {
				return false;
			}
			
			if (jQuery(element).hasClass(this.options.elementOpenClass)) {
				this.closeElement(element);
			} else {
				this.openElement(element);
			}
		},

		/**
		 * function closeElement()
		 * Schließt ein Menüelement.
		 * 
		 * @param HTML-Element toggler Referenz auf das HTML-Element welches als "Toggler" des Menüs dient.
		 * @return void 
		 */
		closeElement: function(element) {

			element = this._getReference(element);
			
			if (jQuery(element).hasClass(this.options.elementPinnedClass) || !jQuery(element).hasClass(this.options.elementOpenClass)) {
				return false;
			}
			
			var content = this.findContent(element);

			jQuery(content).stop().hide(this.options.closeEffect, this.options.closeEffectOptions, this.options.closeEffectSpeed, this.options.closeEffectCallback);
			jQuery(element).removeClass(this.options.elementOpenClass);
			
			jQuery.cookie(this.cookiePrefix + 'currentElement', '');
		},

		/**
		 * function openElement()
		 * Öffnet ein Menüelement.
		 * 
		 * @param HTML-Element toggler Referenz auf das HTML-Element welches als "Toggler" des Menüs dient.
		 * @return void 
		 */
		openElement: function(element) {

			element = this._getReference(element);
		
			if (this.currentElement && this.currentElement != element) {
				this.closeElement(this.currentElement);
				this.currentElement = element;
				jQuery.cookie(this.cookiePrefix + 'currentElement', element.id);
			}

			
			var content = this.findContent(element);
			
			jQuery(content).css('visibility', 'visible');
		
			jQuery(element).addClass(this.options.elementOpenClass);			
			jQuery(content).show(this.options.openEffect, this.options.openEffectOptions, this.options.openEffectSpeed, this.options.openEffectCallback);
			
		},

		
		/**
		 * function openAllElements()
		 * Öffnet alle Menüelemente.
		 * 
		 * @param void
		 * @return void 
		 */
		openAllElements: function() {
			
			this.currentElement = null;
			
			jQuery(this.elements).each( function(index, element) {
				
				if (!jQuery(element).hasClass(this.options.elementOpenClass)) {
					this.openElement(element);
				}
			}.bind(this));
			
			this.closeAllNextClick = true;
		},

		/**
		 * function closeAllElements()
		 * Schließt alle Menüelemente außer dem aktuell geöffneten.
		 * 
		 * @param currentElement mixed ID oder Referenz auf das aktuell geöffnete Element. 
		 * @return void 
		 */
		closeAllElements: function() {

			jQuery(this.elements).each( function(index, element) {
				
				if (!jQuery(element).hasClass(this.options.elementPinnedClass)) {
					this.closeElement(element);
				}
			}.bind(this));

		},
		
		
		/**
		 * OUTDATED!? function hideElement()
		 * Blendet ein Menüelement ohne Effekt aus.
		 * 
		 * @param object element Referenz auf das Accordieonmenüelement
		 * @param void
		 */
		hideElement: function(element) {
return;			
			var content = this.findContent(element);

			jQuery(content).css({
				visibility: 'hidden'
			});

			
		},
		
		/**
		 * function togglePinElement()
		 * Methode wird aufgerufen, wenn auf das Symbol zum Festpinnen des Menüs geklickt wird. Je nach ZUstand wird 
		 * die Methode pinelement() oder unpinelement() aufgerufen.
		 * 
		 * @params mixed ID, Referenz oder Event des Pin-Elements
		 * @return void 
		 */
		togglePinElement: function(pin) {
			
			element = this.findElement(pin);
			
			if (jQuery(element).hasClass(this.options.elementPinnedClass)) {
				this.unpinElement(element);
			} else {
				this.pinElement(element);
			}
		},
		
		/**
		 * function pinElement()
		 * "Pinnt" ein Menü fest. D.h., es ist immer geöffnet.
		 * 
		 * @params mixed ID, Referenz oder Event des Pin-Elements
		 * @return void 
		 */
		pinElement: function(element) {
			
			element = this._getReference(element);
			var content = this.findContent(element);
			
			jQuery(element).addClass(this.options.elementPinnedClass);
			
			if (!jQuery(content).is(':visible')) {
				this.openElement(element);
			}
			
			// Als Cookie speichern
			if (this.pinnedElements.indexOf(element.id) == -1) {
				this.pinnedElements.push(element.id);
				jQuery.cookie(this.cookiePrefix + 'pinnedElements', this._serializeArray(this.pinnedElements));
			}
		},

		/**
		 * function unpinElement()
		 * Entfernt den "angepinnten" Status eines Menüs und schließt es wieder.
		 * 
		 * @params mixed element ID, Referenz oder Event des Pin-Elements
		 * @return void 
		 */
		unpinElement: function(element) {
			
			element = this._getReference(element);
			var content = this.findContent(element);
			
			jQuery(element).removeClass(this.options.elementPinnedClass);
			
			if (jQuery(content).is(':visible')) {
				this.closeElement(element);
			}
			
			// als Cookie speichern
			if (this.pinnedElements.indexOf(element.id) != -1) {
				
				for (var i=0; i<this.pinnedElements.length; i++) { 
				
					if (this.pinnedElements[i] == element.id) {
						this.pinnedElements.splice(i,1); 
					}
				} 
				
				jQuery.cookie(this.cookiePrefix + 'pinnedElements', this._serializeArray(this.pinnedElements));
			}

		},
		
		/**
		 * function _getReference()
		 * Ermittelt das referenz-Objekt je nach übergebenem Wert.
		 * 
		 * @params mixed el ID, Referenz oder Event des Elements
		 * @return object Referenz aus das gesuchte Objekt 
		 */
		_getReference: function(el) {
			
			var paramType = typeof el;
			
			// wurde nur eine ID übergeben?
			if (paramType == 'string') {
				el = el.replace(/#/, '');
				
				return jQuery('#' + el);
			}
			
			// wurde eine Referenz übergeben?
			if (paramType == 'object') {

				// Ist das Objekt ein jQuery-Event?
				if (typeof el.currentTarget != 'undefined') {
					
					// ja, Element aus Objekt zurückgeben
					return el.currentTarget;
				} else {
					
					// nein, dann Objekt wie bekommen zurückgeben
					return el;
				}
			}
			
			// Wert ungültig, dann null zurückgeben
			return null;
			
		},
		
		/**
		 * function _serializeArray()
		 * Wandelt ein Array in ein durch Pipes "|" getrennten String zur Speicherung in Cookies um
		 * 
		 * @param array Array mit den Daten
		 * @return string Zeichenkette mit serialisierten Daten
		 */
		_serializeArray: function(array) {
			
			if (typeof array != 'object') {
				return '';
			}
			
			return array.join('|');
		},

		/**
		 * function _unserializeArray()
		 * Wandelt einen mit _serializeArray() serialisierten String in ein Array um
		 * 
		 * @param string Serialisierter String
		 * @return array Datenarray
		 */
		_unserializeArray: function(string) {

			if (typeof string != 'string') {
				return [];
			}
			
			return string.split('|');
		},
		
		/**
		 * function findContent()
		 * Liefert eine Referenz auf den Inhaltscontainer des Menüs zurück
		 * 
		 * @params mixed element ID, Referenz oder Event des Menüelementes
		 * @return object 
		 */
		findContent: function(element) {
			element = this._getReference(element);
			return jQuery(element).find(this.options.contentSelector);
		},

		/**
		 * function findElement()
		 * Liefert eine Referenz auf das Menüelement zurück
		 * 
		 * @params mixed element ID, Referenz oder Event auf ein DOM-Objekt innerhalb des Menüelementes
		 * @return object 
		 */
		findElement: function(element) {
			element = this._getReference(element);
			return jQuery(element).closest(this.options.elementSelector)[0];
		},

		/**
		 * function findPin()
		 * Liefert eine Referenz auf den "Pin"-Container des Menüs zurück
		 * 
		 * @params mixed element ID, Referenz oder Event des Menüelementes
		 * @return object 
		 */
		findPin: function(element) {
			element = this._getReference(element);
			return jQuery(element).find(this.options.pinSelector);
		}
		

/*	
		// Use the _setOption method to respond to changes to options
		_setOption: function( key, value ) {
		
		// In jQuery UI 1.8, you have to manually invoke the _setOption method from the base widget
		jQuery.Widget.prototype._setOption.apply( this, arguments );
		// In jQuery UI 1.9 and above, you use the _super method instead
		this._super( "_setOption", key, value );

		},
 
		// Use the destroy method to clean up any modifications your widget has made to the DOM

		destroy: function() {

			// In jQuery UI 1.8, you must invoke the destroy method from the base widget
			jQuery.Widget.prototype.destroy.call( this );
			
			// In jQuery UI 1.9 and above, you would define _destroy instead of destroy and not call the base method
		}
	*/
	});
}(jQuery));