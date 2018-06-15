 /*
 * jQuery UI selectmenu
 *
 * Copyright (c) 2009 AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * http://docs.jquery.com/UI
 */

(function($) {

$.widget("ui.selectmenu", {
	
	defaultOptions: {
		minWidth: 52
	},
	
	_init: function() {

		// Falls Select-Feld bereits ausgeblendet, dann nicht nochmal ersetzen. Ggf. dies mit einem Selector ".cmtIsChanged" lösen 
		if (jQuery(this.element).css('display') == 'none') {
			return false;
		}
		
		var self = this;
		var o = jQuery.extend(this.defaultOptions, this.options);

		// IDs aus den Select-IDs erzeugen oder neue erzeugen 
		if (!this.element.attr('id')) {
			var date = new Date()
			var elementID = date.getTime();
		} else {
			var elementID = this.element.attr('id') 
		}
		this.ids = [elementID + '-' + 'select', elementID + '-' + 'menu'];
	
		//define safe mouseup for future toggling
		this._safemouseup = true;
		
		//create menu button wrapper
		this.newelement = $('<a class="'+ this.widgetFullName +' ui-widget ui-state-default ui-corner-all" id="'+this.ids[0]+'" role="button" href="#" aria-haspopup="true" aria-owns="'+this.ids[1]+'"></a>')
			.insertAfter(this.element);
	
		//transfer tabindex
		var tabindex = this.element.attr('tabindex');
		if(tabindex){
			this.newelement.attr('tabindex', tabindex);
		}
		
		//save reference to select in data for ease in calling methods
		this.newelement.data('selectelement', this.element);
		
		//menu icon
		this.selectmenuIcon = $('<span class="'+ this.widgetFullName +'-icon ui-icon"></span>')
			.addClass( (o.style == "popup")? 'ui-icon-triangle-2-n-s' : 'ui-icon-triangle-1-s' )
			.wrap('<div class="'+ this.widgetFullName +'-icon-wrapper" />').parent()
			.prependTo(this.newelement);
			
		//make associated form label trigger focus
		$('label[for='+this.element.attr('id')+']')
			.attr('for', this.ids[0])
			.bind('click', function(){
				self.newelement[0].focus();
				return false;
			});	

		//click toggle for menu visibility
		//this.newelement

		this.selectmenuIcon
			.bind('mousedown', function(event){
				self._toggle(event);
				//make sure a click won't open/close instantly
				if(o.style == "popup"){
					self._safemouseup = false;
					setTimeout(function(){self._safemouseup = true;}, 300);
				}	
				return false;
			})
			.bind('click',function(){
				return false;
			})
			
			/*
			.keydown(function(event){
				var ret = true;
				switch (event.keyCode) {
					case $.ui.keyCode.ENTER:
						ret = true;
						break;
					case $.ui.keyCode.SPACE:
						ret = false;
						self._toggle(event);	
						break;
					case $.ui.keyCode.UP:
					case $.ui.keyCode.LEFT:
						ret = false;
						console.info("up")
						self._moveSelection(-1);
						break;
					case $.ui.keyCode.DOWN:
					case $.ui.keyCode.RIGHT:
						ret = false;
						self._moveSelection(1);
						break;	
					case $.ui.keyCode.TAB:
						ret = true;
						break;	
					default:
						ret = false;
						self._typeAhead(event.keyCode, 'mouseup');
						break;	
				}
				return ret;
			}) */
			.bind('mouseover focus', function(){ 
				$(this).addClass(self.widgetFullName+'-focus ui-state-hover'); 
			})
			.bind('mouseout blur', function(){  
				$(this).removeClass(self.widgetFullName+'-focus ui-state-hover'); 
			});
		
		/*
		this.newelement	
		.keydown(function(event){
			var ret = true;
			switch (event.keyCode) {
				case $.ui.keyCode.ENTER:
					ret = true;
					break;
				case $.ui.keyCode.SPACE:
					ret = false;
					self._toggle(event);	
					break;
				case $.ui.keyCode.UP:
				case $.ui.keyCode.LEFT:
					ret = false;
					console.info("up")
					self._moveSelection(-1);
					break;
				case $.ui.keyCode.DOWN:
				case $.ui.keyCode.RIGHT:
					ret = false;
					self._moveSelection(1);
					break;	
				case $.ui.keyCode.TAB:
					ret = true;
					break;	
				default:
					ret = false;
					self._typeAhead(event.keyCode, 'mouseup');
					break;	
			}
			return ret;
		}) 
		*/
		
		//document click closes menu
		$(document)
			.mousedown(function(event){
				self.close(event);
			});

		//change event on original selectmenu
		this.element
			.click(function(){ this._refreshValue(); })
			.focus(function(){ this.newelement[0].focus(); });
		
		//create menu portion, append to body
		var cornerClass = (o.style == "dropdown")? " ui-corner-bottom" : " ui-corner-all"
		this.list = $('<ul class="' + self.widgetFullName + '-menu ui-widget ui-widget-content'+cornerClass+'" aria-hidden="true" role="listbox" aria-labelledby="'+this.ids[0]+'" id="'+this.ids[1]+'"></ul>')
		.appendTo('body');				
		
		//serialize selectmenu element options	
		var selectOptionData = [];
		this.element
			.find('option')
			.each(function(){
				selectOptionData.push({
					value: $(this).attr('value'),
					text: self._formatText(jQuery(this).text()),
					selected: $(this).attr('selected'),
					classes: $(this).attr('class'),
					parentOptGroup: $(this).parent('optgroup').attr('label'),
					disabled: $(this).attr('disabled'),
					style:  $(this).attr('style')
				});
			});		
		
		//active state class is only used in popup style
		var activeClass = (self.options.style == "popup") ? " ui-state-active" : "";

		// RegExp to clean up optgroup titles
		var optGroupNameRegExp = new RegExp('[:{}()?+.*\\s\\[\\]]', 'g');
		
		//write li's
		for(var i in selectOptionData){
			
			// default settings
			// is disabled?
			var thisLi = $('<li role="presentation"><a href="#" tabindex="-1" role="option" aria-selected="false">'+ selectOptionData[i].text +'</a></li>');
			
			if (!selectOptionData[i].disabled) {
				jQuery(thisLi)
					.mouseup(function(event){
						if(self._safemouseup){
							var changed = $(this).data('index') != self._selectedIndex();
							self.value($(this).data('index'));
							self.select(event);
							if(changed){ self.change(event); }
							self.close(event,true);
						}
						return false;
					})
					.bind('mouseover focus', function(){ 
						self._selectedOptionLi().addClass(activeClass); 
						self._focusedOptionLi().removeClass(self.widgetFullName+'-item-focus ui-state-hover'); 
						$(this).removeClass('ui-state-active').addClass(self.widgetFullName + '-item-focus ui-state-hover'); 
					})
					.bind('mouseout blur', function(){ 
						if($(this).is( self._selectedOptionLi() )){ $(this).addClass(activeClass); }
						$(this).removeClass(self.widgetFullName + '-item-focus ui-state-hover'); 
					});
			} else {
				
				jQuery(thisLi)
					.addClass('ui-disabled')
				jQuery('a', thisLi)
					.addClass('ui-disabled')
			}
			
			jQuery(thisLi)
				.data('index',i)
				.addClass(selectOptionData[i].classes)
				.data('optionClasses', selectOptionData[i].classes|| '')
				.click(function(){
					return false;
				})
				.attr('style', selectOptionData[i].style)

			

			
			//optgroup or not...
			if(selectOptionData[i].parentOptGroup) {
				
				var optGroupName = self.widgetFullName + '-group-' + selectOptionData[i].parentOptGroup.replace(optGroupNameRegExp, '-');
				if(this.list.find('li.' + optGroupName).size()){
					this.list.find('li.' + optGroupName + ':last ul').append(thisLi);
				}
				else{
					$('<li role="presentation" class="'+self.widgetFullName+'-group '+optGroupName+'"><span class="'+self.widgetFullName+'-group-label">'+selectOptionData[i].parentOptGroup+'</span><ul></ul></li>')
						.appendTo(this.list)
						.find('ul')
						.append(thisLi);
				}
			}
			else{
				thisLi.appendTo(this.list);
			}
			
			//this allows for using the scrollbar in an overflowed list
			this.list.bind('mousedown mouseup', function(){return false;});
			
			//append icon if option is specified
			if(o.icons){
				for(var j in o.icons){
					if(thisLi.is(o.icons[j].find)){
						thisLi
							.data('optionClasses', selectOptionData[i].classes + ' ' + self.widgetFullName + '-hasIcon')
							.addClass(self.widgetFullName + '-hasIcon');
						var iconClass = o.icons[j].icon || "";
						
						thisLi
							.find('a:eq(0)')
							.prepend('<span class="'+self.widgetFullName+'-item-icon ui-icon '+iconClass + '"></span>');
					}
				}
			}
		}	
		

		
		//save reference to actionable li's (not group label li's)
		this._optionLis = this.list.find('li:not(.'+ self.widgetFullName +'-group)');
				
		//transfer menu click to menu button
		this.list
			.keydown(function(event){
				var ret = true;
				switch (event.keyCode) {
					case $.ui.keyCode.UP:
					case $.ui.keyCode.LEFT:
						ret = false;
						//self._moveFocus(-1);
						self._moveSelection(-1, event);
						break;
					case $.ui.keyCode.DOWN:
					case $.ui.keyCode.RIGHT:
						ret = false;
						//self._moveFocus(1);
						self._moveSelection(1, event);
						break;	
					case $.ui.keyCode.HOME:
						ret = false;
						self._moveFocus(':first');
						break;	
					case $.ui.keyCode.PAGE_UP:
						ret = false;
						self._scrollPage('up');
						break;	
					case $.ui.keyCode.PAGE_DOWN:
						ret = false;
						self._scrollPage('down');
						break;
					case $.ui.keyCode.END:
						ret = false;
						self._moveFocus(':last');
						break;			
					case $.ui.keyCode.ENTER:
					case $.ui.keyCode.SPACE:
						ret = false;
						self.close(event,false);
						self.newelement.blur()
						//$(event.target).parents('li:eq(0)').trigger('mouseup');
						break;		
					case $.ui.keyCode.TAB:
						ret = true;
						self.close(event,true);
						break;	
					case $.ui.keyCode.ESCAPE:
						ret = false;
						self.close(event,true);
						break;	
					default:
						/*
						ret = false;
						self._typeAhead(event.keyCode,'focus'); */
						break;
				}
				return ret;
			});
			
		//selectmenu style
		if(o.style == 'dropdown'){
			this.newelement
				.addClass(self.widgetFullName+"-dropdown");
			this.list
				.addClass(self.widgetFullName+"-menu-dropdown");	
		}
		else {
			this.newelement
				.addClass(self.widgetFullName+"-popup");
			this.list
				.addClass(self.widgetFullName+"-menu-popup");	
		}
		
		//append status span to button
		var spanText = '';
		if (typeof selectOptionData[this._selectedIndex()] != 'undefined') {
			var spanText = selectOptionData[this._selectedIndex()].text;
		}

		this.selectmenuStatus = this.newelement.prepend('<span class="'+self.widgetFullName+'-status">'+ spanText +'</span>');
		// Original: this.selectmenuStatus = this.newelement.prepend('<span class="'+self.widgetFullName+'-status">'+ selectOptionData[this._selectedIndex()].text +'</span>');

		//add corners to top and bottom menu items
		this.list.find('li:last').addClass("ui-corner-bottom");
		if(o.style == 'popup'){ this.list.find('li:first').addClass("ui-corner-top"); }
		
		//transfer classes to selectmenu and list
		if(o.transferClasses){
			
			var transferClasses = this.element.attr('class') || ''; 
			this.newelement.add(this.list).addClass(transferClasses);
		}
		
		// Breite des neuen Selectfeldes bestimment
		var selectWidth =  parseInt(this.element.css('width'));
		var minWidth = parseInt(this.element.css('min-width'));
		var maxWidth = parseInt(this.element.css('max-width'));

		if (o.width) {
			
			// per Javascript übergebene Breite verwenden
			selectWidth = o.width;
		} 
		else if (selectWidth){
			
			if (selectWidth < parseInt(o.minWidth)) {

				selectWidth = parseInt(o.minWidth);
			}
		} else {
			// Breite ermitteln, sofern keine CSS-Angaben im Originalfeld
			// dazu kommt es aber nie, da jQuery.css() immer einen Wert liefern (computedStyle)
			
			var firstLiContent = this.list.find('li:first a').html();
			var firstLiCopy = jQuery('<span class="' + self.widgetFullName+'-status" style="display: inline; white-space: nowrap">' + firstLiContent + '</span>').appendTo('body')
			var selectWidth = jQuery(firstLiCopy).outerWidth(true)
			jQuery(firstLiCopy).remove();
			
			selectWidth += parseInt(jQuery(this.newelement).css('border-left-width'))
				+ parseInt(jQuery(this.newelement).css('border-right-width'))
				+ 1
				
		}

		// Prüfen, ob Minimalbreite unterschritten
		if (minWidth && selectWidth < minWidth) {
			selectWidth = minWidth;
		}

		// Prüfen, ob Maximalbreite überschritten
		if (maxWidth && selectWidth > maxWidth) {
			selectWidth = maxWidth;
		}
		
		this.newelement.width( selectWidth );


		//this.newelement.width( (o.width) ? o.width : selectWidth);
		
		//set menu width to either menuWidth option value, width option value, or select width 
		if(o.style == 'dropdown') {
			
			var menuWidth = this.list.outerWidth();

			if (o.menuWidth) {
				menuWidth = o.menuWidth;
			} else if (menuWidth < selectWidth) {
				menuWidth = selectWidth;
			}

			// this.list.width( (o.menuWidth) ? o.menuWidth : ((o.width) ? o.width : selectWidth));
			this.list.width(menuWidth);
		} else {
			this.list.width( (o.menuWidth) ? o.menuWidth : ((o.width) ? o.width - o.handleWidth : selectWidth - o.handleWidth));
		}	

		//set max height from option 
		if(o.maxHeight && o.maxHeight < this.list.height()){ this.list.height(o.maxHeight); }	
		

		// Eingabefeld erzeugen
		this.selectmenuEnterValue = jQuery('<input />', {
			type: "text",
			id: this.ids[0]+'-input-value',
			class: 'ui-selectmenu-input-value'
		})
// TODO: Das hier nochmal testen!	
		// Aus Breite der Auswahlanzeige die Breite des Inputfeldes berechnen. 
		var selectmenuStatusDOM = jQuery(this.newelement).find('.' + self.widgetFullName+'-status');
		var enterValueWidth = jQuery(this.selectmenuStatus).width() 
			- parseInt(jQuery(selectmenuStatusDOM).css('padding-left'))
			- parseInt(jQuery(selectmenuStatusDOM).css('padding-right'))
			- parseInt(jQuery(selectmenuStatusDOM).css('margin-left'))
			- parseInt(jQuery(selectmenuStatusDOM).css('margin-right'));
		
		this.selectmenuEnterValue.css({
			width: enterValueWidth + 'px',
			display: 'none'
		});
		jQuery(this.selectmenuEnterValue).appendTo(this.newelement);

		this.selectmenuEnterValue
		.bind('click',function(){
			return false;
		})
// Per Cursortaste nach unten ins Dropdown zu wechseln funktioniert leider irgendwie nicht
//		.bind('keydown', function(event) {
//			var key = event.keyCode;
//			
//			if (key == $.ui.keyCode.UP || key == $.ui.keyCode.DOWN) {
//				this.blur();
//				self.list.css({borderColor: 'red'})
//				console.info(jQuery(self.list).keydown())
//				jQuery(self.list).trigger('keydown', event); //_moveFocus(':first');
//				return false;
//			}
//		})
		.bind('keyup', function(event) {
			self._filterListElements(this.value);
		})
		.bind('keydown', function(event) {
			if (event.keyCode == $.ui.keyCode.ENTER) {
				return false;
			}
		})

		this.selectmenuStatus
		.bind('mousedown', function(event){
			self._showTextInput()
//			self._toggle(event);
//			//make sure a click won't open/close instantly
//			if(o.style == "popup"){
//				self._safemouseup = false;
//				setTimeout(function(){self._safemouseup = true;}, 300);
//			}
//			
//			this.focus();
			
			return false;
		})
		
		// Events für Select-Menü:
		// 1. submit onchange
		if (this.element.data('submit-onchange')) {
			this.element.bind('change', function(ev) {
				jQuery(ev.currentTarget).closest('form').submit();
			})
		}
		
		//hide original selectmenu element
		this.element.hide();
		
		//transfer disabled state
		if(this.element.attr('disabled') == true){ this.disable(); }
		
		//update value
		this.value(this._selectedIndex());
	},
	
	destroy: function() {
		this.element.removeData(this.widgetName)
			.removeClass(this.widgetFullName + '-disabled' + ' ' + this.namespace + '-state-disabled')
			.removeAttr('aria-disabled');
	
		//unbind click on label, reset its for attr
		$('label[for='+this.newelement.attr('id')+']')
			.attr('for',this.element.attr('id'))
			.unbind('click');
		this.newelement.remove();
		this.list.remove();
		this.element.show();	
	},
	
	_typeAhead: function(code, eventType){
		var self = this;
		//define self._prevChar if needed
		if(!self._prevChar){ self._prevChar = ['',0]; }
		var C = String.fromCharCode(code);
		c = C.toLowerCase();
		var focusFound = false;
		function focusOpt(elem, ind){
			focusFound = true;
			$(elem).trigger(eventType);
			self._prevChar[1] = ind;
		};
		this.list.find('li a').each(function(i){	
			if(!focusFound){
				var thisText = $(this).text();
				if( thisText.indexOf(C) == 0 || thisText.indexOf(c) == 0){
						if(self._prevChar[0] == C){
							if(self._prevChar[1] < i){ focusOpt(this,i); }	
						}
						else{ focusOpt(this,i); }	
				}
			}
		});
		this._prevChar[0] = C;
	},
	
	_uiHash: function(){
		return {
			value: this.value()
		};
	},
	
	open: function(event){
		var self = this;
		var disabledStatus = this.newelement.attr("aria-disabled");

		if(disabledStatus != 'true'){
			
			this.newelement
				.addClass('ui-state-active');
			
			this._closeOthers(event);
			this._refreshPosition();
			
			this.list
				.appendTo('body')
				.addClass(self.widgetFullName + '-open')
				.attr('aria-hidden', false)
				.find('li:not(.'+ self.widgetFullName +'-group):eq('+ this._selectedIndex() +') a')[0].focus();	
			
			if (this.options.style == "dropdown") {
				this.newelement.removeClass('ui-corner-all').addClass('ui-corner-top');
			}	

//			if (this.options.style == "dropdown") {
//				if (orientation == 'down') {
//					this.newelement.removeClass('ui-corner-all').addClass('ui-corner-top');
//				} else {
//					this.newelement.removeClass('ui-corner-all').addClass('ui-corner-bottom');
//				}
//			}
//			console.info(orientation);
			
			this._trigger("open", event, this._uiHash());
		}
	},
	
	close: function(event, retainFocus){
		if(this.newelement.is('.ui-state-active')){
			this.newelement
				.removeClass('ui-state-active');
			this.list
				.attr('aria-hidden', true)
				.removeClass(this.widgetFullName+'-open');
			
			if (this.options.style == "dropdown"){
				this.newelement.removeClass('ui-corner-top').removeClass('ui-corner-bottom').addClass('ui-corner-all');
			}
			
			this._hideTextInput();
			this.orientation = null;
			
			if(retainFocus){
				this.newelement[0].focus();
			}
			//event.stopPropagation();

			this._trigger("close", event, this._uiHash());
			
		}
	},
	
	change: function(event) {
		this.element.trigger('change');
		this._trigger("change", event, this._uiHash());
	},
	
	select: function(event) {
		this._trigger("select", event, this._uiHash());
	},

	menuIsActive: function() {
		if (this.newelement.is('.ui-state-active')) {
			return true;
		} else {
			return false;
		}
	},
	
	_showTextInput: function() {
		this.selectmenuEnterValue.show();
		
		if (!this.menuIsActive()) {
			this.open();
		}
		
		this.selectmenuEnterValue.focus();

	},
	
	_hideTextInput: function() {
		this.selectmenuEnterValue.hide();
		this.selectmenuEnterValue.blur();
		
		this.selectmenuEnterValue.prop('value', '');
		
		jQuery(this._optionLis).show()
		
	},
	
	_filterListElements: function(valuePart) {

		var regExp = new RegExp(valuePart, 'i');

		jQuery(this._optionLis).filter(function(index) {
			
//			if (!jQuery(this).is(':visible')) {
//				jQuery(this).hide()
//				return
//			}
		
			if (!jQuery(this).find('a').html().match(regExp)) {
				jQuery(this).hide()
			} else {
				jQuery(this).show()
			}
		});
		
		this._refreshPosition();
	},
	
	
	_closeOthers: function(event){
		$('.'+ this.widgetFullName +'.ui-state-active').not(this.newelement).each(function(){
			$(this).data('selectelement').selectmenu('close',event);
		});
		$('.'+ this.widgetFullName +'.ui-state-hover').trigger('mouseout');
	},

	_toggle: function(event,retainFocus){
		if(this.list.is('.'+ this.widgetFullName +'-open')){ this.close(event,retainFocus); }
		else { this.open(event); }
	},

	_formatText: function(text){
		return this.options.format ? this.options.format(text) : text;
	},

	_selectedIndex: function(){
		return this.element[0].selectedIndex;
	},

	_selectedOptionLi: function(){
		return this._optionLis.eq(this._selectedIndex());
	},

	_focusedOptionLi: function(){
		return this.list.find('.'+ this.widgetFullName +'-item-focus');
	},

	_moveSelection: function(amt, event){
		var listLength = this._optionLis.length;
		
		var currIndex = parseInt(this._selectedOptionLi().data('index'), 10);		
		var newIndex = currIndex + amt;
		
		if (newIndex < 0) {
			newIndex = listLength-1
			return false;	// diese Zeile auskommentieren, wenn nach erstem Wert wieder der letzte angezeigt werden soll 
		}
		
		if (newIndex >= listLength) {
			newIndex = 0
			return false;	// diese Zeile auskommentieren, wenn nach letztem Wert wieder der erste angezeigt werden soll
		}
		
		//this._optionLis.eq(newIndex);
		this.value(jQuery(this._optionLis.eq(newIndex)).data('index'))

		this.select(event);
		this.change(event);

		return true;
		//return this._optionLis.eq(newIndex).trigger('mouseup');
	},

	_moveFocus: function(amt){
		if(!isNaN(amt)){
			var currIndex = parseInt(this._focusedOptionLi().data('index'), 10);
			var newIndex = currIndex + amt;
		}
		else { var newIndex = parseInt(this._optionLis.filter(amt).data('index'), 10); }
		
		if(newIndex < 0){ newIndex = 0; }
		if(newIndex > this._optionLis.size()-1){
			newIndex =  this._optionLis.size()-1;
		}
		var activeID = this.widgetFullName + '-item-' + Math.round(Math.random() * 1000);
		
		this._focusedOptionLi().find('a:eq(0)').attr('id','');
		this._optionLis.eq(newIndex).find('a:eq(0)').attr('id',activeID)[0].focus();
		this.list.attr('aria-activedescendant', activeID);
	},
	
	_scrollPage: function(direction){
		var numPerPage = Math.floor(this.list.outerHeight() / this.list.find('li:first').outerHeight());
		numPerPage = (direction == 'up') ? -numPerPage : numPerPage;
		this._moveFocus(numPerPage);
	},
	
	_setData: function(key, value) {
		this.options[key] = value;
		if (key == 'disabled') {
			this.close();
			this.element
				.add(this.newelement)
				.add(this.list)
					[value ? 'addClass' : 'removeClass'](
						this.widgetFullName + '-disabled' + ' ' +
						this.namespace + '-state-disabled')
					.attr("aria-disabled", value);
		}
	},
	
	value: function(newValue) {
		if (arguments.length) {
			this.element[0].selectedIndex = newValue;
			this._refreshValue();
			this._refreshPosition();
		}
		return this.element[0].selectedIndex;
	},
	
	_refreshValue: function() {
		var activeClass = (this.options.style == "popup") ? " ui-state-active" : "";
		var activeID = this.widgetFullName + '-item-' + Math.round(Math.random() * 1000);
		//deselect previous
		this.list
			.find('.'+ this.widgetFullName +'-item-selected')
			.removeClass(this.widgetFullName + "-item-selected" + activeClass)
			.find('a')
			.attr('aria-selected', 'false')
			.attr('id', '');
		//select new
		this._selectedOptionLi()
			.addClass(this.widgetFullName + "-item-selected"+activeClass)
			.find('a')
			.attr('aria-selected', 'true')
			.attr('id', activeID);
			
		//toggle any class brought in from option
		var currentOptionClasses = this.newelement.data('optionClasses') ? this.newelement.data('optionClasses') : "";
		var newOptionClasses = this._selectedOptionLi().data('optionClasses') ? this._selectedOptionLi().data('optionClasses') : "";
		this.newelement
			.removeClass(currentOptionClasses)
			.data('optionClasses', newOptionClasses)
			.addClass( newOptionClasses )
			.find('.'+this.widgetFullName+'-status')
			.html( 
				this._selectedOptionLi()
					.find('a:eq(0)')
					.html() 
			);
			
		this.list.attr('aria-activedescendant', activeID)
	},
	
	_refreshPosition: function(){
		
		if (!this.menuIsActive) {
			return
		}
		
		//set left value
		this.list.css('left', this.newelement.offset().left);
		
		//set top value: dropdown
//		var menuTop = this.newelement.offset().top + this.newelement.height();

		// Top-Position abhängig von der Scrollposition ermitteln
		var viewportBottomPos = jQuery(document).scrollTop() + jQuery(window).height();
		var listHeight = jQuery(this.list).height();
		var selectPos = jQuery(this.newelement).offset().top;
		var selectHeight = jQuery(this.newelement).height();
		var documentHeight = jQuery(document).height();
		
		console.info(selectPos, listHeight, viewportBottomPos)
		
		switch (this.orientation) {
			case 'down':
				var menuTop = selectPos + selectHeight
				this.newelement.removeClass('ui-corner-all').addClass('ui-corner-top');
				this.list.removeClass('ui-corner-all').removeClass('ui-corner-bottom').addClass('ui-corner-bottom');
				break;
				
			case 'up':
				var menuTop = selectPos - listHeight;
				this.newelement.removeClass('ui-corner-all').addClass('ui-corner-bottom');
				this.list.removeClass('ui-corner-all').removeClass('ui-corner-bottom').addClass('ui-corner-top');
				break;
				
			default:
				
				if (selectPos + listHeight < viewportBottomPos) {
					// Menü + Dropdownliste passen auf Bildschirm (nach unten ist genug Platz):
					// Dropdown normal nach unten öffnen
					var menuTop = selectPos + selectHeight
					this.orientation = 'down';
					
				} else {
					// Menü + Dropdownliste passen nicht auf Bildschirm (nach unten ist nicht genug Platz):
					// Dropdown nach oben öffnen
					var menuTop = selectPos - listHeight;
					this.orientation = 'up';

				}
				break;
		}

		// set top value: popup
		var scrolledAmt = this.list[0].scrollTop;
		
		this.list.find('li:lt('+this._selectedIndex()+')').each(function(){
			scrolledAmt -= $(this).outerHeight();
		});
		
		if(this.newelement.is('.'+this.widgetFullName+'-popup')){
			menuTop+=scrolledAmt; 
			this.list.css('top', menuTop); 
		} else { 
			this.list.css('top', menuTop+'px');
		}
//		console.info(this.orientation)
		return;
	}
});

$.extend($.ui.selectmenu, {
	getter: "value",
	version: "@VERSION",
	eventPrefix: "selectmenu",
	defaults: {
		transferClasses: true,
		style: 'dropdown', 
		width: null, 
		menuWidth: null, 
		handleWidth: 26, 
		maxHeight: null,
		icons: null, 
		format: null,
		minWidth: 52
	}
});

})(jQuery);