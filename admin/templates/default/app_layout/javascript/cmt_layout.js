/**
 * cmt_layout.js
 * Main Javascript for Content-o-mat's layout mode.
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2016-10-28
 * 
 */
var CMTLayout = {
	
	buttonPrefix: '.cmt-button-',
	menuClass: '.cmt-context-menu',
	objectClass: '.cmt-object',
	objectVisibilityClass: '.cmt-object-visible',
	objectInvisibilityClass: '.cmt-object-not-visible',
	elementClass: '.cmt-element',
	imageClass: '.cmt-element-image',
	groupClass: '.cmt-group',
	tempLinkClass: '.cmt-temp-link',
	linkClass: '.cmt-link',
	linkWrapperClass: '.cmt-link-wrapper',
	wrapperClass: '.cmt-element-wrapper',
	imageWrapperClass: '.cmt-image-wrapper',
	extendedContextMenuClass: '.cmt-menu-extended',
	mainButtonsContainerClass: '.cmt-buttons-main',
	charButtonsContainerClass: '.cmt-buttons-chars',
	blockButtonsContainerClass: '.cmt-buttons-block',
	imageButtonsContainerClass: '.cmt-buttons-image',
	handleBarClass: '.cmt-handle-bar',
	selectedClass: '.cmt-selected',
	selectedImageClass: '.cmt-selected',
	visibleClass: '.cmt-visible',
	deletedClass: '.cmt-deleted',
	hoverClass: '.cmt-hover',
	depthClass: '.cmt-level-1',
	footerID: '#cmt-footer',
	previewButtonID: '#cmt-preview-page',
	saveButtonID: '#cmt-save-page',
	previewClass: '.cmt-preview',
	newObjectClass: '.cmt-new-object',
	pageContent: {},
	cmtPageID: 0,
	cmtLanguage: '',
	tempTemplateID: null,
	scriptNr: 1,
	htmlEditorNr: 1,
	htmlEditorSettings: {},
	dontFocus: false,

	initialize: function() {
		jQuery(document).on('ready.CMTLayout', this.init.bind(this));
		this.initBasics();
	},
	
	/**
	 * function init()
	 * General initialization method after a page is loaded
	 * 
	 * @param {Object} ev jQuery event object 
	 */
	init: function(ev) {

		// inti page
		this.initPage();
		
		// init objects
		jQuery(this.objectClass).each(function(index, el) {
			this.initObject(el);
		}.bindScope(this));
	},
	
	/**
	 * function initBasics()
	 * Place to init essential Javascript things. Is executed right after the var CMTLayout is created (before page load)
	 * 
	 * @param void
	 * @return void
	 */
	initBasics: function() {
		
		// bind the functions scope to 'scope'
		Function.prototype.bindScope = function(scope) {
			var _function = this;

			return function() {
				return _function.apply(scope, arguments);
			};
		};
		
		// modify jQuery's replaceWith() function to return the new object's reference 
		$.fn.replaceWithPush = function(a) {
		    var $a = $(a);

		    this.replaceWith($a);
		    return $a;
		}
	},

	/**
	 * function initPage()
	 * Does all the initialization things to make the layout mode run: Insert menu, prepare layout objects, attach events and more.
	 * 
	 * @param void
	 * @return void
	 * 
	 */
	initPage: function() {
		
		// add CMT selector to body
		jQuery('body').addClass('cmt-layout');
		
		// init menu
		CMTLayoutMenu.init();
				
		// init droppable cols
		this.initDragability();
	
		// TODO: move to method "receiveFocus"???? YES! TODO!
		jQuery('body').on('click.CMTLayout', function(ev) {
			
			// workaround for Google Chrome: The browser don't stops propagation or fires this body event 
			// allthough propagation is stopped in context menus buttons.
			var target = jQuery(ev.target)
			
			// if (target.hasClass('cmt-flat-button') || target.hasClass(this.objectClass.substring(1))) { // makes other troubles :-(
			if (target.hasClass(this.objectClass.substring(1))) {
				return;
			}
			
			// select parent object when clicking in an editable area
			this.hideContextMenu(ev);

			this.receiveFocus(ev.currentTarget, ev);
		
			
		}.bindScope(this));
			
		// init object select
		this.initControls();
		
		// init page function keys
		this.initFunctionKeys();
		
		// init "normal" links (because method CMTLayout::receiveFocus() prevents default behavior of elements, so links don't work) 
		jQuery('a:not(.cmt-link)').on('click', function(ev) {
			location.href = jQuery(this).attr('href');
		});
	},
	
	// TODO: move to CMTLayoutMenu!!!
	initControls: function() {
		
		jQuery(this.previewButtonID).on('click.cmtLayout', function(ev) {
			ev.preventDefault();
			jQuery(document.body).toggleClass(this.previewClass.substring(1));
			
			// TODO: move to own function "findSelectedElement()"
			var el = this.getObject(jQuery(this.selectedClass));
			CMTLayout.hideContextMenu({currentTarget: el});
		}.bindScope(this));
		
		jQuery(this.newObjectClass).draggable({
			connectToSortable: this.groupClass,
			helper: "clone",
			revert: "invalid",
			containment: 'window',
			scroll: true,			// scroll and scrollSensitivity don't work in this context !?
			scrollSensitivity: 100,
			appendTo: 'body',
			start: function(ev, ui) {
				jQuery('body').addClass('cmt-dragging');
			}.bindScope(this),
			stop: function(ev, ui) {
				
				jQuery('body').removeClass('cmt-dragging');
				jQuery('.cmt-group').removeClass('cmt-hover');
				
				this.insertNewObject();
				
			}.bindScope(this)
			
		});

		jQuery(this.saveButtonID).on('click.cmtLayout', function(ev) {
			ev.preventDefault();
			CMTLayout.receiveFocus(ev.currentTarget, ev)
			this.savePage();
		}.bindScope(this));
		
	},

	/**
	 * function initObject()
	 * Initializes a single layout object and it's elements (texts, images, ...)
	 * 
	 * @param {Object} el Object / reference to a DOM-element type 'layout object'
	 * @return void
	 */
	initObject: function(el) {

		this.initObjectElements(el);
		this.initPasteing(el);
		this.initKeys(el);
		this.initContextMenu(el);
		this.initLinks(el);
		this.initHandleBar(el);
		this.initLayers(el);
	},	
	
	initLayers: function(el) {
		
		jQuery('.cmt-layer', el).each(function(index, layer) {
			
			jQuery('.cmt-layer-handle', layer).on('click', function(handle) {
				jQuery(layer).toggleClass('cmt-open');
			}.bindScope(this))
			
			if (jQuery(layer).data('cmt-layer-open')) {
				jQuery('.cmt-layer-content', layer).trigger('click');
			}
		}.bindScope(this));
	},
	
	/**
	 * function initHandleBar()
	 * Initializes the handle bar and its buttons of every single layout object
	 * 
	 * @param {Object} el Object / reference to a DOM-element type 'layout object'
	 * @return void
	 */
	initHandleBar: function(el) {

		// toggle visibility
		jQuery(this.buttonPrefix + 'visibility', el).on('click.cmtLayout', function(ev) {

			var button = ev.currentTarget;
			var object = jQuery(button).closest(this.objectClass);

			jQuery(object).toggleClass(this.objectVisibilityClass.substring(1));
			jQuery(object).toggleClass(this.objectInvisibilityClass.substring(1));
			
		}.bindScope(this));

		// duplicate object
		jQuery(this.buttonPrefix + 'duplicate', el).on('click.cmtLayout', function(ev) {
		
			var button = ev.currentTarget;
			var object = jQuery(button).closest(this.objectClass);

			this.confirmObjectActionDialog({
				object: object,
				action: 'duplicate',
				confirm: this.duplicateObject.bindScope(this)
			});
			
			this.receiveFocus(object);
			ev.stopPropagation();
			
		}.bindScope(this));
		
		// delete object
		jQuery(this.buttonPrefix + 'delete', el).on('click.cmtLayout', function(ev) {

			var button = ev.currentTarget;
			var object = jQuery(button).closest(this.objectClass);

			this.receiveFocus(object);
			ev.stopPropagation();
			
			this.confirmObjectActionDialog({
				object: object,
				action: 'delete',
				confirm: function(object) {
					jQuery(object).addClass(this.deletedClass.substring(1));
				}.bindScope(this)
			});
		
		}.bindScope(this));
			
		// show object informations in main menu
		jQuery(this.buttonPrefix + 'settings', el).on('click.cmtLayout', function(ev) {
			var button = ev.currentTarget;
			var object = jQuery(button).closest(this.objectClass);

			this.receiveFocus(object, ev);
			//ev.stopPropagation();
			
			this.showObjectInformations(object);
			this.tempTemplateID = jQuery(object).data('cmt-object-template-id');

		}.bindScope(this));
	},

	/**
	 * function initDragability()
	 * Initializes the drag and drop functionality of layout groups(!)
	 * 
	 * @param void
	 * @return void
	 * 
	 */
	initDragability: function() {

		jQuery(this.groupClass).sortable ({
			tolerance: 'pointer',
			scroll: true,
			handle: this.handleBarClass,
			connectWith: this.groupClass,
			start: function(ev, ui) {
				
				jQuery('body').addClass('cmt-dragging');
				
				// if dragged element is "new object"-icon then quit
				if (jQuery(ui.item).hasClass('cmt-new-object')) {
					return;
				}
				
				jQuery(ui.helper).addClass(CMTLayout.depthClass.substring(1));
				CMTLayout.receiveFocus(ui.helper);
				jQuery('#cmt-content').css('overflow-x', 'hidden');
				document.activeElement.blur();

			},
			over: function(ev, ui) {
				jQuery(this).addClass(CMTLayout.hoverClass.substring(1));
			},
			
			receive: function(ev, ui) {
				jQuery(this).removeClass(CMTLayout.hoverClass.substring(1));
				jQuery(ui.item).removeClass(CMTLayout.depthClass.substring(1));

				CMTLayout.receiveFocus(jQuery(ui.item).find('.cmt-object-content-wrapper'))
			},
			update: function(ev, ui) {
			},

			stop: function(ev, ui) {
				
				jQuery('body').removeClass('cmt-dragging');
				
				jQuery(ui.item).removeClass(CMTLayout.depthClass.substring(1));
				ev.stopImmediatePropagation();
				jQuery('#cmt-content').css('overflow-x', 'auto');
			},
			
			out: function(ev, ui) {
				jQuery(this).removeClass(CMTLayout.hoverClass.substring(1));
			}
		});
		
		jQuery(this.groupClass).droppable({
			//accept: this.objectClass + ',' + '.cmt-new-object',
			accept: '.cmt-new-object',
			//activeClass: 'cmt-selected',
			over: function(ev,ui) {
				jQuery(this).addClass('cmt-hover');
			},
			out: function(ev,ui) {
				jQuery(this).removeClass('cmt-hover');
			}				
		})
	},
	
	initImageElement: function(elementWrapper) {
		
		var elementType = jQuery(elementWrapper).data('element-type');
		var elementNr = jQuery(elementWrapper).data('element-nr');
		var menuType = jQuery(elementWrapper).data('menu-type');
//		var elementNode = jQuery(elementWrapper).closest('.cmt-element-wrapper');
		var image = jQuery('img', elementWrapper);
//console.info(elementNode);
//console.info(elementWrapper);
		jQuery(image).data(jQuery(elementWrapper).data());



		// if is placeholder, then disable resizing

		if (jQuery(elementWrapper).data('cmt-is-placeholder') == 1) {

			// first add resizabillity
			this.makeImageResizable(image);
			// and then disable it for placeholder images
			jQuery(image).resizable('disable');
			
			this.setPlaceHolder(jQuery(image).parent());
		} else {

			// first set the image source attribute
			jQuery(image).attr('src', jQuery(image).data('cmt-image-path'));
			
			// and then init resizability
			this.makeImageResizable(image);
		}

		// jquery ui creates a wrapping div around the image tag. This div ist the "image" reference from now on!
		var elementNode = jQuery(image).closest('.ui-wrapper');

		if (jQuery(elementNode).parent().is('a')) {

			// get outer link and remove it's contents
			var aWrapper = jQuery(elementNode).closest('.cmt-link').clone(true);
			
			// is a it ugly, but no click event handlers work on the link. Don't know why.
			jQuery(aWrapper).attr('href', 'Javascript:void(0);');
			jQuery(aWrapper).html('');
			
			// remove link wrapper and link
			var link = jQuery(elementNode).closest('.cmt-link');
			
			//jQuery(link).children().first().children().first().unwrap();
			jQuery(link).children().first().unwrap();
			
			// jQuery(elementNode).children().first().wrap(aWrapper);
			jQuery('img', elementNode).wrap(aWrapper);

		}
	
		jQuery(elementNode).addClass(this.elementClass.substring(1));
		jQuery(elementNode).addClass((this.elementClass + '-' + elementType).substring(1));
		jQuery(elementNode).addClass((this.elementClass + '-' + elementType + '-' + elementNr).substring(1));
		
		// copy wrapper's data to jQuery data object associated with the element
		jQuery(elementNode).data(jQuery(elementWrapper).data());

		if (menuType == 'extended') {
			jQuery(elementNode).addClass(this.extendedContextMenuClass.substring(1));
		}

		// remove content-o-mat's wrapper
		jQuery(elementNode).closest('.cmt-element-wrapper').children().first().unwrap();
		
		jQuery(elementNode).on('click.CMTLayout', function(ev) {
			this.receiveFocus(ev.target, ev);
		}.bindScope(this));
			

		this.initContextMenu($(elementNode).closest('.cmt-object'));

		// prevent image dragging
		jQuery(image).on('dragstart', function(ev) {
			ev.preventDefault();
		});
	},
	
	 /**
	  * function initObjectElements
	  * Initializes all elements of a layout object like text input fields or images.
	  * 
	  * @param {Object} el Object / reference to a DOM-element type 'layout object'
	  * @return void
	  */
	initObjectElements: function(el) {

		// first search and unwrap from PHP script passed elements
		jQuery(this.wrapperClass, el).each(function (index, elementWrapper) {

			var elementType = jQuery(elementWrapper).data('element-type');
			var elementNr = jQuery(elementWrapper).data('element-nr');
			var menuType = jQuery(elementWrapper).data('menu-type');
	
			var elementNode = jQuery(elementWrapper).parentsUntil('.cmt-element-wrapper').first();

			switch (elementType) {
			
				case 'image':
					var image = jQuery('img', elementNode);
					var src = jQuery(image).attr('src');
					
					jQuery(image).attr('src', '#');
										
					jQuery(image).one('load', function() {
						this.initImageElement(elementWrapper);
					}.bind(this)).bind(this);
					jQuery(image).attr('src', src)
					return true;
				
					break;
				
				// PHP scripts
				case 'script':
					
					jQuery(elementNode).data('cmt-script-nr', this.scriptNr);
					jQuery(elementNode).attr('contenteditable', false);

					jQuery('<div class="cmt-script-container cmt-clearfix" id="cmt-script-container-' + this.scriptNr + '"></div>').insertAfter(elementNode);
					
					// get the data from the wrapper at this point
					//this.loadElementScript(jQuery(elementWrapper).data('cmtScriptBasePath') + '/' + jQuery(elementWrapper).data('cmtScriptPath'), jQuery('#cmt-script-container-' + this.scriptNr));
					this.scriptNr++;
					
					// add a refresh button
					var refreshButton = jQuery('<span class="cmt-flat-button cmt-button-refresh"></span>').insertAfter(elementNode);
					jQuery(refreshButton).on('click', function(ev) {
						ev.preventDefault();
						
						var parent = jQuery(ev.currentTarget).parent();
						var script = jQuery('.cmt-element-script-1', parent);
						this.loadElementScript(jQuery(script).data('cmtScriptBasePath') + '/' + jQuery(script).data('cmtScriptPath'), jQuery('.cmt-script-container', parent));
					}.bindScope(this));
					
					break;
				
				// HTML editors
				case 'html':
//					break;
					jQuery(elementNode).attr('contenteditable', false);
					jQuery(elementNode).attr('id', 'cmt-mce-editor-' + this.htmlEditorNr);
					
					// create a container for tinyMCE's menu
					var menu = jQuery('<div class="cmt-mce-menu" id="cmt-mce-menu-' + this.htmlEditorNr + '" />');
					jQuery(menu).insertBefore(elementNode);

					var settings = {
						selector: '#cmt-mce-editor-' + this.htmlEditorNr,
						fixed_toolbar_container: '#cmt-mce-menu-' + this.htmlEditorNr,
						inline: true,
						auto_focus: true,
					};
					
					var addSettings = this.htmlEditorSettings;

					jQuery.extend(settings, addSettings)

					tinymce.EditorManager.init(
						settings
					)

					this.htmlEditorNr++;
					break;
			}

			jQuery(elementNode).addClass(this.elementClass.substring(1));
			jQuery(elementNode).addClass((this.elementClass + '-' + elementType).substring(1));
			jQuery(elementNode).addClass((this.elementClass + '-' + elementType + '-' + elementNr).substring(1));
			
			// copy wrapper's data to jQuery data object associated with the element
			jQuery(elementNode).data(jQuery(elementWrapper).data());

			if (menuType == 'extended') {
				jQuery(elementNode).addClass(this.extendedContextMenuClass.substring(1));
			}

			// remove content-o-mat's wrapper
			if (elementType != 'image') {
				//all elements
				jQuery(elementNode).html(jQuery(elementWrapper).html());
			} else {
				// except images
				jQuery(elementNode).closest('.cmt-element-wrapper').children().first().unwrap();
			}
			
			jQuery(elementNode).on('click.CMTLayout', function(ev) {
				this.receiveFocus(ev.target, ev);
			}.bindScope(this));

		}.bindScope(this));

		// now init text elements
		jQuery(this.elementClass+':not(.cmt-element-image)', el).each(function (index, contentElement){

			// make editable
			jQuery(contentElement).attr('contenteditable', true);
			
			// calculate min-height for empty elements
			var tagName = contentElement.tagName;
			var tempNode = jQuery('<' + tagName + ' style="display: none">&nbsp;</' +tagName + '>');
			jQuery(document.body).append(tempNode);
			
			// adjust height of element if it is empty
			var contentElementCSS = {'min-height': jQuery(tempNode).outerHeight()+'px'}
			
			// some tag types need more CSS styling
			switch(tagName.toLowerCase()) {
			
				case 'span':
					contentElementCSS.display = 'inline-block';
					contentElementCSS.minWidth = '1em';
					break;
			}
			
			jQuery(contentElement).css(contentElementCSS);
			jQuery(tempNode).remove();
			
			// prevent p-tags if parent tag is a <p>
//			if (contentElement.tagName.toLowerCase() == 'p') {
//				
//			}
			
		}.bindScope(this));		

		// init script elements
		jQuery('.cmt-element-script', el).each(function (index, contentElement) {
			
			jQuery(contentElement).on('click.CMTLayout', function(ev) {
				this.editScript(ev);
			}.bindScope(this));
		}.bindScope(this));
		
		// trigger script loading
		jQuery('.cmt-element-script + .cmt-button-refresh').trigger('click');
		this.receiveFocus('body');
		
	},
	
	/**
	 * function initPasteing()
	 * Inits the text paste event on the element of a layout object.
	 * 
	 * @param {Object} el Reference to the layout object.
	 * @return void
	 */
	initPasteing: function (el) {
		jQuery(this.elementClass, el).on('paste', function (event) {

			event.preventDefault();
			this.pasteText(event);
		}.bindScope(this));			
	},
	
	/**
	 * function initKeys()
	 * Init the keydown events for the layout object
	 * 
	 * @param {Object} el Reference to the layout object.
	 * @return void
	 */
	initKeys: function(el) {
		jQuery(el).on('keydown', this.triggerKeyAction.bind(this));
	},

	initFunctionKeys: function(el) {
		jQuery(document).on('keydown', this.triggerFunctionKeyAction.bind(this));
	},
	
	/**
	 * function initLinks()
	 * Init all links in a layout object.
	 * 
	 * @param {Object} el Reference to the layout object.
	 * @return void
	 */
	initLinks: function(el, html) {

		var s = this.linkWrapperClass;

		// special treatment for links in HTML editors
		if (typeof html == 'undefined') {
			s += ':not(.cmt-element-html ' + s +')';
		}
		 
		// first search and unwrap from PHP script passed elements
		jQuery(s, el).each(function (index, linkWrapper) {

			var link = jQuery('a', linkWrapper).first();
		
			// copy all data
			jQuery.extend(jQuery(link).data(), jQuery(linkWrapper).data());

			// mark as cmt link
			jQuery(link).addClass(this.linkClass.substring(1));

			// remove wrapper
			jQuery(link).unwrap();
		
			// add click event to link
			jQuery(link).on('click.CMTLayout', function(ev) {

				ev.preventDefault();
				
				// if an image is linked then don't execute the actions because they will be executed by the parent's click event
				if (!jQuery(ev.currentTarget).parent().hasClass('ui-wrapper')) {
					
					// show context menu after the whole object has gained the focus because method receiveFocus() hides the context menu automatically
					jQuery(link).on('afterReceiveFocus', function(e) {
						this.showContextMenu(ev);
					}.bindScope(this))
				}
				
			}.bindScope(this));
		}.bindScope(this));
	},
	
	/**
	 * function initContextMenu()
	 * inits the context menu's base functionalities (show and hide on click, that's all)
	 * 
	 * @param {Object} el reference to the layout object
	 * @return void
	 */
	initContextMenu: function(el) {

		el = jQuery(el).andSelf()
		// create general editor buttons
		this.initContextMenuButtons(el);

		// show / hide context menu event
		jQuery(this.elementClass, el).on('click.CMTLayout keyup.CMTLayout', function(ev) {

			// TODO: check second condition!
			if (this.getSelection(ev.currentTarget) != '' || jQuery(ev.currentTarget).hasClass('ui-wrapper')) {
				this.hideContextMenu(ev)
				this.showContextMenu(ev);
			} else {
				//this.hideContextMenu(ev);
			}

		}.bindScope(this));

		// ??? Does this work???
//		jQuery(this.elementClass, el).on('blur', function(ev) {
//			this.hideContextMenu(ev);
//		}.bindScope(this));
		
	},

	/**
	 * function initContextMenuButtns()
	 * Add functionalities to the context menu buttons
	 * 
	 * @param {Object} el reference to the layout object
	 * @return void
	 */
	initContextMenuButtons: function(el) {


		// TODO: Do we need this anymore?
		jQuery(this.buttonPrefix + 'a', el).on('click.CMTLayout', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			ev.stopImmediatePropagation();
			return false;
		}.bindScope(this));
		
		// link button
		jQuery(this.buttonPrefix + 'a', el).on('mousedown.CMTLayout', function(ev) {

			// search for existing link
			var object = this.getObject(ev.currentTarget);
			var link = jQuery(this.linkClass + this.selectedClass, object);

			// no existing link: wrap selected text and insert new link! 
			if (link.length != 1) {
				
				//document.execCommand('insertHTML', false, '<a href="Javascript:void(0);" class="' + this.tempLinkClass.substring(1) + '">' + this.getSelection() + '</a>');
				document.execCommand('createLink', false, 'cmtTempLinkMarker');

				// do more CMT link insertion ...
				//var link = jQuery(this.tempLinkClass, document.activeElement);
				var link = jQuery('a[href="cmtTempLinkMarker"]', document.activeElement);
				
				jQuery(link).attr('href', 'Javascript:void(0)');
				jQuery(link).addClass(this.tempLinkClass.substring(1));
				jQuery(link).addClass(this.selectedClass.substring(1));
				jQuery(link).removeAttr('_moz_dirty');
				
				jQuery(link).data('cmt-link-id', 0);
				jQuery(link).data('cmt-link-type', 'internal');

				jQuery(link).on('click.CMTLayout', function(ev) {
					this.receiveFocus(ev.currentTarget);
					
					jQuery(link).on('afterReceiveFocus.CMTLayout', function(e) {
						this.showContextMenu(ev);
					}.bindScope(this));
				}.bindScope(this));
				

			}
					
		
			// hide context menu
			//jQuery(ev.currentTarget).trigger('click.CMTLayout');
			this.hideContextMenu({currentTarget: link});

//			CMTLayoutMenu.dontCloseOnFocus = this.msie;		// true for IEs
			
			this.editLink({currentTarget: link})

			return false;			
		}.bindScope(this));

		// remove format button
		jQuery(this.buttonPrefix + 'remove-format', el).on('mousedown.CMTLayout', function(ev) {
			this.removeFormat(ev);
			return false;			
		}.bindScope(this));
		
		// unlink
		jQuery(this.buttonPrefix + 'unlink', el).on('mousedown.CMTLayout', function(ev) {
			this.unlink(ev);
			this.dontFocus = true;
			return false;			
		}.bindScope(this));

		// image button
		jQuery(this.buttonPrefix + 'image', el).on('click.CMTLayout', function(ev) {
			this.editImage(ev);
			this.hideContextMenu(ev);
		}.bindScope(this));

		// image link button
		jQuery(this.buttonPrefix + 'image-a', el).on('click.CMTLayout', function(ev) {

			var image = jQuery(ev.currentTarget).closest(this.objectClass).find(this.selectedImageClass);

			if (jQuery(image).hasClass('ui-wrapper')) {
				image = jQuery(image).find('img');
			}

			if (jQuery(image).parent().prop('tagName').toLowerCase() != 'a') {
				var i = jQuery(image).wrap('<a href="Javascript:void(0);" class="' + this.tempLinkClass.substring(1) + '"></a>');
				var link = jQuery(i).parent();
				
				jQuery(link).data('cmt-link-id', 0);
			} else {
				var link = jQuery(image).parent();
			}
		
			// hide context menu
			this.editLink({currentTarget: link});
			this.hideContextMenu({currentTarget: link});
			
			return false;			
		}.bindScope(this));

		// remove format button
		jQuery(this.buttonPrefix + 'image-remove-format', el).on('click.CMTLayout', function(ev) {

			var wrapper = jQuery(ev.currentTarget).closest(this.objectClass).find(this.selectedImageClass);
			this.resetImageSize(wrapper);
			
			// hide context menu
			this.hideContextMenu({currentTarget: wrapper});
			return false;	
		}.bindScope(this));

		// unlink image button
		jQuery(this.buttonPrefix + 'image-unlink', el).on('click.CMTLayout', function(ev) {

			var wrapper = jQuery(ev.currentTarget).closest(this.objectClass).find(this.selectedImageClass);
			var image = jQuery(wrapper).find('img');
			
			// remove link
			if (jQuery(image).parent().prop('tagName').toLowerCase() == 'a') {
				jQuery(image).unwrap();
			}
			
			// hide context menu
			this.hideContextMenu({currentTarget: image});
			return false;	
		}.bindScope(this));
		
		jQuery(this.buttonPrefix + 'image-remove', el).on('click.CMTLayout', function(ev) {
			var wrapper = jQuery(ev.currentTarget).closest(this.objectClass).find(this.selectedImageClass);
			this.deleteImage(wrapper);
			this.hideContextMenu(ev);
		}.bindScope(this));

		
		if (!jQuery.isEmptyObject(jQuery(this.extendedContextMenuClass, el))) {
			this.initContextMenuExtendedButtons(el);
		}
		
		// initially hide extended buttons
		jQuery(this.charButtonsContainerClass +', ' + this.blockButtonsContainerClass, el).css({display: 'none'});
	},

	/**
	 * function initContextMenuExtendedButtons()
	 * Add functionalities to the context menu extended buttons (for plain text elements)
	 * 
	 * @param {Object} el reference to the layout object
	 * @return void
	 */
	initContextMenuExtendedButtons: function(el) {
		
		// bold button
		jQuery(this.buttonPrefix+'b', el).on('mousedown', function(ev) {
			document.execCommand('bold', false, null);
			
			ev.stopImmediatePropagation();
			
			this.dontFocus = true;
			return false;			
		}.bindScope(this));

		// italic button
		jQuery(this.buttonPrefix+'i', el).on('mousedown', function(ev) {
			document.execCommand('italic', false, null);
			ev.stopImmediatePropagation();
			
			this.dontFocus = true;
			return false;			
		}.bindScope(this));
		
		// underline button
		jQuery(this.buttonPrefix+'u', el).on('mousedown', function(ev) {
			document.execCommand('underline', false, null);
			ev.stopImmediatePropagation();
			
			this.dontFocus = true;
			return false;			
		}.bindScope(this));
		
		// strike button
		jQuery(this.buttonPrefix+'strike', el).on('mousedown', function(ev) {
			document.execCommand('strikeThrough', false, null);
			ev.stopImmediatePropagation();
			
			this.dontFocus = true;
			return false;			
		}.bindScope(this));
		
		// paragraph button
		// 2017-04-20: removed: We don't need this function anymore. Insert <p> by hiting "return" key
//		jQuery(this.buttonPrefix+'p', el).on('mousedown', function(ev) {
//			document.execCommand('formatBlock', false, 'p');
//			this.dontFocus = true;
//			return false;			
//		}.bindScope(this));
		
		// unordered list
		jQuery(this.buttonPrefix+'ul', el).on('mousedown', function(ev) {
			document.execCommand('insertUnorderedList', false, null);
			this.dontFocus = true;
			return false;			
		}.bindScope(this));
		
		// ordered list
		jQuery(this.buttonPrefix+'ol', el).on('mousedown', function(ev) {
			document.execCommand('insertOrderedList', false, null);
			this.dontFocus = true;
			return false;			
		}.bindScope(this));
	},

	/**
	 * function showContextMenu()
	 * Add functionalities to the context menu extended buttons (for plain text elements)
	 * 
	 * @param {Object} ev jQuery's event object
	 * @return void
	 */
	showContextMenu: function(ev) {
		
//		this.hideContextMenu(ev);
		
		var editor = jQuery(ev.currentTarget).closest(this.objectClass);
		var menu = jQuery(this.menuClass, editor);
		var contentElement = ev.currentTarget;
		var isExtendedEditor = jQuery(contentElement).hasClass(this.extendedContextMenuClass.substring(1));
		var tagName = contentElement.tagName.toLowerCase();

		jQuery(this.mainButtonsContainerClass).show();
		
		// show / hide extended menu: block format buttons
		if (isExtendedEditor && tagName != 'p') {
			jQuery(this.blockButtonsContainerClass).show();
		} else {
			jQuery(this.blockButtonsContainerClass).hide();
		}

		// show / hide extended menu: character format buttons
		if (isExtendedEditor) {
			jQuery(this.charButtonsContainerClass).show();
		} else {
			jQuery(this.charButtonsContainerClass).hide();
		}
		
		// show image buttons
		if (jQuery(contentElement).hasClass(this.imageClass.substring(1))) {
			jQuery(this.imageButtonsContainerClass).show();
			jQuery(this.mainButtonsContainerClass).hide();
			jQuery(this.blockButtonsContainerClass).hide();
			
			// JQuery ui wraps a resizable image, so select its parent
			jQuery(contentElement).addClass(this.selectedClass.substring(1));

		} else {
			jQuery(this.imageButtonsContainerClass).hide();
		}		
			
		// at least show menu
		if (!jQuery(menu).hasClass('cmt-visible')) {
			
			jQuery(menu).css({top: '0', left: '0'});
			jQuery(menu).addClass('cmt-visible');
			
		}
		
		// and move it to the current selection's position
		this.positionContextMenu(ev);
	},

	/**
	 * function hideContextMenu()
	 * Hides the context menu
	 * 
	 * @param {Object} ev jQuery's event object
	 * @return void
	 */
	hideContextMenu: function(ev) {
		jQuery(this.menuClass).removeClass('cmt-visible');
	},

	/**
	 * function positionContextMenu()
	 * Refreshes / sets the context menu's position above the current selection (one menu each layout object is used for all object's elements)
	 * 
	 * @param {Object} ev jQuery's event object
	 * @return void 
	 */
	positionContextMenu: function(ev) {
	
		var editor = jQuery(ev.currentTarget).closest(this.objectClass);
		var menu = jQuery(this.menuClass, editor);
		var contentElement = ev.currentTarget;
		var tagName = contentElement.tagName.toLowerCase();
	
		switch (true) {
		
			// image container was clicked
			case jQuery(contentElement).hasClass(this.imageClass.substring(1)):
				
				var scrollOffset = this.getScrollPosition();
				var coords = {
					center: ev.clientX + scrollOffset.scrollLeft,
					top: ev.clientY + scrollOffset.scrollTop
				};				
				break;
			
			// click on link
			case jQuery(contentElement).hasClass(this.linkClass.substring(1)):

				var coords = {
					center: ev.pageX,
					top: ev.pageY
				};	
				
				break;

			// keypress
			default:

				var coords = this.getSelectionsCoords();
				break;
		}

		var offset = jQuery(editor).offset();

		var menuHeight = jQuery(menu).outerHeight();
		var menuWidth = jQuery(menu).outerWidth();

		var x = coords.center - offset.left - menuWidth / 2;
		var y = coords.top - offset.top - menuHeight;
	
		if (x < 0) {
			x = 0;
		}
		jQuery(menu).css({top: y + 'px', left: x+'px'});	
	},
	

	triggerKeyAction: function(ev) {
		
		var editor = document.activeElement;

		// hide context menu if not Shift and an arrow key is pressed
		if (!(ev.shiftKey && (ev.which >=37 && ev.which <= 40))) {
			this.hideContextMenu();
		}
		
		
		switch(ev.which) {
			
			// return key
			case 13:
				if (editor.tagName.toLowerCase() == 'p') {
					ev.preventDefault();
					this.insertLineBreak(editor);
				}
				break;
		}
	},

	/**
	 * function triggerFunctionKeyAction()
	 * Trigger global key actions like "ctrl-s" to save the page.
	 * 
	 * @param {Object} ev jQuery's event object
	 * @return void 
	 */
	triggerFunctionKeyAction: function(ev) {
		
		// function need a pressed ctrl-key right now! 
		if (!ev.ctrlKey) {
			return;
		}
		
		//var editor = document.activeElement;
		
		switch(ev.which) {
			
			// save page
			case 83:
				this.savePage(ev);
				ev.preventDefault();
				ev.stopPropagation();
				break;
		}
	},
	
	getObject: function(el) {
		return jQuery(el).closest(this.objectClass);
	},
	
	getGroup: function(el) {
		return jQuery(el).closest(this.groupClass);
	},
	
	/**
	 * function insertLineBreak()
	 * Inserts a <br/> when 'return' key is pressed and prevents <p> insertion. IE inserts <p> tags even if the parent node is a <p> too.
	 * 
	 * @param editor Reference to the current editable area
	 * @return void
	 */
	insertLineBreak: function(editor) {

		var sel = window.getSelection();
		var range = sel.getRangeAt(0);

   		 var br = document.createElement('br');
         range.insertNode(br);
         
         // Place caret after new node. Solution as described here:
         // http://stackoverflow.com/questions/9828623/inserting-caret-after-an-inserted-node
		range.setStartAfter(br);
		range.setEndAfter(br); 
		sel.removeAllRanges();
		sel.addRange(range);
	},

	pasteText: function(event) {
		var editorContent = event.currentTarget;

		if (event.clipboardData || event.originalEvent.clipboardData) {
			content = this.prepareTextForPasting((event.originalEvent || event).clipboardData.getData('text/plain'), editorContent);
			document.execCommand('insertText', false, content);
	    } else if (window.clipboardData) {
	    	
	        content = this.prepareTextForPasting(window.clipboardData.getData('Text'), editorContent);

	    	// ie < 11
	    	if (typeof document.selection != 'undefined') {
	    		document.selection.createRange().pasteHTML(content);
	    	} else {
	    	// ie >= 11
	    		 var sel = window.getSelection();
	    		 var range = sel.getRangeAt(0);
	    		 var frag = document.createDocumentFragment();
	    		 var text = document.createTextNode(content);
	    		 
	    		 frag.appendChild(text);
	             range.deleteContents();
	             range.insertNode(frag);
	    		 jQuery(editorContent).html(jQuery(editorContent).text().replace(/(\r\n)/, '<br /><br />'));

	    	}
	    }
		return;
	},
	
	/**
	 * function prepareTextForPasting()
	 * Shortens a text to 256 chars (length of database fieldtype "varchar") before pasting in "head" elements.
	 * 
	 * @param {String} text The text to paste
	 * @param {Object} element DOM element
	 * @returns {String} Returns the shortened text
	 */
	prepareTextForPasting: function(text, element) {
		
		// shorten text to max length in headlines
		if (jQuery(element).hasClass('cmt-editor-head')) {
			text = text.substr(0, 255).replace(/\<b?r?\s?\/?$i/, '');
		}
		
		return text;
	},
	
	/**
	 * function insertNewObject()
	 * Inserts a new layout object in a layout group. Method is called when the "new object" icon is dropped.
	 * 
	 * @param void
	 * @return void
	 */
	insertNewObject: function() {
		var newObject = jQuery(this.groupClass + ' ' + this.newObjectClass + '.ui-draggable');
		
		// abort if new object handle is dropped outside a group/ column
		if (!newObject.length) {
			return
		}
		jQuery(newObject).css({
			width: '100%'
		});
		
		this.loadNewObject(newObject, {
			templateID: jQuery('#cmt-select-object').val()
		});
	},
	
	/**
	 * function loadNewObject()
	 * Performs an AJAX request, loads the new object template and inserts it into the DOM.
	 * 
	 * @param {Object} newObject New object's wrapper <div />
	 * @param {Object} params Parameters in an object. Currently only params.templateID is used.
	 * @return void
	 */
	loadNewObject: function(newObject, params) {
		
		this.isInProgress(newObject);
		
		var request = jQuery.ajax({
			method: 'POST',
			dataType: 'html',
			data: {
				cmtAction: 'getObjectTemplate',
				cmtObjectTemplateID: params.templateID
			}
		})
		
		request.done(function(response) {
			
			this.isRelaxed(newObject);
			
			n = jQuery(response)
			jQuery(newObject).replaceWith(n);

			this.initObject(jQuery(n));
		}.bindScope(this));
	},
	
	duplicateObject: function(object) {
		var clonedObject = jQuery(object).clone(true, true);

		jQuery(clonedObject).removeData('cmt-object-id');
		jQuery(clonedObject).removeAttr('data-cmt-object-id');

		var o = jQuery(clonedObject).insertAfter(object);

		// delete data('cmt-link-id') in duplicated a! 
		// Sucks: remove both notations because both are stored (allthough the jQuery doc says, 
		// that data ist sored in camel case notation
		jQuery('a.cmt-link', o).removeData('cmtLinkId');
		jQuery('a.cmt-link', o).removeData('cmt-link-id');
		
	},
	
	getSelection: function() {
		
	    var sel;
	    if (window.getSelection) {
	        sel = window.getSelection();

	        if (sel.rangeCount) {
	            return sel.getRangeAt(0);
	        }
	    } else if (document.selection) {
	        return document.selection.createRange();
	    }
	    return null;		
	},
	
	getSelectionsCoords: function() {
		var sel = window.getSelection();
        var range = sel.getRangeAt(0);
        var boundary = range.getBoundingClientRect();
        
        var scrollOffset = this.getScrollPosition();
     
        return {
        	left: boundary.left + scrollOffset.scrollTop,
        	right: boundary.right,
        	top: boundary.top + scrollOffset.scrollTop,
        	width: boundary.right - boundary.left,
        	center: (boundary.left + boundary.right) / 2
        };		
	},
	
	editLink: function(ev) {
		
		this.removeTempLinks();
		
		var link = ev.currentTarget;
		var linkID = jQuery(link).data('cmt-link-id');
		
		// handle f***ing automatic camelCase conversion of data-attribute names in jQuery
		var data = {};

		jQuery.each(jQuery(link).data(), function (index, el) {
			data[this.unCamelCase(index)] = el;
		}.bindScope(this));
			
		var subpanel = data['cmt-link-type'] || 'internal';

		CMTLayoutMenu.passDataToMenu({
			panel: 'link',
			caller: link,
			data: data
		});

		CMTLayoutMenu.showPanel('link', 'link-' + subpanel);
	},

	/**
	 * function editScript()
	 * Set or change a PHP script's source.
	 * 
	 * @param ev jQuery event object
	 * @return void
	 */
	editScript: function(ev) {
		var script = ev.currentTarget;
		
		CMTLayoutMenu.passDataToMenu({
			panel: 'script',
			caller: script,
			data: {
				'cmt-script-path': jQuery(script).data('cmtScriptPath'),
				'cmt-script-base-path': jQuery(script).data('cmtScriptBasePath'),
			}
		});
		
		CMTLayoutMenu.showPanel('script');
	},
	
	/**
	 * function editImage()
	 * Set or change the a image's source
	 * 
	 * @param {Object} ev jQuery event object
	 * @return void
	 */
	editImage: function(ev) {
		
		ev.preventDefault();
		ev.stopPropagation();
		
		var object = this.getObject(ev.currentTarget);
		var wrapper = jQuery('.ui-wrapper' + this.selectedClass, object);
		var image = jQuery('img', wrapper);

		jQuery(wrapper).data({
			'cmt-image-width': jQuery(wrapper).width(),
			'cmt-image-height': jQuery(wrapper).height(),
			'cmt-is-placeholder': jQuery(wrapper).data('cmt-is-placeholder'),
			'cmt-image-path': jQuery(wrapper).data('cmt-image-path'),
			'cmt-image-base-path': jQuery(wrapper).data('cmt-image-base-path'),
			'cmt-old-image-path': jQuery(image).attr('src'),
			'cmt-old-image-classname': jQuery(image).attr('class')
		});

		// handle f***ing automatic camelCase conversion of data-attribute names in jQuery
		var data = {};
		jQuery.each(jQuery(wrapper).data(), function (index, el) {
			data[this.unCamelCase(index)] = el;
		}.bindScope(this));

		CMTLayoutMenu.passDataToMenu({
			panel: 'image',
			caller: wrapper,
			data: data
		});

		CMTLayoutMenu.showPanel('image', 'image-select');
	},
	
	/**
	 * function resetImageSize()
	 * Resets a resizable image size (internally resets the image and the wrapper size)
	 * 
	 * @param {Object} image Reference to the image
	 * @return void
	 */
	resetImageSize: function(wrapper) {
		
		var image = jQuery('img', wrapper);

		
		// check if image fits in wrapper
		maxWidth = parseInt(jQuery(image).closest('.cmt-object-content-wrapper').css('width'));
		
		if (parseInt(jQuery(wrapper).css('width')) > maxWidth) {
			
			var imageRatio = parseFloat(jQuery(image).css('width')) / parseInt(jQuery(image).css('height')) 
			
			jQuery(wrapper).css({
				width: '100%',
				height: maxWidth / imageRatio + 'px'
			});
			
			jQuery(wrapper).addClass('cmt-image-resized');
			
		} else {

			jQuery(wrapper).css({
				width: jQuery(wrapper).data('cmt-image-original-width') + 'px',
				height: jQuery(wrapper).data('cmt-image-original-height') + 'px'
			});
			
			jQuery(wrapper).removeClass('cmt-image-resized');
		}
		
		// reset image 
		jQuery(image).css({
			width: '100%',
			height: 'auto'
		});
		
		jQuery(image).removeAttr('width');
		jQuery(image).removeAttr('height');

		this.showImageSize(wrapper)
	},
	
	/**
	 * function undoImageSelection()
	 * Restores the original image source, width and height stored in the image wrapper's data (e.g. when image selection was aborted).
	 * 
	 * @param {Object} wrapper Reference to the image wrapper(!)
	 * @return void
	 */
	undoImageSelection: function(wrapper) {

		// reset image size for wrapper
		jQuery(wrapper).css({
			width: jQuery(wrapper).data('cmt-image-width') + 'px',
			height: jQuery(wrapper).data('cmt-image-height') + 'px'
		});
		
		// reset image size for image
		var image = jQuery('img', wrapper);
		
		jQuery(image).css({
			width: jQuery(wrapper).data('cmt-image-width') + 'px',
			height: jQuery(wrapper).data('cmt-image-height') + 'px'
		});
		
		jQuery(image).prop('class', jQuery(wrapper).data('cmt-old-image-classname'));
		
		// reset src attribute
		jQuery(image).attr('src', jQuery(wrapper).data('cmt-old-image-path'));

		if (jQuery(wrapper).data('cmt-is-placeholder') == 1) {
			this.setPlaceHolder(jQuery(wrapper));
			jQuery(image).resizable('disable');
		}
		
	},
