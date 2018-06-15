/**
 * cmt_administration_functions.js
 * Zentrale Javascript-Datei für navigationsframe im Administrationsbereich
 *
 * @version 2011-02-27
 */

	CMT = new Hash();
	CMT.Accordion = new Class({
		
		/**
		 * function initialize()
		 * Konstruktor
		 * 
		 * @param object params Objekt mit Paramterern, die das Verhalten des Menüs regeln (überschreiben Default-Paramater)
		 * @return void
		 */
		initialize: function(params) {

			if (typeof params != 'Object') {
				params = {};
			}
			
			this.params = {
				openAllButtonSelector: '.cmtOpenAllAccordionElements',
				closeAllButtonSelector: '.cmtCloseAllAccordionElements',
				toggleAccordionElementSelector: '.cmtToggleAccordionElement',
				togglerEventName: 'click',
				pinEventName: 'click',
				closeAllEventName: 'click',
				openAllEventName: 'click',
				togglerClass: 'cmtMenuToggler',
				contentClass: 'cmtMenuContent',
				pinClass: 'cmtMenuPin',
				elementOpenClass: 'cmtMenuIsOpen',
				pinnedClass: 'cmtMenuIsPinned',
				openElementWhenInit: 0,
				pinElementsWhenInit: [],
				effectOpenOptions: {
					duration: 500
				},
				effectCloseOptions: {
					duration: 250
				}
			};
			this.params = Object.merge(this.params, params);

			this.currentElementIndex = null;
			
			window.addEvent('domready', this.initNavigation.bind(this));
		},

		/**
		 * function initNavigation()
		 * Methode initalisiert das Menü nachdem das DOM der Seite geladen ist.
		 * 
		 * @param object ev Objekt vom Typ "Event", wird von MooTools übergeben.
		 */
		initNavigation: function(ev) {
			
			this.togglers = $$('.'+this.params.togglerClass);
			this.contents = $$('.'+this.params.contentClass);
			this.pins = $$('.'+this.params.pinClass);
			
			// Toggler erstellen
			this.togglers.each(this.initToggler.bind(this));
			
			// Pins erstellen
			this.pins.each(this.initPin.bind(this));
			
			// Menüsteuerung erstellen
			// alle Menüs öffnen
			if (this.params.openAllButtonSelector) {
				$$(this.params.openAllButtonSelector).each( function(el, index) {
					el.addEvent(this.params.openAllEventName, this.openAllElements.bind(this))
				}, this)
			}

			// alle Menüs schließen
			if (this.params.closeAllButtonSelector) {
				$$(this.params.closeAllButtonSelector).each( function(el, index) {
					el.addEvent(this.params.closeAllEventName, this.closeAllElements.bind(this))
				}, this)
			}
			
			// Muss bei Initalisierung ein Menü gepinnt oder angezeigt werden?
			if (this.params.pinElementsWhenInit.length) {
				
				this.params.pinElementsWhenInit.each( function(togglerIndex) {
					this.pinElement(this.togglers[togglerIndex]);
				}, this);
				
			} else if (this.params.openElementWhenInit != null) {
				this.openElement(this.togglers[this.params.openElementWhenInit]);
			}

//			var Collapsable = new Class({
//				Extends: Fx.Reveal,
//				initialize: function(clicker, section, options) {
//					this.clicker = $(clicker).addEvent('click', this.toggle.bind(this));
//					this.parent($(section), options);
//				}
//			});
//			
//			new Collapsable($('layerHandle_1'), $('layerContent_1'));
		},
		
		/**
		 * function initToggler()
		 * Initialisiert die für das Auf- und Zuklappen verantwortliche Schaltfläche des Menüs.
		 * 
		 * @param HTML-Element el Referenz auf das HTML-Element welches als "Toggler" (Handle) des Menüs dient.
		 * @return void 
		 */
		initToggler: function(el) {
			el.addEvent(this.params.togglerEventName, this.toggleElement.bind(this));

			var index = this.getTogglerIndex(el);
			var content = this.contents[index];
			
			// Ist Element von vornherein offen?
			if (!el.hasClass(this.params.elementOpenClass)) {
				content.hide();
			}

		},

		/**
		 * function initPin()
		 * Initialisiert die für das Feststellen ("pinnen") verantwortliche Schaltfläche des Menüs.
		 * 
		 * @param HTML-Element el Referenz auf das HTML-Element welches als "Pin" des Menüs dient.
		 * @return void 
		 */
		initPin: function(el) {
			el.addEvent(this.params.pinEventName, this.togglePin.bind(this));
		},

		/**
		 * function toggleElement()
		 * Event-Aufruf: Klappt ein Menü je nach vorherigem Zustand auf oder zu.
		 * 
		 * @param Object ev Das von MooTools übergebene Event-Objekt. Diese Methode wird vom Event des Toogle-Elements (-knopfes) aufgerufen
		 * @return void 
		 */
		toggleElement: function(ev) {

			var toggler = ev.target;

			if (!toggler.hasClass(this.params.togglerClass)) {
				toggler = toggler.getParent('.'+this.params.togglerClass);
			}

			if (toggler.hasClass(this.params.pinnedClass)) {
				return false;
			}
			
			if (this.currentElementIndex !== null) {
				this.closeElement(this.togglers[this.currentElementIndex]);
			}
			
			if (toggler.hasClass(this.params.elementOpenClass)) {
				this.closeElement(toggler);
			} else {
				this.openElement(toggler);
			}
		},

		/**
		 * function closeElement()
		 * Schließt ein Menüelement.
		 * 
		 * @param HTML-Element toggler Referenz auf das HTML-Element welches als "Toggler" des Menüs dient.
		 * @return void 
		 */
		closeElement: function(toggler) {
		
			if (toggler.hasClass(this.params.pinnedClass)) {
				return false;
			}
			
			var index = this.getTogglerIndex(toggler);
			this.currentElementIndex = index;
			var content = this.contents[index];
			
			content.dissolve(this.params.effectCloseOptions)
			toggler.removeClass(this.params.elementOpenClass);
		},

		/**
		 * function openElement()
		 * Öffnet ein Menüelement.
		 * 
		 * @param HTML-Element toggler Referenz auf das HTML-Element welches als "Toggler" des Menüs dient.
		 * @return void 
		 */
		openElement: function(toggler) {
			
			var index = this.getTogglerIndex(toggler);
			this.currentElementIndex = index;
			var content = this.contents[index];
			
			content.reveal(this.params.effectOpenOptions)
			toggler.addClass(this.params.elementOpenClass);
		},

		/**
		 * function togglePin()
		 * Event-Aufruf: Pinnt ein Menü je nach vorherigem Zustand an oder ab.
		 * 
		 * @param Object ev Das von MooTools übergebene Event-Objekt. Diese Methode wird vom Event des Pin-Elements (-knopfes) aufgerufen
		 * @return void 
		 */
		togglePin: function(ev) {
			
			ev.stop();
			
			var pin = ev.target;
			var index = this.getPinIndex(pin);
			var toggler = this.togglers[index];
			
			if (!toggler.hasClass(this.params.pinnedClass)) {
				this.pinElement(toggler)
			} else {
				this.unpinElement(toggler);
			}
		},

		/**
		 * function pinElement()
		 * Pinnt ein Menüelement fest. Ist das Menüelement geschlossen, wird es geöffnet.
		 * 
		 * @param HTML-Element toggler Referenz auf das HTML-Element welches als "Toggler"(!) des Menüs dient.
		 * @return void 
		 */
		pinElement: function(toggler) {
			
			var index = this.getTogglerIndex(toggler);
			if (index != this.currentElementIndex) {
				this.openElement(toggler);
			}
			
			toggler.addClass(this.params.pinnedClass);
			
		},

		/**
		 * function upinElement()
		 * Löst ein festgepinntes Menüelement wieder. Das Element wird geschlossen, wenn es nicht das aktuell geöffnete ist.
		 * 
		 * @param HTML-Element toggler Referenz auf das HTML-Element welches als "Toggler"(!) des Menüs dient.
		 * @return void 
		 */
		unpinElement: function(toggler) {
			toggler.removeClass(this.params.pinnedClass);
			
			var index = this.getTogglerIndex(toggler);
// TODO: Testen, da stimmt was nicht!			
			if (index != this.currentElementIndex && toggler.hasClass(this.params.elementOpenClass)) {
				this.closeElement(toggler);
			}
		},

		/**
		 * function openAllElements()
		 * Öffnet alle Menüelemente.
		 * 
		 * @param void
		 * @return void 
		 */
		openAllElements: function() {
			this.togglers.each( function(toggler, index) {
				this.openElement(toggler);
			}, this);
			
			this.currentElementIndex = -1;
		},

		/**
		 * function closeAllElements()
		 * Schließt alle Menüelemente.
		 * 
		 * @param void
		 * @return void 
		 */
		closeAllElements: function() {
			this.togglers.each( function(toggler, index) {
				this.closeElement(toggler);
			}, this);
			
			this.currentElementIndex = -1;
		},

		/**
		 * function getTogglerIndex()
		 * Hilfsmethode: Gibt die Index-Nummer des übergebenen Toggler-Elements zurück.
		 * 
		 * @param HTML-Element toggler Referenz auf das HTML-Element des "Togglers"
		 * @return void 
		 */
		getTogglerIndex: function(toggler) {
			return parseInt(this.togglers.indexOf(toggler));
		},
		
		/**
		 * function getPinIndex()
		 * Hilfsmethode: Gibt die Index-Nummer des übergebenen Pin-Elements zurück.
		 * 
		 * @param HTML-Element pin Referenz auf das HTML-Element des "Pin"
		 * @return void 
		 */
		getPinIndex: function(pin) {
			return parseInt(this.pins.indexOf(pin));
		}
	});
	
	cmtAccordion = new CMT.Accordion();
	

