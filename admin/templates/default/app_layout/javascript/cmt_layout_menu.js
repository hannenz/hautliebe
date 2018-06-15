var CMTLayoutMenu = {
	
	menuID: '#cmt-menu',
	openMenuClass: '.cmt-menu-open',
	panelClass: '.cmt-panel',
	panelIDPrefix: '#cmt-panel-',
	subpanelIDPrefix: '#cmt-subpanel-',
	subpanelClass: '.cmt-subpanel',
	visibilityClass: '.cmt-visible',
	selectedClass: '.cmt-selected',

	initialize: function() {

		jQuery(document).on('ready.CMTLayoutMenu', this.init.bind(this));
	},
	
	init: function(ev) {

		// init menu
		this.initMenu();
		this.initButtons();
		this.initSubpanelSelection();
		
		// init special fields
		this.initDownloadSelection();
		this.initScriptSelection();
		this.initPageSelection();
		this.initImageSelection();
		this.initObjectPanel();
		
		// show first panel
		this.showPanel();

	},

	initMenu: function() {
		
		// add outer container for menu and effect
		jQuery('body').html('<div id="cmt-container"><div id="cmt-pusher"><div id="cmt-menu" class="cmt-level-2"></div><div id="cmt-content">' + jQuery('body').html() + '</div></div></div>'); 

		// copy body's paddings and margins to content wrapper container
		jQuery('#cmt-content').css({
			paddingTop: parseInt(jQuery('body').css('paddingTop')) + parseInt(jQuery('body').css('marginTop')) + 'px',
			paddingBottom:  parseInt(jQuery('body').css('paddingBottom')) + parseInt(jQuery('body').css('marginBottom')) + 'px',
			paddingLeft:  parseInt(jQuery('body').css('paddingLeft')) + parseInt(jQuery('body').css('marginLeft')) + 'px',
			paddingRight:  parseInt(jQuery('body').css('paddingRight')) + parseInt(jQuery('body').css('marginRight')) + 'px',
		});
		
//		jQuery('#cmt-content').css({
//			marginTop: jQuery('body').css('marginTop'),
//			marginBottom: jQuery('body').css('marginBottom'),
//			marginLeft: jQuery('body').css('marginLeft'),
//			marginRight: jQuery('body').css('marginRight')
//		})

		jQuery(document.body).css({
			paddingTop: 0,
			paddingBottom: 0,
			paddingLeft: 0,
			paddingRight: 0,
			marginTop: 0,
			marginBottom: 0,
			marginLeft: 0,
			marginRight: 0 
		});
		
		// create menu handle
		jQuery('#cmt-menu-handle').appendTo('body').on('click', function(ev) {
			
			if (jQuery('body').hasClass(this.openMenuClass.substring(1))) {
				this.closeMenu();
			} else {
				this.openMenu();
			}
		}.bindScope(this));
		jQuery('#cmt-menu-handle').css('width', jQuery('#cmt-menu-handle').width+'px')
		
		// create menu main content
		jQuery('#cmt-panel-main').appendTo('#cmt-menu');
		
		// init dynamic containers
		jQuery(window).on('resize.CMTLayoutMenu', function(ev) {
			this.recalculateDynamicContainersHeight();
		}.bindScope(this));
		
		// catch clicks on menu
		jQuery(this.menuID + ', #cmt-menu-handle').on('mousedown.CMTLayout, click.CMTLayout, mouseup.CMTLayout', function(ev) {
			ev.stopPropagation();
		});
		
		// init elements with calculated max height.
		jQuery(window).trigger('resize.CMTLayoutMenu');
	},
	
	
	recalculateDynamicContainersHeight: function(){
		var availableHeight = jQuery(this.menuID).height();
		var dynamicElement = jQuery(this.menuID + ' .cmt-subpanel.cmt-visible .cmt-dynamic-content');

		dynamicElement.first().each(function(index, el) {
			
			var getSiblingsHeight = function(el) {
				
				jQuery(el).prevAll().filter(':visible').each(function(i, sibling) {
					availableHeight -= jQuery(sibling).outerHeight(true);
				});
				
				jQuery(el).nextAll().filter(':visible').each(function(i, sibling) {
					availableHeight -= jQuery(sibling).outerHeight(true);
				});
				
				var parent = jQuery(el).parent();
				if (jQuery(parent).attr('id') != 'cmt-menu') {
					getSiblingsHeight(parent);
				}
			}
			
			return getSiblingsHeight(el)
			
		})
		
		availableHeight -= jQuery(dynamicElement).first().outerHeight(true) - jQuery(dynamicElement).first().height();

		jQuery(dynamicElement).first().css('height', availableHeight + 'px');
	},
	
	resetDynamicContainersHeight: function() {

		var dynamicElement = jQuery(this.menuID + ' .cmt-dynamic-content');
		
		dynamicElement.each(function(index, el) {
			jQuery(el).css('height', 'auto');
		});
	},
	
	initButtons: function() {
		
		// save link button
		jQuery('.cmt-button-save-link').on('click.CMTLayoutMenu', this.passDataToCaller.bindScope(this));
		
		// save image button
		jQuery('.cmt-button-save-image').on('click.CMTLayoutMenu', this.passDataToCaller.bindScope(this));
		
		// save script button
		jQuery('.cmt-button-save-script').on('click.CMTLayoutMenu', this.passDataToCaller.bindScope(this));
		
		// abort buttons
		jQuery('.cmt-button-abort:not(.cmt-ignore)').on('click.CMTLayoutMenu', function() {
			this.closePanel();
		}.bindScope(this));
	},
	
	initSubpanelSelection: function() {
		
		jQuery('.cmt-select-subpanel').on('change', function(ev) {
			
			var panel = jQuery(ev.currentTarget).closest(this.panelClass);
			var subpanel = jQuery('option:selected', ev.currentTarget).data('subpanel-id');
	
			this.showPanel(panel, jQuery('#' + subpanel));
		}.bindScope(this))
	},
	
	initObjectPanel: function() {
		
		jQuery('#cmt-data-object-template-id').on('change', function(ev) {
			var select = ev.currentTarget;
			CMTLayout.changeObjectTemplate(jQuery(this.getCurrentPanel()).data('caller'), select.value);
		}.bindScope(this));
		
		jQuery('#cmt-button-abort-object-panel').on('click.CMTLayout', function(ev) {
			CMTLayout.resetObjectTemplate(jQuery(this.getCurrentPanel()).data('caller'));
			this.showPanel();
		}.bindScope(this));
		
		jQuery('#cmt-button-save-object-panel').on('click.CMTLayout', function(ev) {
			CMTLayout.saveObjectTemplate();
			this.showPanel();
		}.bindScope(this));
	},
	
	initDownloadSelection: function() {
		
		// load directory content on change
		jQuery('#cmt-data-download-url').on('change', function(ev) {
			this.loadDirectory(jQuery(ev.currentTarget).val());
		}.bindScope(this));
		
		// change input field value on click
		jQuery('#cmt-directory-content-container').on('click', 'a.cmt-select-directory', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			
			this.loadDirectory(jQuery(ev.currentTarget).data('cmt-path'));
			
		}.bindScope(this));
		
		// select file click
		jQuery('#cmt-directory-content-container').on('click', 'a.cmt-select-file', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			jQuery('#cmt-directory-content-container a' + this.selectedClass).removeClass(this.selectedClass.substring(1));
			jQuery(ev.currentTarget).addClass(this.selectedClass.substring(1));
			
			jQuery('#cmt-data-download-url').val(jQuery(ev.currentTarget).data('cmt-path'));
		}.bindScope(this));
	},

	initScriptSelection: function() {
		
		// load new directory content
		jQuery('#cmt-scripts-content-container').on('click', 'a.cmt-select-directory', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			
			this.loadScripts(jQuery(ev.currentTarget).data('cmt-path'), jQuery(ev.currentTarget).data('cmt-base-path'));

		}.bindScope(this));
		
		// select file click
		jQuery('#cmt-scripts-content-container').on('click', 'a.cmt-select-file', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			jQuery('#cmt-scripts-content-container a' + this.selectedClass).removeClass(this.selectedClass.substring(1));
			jQuery(ev.currentTarget).addClass(this.selectedClass.substring(1));
			
			jQuery('#cmt-data-script-path').val(jQuery(ev.currentTarget).data('cmt-path'));
		}.bindScope(this));
	},
	
	initImageSelection: function() {
		
		// change directory content on click
		jQuery('#cmt-images-content-container').on('click.CMTLayoutMenu', 'a.cmt-select-directory', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			
			this.loadImages(jQuery(ev.currentTarget).data('cmt-path'), jQuery('#cmt-data-image-base-path').val());
			
		}.bindScope(this));
		
		// select file click
		jQuery('#cmt-images-content-container').on('click.CMTLayoutMenu', '.cmt-select-image', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();
			
			var image = jQuery(ev.currentTarget).find('img');

			// select clicked image
			jQuery('#cmt-images-content-container ' + this.selectedClass).removeClass(this.selectedClass.substring(1));
			jQuery(ev.currentTarget).addClass(this.selectedClass.substring(1));
			
			// now asign image src to calling image
			var panel = jQuery(image).closest(this.panelClass);
			var caller = jQuery(panel).data('caller');

			CMTLayout.setImageSource({
				wrapper: caller,		// wrapper!!
				image: image,		// not neccessary
				src: jQuery(image).data('cmt-image-path'),
				width: jQuery(image).data('cmt-image-width'),
				height: jQuery(image).data('cmt-image-height')
			});
			
		}.bindScope(this));
	},
	
	/**
	 * function loadDirectory()
	 * AJAX-Request: load a directory content in the download link selection container.
	 *  
	 * @param path A file path
	 * 
	 * @return void
	 */	
	loadDirectory: function(path) {
		
		this.isInProgress('#cmt-directory-content-container');
		
		jQuery.ajax({
			type: 'POST',
			data: {
				cmtPath: path,
				cmtAction: 'loadDirectory'
			}
		}).done(function(response) {
			response = JSON.parse(response);
			
			if (response.error) {
				// TODO: handle error
				console.info('error')
			} else {
				jQuery('#cmt-directory-content-container').html(response.html);
			}
			
			this.isRelaxed('#cmt-directory-content-container');
		}.bindScope(this));
	},

	/**
	 * function loadScripts()
	 * AJAX-Request: load a directory content in the script selection container.
	 *  
	 * @param path A file path
	 * 
	 * @return void
	 */
	loadScripts: function(path, basePath) {

		this.isInProgress('#cmt-scripts-content-container');
		
		jQuery.ajax({
			type: 'POST',
			data: {
				cmtPath: path,
				cmtBasePath: basePath,
				cmtAction: 'loadScripts'
			}
		}).done(function(response) {
			response = JSON.parse(response);
			
			if (response.error) {
				// TODO: handle error
				console.info('error')
			} else {
				jQuery('#cmt-scripts-content-container').html(response.html);
			}
			
			this.isRelaxed('#cmt-scripts-content-container');
		}.bindScope(this));
	},

	/**
	 * function loadImages()
	 * AJAX-Request: load a directory content in the images selection container.
	 *  
	 * @param string path A file path
	 * @param string basePath The base/ root path of the image element.
	 * 
	 * @return void
	 */
	loadImages: function(path, basePath) {
		
		this.isInProgress('#cmt-images-content-container');
		
		jQuery.ajax({
			type: 'POST',
			data: {
				cmtPath: path,
				cmtBasePath: basePath,
				cmtAction: 'loadImages'
			}
		}).done(function(response) {
			response = JSON.parse(response);
			
			if (response.error) {
				// TODO: handle error
				console.info('error')
			} else {		
				this.isRelaxed('#cmt-images-content-container');
				jQuery('#cmt-images-content-container').html(response.html);
				
				// Sort alphabetically
				var list = jQuery(".cmt-images");
				var listItems = list.children("li").get();
				listItems.sort (function (a, b) {
					var compA = $(a).find("figcaption").text().toUpperCase();
					var compB = $(b).find("figcaption").text().toUpperCase();
					return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
				});
				jQuery.each (listItems, function (i, item) {
					list.append (item);
				});
				CMTLayoutMenu.initViewStyle ();
				CMTLayoutMenu.initUpload ();
			}
		}.bindScope(this));
	},
	
	initPageSelection: function() {
		
		// load pages content on change language
		jQuery('#cmt-data-internal-language').on('change.CMTLayoutMenu', function(ev) {
			this.loadPages({
				parentID: 'root',
				language: jQuery(ev.currentTarget).val()
			})
		}.bindScope(this));
		
		// select file click
		jQuery('#cmt-pages-content-container').on('click.CMTLayoutMenu', 'a.cmt-select-page', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			jQuery('#cmt-pages-content-container a' + this.selectedClass).removeClass(this.selectedClass.substring(1));
			jQuery(ev.currentTarget).addClass(this.selectedClass.substring(1));
		
			jQuery('#cmt-data-internal-page-id').val(jQuery(ev.currentTarget).data('cmt-internal-page-id'));

			//this.loadPages(jQuery(ev.currentTarget).data('cmt-internal-page-id'), jQuery('#cmt-data-language').val());
			
		}.bindScope(this));
		
		// show file's children click
		jQuery('#cmt-pages-content-container').on('click.CMTLayoutMenu', 'a.cmt-select-children', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			this.loadPages({
				parentID: jQuery(ev.currentTarget).data('cmt-internal-parent-id'), 
				language: jQuery('#cmt-data-language').val()
			});
			
		}.bindScope(this));
		
		// show file's parent click
		jQuery('#cmt-pages-content-container').on('click.CMTLayoutMenu', 'a.cmt-select-parent', function(ev) {
			ev.preventDefault();
			ev.stopPropagation();

			this.loadPages({
				pageID: jQuery(ev.currentTarget).data('cmt-internal-parent-id'), 
				language: jQuery('#cmt-data-language').val()
			});
			
		}.bindScope(this));
	},
	
	loadPages: function(params) {
		
		this.isInProgress(jQuery('#cmt-pages-content-container'));
		
		jQuery.ajax({
			type: 'POST',
			data: {
				cmtInternalPageID: params.pageID,
				cmtInternalLanguage: params.language,
				cmtInternalParentID: params.parentID,
				cmtAction: 'loadPages'
			}
		}).done(function(response) {
			response = JSON.parse(response);
			
			if (response.error) {
				// TODO: handle error
				console.info('error');
			} else {

				jQuery('#cmt-pages-content-container').html(response.html);
				jQuery('#cmt-pages-content-container a.cmt-select-page[data-cmt-internal-page-id=' + jQuery('#cmt-data-internal-page-id').val() + ']').addClass('cmt-selected');
			}
			
			this.isRelaxed(jQuery('#cmt-pages-content-container'));
		}.bindScope(this));
	},
	
	
	openMenu: function(ev) {
		jQuery('body').addClass(this.openMenuClass.substring(1));
		
		var ms = parseFloat(jQuery('#cmt-pusher').css('transition-duration')) * 1000 + 200;
		setTimeout(CMTLayout.onMenuOpened,ms)
	},

	closeMenu: function(ev) {
		jQuery('body').removeClass(this.openMenuClass.substring(1));

		var ms = parseFloat(jQuery('#cmt-pusher').css('transition-duration')) * 1000 + 200;
		setTimeout(CMTLayout.onMenuOpened,ms)
	},	
	
	showPanel: function(panel, subpanel) {
				
		this.openMenu();
		this.hideAllMessages();
	
		// hide all other panels and subpanels
		jQuery(this.panelClass + ',' + this.subpanelClass).removeClass(this.visibilityClass.substring(1));
		
		// show panel
		if (typeof panel == 'string') {
			panel = jQuery(this.panelIDPrefix + panel);
		} else if (typeof panel != 'object') {
			panel = jQuery(this.panelClass).first()
		}
		
		if (!jQuery(panel).hasClass(this.visibilityClass.substring(1))) {
			jQuery(panel).addClass(this.visibilityClass.substring(1));
		}

		// show subpanel
		if (typeof subpanel != 'object') {
			subpanel = jQuery(this.subpanelIDPrefix + subpanel);
		} else if (typeof subpanel != 'object') {
			subpanel = jQuery(this.subpanelClass, panel).first();
		}
			
		if (!jQuery(subpanel).hasClass(this.visibilityClass.substring(1))) {
			jQuery(subpanel).addClass(this.visibilityClass.substring(1));
		}

		// adjust element heights
		jQuery(window).trigger('resize.CMTLayoutMenu');
		
		// do some internal actions if needed
		this.initPanel(panel, subpanel);

	},
	
	initPanel: function(panel, subpanel) {

		var panelID = '#' + jQuery(panel).prop('id') || '';
		var subpanelID = '#' + jQuery(subpanel).prop('id') || '';

		// on panel action
		switch(panelID.replace(this.panelIDPrefix, '')) {
		
			case 'image':
				this.loadImages(jQuery('#cmt-data-image-path').val(), jQuery('#cmt-data-image-base-path').val());
				break;
				
			case 'link-internal':
				this.loadPages({
					pageID: jQuery('#cmt-data-internal-page-id').val(),
					language: jQuery('#cmt-data-internal-language').val()
				});
				break;
				
			case 'script':
				this.loadScripts(jQuery('#cmt-data-script-path').val(), jQuery('#cmt-data-script-base-path').val());
				break;
				
			case 'object':
				//this.dontCloseOnFocus = true;
				break;
		}
		
		// on subpanel action
		switch(subpanelID.replace(this.subpanelIDPrefix, '')) {
		
			case 'link-download':
				this.loadDirectory(jQuery('#cmt-data-download-url').val());
				break;
// TODO: ??? will be also executed some lines above!!!				
			case 'link-internal':
				this.loadPages({
					pageID: jQuery('#cmt-data-internal-page-id').val(),
					language: jQuery('#cmt-data-internal-language').val()
				});
				break;
				
			case 'image-select':
				var caller = jQuery(panelID).data('caller');
				var wrapper = jQuery(caller).closest('ui-wrapper');
				
				jQuery(wrapper).data({
					'cmt-image-width': jQuery(wrapper).width(),
					'cmt-image-height': jQuery(wrapper).height(),
					'cmt-image-path': jQuery(caller).attr('src')
				})
				break;
		}
	},
	
	closePanel: function() {
		
		// avoid immediate closing after a button in an object's handle bar is clicked 
		// (fires CMTLayout.receiveFocus() which closes all panels, even the panel openen right before with the same click)
		// TODO: do we need this anymore?
/*
		if (this.dontCloseOnFocus) {
			this.dontCloseOnFocus = false;
			return;
		}
*/		
		var panel = this.getCurrentPanel();
		var subpanel = this.getCurrentSubpanel();
		var panelID = '#' + jQuery(panel).attr('id');
		var panelType = panelID.replace(this.panelIDPrefix, '')

		switch (panelType) {
			case 'link':

				// remove all temporary links
				CMTLayout.removeTempLinks(true);
				break;
				
			case 'image':
				
				// restore old image
				CMTLayout.undoImageSelection(jQuery(panel).data('caller'));
				break;
				
			case 'script':
				
				// restore script path
				//CMTLayout.restore
				break;
				
		}
		
		this.resetDynamicContainersHeight();
		
		//jQuery(panel).css('height', 'auto');
		
		// show initial panel
		this.showPanel();
	},
	
	passDataToMenu: function(params) {
		
		var panel =  jQuery(this.panelIDPrefix + params.panel);
	
		// pass form data
		for (var key in params.data) {
			
			jQuery(panel).data(key, params.data[key] )
		
			var elID = '#' + key.replace('cmt-', 'cmt-data-');

			var el = jQuery(elID, panel);
			var tagName = jQuery(el).prop('tagName') || '';
		
			switch (tagName.toLowerCase()) {
				case 'input':
					jQuery(el).val(params.data[key]);
					break;
					
				case 'select':
//					jQuery(el).val(params.data[key]).change();
					jQuery(el).val(params.data[key]);
					break;
					
				default:
					jQuery(el).text(params.data[key]);
					break;
			}
		}
		
		// pass other data
		jQuery(panel).data('caller', params.caller);

	},
	
	passDataToCaller: function(ev) {
		
		var panel = jQuery(ev.currentTarget).closest(this.panelClass);
		var data = {};

		jQuery('select, input', panel).each(function(index, el) {
			
			data[el.name.replace('cmt-data-', 'cmt-')] = jQuery(el).val();
		});
		
		CMTLayout.getDataFromMenu({
			caller: jQuery(panel.data('caller')),
			data: data,
			panel: panel[0].id.replace(this.panelIDPrefix.substring(1), '')
		});
	
		// reset panels
		this.showPanel();
	},
	
	getCurrentPanel: function() {
		return jQuery(this.panelClass + this.visibilityClass).first();
	},
	
	getCurrentSubpanel: function() {
		return jQuery(this.subpanelClass + this.visibilityClass).first();
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
	
	showMessage: function(messageType, type, additionalMessage) {
		
		if (typeof additionalMessage != 'undefined') {
			additionalMessage = '<br/>' + additionalMessage;
		} else {
			additionalMessage = '';
		}
		
		var typeSuffix = 'info';
		
		switch (messageType) {
			case 'error':
				typeSuffix = 'error';
				break;
				
			case 'success':
				typeSuffix = 'success';
				break;
				
			case 'warning':
				typeSuffix = 'warning';
				break;
				
			case 'info':
				typeSuffix = 'info';
				break;
		}
		
		var messageContent = jQuery('#cmt-message-contents .cmt-message-' + type).html();
		
		jQuery('<div class="cmt-message cmt-message-' + typeSuffix + '" style="display: none">' + messageContent + '<br />' + additionalMessage + '</div>')
		.appendTo('#cmt-messages')
		.on('click', function(ev) {
			jQuery(this).fadeOut({
				complete: function() {
					jQuery(this).remove();
				} 
			});
		})
		.fadeIn();
	},
	
	hideAllMessages: function() {
		jQuery('#cmt-messages .cmt-message').fadeOut({
			complete: function() {
				jQuery(this).remove();
				CMTLayoutMenu.recalculateDynamicContainersHeight();
			} 
		});
	},

	initViewStyle: function () {

		jQuery(".cmt-images-view-options input[type=radio]").on("change", function () {
			switch (jQuery(this).val()) {
				case "list":
					jQuery(".cmt-images").addClass ("cmt-images-view-style--list");
					jQuery(".cmt-images").removeClass ("cmt-images-view-style--grid");
					break;
				case "grid":
					jQuery(".cmt-images").addClass ("cmt-images-view-style--grid");
					jQuery(".cmt-images").removeClass ("cmt-images-view-style--list");
					break;
			}
		});

		var range = jQuery("input[type=range]");
		range.on("input", function (ev) {
			jQuery(".cmt-images li").css("width", range.val() + "px");
		});
		jQuery(".cmt-images li").css("width", range.val() + "px");
	},

	initUpload: function () {

		console.log ("initUpload");
		var uploadURL = jQuery("#cmtUploadURL").val ();
		jQuery("#cmtUploadFiles").on ("change", function () {
			console.log ("about to upload", this.files);
			uploadFileList (this.files);
		});

		jQuery("body").on ("dragenter", function (ev) {
			jQuery("#cmt-images-content-container .cmt-images").css ("outline", "2px dashed purple");
		});

		jQuery("body").on ("dragleave", function (ev) {
			jQuery("#cmt-images-content-container .cmt-images").css ("outline", "0");
		});

		jQuery("#cmt-images-content-container .cmt-images").on ("drop", function (ev) {
			ev.stopPropagation ();
			ev.preventDefault ();
			console.log ("Dropping: ", ev.dataTransfer.files);
			uploadFileList (ev.dataTransfer.files);
		});

		function uploadFileList (files) {

			var filelist = [],
				totalSize = 0,
				totalProgress = 0,
				currentUpload = null;

			for (var i = 0; i < files.length; i++) {
				filelist.push (files[i]);
				totalSize += files[i].size;
			}
			startNextUpload ();

			function startNextUpload () {
				if (filelist.length > 0) {
					currentUpload = filelist.shift ();
					uploadFile (currentUpload);
				}
			}

			function uploadFile (file) {
				var xhr = new XMLHttpRequest ();
				xhr.upload.addEventListener ("progress", onProgress, false);
				xhr.addEventListener ("load", onLoad, false);
				xhr.addEventListener ("error", onError, false);
				xhr.open ("POST", uploadURL);
				var formdata = new FormData ();
				formdata.append ("cmtUploadFiles", file);
				formdata.append ("cmtUploadPath", $("input[name=cmtUploadPath]").val());
				xhr.send (formdata);

				function onProgress (event) {
					var progress = totalProgress + event.loaded;
				}

				function onLoad (event) {
					try {
						if (event.target.status < 200 || event.target.status >= 400) {
							throw event.target.status;
						}

						var response = JSON.parse (event.target.responseText);

						if (!response.error) {
							// TODO: Wouldn't it be better to call loadImages in 
							// the first place? 
							// CMTLayoutMenu.loadImages ();
							$('#cmt-images-content-container').html(response.html);
							CMTLayoutMenu.initUpload ();
							CMTLayoutMenu.initViewStyle ();
						}
						else {
							uploadError ();
						}
					}
					catch (e) {
						uploadError (e.toString ());
					}

					totalProgress += currentUpload.size;
					startNextUpload ();
				}

				function onError (event) {
					uploadError (xhr.status);
					console.log ("Upload failed: " + currentUpload.name);
					totalProgress += currentUpload.size;
					startNextUpload ();
				}
			}

			function uploadError (mssg) {
				var errorMessage = "Upload failed: " + currentUpload.name + ": " + mssg;
				alert (errorMessage);
			}
		}
	}
};