// ????	
	saveImageSelection:function(wrapper) {
		
		if (jQuery(wrapper).data('cmt-is-placeholder') == 1) {
			this.setPlaceHolder(jQuery(wrapper));
			jQuery(image).resizable('disable');
		}
	},
	
	/**
	 * function setImageSource()
	 * Sets the source atribute of an image selected by its wrapper and does some other magic things to display wrapper and image correctly.
	 * 
	 * @param {Object} params src, wrapper, image as keys with correspondig values.
	 * @return void
	 */
	setImageSource: function(params) {
		
		var wrapper = jQuery(params.wrapper);
		var image = jQuery('img', wrapper)

		var parent = jQuery(wrapper).parent();

		jQuery(image).attr('src', params.src);
		
		// set sizes
		if (params.width) {
			
			if (jQuery(parent).width() < parseInt(params.width)) {
				params.width = '100%';
				params.height = 'auto';
			}
			
			jQuery(image).css('width', params.width);
			jQuery(wrapper).css('width', params.width);
		}
		
		if (params.height) {
			jQuery(image).css('height', params.height);
			jQuery(wrapper).css('height', params.height);
		}
		
		if (params.className) {
			jQuery(image).addClass(params.className);
		}
		
		// unset height and width attributes on change
		jQuery(image).removeAttr('width')
		jQuery(image).removeAttr('height')
		jQuery(wrapper).removeClass('cmt-image-resized');
		
		this.showImageSize(wrapper);

// TODO: is image or wrapper the placeholder???		
		this.unsetPlaceHolder(wrapper);
		jQuery(image).resizable('enable');
	},
	
	
	deleteImage: function(wrapper) {
		
		var data = jQuery(wrapper).data();
		var className = jQuery(wrapper).attr('class');
		
		var image = jQuery('.ui-resizable', wrapper);
		
		// Do AJAX request, get placeolder template and init it!
		jQuery.ajax({
			type: 'POST',
			dataType: 'html',
			data: {
				cmtAction: 'getImagePlaceholder',
				cmtLanguage: this.cmtLanguage,
				cmtPageID: this.cmtPageID,
				elementNr: jQuery(wrapper).data('elementNr'),
				elementType: jQuery(wrapper).data('elementType')
			}
		})
		.done(function(response) {
			
			// substitute old image with new placholder image
			jQuery(image).resizable('destroy');
			var newImage = jQuery(image).replaceWithPush(response);
			var wrapper = jQuery(newImage).parent();

			jQuery(newImage).data(data)
			jQuery(newImage).data('cmt-is-placeholder', 1);
			jQuery(newImage).data('cmt-image-path', '');
			jQuery(newImage).data('cmt-image-height', '');
			jQuery(newImage).data('cmt-image-width', '');
			this.initObjectElements(wrapper.parent());
			this.initContextMenu(wrapper.parent())

		}.bindScope(this));
	},
	
	/**
	 * function makeImageResizable()
	 * Executes jQuery UI's resizable() method for the given image.
	 * 
	 * @param {Object} Reference to the image
	 * 
	 * @return void
	 */
	makeImageResizable: function(image) {
		
		

		jQuery(image).resizable({
			
			ghost: false,
			//maxWidth: '100px',
			create: function(ev) {

				var wrapper = ev.target;

				var image = jQuery('img.ui-resizable', wrapper);

				var originalWidth = parseInt(jQuery(image).data('cmt-image-original-width'));
				var originalHeight = parseInt(jQuery(image).data('cmt-image-original-height'));
				
				if (
					(jQuery(image).attr('width') || jQuery(image).attr('height'))
					|| originalWidth > jQuery(image).width()
					|| originalHeight > jQuery(image).height()
				) {
					jQuery(wrapper).addClass('cmt-image-resized');
				}

				jQuery(wrapper).append(jQuery('<div class="cmt-label cmt-image-size"></div>'));
				jQuery(wrapper).append(jQuery('<div class="cmt-icon cmt-icon-warning cmt-image-shrunk"><span class="cmt-image-original-width"></span><span class="cmt-image-original-size-separator"></span><span class="cmt-image-original-width"></span></div>'));
				
			
				// check if image fits in wrapper
				maxWidth = parseInt(jQuery(image).closest('.cmt-object-content-wrapper').css('width'));
				
				if (parseInt(jQuery(wrapper).css('width')) > maxWidth) {
					
					var imageRatio = parseFloat(jQuery(image).css('width')) / parseInt(jQuery(image).css('height')) 
					
					jQuery(wrapper).css({
						width: '100%',
						height: maxWidth / imageRatio + 'px'
					});
					jQuery(wrapper).addClass('cmt-image-resized');
					
					jQuery(image).css({
						width: '100%',
						height: 'auto'
					});
				}
				
				// show image size in label
				this.showImageSize(wrapper);

			}.bindScope(this),
			
			resize: function(ev, ui) {
				
				// preserve aspect ratio or not depending on dragged handle: dirty dirty Vogts!
				var handle = jQuery(ui.element).data('ui-resizable').axis;
				var resizable = jQuery(ui.element).data('ui-resizable')
				
				if (handle == 'se') {
					resizable._aspectRatio = true;
				} else {
					resizable._aspectRatio = false;
				}
				
				this.showImageSize(ui.element);
				this.setImageSize(ui.element, jQuery(ui.element).width(), jQuery(ui.element).height());
				
			}.bindScope(this),
			
			start: function(ev, ui) {
				this.hideContextMenu(ev);
				jQuery(ui.originalElement).closest('.ui-wrapper').addClass('cmt-image-resized');
			}.bindScope(this)
		});
	},

	/**
	 * function showImageSize()
	 * Shows the image's original size and its current size in labels
	 *  
	 * @param {Object} wrapper The jQueryui image resize wrapper
	 */
	showImageSize: function(wrapper) {
		jQuery('.cmt-label.cmt-image-size', wrapper).text(jQuery(wrapper).width() + ' x ' + jQuery(wrapper).height());
		
		var originalSizeText = jQuery(wrapper).data('cmt-image-original-width') + ' x ' + jQuery(wrapper).data('cmt-image-original-height')
		jQuery('.cmt-icon.cmt-image-shrunk', wrapper).attr('title', originalSizeText);
	},
	
	/**
	 * function setImageSize()
	 * Sets the image's size in its attributes width="123" and height="123"
	 * 
	 * @param {Object} wrapper The jQueryui image resize wrapper
	 * @param {Number} width Width in pixels
	 * @param {Number} height Height in pixels
	 * 
	 * @return void
	 */
	setImageSize: function(wrapper, width, height) {
		
		var image = jQuery('.cmt-image', wrapper);
		
		jQuery(image).attr('width', parseInt(width));
		jQuery(image).attr('height', parseInt(height));
	},

	/**
	 * function setPlaceHolder
	 * Simply adds the class name ".cmt-is-placeholder" to passed element.
	 * 
	 * @param {Object} el The DOM element.
	 * @return void
	 */
	setPlaceHolder: function(el) {
		jQuery(el).addClass('cmt-is-placeholder');
	},

	/**
	 * function unsetPlaceHolder
	 * Simply removes the class name ".cmt-is-placeholder" from passed element.
	 * 
	 * @param {Object} el The DOM element.
	 * @return void
	 */
	unsetPlaceHolder: function(el) {
		jQuery(el).removeClass('cmt-is-placeholder');
	},

	/**
	 * function removeTempLinks()
	 * Removes all links in the document marked with the temporary link CSS selector.
	 * 
	 * @param Boolean all true: removes temporary links in the whole document, false: remove all links except in the as selected marked object.
	 */
	removeTempLinks: function(all) {
		
		if (all) {
			var selector = this.objectClass + ' a' + this.tempLinkClass; 
		} else {
			var selector = this.objectClass + ':not(' + this.selectedClass + ') a' + this.tempLinkClass
		}
		jQuery(selector).contents().unwrap();
	},
	
	/**
	 * function removeFormat()
	 * Removes the format of the current text selection (unwraps the selection)
	 * 
	 * @param {Object} ev jQuery's event object
	 * 
	 * @return void
	 */
	removeFormat: function(ev) {

		document.execCommand('removeFormat', false, false);
	},
	
	/**
	 * function unlink()
	 * Removes a link
	 * 
	 * @param {Object) ev JQuery's object element
	 * 
	 * @return void
	 */
	unlink: function(ev) {
		var sel = window.getSelection()

		var parentEl = sel.getRangeAt(0).commonAncestorContainer
		jQuery(parentEl).unwrap();
	},
	
	/**
	 * function savePage()
	 * Save the whole page and its contents.
	 * 
	 * @param {Object} ev Event
	 */
	savePage: function(ev) {

		// progress button "save page"
		this.isInProgress(jQuery('#cmt-save-page'));
		
		// trigger HTML editors
		tinymce.EditorManager.triggerSave();

		// collect links
		var pageLinks = {};
		var linkCounter = 1;
		var date = new Date();
		
		// collect page contents
		jQuery(this.groupClass).each(function(index, group) {
			
			var groupNr = jQuery(group).data('cmt-group-nr');
			this.pageContent[groupNr] = {};			
			
			jQuery(this.objectClass, group).each(function (objectIndex, object) {
				
				var objectData = {
					visibility: jQuery(object).hasClass(this.objectVisibilityClass.substring(1)),
					cmtObjectID: jQuery(object).data('cmt-object-id'),
					cmtObjectTemplateID: jQuery(object).data('cmt-object-template-id'),
					elements: {}
				};
				
				this.pageContent[groupNr][objectIndex] = objectData;

				// collect elements
				jQuery(this.elementClass, object).each(function (elementIndex, element) {
					
					this.isInProgress(element);
					
					// extract links
					var elContent = jQuery(element).clone(true);
					var elLinks = jQuery(elContent).find(this.linkClass);
					var elLinksRaw = jQuery(element).find(this.linkClass); 
					
					// if element is an image, search at the parent level for link
					if (jQuery(element).hasClass(this.imageClass.substring(1))) {
						var ec = jQuery(element).clone(true);
						elLinks = jQuery(ec).find(this.linkClass);
						elContent = jQuery('<div class="cmt-temp-wrapper">').append(jQuery(ec).children().first());	// nasty workaround
					}

					// replace link tags with custom tags for better php recognization
					jQuery.each(elLinks, function(index, link) {
						
						var tempLinkId = date.getTime();
						tempLinkId = tempLinkId.toString() + linkCounter.toString();
						
						pageLinks[tempLinkId] = jQuery(link).data();

						jQuery(link).addClass('cmt-temp-link-' + tempLinkId);
						jQuery(elLinksRaw[index]).addClass('cmt-temp-link-' + tempLinkId);
		
						jQuery(link).replaceWith(jQuery('<cmtlink:' + tempLinkId +'>' + jQuery(link).html() + '</cmtlink:' + tempLinkId +'>'));

						++linkCounter;
					
					}.bindScope(this));

					var reg = new RegExp (this.elementClass.substring(1) + '-([a-z]+)-([0-9])');
					var match = reg.exec(element.className);
					
					var type = RegExp.$1;
					var typeNr = RegExp.$2;
					
					// do tag specific cleanup work
					switch(type) {
					
						// handle image elements
						case 'image':

							if (jQuery(element).data('cmt-is-placeholder')) {
								var elementContent = '';
								break;
							}

							// cleanup nasty workaround wrapper;
							elContent = jQuery(elContent).closest('.cmt-temp-wrapper').children().first().unwrap();

							// cleanup image tag
							var elementImage =  jQuery('img', elContent).andSelf('img');
					
							if (!jQuery(element).hasClass('cmt-image-resized')) {
								// remove attributes 'width' and 'height' if image was not resized
								jQuery(elementImage).removeAttr('width');
								jQuery(elementImage).removeAttr('height');
							} else {
								// else add height and width
								var height = parseInt(jQuery(element).css('height'));
								var width = parseInt(jQuery(element).css('width'));

								jQuery(elementImage).attr('height', height);
								jQuery(elementImage).attr('width', width);
							}

							// delete style and other attributes
							jQuery(elementImage).removeAttr('style');
							jQuery(elementImage).removeAttr('contentEditable');
							
							// remove all 'cmt-' and 'ui-' prefixed class names
							var elementClass = jQuery(elementImage).attr('class');
							var reg = new RegExp (/((cmt-|ui-)[^\s]+)+/g);
							elementClass = elementClass.replace(reg, '');
							
							elementClass = elementClass.trim();
							if (!elementClass) {
								jQuery(elementImage).removeAttr('class');
							} else {
								jQuery(elementImage).attr('class', elementClass);
							}

							// cleanup image src / remove leading '../'
							var src = jQuery(elementImage).attr('src');
							src = src.replace(/^(\.\.\/)+/, '');
							jQuery(elementImage).attr('src', '{PATHTOWEBROOT}' + src);
							
							// add attributes
							jQuery(elementImage).attr('alt', jQuery(element).data('cmtImageAltText'));
							
							// get tag's content
							var elementContent = jQuery('<div>').append(elContent).html(); // jQuery(elContent).toString();
							
							// add optional HTML
							if (jQuery(element).data('cmtImageAddHtml')) {
								elementContent = elementContent.replace(/(\/?>)$/, ' ' + jQuery(element).data('cmtImageAddHtml') + ' $1');
							}

							break;
							
						// include scripts
						case 'script':
							//var elementContent = jQuery(elContent).data('cmt-script-base-path') + '/' + jQuery(elContent).data('cmt-script-path');
							var elementContent = jQuery(elContent).data('cmt-script-path');
							break;
						
						// HTML editor content needs special cleanup
						case 'html':
							var elementContent = this.cleanupHtmlEditorContent(jQuery(elContent));
							break;
						
						default:
							var elementContent = jQuery(elContent).html().toString();
						break;
					}
					
					this.pageContent[groupNr][objectIndex].elements[elementIndex] = {
						type: type,
						typeNr: typeNr,
						content: elementContent
					};

				}.bindScope(this));
				
			}.bindScope(this));
		}.bindScope(this));

		// collect deleted objects
		var deletedObjects = {};
		var oi = 1;
		
		jQuery(this.groupClass + ' ' + this.deletedClass).each(function(index, el) {
			
			if (jQuery(el).data('cmt-object-id')) {
				deletedObjects[oi++] = jQuery(el).data('cmt-object-id');
			}
		});

		// collect page data
		var pageData = {};
		
		jQuery(CMTLayoutMenu.menuID + ' .cmt-page-data').each(function (index, el) {
			pageData[jQuery(el).attr('name')] = jQuery(el).val();
		});
		
		// now send data
		pageContent = JSON.stringify(this.pageContent);
		deletedObjects = JSON.stringify(deletedObjects);
		pageLinks = JSON.stringify(pageLinks);
		pageData = JSON.stringify(pageData);

		jQuery.ajax({
			type: 'POST',
			data: {
				cmtPageContent: pageContent,
				cmtAction: 'savePage',
				cmtLanguage: this.cmtLanguage,
				cmtPageID: this.cmtPageID,
				cmtDeletedObjectIDs: deletedObjects,
				cmtPageLinks: pageLinks,
				cmtPageData: pageData
			}
		})
		.done(function(response) {
			response = JSON.parse(response);
			
			if (!response.savingSuccessful) {

				// show message
				CMTLayoutMenu.showMessage('error', 'page-not-saved');

			} else {
				
				// update object IDs
				var objectIDs = response.objectIDs;
				
				jQuery(this.groupClass).each(function(groupIndex, group) {
					
					var groupNr = jQuery(group).data('cmt-group-nr');
					
					jQuery(this.objectClass, group).each(function(objectIndex, object) {
						var objectID = objectIDs[groupNr][objectIndex];
						jQuery(object).data('cmt-object-id', objectID);
					}.bindScope(this))
				}.bindScope(this))
				
				// remove deleted objects from DOM
				jQuery(this.groupClass + ' ' + this.deletedClass).remove();
				
				// update links
				var linkIDs = response.linkIDs;
				
				for (key in linkIDs) {
					
					var linkClass = 'cmt-temp-link-' + key;
					
					jQuery('.' + linkClass).data('cmt-link-id', linkIDs[key]);
					jQuery('.' + linkClass).removeClass(linkClass);
				}
				
				// show message
				CMTLayoutMenu.showMessage('success', 'page-saved');
			}
			
			// unprogress button "save page"
			this.isRelaxed(jQuery('#cmt-save-page'));
			
			// unprogress all other elements
			this.isRelaxed(jQuery('.cmt-in-progress'));
			
		}.bindScope(this));
	},
	
	/**
	 * function setPageID()
	 * Method to set the internal variable cmtPageID from outside the CMTLayout object properly.
	 * 
	 * @param {Number} pid The id of the current page.
	 */
	setPageID: function(pid) {
		this.cmtPageID = parseInt(pid);
	},

	/**
	 * function setLanguage()
	 * Method to set the internal variable cmtLanguage from outside the CMTLayout object properly.
	 * 
	 * @param {String} lang Content-o-mat's language shortcut, e.g. 'de', 'en'
	 */
	setLanguage: function(lang) {
		this.cmtLanguage = lang;
	},
	
	/**
	 * function getScrollPosition()
	 * Internal helper: returns the page's scroll position depending on the browser
	 * 
	 * @param void
	 * @return Object Keys scrollTop and scrollLeft with corresponding values.
	 */
	getScrollPosition: function() {
		
		if( typeof( window.pageYOffset ) == 'number' ) {
	        // FF?
	        scrOfY = window.pageYOffset;
	        scrOfX = window.pageXOffset;
	    } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
	        //DOM 
	        scrOfY = document.body.scrollTop;
	        scrOfX = document.body.scrollLeft;
	    } else {
	    	scrOfY = 0;
	        scrOfX = 0;
	    }
		
		return {
			scrollTop: scrOfY,
			scrollLeft: scrOfX
		};
	},
	
	/**
	 * function getDataFromMenu()
	 * Method is called after CMTLayoutMenu closed the current panel and performs panel specific actions.
	 *  
	 * @param {Object} params Variables are stored in this object (
	 * 				 params.panel: currently closed panel name, 
	 * 				 params.caller: layout object that called the currently closed panel
	 * 				 params.data: panel specific data
	 * 
	 * @return void
	 */
	getDataFromMenu: function(params) {
		
		var caller = params.caller;
		
		// do some individual actions
		switch(params.panel) {
		
			case 'link':
				
				// add class to view if passed in addHTML
				var addHTML = params.data['cmt-add-html'];

				var reg = new RegExp (/class\=(\'|\")?(.*)\1/);
				var match = reg.exec(addHTML);

				if (match) {
					jQuery(caller).attr('class', this.linkClass.substring(1) + ' ' + RegExp.$2);
				}
				
				// now replace temp link selector with link class
				jQuery(caller).removeClass(this.tempLinkClass.substring(1));
				jQuery(caller).addClass(this.linkClass.substring(1));

				break;
				
			case 'script':

				var path = params.data['cmt-script-path'];
				delete params.data['cmt-script-base-path'];
				
				var basePath = jQuery(caller).data('cmtScriptBasePath');
				
				// cleanup path
				var reg = new RegExp ('^(((..\/)+)|.\/|\/)?' + basePath, 'i');
				path = path.replace(reg, '');
				
				jQuery(caller).text(path);
				params.data['cmt-script-path'] = path;
				
				// load content
				this.loadElementScript(jQuery(caller).data('cmtScriptBasePath') + '/' + path, jQuery('#cmt-script-container-' + jQuery(caller).data('cmtScriptNr')));
				
				break;
				
			case 'image':
				jQuery(caller).data('cmt-is-placeholder', 0);
				this.unsetPlaceHolder(caller);
				break;
		}

		// now pass data to caller
		jQuery.extend(jQuery(caller).data(), this.camelCase(params.data));
		
	},
	
	/**
	 * function showObjectInformations()
	 * Open the object menu panel and show current informations like object's template.
	 * 
	 * @param {Object} object Reference of the selected layout object.
	 */
	showObjectInformations: function(object) {

		var data = {};
		jQuery.each(jQuery(object).data(), function (index, el) {
			data[this.unCamelCase(index)] = el;
		}.bindScope(this));
		
		CMTLayoutMenu.passDataToMenu({
			panel: 'object',
			caller: object,
			data: data
		});
		
		CMTLayoutMenu.showPanel('object')
	},
	
	/**
	 * function changeObjectTemplate()
	 * Substitutes the current object template with an other template. Keeps the content!
	 * 
	 * @param {Mixed} layoutObject Selector or jQuery object, change this layout objects template
	 * @param {Number} templateID New template's id
	 * 
	 * @return void
	 */
	changeObjectTemplate: function(layoutObject, templateID) {

		var reg = new RegExp (this.elementClass.substring(1) + '-([a-z]+)-([0-9])');
		
		// copy contents to object
		jQuery(this.elementClass, layoutObject).each(function(index,element) {
			
			var match = reg.exec(element.className);
			if (jQuery(element).html() != '') {
				jQuery(layoutObject).data(match[0], jQuery(element).html());
			}
		});
		
		// load template
		var request = jQuery.ajax({
			method: 'POST',
			dataType: 'html',
			data: {
				cmtAction: 'getObjectTemplate',
				cmtObjectTemplateID: templateID
			}
		})
		
		request.done(function(response) {
			
			var newObject = jQuery(response);
			var reg = new RegExp (this.elementClass.substring(1) + '-([a-z]+)-([0-9])');
			
			// copy data
			jQuery(newObject).data(jQuery(layoutObject).data());

			// replace old object with new
			jQuery(layoutObject).replaceWith(newObject);
			this.initObject(jQuery(newObject));
			this.selectObject(newObject);

			// copy and paste old contents in new object
			var data = jQuery(newObject).data();
			for (a in data) {
				if (a.indexOf('cmt') === 0) {
					jQuery('.' + this.unCamelCase(a), jQuery(newObject)).html(data[a]);
				}
			}
			
			jQuery(newObject).data('cmt-object-template-id', templateID);
			
			if (jQuery(layoutObject).hasClass(this.objectVisibilityClass.substring(1))) {
				jQuery(newObject).toggleClass(this.objectInvisibilityClass.substring(1));
				jQuery(newObject).toggleClass(this.objectVisibilityClass.substring(1));
			}
			
			// pass new object's reference to panel
			jQuery(CMTLayoutMenu.getCurrentPanel()).data('caller', newObject);

		}.bindScope(this));
	},
	
	/**
	 * function resetObjectTemplate()
	 * Resets layout object's template to last version e.g. after the template selection was abort.
	 * 
	 * @param {Mixed} layoutObject jQuery object or selector
	 * 
	 * @return void
	 */
	resetObjectTemplate: function(layoutObject) {
		
		// reset object template
		if (this.tempTemplateID !== null) {
			this.changeObjectTemplate(layoutObject, this.tempTemplateID);
		}
		this.tempTemplateID = null;
	},
	
	/**
	 * function saveObjectTemplate()
	 * Helper: Resets the internal temporary template id variable. Called in CMTMenu.
	 */
	saveObjectTemplate: function() {
		this.tempTemplateID = null;
	},
	
	/**
	 * function receiveFocus()
	 * The passed element gets the "focus" (CMT adds a 'cmt-selected' class or removes it. Some elements (e.g. images) have to be treated separately.
	 * 
	 * @param {Object} el The HTML element which should gain the focus.
	 * @param {Object} ev Optional: jQuery event object.
	 * @return void
	 */
	receiveFocus: function(el, ev) {

		// set 'dontFocus' to true if the click event should be ignored!
		if (this.dontFocus) {
			this.dontFocus = false;
			return false;
		}
		
		// reset the focus
		this.resetFocus();

		// select all elements and it's parents
		this.selectLink(el);
		this.selectElement(el);
		this.selectObject(el);
		this.selectGroup(el);

		// hide object dialogs when displayed
		this.resetObjectActionDialog(null, jQuery(el).closest(this.objectClass));
		
		this.hideContextMenu();
		
		// close open panel
		CMTLayoutMenu.closePanel();

		// trigger custom 'afterReceiveFocusEvent
		jQuery(el).trigger('afterReceiveFocus')

		this.removeTempLinks(true)
		
		if (typeof ev == 'object' && typeof ev.stopPropagation == 'function') {
			ev.stopPropagation();
			ev.preventDefault();
		}
		
	},
	
	/**
	 * function resetFocus()
	 * Resets the focus (removes selector .cmt-selected from elements)
	 * 
	 * @param void
	 * @return void
	 */
	resetFocus: function() {

		jQuery(
			this.groupClass + this.selectedClass + ', ' 
			+ this.groupClass + ' ' + this.selectedClass + ', ' 
			+ this.groupClass + ' ' + this.selectedImageClass
		).removeClass(this.selectedClass.substring(1));

	},
	
	/**
	 * function selectLink()
	 * Marks a link with a selector ('.cmt-selected').
	 * 
	 * @param {Object} el Reference to an DOM element.
	 * 
	 */	
	selectLink: function(el) {
	
		var link = jQuery(el).closest(this.linkClass);
		var wrapper = jQuery(el).closest('.ui-wrapper').first(); //addClass(this.selectedClass.substring(1));
		
		// if image is linked!
		if (wrapper.length) {
			link = wrapper;
		}
		
		jQuery(link).addClass(this.selectedClass.substring(1))
		return;
	},

	/**
	 * function selectElement()
	 * Marks a layout element with a selector ('.cmt-selected'): Traverses up the DOM until an element is found.
	 * 
	 * @param {Object} el Reference to an DOM element.
	 * 
	 */	
	selectElement: function(el) {
		jQuery(el).closest(this.elementClass).addClass(this.selectedClass.substring(1));
		return;
	},
	
	/**
	 * function selectObject()
	 * Marks a layout object with a selector ('.cmt-selected'): Traverses up the DOM until an object is found.
	 * 
	 * @param {Object} el Reference to an DOM element.
	 * 
	 */	
	selectObject: function(el) {
		jQuery(el).closest(this.objectClass).addClass(this.selectedClass.substring(1));
		return;
	},
	
	/**
	 * function selectGroup()
	 * Marks a layout group with a selector ('.cmt-selected'): Traverses up the DOM until a group is found.
	 * 
	 * @param {Object} el Reference to an DOM element.
	 * 
	 */
	selectGroup: function(el) {
		jQuery(el).closest(this.groupClass).addClass(this.selectedClass.substring(1));
		return;
	},
	
	/**
	 * function unCamelCase()
	 * "uncamelcases" a string  or the keys in an object: "cmtImageWidth" => "cmt-image-width"
	 * 
	 * @param {Mixed} data String or object
	 * @returns String or object
	 */
	unCamelCase: function(data) {
		switch(typeof data) {

			case 'string':
				return this._unCamelCase(data);
				break;
				
			case 'object':
				var d = {};
				jQuery.each(data, function (index, el) {
					d[this._unCamelCase(index)] = el;
				}.bindScope(this));
				return d;
				break;
				
			default:
				return data;
		}
	},
	
	_unCamelCase: function(str) {
		str = str.replace(/\W+/g, '-').replace(/([a-z\d])([A-Z])/g, '$1-$2').toLowerCase();
		return str.replace(/([0-9]+)/g, '-$1');
	},

	/**
	 * function camelCase()
	 * "camelcases" a string  or the keys in an object: "cmt-image-width" => "cmtImageWidth"
	 * 
	 * @param {Mixed} data String or object
	 * @returns String or object
	 */
	camelCase: function(data) {
		switch(typeof data) {

			case 'string':
				return this._camelCase(data);
				break;
				
			case 'object':
				var d = {};
				jQuery.each(data, function (index, el) {
					d[this._camelCase(index)] = el;
				}.bindScope(this));
				return d;
				break;
				
			default:
				return data;
		}
	},
	
	_camelCase: function(str) {
		return str.replace(/\W+(.)/g, function (x, chr) {
			return chr.toUpperCase();
        });
	},
	
	/**
	 * function confirmObjectActionDialog()
	 * Creates a two button / area overlay in a layout object for "confirm" and "reject" actions.
	 * 
	 * @param {Object} params Parameters are passed in an object with the folowing elements:
	 * params.object: The HTML element (layout object)
	 * params.actionName: Name of the action (e.g. "delete")
	 * params.confirmAction: Function which is called after the user clicks the "confirm" button
	 * params.rejectAction: Function which is called after the user clicks the "reject" button
	 * 
	 * @return void
	 */
	confirmObjectActionDialog: function(params) {
		
		var object = params.object;
		var actionName = params.action;
		var confirmAction = params.confirm
		var rejectAction = params.reject
		
		var contentWrapper = jQuery('.cmt-object-content-wrapper', object);
		
		var confirm = jQuery('<div class="cmt-confirm-area cmt-object-action-' + actionName + '"></div>');
		var reject = jQuery('<div class="cmt-reject-area cmt-object-action-' + actionName + '"></div>');
	
		this.resetObjectActionDialog(object);
		jQuery(contentWrapper).append(reject);
		jQuery(contentWrapper).append(confirm);

		// init reject action
		jQuery(reject).on('click.CMTLayout', function(ev) {

			this.resetObjectActionDialog(object)
			
			if (typeof rejectAction == 'function') {
				rejectAction(object);
			}

		}.bindScope(this));
		
		// init confirmation action
		jQuery(confirm).on('click.CMTLayout', function(ev) {

			this.resetObjectActionDialog(object);
			
			if (typeof confirmAction == 'function') {
				confirmAction(object);
			}
		}.bindScope(this))

	},
	
	/**
	 * function resetObjectActionDialog()
	 * Resets / removes the action dialog buttons (e.g. "delete" <=> "abort") from a layout object.
	 * 
	 * @param {Object} object Optional: The parent object of the two dialog buttons. Default is 'document'  
	 * @param {Object} exceptObject Optional: Remove all dialog buttons except the buttons of this object.
	 * 
	 * @return void
	 */
	resetObjectActionDialog: function(object, exceptObject) {
		
		if (typeof object == 'undefined' || !object) {
			object = document;
		}
		
		if (typeof exceptObject != 'undefined') {
			var except = jQuery('.cmt-object-content-wrapper', exceptObject);
		} else {
			var except = null
		}
		
		jQuery('.cmt-object-content-wrapper', object).not(except).each(function(index, wrapper) {

			jQuery('.cmt-reject-area', wrapper).remove();
			jQuery('.cmt-confirm-area', wrapper).remove();
			
		});
	},
	
	/**
	 * function loadElementScript()
	 * Loads dynamically produced script content.
	 * TODO: pass contents of head1-5 also in POST request!
	 *  
	 * @param {String} path Script's file path.
	 * @param {Object} target Target HTML element. The dynamic content goes in here.
	 */
	loadElementScript: function(path, target) {
		
		this.isInProgress(target);
		
		var wrapper = jQuery(target).closest('.cmt-object');
		var content = {};
		
		for (var i = 1; i <=5; i++) {
			content['head' + i] = jQuery('.cmt-element-head-' + i, wrapper).text();
		}

		var postData = jQuery.extend(content, {
			cmtAction: 'loadElementScript',
			cmtLanguage: this.cmtLanguage,
			cmtPageID: this.cmtPageID,
			cmtPath: path
		});
		
		jQuery.ajax({
			type: 'POST',
			data: postData
		})
		.done(function(response) {
			
			this.isRelaxed(target);
			jQuery(target).html(response);
			
		}.bindScope(this));
		
	},
	
	/**
	 * function setHTMLEditorConfig()
	 * Sets additional configuration vars for the TinyMCE HTML editor
	 * 
	 * @param {Object} settings A settings object.
	 * @return {Boolean}
	 */
	setHTMLEditorConfig: function(settings) {
		
		if ((typeof settings).toLowerCase() != 'object') {
			return false
		}
		
		this.htmlEditorSettings = settings;
		return true;
	},
	
	/**
	 * function cleanupHtmlEditorContent()
	 * Cleans the content of html type fields from TinyMCE class names and data.
	 * 
	 * @param {Object} contentFragment The content as DOM fragment
	 * @returns string Cleaned HTML text.
	 */
	cleanupHtmlEditorContent: function(contentFragment) {
		
		// clear class names: remove class names containing 'mce'
		jQuery('[class|=mce]', contentFragment).removeClass(function (index, css) {
			return (css.match (/(^|\s)mce-\S+/g) || []).join(' ');
		});
		
		jQuery('*', contentFragment).filter(function(index, el) {
			
			// remove all mce prefixed data
			for (var property in jQuery(el).data()) {
				if (property.indexOf('mce') != -1) {
					jQuery(el).removeAttr('data-' + this.unCamelCase(property));
					jQuery(el).removeProp('data-' + this.unCamelCase(property));
				}
			}
			
			// remove empty 'class=""' attributes
			if (jQuery(el).attr('class') == '') {
				jQuery(el).removeAttr('class');
			}

			return el;
		}.bindScope(this));

		return jQuery(contentFragment).html();
	},
	
	/**
	 * function onMenuClosed()
	 * "Event": Method is called from menu script when menu closing is finished
	 * 
	 * @param void
	 * @return void
	 */
	onMenuClosed: function() {
		
		jQuery('.ui-wrapper.cmt-element-image').each(function(index, el) {
			this.showImageSize(el);
		}.bindScope(CMTLayout));
		
	},

	/**
	 * function onMenuOpened()
	 * "Event": Method is called from menu script when menu opening is finished
	 * 
	 * @param void
	 * @return void
	 */
	onMenuOpened: function() {
		
		jQuery('.ui-wrapper.cmt-element-image').each(function(index, el) {
			this.showImageSize(el);
		}.bindScope(CMTLayout));		
	},
	
	/**
	 * function isInProgress()
	 * Adds an visual progress indicator to the given element (method calls the CMTLayoutMenu method of the same name)
	 * 
	 * @param {Object} el Reference to HTML-DOM element
	 * @return void
	 */
	isInProgress: function(el) {
		CMTLayoutMenu.isInProgress(el);
	},

	/**
	 * function isRelaxed()
	 * Removes the visual progress indicator from the given element (method calls the CMTLayoutMenu method of the same name)
	 * 
	 * @param {Object} el Reference to HTML-DOM element
	 * @return void
	 */
	isRelaxed: function(el) {
		CMTLayoutMenu.isRelaxed(el);
	}

};

CMTLayout.initialize();
