//var CMTLayout = {
//	
//	initialize: function() {
//		jQuery(document).on('ready', this.init.bind(this));
//		this.placeholderHeadline= 'neue Überschrift';
//		this.initBasics();
//	},
//	
//	init: function(ev) {
//		jQuery(this.editorClass).each(function(index, el) {
//			this.createEditor(el);
//		}.bindScope(this));
//	},
//	
//	initBasics: function() {
//		Function.prototype.bindScope = function(scope) {
//			var _function = this;
//
//			return function() {
//				return _function.apply(scope, arguments);
//			};
//		};	
//	},
//	
//	createEditor: function(el) {
//		
//		var editorContent = jQuery(this.contentClass, el)[0];
//		
//		// get editor type
//		var editorType = 'head';
//		
//		if (jQuery(el).hasClass('cmt-editor-text')) {
//			editorType = 'text';
//		}
//		
//		if (jQuery(el).hasClass('cmt-editor-extended')) {
//			editorType = 'extended';
//			this.createExtendedEditorButtons(el, editorContent);
//		}
//		
//		var editorConfig = null;
//		editorConfig = this.editorSettings[editorType];
//		editorConfig.element = editorContent;
//
//		// prevent p-tags if parent tag is a <p>
//		if (editorContent.tagName.toLowerCase() == 'p') {
//			
//			if (typeof editorConfig.tags != 'undefined') {
//				editorConfig.tags['paragraph'] = 'br';
//			} else {
//				editorConfig.tags = {paragraph: 'br' };
//			}
//		}
//
//		// create editor
//		jQuery(el).data('medium', new Medium(editorConfig));
//		
//	},
//	
//	createExtendedEditorButtons: function(el, target) {
//
//		// bold button
//		jQuery(this.buttonPrefix+'bold', el).on('mousedown', function(ev) {
//			
//			var medium = jQuery(el).data('medium');
//			
//			medium.invokeElement('b', {});
//			jQuery('b', el).removeClass('medium-b');
//			return false;			
//		});
//
//		// italic button
//		jQuery(this.buttonPrefix+'italic', el).on('mousedown', function(ev) {
//			
//			var medium = jQuery(el).data('medium');
//			
//			medium.invokeElement('i', {});
//			jQuery('i', el).removeClass('medium-i');
//			return false;			
//		});
//		
//		// underline button
//		jQuery(this.buttonPrefix+'underline', el).on('mousedown', function(ev) {
//			
//			var medium = jQuery(el).data('medium');
//			
//			medium.invokeElement('u', {});
//			jQuery('u', el).removeClass('medium-u');
//			return false;			
//		});
//
//		// strike button
//		jQuery(this.buttonPrefix+'strike', el).on('mousedown', function(ev) {
//			
//			var medium = jQuery(el).data('medium');
//			
//			medium.invokeElement('strike', {});
//			jQuery('strike', el).removeClass('medium-strike');
//			return false;			
//		});
//		
//		// unordered list button
//		if (target.tagName.toLowerCase() != 'p') {
//
//			// paragraph button
//			jQuery(this.buttonPrefix+'p', el).on('mousedown', function(ev) {
//				
//				var medium = jQuery(el).data('medium');
//				
//				medium.invokeElement('p', {});
//				jQuery('p', el).removeClass('medium-p');
//				return false;			
//			});
//			
//			jQuery(this.buttonPrefix+'ul', el).on('mousedown', function(ev) {
//				
//				var medium = jQuery(el).data('medium');
//				var range = CMTLayout.getSelection();
//				var parent = range.startContainer.parentNode;
////				console.info(range)				
////				if (parent.tagName.toLowerCase() == 'p') {
////					parent.innerHTML = '</p>hallo' + parent.innerHTML;
////				}
//
//				if (range.collapsed) {
//					var li = document.createElement('li');
//					var ul = document.createElement('ul');
//	
//					ul.appendChild(li);
//	
//					medium.focus();
////				    var resp = medium.insertHtml(ul);
////				    console.info(resp)
////				    console.info(medium)
//					
//					jQuery(ul).insertAfter(parent);
//				} else {
//					medium.invokeElement('ul', {});
//					medium.invokeElement('li', {});
//				}
//				
//			    return false;	
//			});
//		}
//	},
//	
//	getSelection: function() {
//
//	    var sel;
//	    if (window.getSelection) {
//	        sel = window.getSelection();
//	        if (sel.rangeCount) {
//	            return sel.getRangeAt(0);
//	        }
//	    } else if (document.selection) {
//	        return document.selection.createRange();
//	    }
//	    return null;		
//	},
//	
//	editorSettings: {
//
//		// simple headline
//		head: {
//			mode: Medium.richMode,
//		    maxLength: 255,
//		    placeholder: 'neue Überschrift',
//		    tags: {
//		    	'break': 'br',
//		    	'paragraph': 'br'
//		    }
//		},
//		
//		// simple text
//		text: {
//		    mode: Medium.richMode,
//		    placeholder: 'neuer Text',
//		    maxLength: -1,
//		    tags: {
//				'break': 'br',
//				'horizontalRule': 'hr',
//				'paragraph': 'p',
//				'outerLevel': [],
//				'innerLevel': ['a']
//			},
//
//		},
//		
//		// extended text
//		extended: {
//		    mode: Medium.richMode,
//		    placeholder: 'neuer erweiterter Text',
//		    maxLength: -1,
////		    tags: null,
//		    attributes: null,
//		    tags: {
//				'break': 'br',
//				'horizontalRule': 'hr',
//				'paragraph': 'br',
//				'outerLevel': ['pre', 'blockquote', 'ul', 'p'],
//				'innerLevel': ['a', 'b', 'u', 'i', 'strong', 'strike', 'li', 'p']
//			},
//			keyContext: {
//			    'enter': function(e, element) {
//				    var sib = element.previousSibling;
//
//				    if (sib && sib.tagName.toLowerCase() == 'li') {
//					    //element.style.color = sib.style.color;
//					    element.className = sib.className;
//					    this.cursor.caretToBeginning(element);
//				    }
//			    }
//			},
//			beforeInvokeElement: function(){
//				//this.setClean(false);
//				  //console.info(this)
//				}
//		}
//	}
//	
//};

var CMTLayout = {
	
//	allowedTags: {
//		'a': ['href', 'class', 'title', 'target'],
//		'u': [],
//		'p': [],
//		'ul': [],
//		'ol': [],
//		'li': [],
//		'i': [],
//		'strike': [],
//		'b': [],
//		'br': []
//	},
	buttonPrefix: '.cmt-button-',
	menuClass: '.cmt-context-menu',
	editorClass: '.cmt-editor',
	contentClass: '.cmt-editor-content',
	tempLinkClass: '.cmt-temp-link',
	linkClass: '.cmt-link',

	initialize: function() {
		jQuery(document).on('ready', this.init.bind(this));
		this.initBasics();
	},
	
	init: function(ev) {
		jQuery(this.editorClass).each(function(index, el) {
			this.createEditor(el);
			this.initPasteing(el);
			this.initKeys(el);
			this.initContextMenu(el);
			this.initLinks(el);
		}.bindScope(this));
	},
	
	initBasics: function() {
		Function.prototype.bindScope = function(scope) {
			var _function = this;

			return function() {
				return _function.apply(scope, arguments);
			};
		};	
	},
	
	initPasteing: function (el) {
		jQuery(this.contentClass, el).on('paste', function (event) {

			event.preventDefault();
			//if (event.ctrlKey && event.keyCode == 86) {
				this.pasteText(event);
			//}
		}.bindScope(this));			
	},
	
	initKeys: function(el) {
		
		jQuery(el).on('keydown', this.triggerKeyAction.bind(this));
	},

	initLinks: function(el) {
		
		jQuery(this.linkClass, el).on('click', this.editLink.bindScope(this));
	},
	
	initContextMenu: function(el) {

		jQuery(this.contentClass, el).on('mouseup keyup', function(ev) {
			if (this.getSelection(ev.currentTarget) != '') {
				this.showContextMenu(ev);
			} else {
				this.hideContextMenu(ev);
			}
		}.bindScope(this));
	},
	
	
	showContextMenu: function(ev) {
		
		var editor = jQuery(ev.currentTarget).closest(this.editorClass);
		var menu = jQuery(this.menuClass, editor);
		var contentElement = ev.currentTarget;
		var isExtendedEditor = jQuery(contentElement).hasClass('cmt-editor-extended');

		// show / hide extended menu: block format butons
		if (isExtendedEditor && contentElement.tagName.toLowerCase() != 'p') {
			jQuery('.cmt-buttons-block').show();
		} else {
			jQuery('.cmt-buttons-block').hide()
		}

		// show / hide extended menu: block format butons
		if (isExtendedEditor) {
			jQuery('.cmt-buttons-chars').show();
		} else {
			jQuery('.cmt-buttons-chars').hide()
		}
		
		// at least show menu
		if (!jQuery(menu).hasClass('cmt-visible')) {
			
			jQuery(menu).css({top: '0', left: '0'});
			jQuery(menu).addClass('cmt-visible');
		}
		
		// and move it to the current selection's position
		this.positionContextMenu(menu);
	},
	
	/**
	 * function positionContextMenu()
	 * Refreshes / sets the context menu's position above the current selection
	 * 
	 * @param menu reference to a / the corresponding context menu 
	 */
	positionContextMenu: function(menu) {
		
		var editor = jQuery(menu).closest(this.editorClass);
		var coords = this.getSelectionsCoords();
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
	
	hideContextMenu: function(ev) {
		var editor = jQuery(ev.currentTarget).closest(this.editorClass);
		var menu = jQuery(this.menuClass, editor)
		
		if (jQuery(menu).hasClass('cmt-visible')) {
			jQuery(menu).removeClass('cmt-visible');
		}
		
	},
	
	triggerKeyAction: function(ev) {
		
		var editor = document.activeElement;

		switch(ev.which) {
			
			// return key
			case 13:
				if (editor.tagName.toLowerCase() == 'p') {
					this.insertLineBreak(editor);
				}
				break;
		}
	},
	
	/**
	 * function insertLineBreak()
	 * IE only! Inserts a <br/> when 'return' key is pressed and prevents <p> insertion. IE inserts <p> tags even if the parent node is a <p> too.
	 * 
	 * @param editor Reference to the current editable area
	 * @return void
	 */
	insertLineBreak: function(editor) {

		// ie
		if (window.clipboardData) {
			
			ev.preventDefault();
			
			// ie < 11
	    	if (typeof document.selection != 'undefined') {
	    		var sel = document.selection;
	    		var range = sel.createRange();
	    		
	    	} else {
		    	// ie >= 11
	    		var sel = window.getSelection();
	    		var range = sel.getRangeAt(0);
	    	}

	   		 var br = document.createElement('br');
	         range.insertNode(br);

		}
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
		
//		jQuery('*', clipboardData).each(function (index, el) {
////		jQuery(clipboardData).find('*').each(function (index, el) {
//			this.cleanNode(el);
//		}.bindScope(this));
	},
	
	prepareTextForPasting: function(text, element) {
		
		// shorten text to max length in headlines
		if (jQuery(element).hasClass('cmt-editor-head')) {
			text = text.substr(0, 255).replace(/\<b?r?\s?\/?$i/, '');
		}
		
		return text;
	},
	
//	cleanNode: function(el) {
//
//		var tagName = el.tagName.toLowerCase();
//		
//		if (typeof this.allowedTags[tagName] == 'undefined') {
//			jQuery(el).contents().unwrap();
//		}
//		
//		jQuery(el.attributes).each(function(index, element) {
//			var attr = element.name;
//
//			if (jQuery.inArray(attr, this.allowedTags[tagName]) == -1) {
//				jQuery(el).removeAttr(attr);
//			}
//		}.bindScope(this));
//		
//		return el;
//	},
	
	createEditor: function(el) {
		
		jQuery(this.contentClass, el).each(function (index, contentElement){

			// make editable
			jQuery(contentElement).attr('contenteditable', true);
			
			// calculate min-height for empty elements
			var tagName = contentElement.tagName;
			var tempNode = jQuery('<' + tagName + ' style="display: none">&nbsp;</' +tagName + '>');
			jQuery(document.body).append(tempNode);
			
			jQuery(contentElement).css({'min-height': jQuery(tempNode).outerHeight()+'px'});
			jQuery(tempNode).remove();
			
			// prevent p-tags if parent tag is a <p>
			if (contentElement.tagName.toLowerCase() == 'p') {
				
			}

		}.bindScope(this));
		
		// create general editor buttons
		this.createEditorButtons(el);
		
		// if editor is defined as "extended", show formatting buttons
		

	},
	
	createEditorButtons: function(el) {

		// link button
		jQuery(this.buttonPrefix + 'a', el).on('mousedown', function(ev) {
			if (CMTLayout.isActiveEditorsButton()) {
				document.execCommand('insertHTML', false, '<a href="Javascript:void(0);" class="' + this.tempLinkClass.substring(1) + '">' + this.getSelection() + '</a>');
				
				// do more CMT link insertion ...
				var link = jQuery(this.tempLinkClass, document.activeElement);
				
				jQuery(link).data('cmt-link-id', 0);
//				jQuery(link).on('click', function(ev) {
//					this.editLink(ev);
//				}.bindScope(this));
				jQuery(link).on('click', this.editLink.bindScope(this));
				
				// hide context menu
				jQuery(document.activeElement).trigger('mouseup');
			}
			return false;			
		}.bindScope(this));
		
		// unlink / remove button
		jQuery(this.buttonPrefix + 'remove-format', el).on('mousedown', function(ev) {
			if (CMTLayout.isActiveEditorsButton()) {
				document.execCommand('removeFormat', false, null);
			}
			return false;			
		});
		
		if (!jQuery.isEmptyObject(jQuery('.cmt-editor-extended', el))) {
			this.createExtendedEditorButtons(el);
		}
		
		// initially hide extended buttons
		jQuery('.cmt-buttons-chars, .cmt-buttons-block', el).css({display: 'none'});
	},
		
	createExtendedEditorButtons: function(el) {
		
		// bold button
		jQuery(this.buttonPrefix+'b', el).on('mousedown', function(ev) {
			if (CMTLayout.isActiveEditorsButton()) {
				document.execCommand('bold', false, null);
			}
			return false;			
		});

		// italic button
		jQuery(this.buttonPrefix+'i', el).on('mousedown', function(ev) {
			if (CMTLayout.isActiveEditorsButton(el)) {
				document.execCommand('italic', false, null);
			}
			return false;			
		});
		
		// underline button
		jQuery(this.buttonPrefix+'u', el).on('mousedown', function(ev) {
			if (CMTLayout.isActiveEditorsButton(el)) {
				document.execCommand('underline', false, null);
			}
			return false;
		});

		// strike button
		jQuery(this.buttonPrefix+'strike', el).on('mousedown', function(ev) {
			if (CMTLayout.isActiveEditorsButton(el)) {
				document.execCommand('strikeThrough', false, null);
			}
			return false;
		});
		
		// paragraph button
		jQuery(this.buttonPrefix+'p', el).on('mousedown', function(ev) {
			
			if (CMTLayout.isActiveEditorsButton(el)) {
				document.execCommand('formatBlock', false, 'p');
			}
			return false;			
		});
		
		jQuery(this.buttonPrefix+'ul', el).on('mousedown', function(ev) {
			
			if (CMTLayout.isActiveEditorsButton(el)) {
				document.execCommand('insertUnorderedList', false, null);
			}
		    return false;
		});
	},
	
	isActiveEditorsButton: function() {
	return true;
		var activeEl = document.activeElement;

		if (jQuery(activeEl).hasClass('cmt-editor-extended') && !jQuery.isEmptyObject(jQuery(activeEl).closest(this.editorClass))) {
			return true;
		}
		return false;
		
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
		sel = window.getSelection();
        range = sel.getRangeAt(0),
        boundary = range.getBoundingClientRect();
        return {
        	left: boundary.left,
        	right: boundary.right,
        	top: boundary.top,
        	width: boundary.right - boundary.left,
        	center: (boundary.left + boundary.right) / 2
        };		
	},
	
	editLink: function(ev) {
		var link = ev.currentTarget;
		var linkID = link.data('cmt-link-id');
	}
};

CMTLayout.initialize();

