/**
 * cmt_filebrowser_functions.js
 * All javascripting for the filebrowser application in content-o-mat
 * 
 * @author A.Alkaissi, J.Hahn <info@content-o-mat.de>
 * @version 2015-04-27
 *
 */

cmtFileBrowser = {

	options: {
		directoriesTableID: '#cmtDirectoriesTable',
		filesTableID: '#cmtFilesTable',
		directoriesNodePrefix: 'cmtDirectoryNode',
		filesNodePrefix: 'cmtFileNode',
		nodeID: 'root',
	},
	
	selectedFiles :'',
	currentRenameContainer: null,
	counter: 1,
	imagePreviewDialogID: '#cmtDialogPreviewImage',
	textEditorDialogID: "#cmtDialogTextEditor",
	textEditorID: "#cmtTextEditor",
	uploadURL: null, 
	dropUploadProgressBar: null,
		
	/**
	 * function initialize()
	 * 
	 * @param void
	 * @return void
	 */
	initialize: function() {
		jQuery(document).ready(this.initFileBrowser.bind(this));
	},
	
	
	/**
	 * function initFileBrowser()
	 * Init filebrowser after page loaded
	 */
	initFileBrowser: function() {
		
		// Init treetable for directories (left column)
		this.initDirectoriesView();
		
		// Init directory content (right column)
		this.initDirectoryContent();
		
		// Init messages
		this.initMessages();

		// Init columns height
		this.initColumns();
		this.resizeColumns();

		// Init buttons
		jQuery('#cmtButtonDeleteSelectedFiles').click(this.deleteSelectedFilesDialog.bind(this));
		jQuery('#cmtButtonUploadFiles').click(this.showUploadFilesDialog.bind(this));

		this.initMultiUpload ();
		this.initDropUpload ();
	},
	
	initColumns: function() {
		
		jQuery(window).on('resize', function(ev) {
			this.resizeColumns();
		}.bindScope(this));
		
		jQuery('body').css({
			overflow: 'hidden'
		});
		
		// change containers' overflow status
		jQuery('.cmtAdjustHeight', '#cmtContentContainer').each(function (index, el) {
			jQuery(el).css({
				overflowY: 'auto',
				overflowX: 'hidden'
			});
		});

	},
	
	resizeColumns: function() {

		var totalHeight = jQuery('#cmtMainContent').height() - parseInt(jQuery('#cmtMainContent').css('padding-top'))  - parseInt(jQuery('#cmtMainContent').css('padding-bottom'));
		var staticElementsHeight = parseInt(jQuery('#cmtHeaderContainer').height()) + parseInt(jQuery('#cmtFooterContainer').height());
		var availableHeight = totalHeight - staticElementsHeight;

		jQuery('#cmtContentContainer').height(availableHeight);

		jQuery('.cmtAdjustHeight', '#cmtContentContainer').each(function (index, el) {
			var borders = parseInt(jQuery(el).css('border-top-width')) + parseInt(jQuery(el).css('border-bottom-width'));
			jQuery(el).height(availableHeight - borders);
		});
		
	},
	
	hideMessages: function(){
		cmtPage.hideMessage('#cmtMessageContainer');
	},

	/**
	 * function initDirectoriesView()
	 * inits the directories view of the file browser (left column)
	 * 
	 * @param void
	 * @return void
	 */
	initDirectoriesView: function() {
		
		jQuery(this.options.directoriesTableID).treeTable({
			indent: 20,
			nodesDraggable: false,		// filebrowser uses own drag'n'drop scripting for both columns
			rootDir: this.options.nodeID,
			nodePrefix: this.options.directoriesNodePrefix,
// Event besser in onBeforeAjax: auch in cmtTableTree überarbeiten!
			beforeLoadAjaxUrl: function(node, url){
				var nodePath = jQuery(node).data('path');
				var url = url.replace(/\{directoryPath\}/, nodePath);
				return url;
			},
			
			ajaxURL: document.URL + '&cmtAction=showDirectories&parent={nodeID}&cmtDirectory={directoryPath}'
		});

	},
	
	initDirectoryContent: function() {
		this.initPreviewFiles();
		// this.initDragnDrop();
		this.initEntryRename();
	},
	
	/**
	 * function initEntryRename()
	 * Inits all file and directory entries to rename them inline
	 * 
	 *  @param void
	 *  @return void
	 */
	initEntryRename: function() {
		
		jQuery('.cmtEntryName').each( function(index, el) {
			
			// show input field on click
			jQuery(el).on('click.cmtFilebrowser', this.showEntryRenameContainer.bind(this));
			
			var parent = jQuery(el).closest('td');
			jQuery(parent).wrapInner('<div style="position: relative"></div>');
			
			jQuery(parent).find('.cmtRenameEntryContainer').css({
				position: 'absolute',
				left: jQuery(el).position()['left'] + 'px',
				top: jQuery(el).position()['top'] - 2 + 'px'
			}).on('click.cmtFilebrowser', this.clickOnRenameContainer.bind(this))
		}.bindScope(this))
		
		// hide input field by clicking the back buttons
		jQuery('.cmtEditFileBack').on('click.cmtFilebrowser', this.hideEntryRenameContainer.bind(this));
		jQuery('.cmtEditFileSave').on('click.cmtFilebrowser', this.saveNewFilename.bind(this));

		// hide input field by clicking the document
		jQuery(document).on('click.cmtFilebrowser', this.hideEntryRenameContainer.bind(this));
		
	},
	
	initPreviewFiles: function() {
		
		/*
		 *  create images preview
		 */
		jQuery('#cmtFilesTable a[data-typecategory=image]').each(function (index, tag) {
				
			tag.href = 'Javascript:void(0)';
			
			jQuery(tag).click(function(event) {

				//event.preventDefault();
				var el = event.target;
				
				var fileType = jQuery(el).data('type');
				var imageWidth = jQuery(el).data('imagewidth');
				var imageHeight = jQuery(el).data('imageheight');
				var filePath = jQuery(el).data('filepath');
				var fileExtention = jQuery(el).data('type');
				var fileTitle = jQuery(el).parent().find('span').text();
				var dialogPaddingOffset = 53;	// a little bit "Pfusch"
				var dialogMargin = 20; // a little bit "Pfusch"
	
				if(!imageWidth || !imageHeight) {
					return
				}
		
				var dialogWidth = 'auto' //imageWidth;
				var dialogHeight = 'auto' //imageHeight;

				
				var viewportWidth = jQuery(window).width();
				var viewportHeight = jQuery(window).height();
				
				var viewportRatio = viewportWidth / viewportHeight;
				var imageRatio = imageWidth / imageHeight;
				
				if (imageWidth > viewportWidth || imageHeight > viewportHeight) {
						
					if (viewportRatio > 1 && imageRatio < 1) {
						dialogHeight = viewportHeight - dialogMargin;
						dialogWidth = 'auto'
					} else if (viewportRatio < 1 && imageRatio > 1) {
						dialogWidth = viewportWidth - dialogMargin;
						dialogHeight = 'auto'
					} else {
						var factor = (viewportHeight - dialogMargin - dialogPaddingOffset) / imageHeight;
						dialogWidth = factor * imageWidth;
						dialogHeight = factor * imageHeight + dialogPaddingOffset 
					}
				}

				jQuery(this.imagePreviewDialogID).html('<img src="'+filePath+'" width="100%" />');

				jQuery(this.imagePreviewDialogID).dialog({
					resizable: false,
					width: dialogWidth,
					height: dialogHeight,
					modal: true,
					title: fileTitle,
					close: function() {
						jQuery(jQuery.data(this, 'cmtOpener')).tooltip('close');
						cmtPage.unselectRow(jQuery(jQuery.data(this, 'cmtOpener')).closest('.cmtSelectable'));
					}
				});
				
				// register opener tag for opened dialog
				jQuery(this.imagePreviewDialogID).data('cmtOpener', tag);

			}.bindScope(this)).bind(this);
		}.bindScope(this));
		
		/*
		 *  create file editors
		 */
		jQuery('#cmtFilesTable a[data-typecategory=text]').on('click', function(event) {
			
			var tag = event.target;
			tag.href = 'Javascript:void(0)';

			var filePath = jQuery(tag).data('filepath');
			
			var url = document.URL + '&cmtAction=readFile&cmtFile=' + filePath;					
			
			jQuery.ajax({
				url: url,
				dataType: 'json',
				type:'POST',
				complete: this._loadTextEditorContent.bindScope(this) 
			});
			
			// register opener tag for opened dialog
			jQuery(this.textEditorDialogID).data('cmtOpener', jQuery(tag).closest('.cmtSelectable'));

			
		}.bindScope(this)).bind(this);
		
		/*
		 * create image previews in tooltips 
		 */
		jQuery('.cmtFile', '#filesColumn').tooltip({
			position: {
				my: "left-124 center",
				at: "left center",
				using: function( position, feedback ) {
					$( this ).css( position );
					$( "<div>" )
					.addClass( "arrow right" )
					.addClass( feedback.vertical )
					.addClass( feedback.horizontal )
					.appendTo( this );
				}
			},
			items: "[data-imageWidth]",
			content: function() {
				var element = jQuery( this );

				if ( element.is( "[data-imageWidth]" ) ) {
					var fileType = jQuery(element[0]).data('typecategory');
					
					if(fileType != 'image'){
						return false;
					}
					var filePath = jQuery(element[0]).data('filepath');
					return '<img  src="' + filePath +	'" style="width: 100px" alt="' + jQuery(element[0]).data('filename') + '" />';
				}
				
			}
		});
	},
	
	/**
	 * function _loadTextEditorContent()
	 * Loads the content of a file and opens it in a text editor
	 * 
	 * @param void
	 * @return void
	 */
	_loadTextEditorContent: function(data) {
		
		var response = data.responseJSON;

		if (response.errorMessage) {
			cmtPage.showMessage(jQuery.base64Decode(response.errorMessage));	
			jQuery(textEditorDialogID).dialog('close');
		} else {

			var viewportWidth = jQuery(window).width();
			var viewportHeight = jQuery(window).height();
			
			// generate texteditor container manually every time the editor is shown, otherwise ACE will not work
			var textEditor = jQuery('<div id="' + this.textEditorID.substr(1) + '" class="cmtEditor" data-language="' + response.fileType + '">' + jQuery.base64Decode(response.fileContent) + '</div>');
			jQuery(this.textEditorDialogID).prepend(textEditor)
			
			// create dialog
			jQuery(this.textEditorDialogID).dialog({
				resizable: false,
				width: viewportWidth - 40,
				height: viewportHeight - 40,
				modal: true,
				title: response.fileName,
				close:function() {
					// remove text editors content 
					jQuery(this.textEditorID).remove();
				}.bindScope(this)
			});
			
			// adjust height for content container
			var textEditorHeight = jQuery(this.textEditorDialogID).height();
			var buttonsHeight = jQuery('.cmtDialogButtons', this.textEditorDialogID).outerHeight()
			jQuery(this.textEditorID).css({
				height: textEditorHeight - buttonsHeight + 'px'
			})
			
			// init dialog buttons
			jQuery('.cmtButtonBack', this.textEditorDialogID).on('click', function(ev) {
				
				jQuery(this.textEditorDialogID).dialog("close");
				cmtPage.unselectRow(jQuery(this.textEditorDialogID).data('cmtOpener'));
				
			}.bindScope(this)).bind(this)

			jQuery('.cmtButtonSave', this.textEditorDialogID).on('click', function(ev) {
				
				this._saveTextEditorContent(ev);
				//jQuery(ev.target).closest(this.textEditorDialogID).dialog('close');
			}.bindScope(this)).bind(this)

			// store some data
			jQuery(this.textEditorID).data('cmtFilePath', response.filePath)
			
			// add message container
			jQuery(this.textEditorDialogID).prepend('<div id="cmtDialogMessageContainer"></div>');
			
			// start ACE editor
			jQuery(this.textEditorDialogID).css('overflow', 'hidden')
			jQuery(this.textEditorID).addClass('cmtEditor');
			cmtPage.initCodeEditor(jQuery(this.textEditorID).parent());
		}

	},
	
	/**
	 * function _saveTextEditorContent()
	 * Saves the content of the file loaded in the text editor
	 * 
	 * @param void
	 * @return void
	 */
	_saveTextEditorContent: function() {

		cmtPage.showDialogOverlay(this.textEditorDialogID);

		var editor = ace.edit(this.textEditorID.substring(1));
		var code = editor.getSession().getValue();
		
		var filePath = jQuery(this.textEditorID).data('cmtFilePath');

		var url = document.URL + '&cmtAction=writeFile&cmtFile=' + filePath;					
		jQuery.ajax({
			url: url,
			dataType: 'json',
			type: 'POST',
			data: {
				'cmtFileContent': code
			},
			complete: function(data) {
				response = data.responseJSON;
				
				if (response.success) {
					jQuery(this.textEditorDialogID).dialog("close");
					cmtPage.showMessage(jQuery('#cmtSaveEditFileSuccess').html(), 'success');
					cmtPage.unselectRow(jQuery(this.textEditorDialogID).data('cmtOpener'));
				} else {
					cmtPage.showMessage(jQuery('#cmtSaveEditFileError').html(), 'error', '#cmtDialogMessageContainer')
				}
				
				cmtPage.hideDialogOverlay();
			}.bindScope(this)
		});
	},

	/**
	 * function saveNewFilename()
	 * Saves the changed name of a file.
	 * 
	 * @param {Object} ev jQuery event object
	 * @param void
	 */
	saveNewFilename: function(ev) {
		ev.stopPropagation();

		var target = ev.target;
		var entry = jQuery(target).closest('td')
		var oldNameContainer = jQuery('.cmtEntryName', entry);
		
		var newName = jQuery('.cmtEntryNewName', entry).val();
		var oldName = jQuery(oldNameContainer).text();
		
		var currentDirectory = jQuery(oldNameContainer).data('current-directory');
		
		cmtPage.showOverlay();
		
		jQuery.ajax({
			url: document.URL,
			data: {
				oldFileName: oldName,
				newFileName: newName,
				currentDirectory: currentDirectory,
				cmtAction: 'rename',
				dataType: 'JSON'
			},
			method: 'POST',
			complete: this._saveNewFileNameResponse.bindScope(this)
		})
	},
	
	/**
	 * function _saveNewFileNameResponse()
	 * Helper: Is called after saving the new filename via Ajax
	 * 
	 * @param {Object} data jQuery Ajax request object
	 * @return void
	 */
	_saveNewFileNameResponse: function (data) {
		var response = jQuery.parseJSON(data.responseText);
		
		if (response.success) {
			
			jQuery('.cmtDialogVar1', '#cmtRenameSuccess').text(response.oldFileName)
			jQuery('.cmtDialogVar2', '#cmtRenameSuccess').text(response.newFileName)
			var messageText = jQuery('#cmtRenameSuccess').html()
			
			cmtPage.showMessage(messageText, 'success');
			
			var entry = jQuery(this.currentRenameContainer).closest('tr')
			jQuery('.cmtEntryName', entry).text(response.newFileName)
		} else {
			jQuery('.cmtDialogVar1', '#cmtRenameError').text(response.oldFileName)
			jQuery('.cmtDialogVar2', '#cmtRenameError').text(response.newFileName)
			var messageText = jQuery('#cmtRenameError').html()
			
			cmtPage.showMessage(messageText, 'error');
		}
		
		this.hideEntryRenameContainer();
		cmtPage.hideOverlay();
		cmtPage.unselectRow(entry);
	},
	
	/**
	 * function showEntryRenameContainer()
	 * Shows an inline filename input field and hides other, open fields.
	 * 
	 * @param {Object} ev jQuery-Event object (optional)
	 * @return void
	 */
	showEntryRenameContainer: function(ev) {

		ev.stopPropagation();
		this.hideEntryRenameContainer();
		
		var target = ev.target;
		var entry = jQuery(target).closest('tr')
		
		cmtPage.selectRow(entry)

		this.currentRenameContainer = jQuery(entry).find('.cmtRenameEntryContainer')

		var newName = jQuery('.cmtEntryNewName', this.currentRenameContainer);
		var oldName = jQuery('.cmtEntryName', entry);
		
		// refresh content
		jQuery(newName).val(jQuery(oldName).text());

		jQuery(oldName).fadeOut({
			duration: 100
		});		
		
		jQuery(this.currentRenameContainer).fadeIn({
			duration: 200,
			complete: function() {
				jQuery(newName).select();
			}
		});

	},

	/**
	 * function hideEntryRenameContainer()
	 * Hidess an active inline filename input field.
	 * 
	 * @param {Object} ev jQuery-Event object (optional)
	 * @return void
	 */
	hideEntryRenameContainer: function(ev) {

		if (ev) {
			ev.stopPropagation();
			var target = ev.target;
		}
		
		if (this.currentRenameContainer) {
			var row =jQuery(this.currentRenameContainer).closest('tr');
			cmtPage.unselectRow(row);
			
			jQuery(this.currentRenameContainer).fadeOut(200);
			jQuery('.cmtEntryName', row).fadeIn(200)
		}
		
		return true;
	},
	
	clickOnRenameContainer: function(ev) {
		ev.stopPropagation();
	},
	
	/**
	 * function showUploadFilesDialog()
	 * Shows the file upload dialog
	 * 
	 * @param {Object} event jQuery Event object
	 * @return void
	 *
	 **/
	showUploadFilesDialog: function(event){
		jQuery('#cmtDialogUploadFiles').dialog({
			resizable: false,
			width:500,
			modal: true,
			title: jQuery('#cmtDialogUploadFiles').data('title')
		});
		
		jQuery('.cmtButtonBack', '#cmtDialogUploadFiles').on('click', function (ev) {
			jQuery('#cmtDialogUploadFiles').dialog('close');
		})
	},
	
	
	/**
	 *
	 * - show the delete selected files and directories dialog
	 * - delete selected files and directories
	 *
	 **/
	deleteSelectedFilesDialog: function(event){
		
		this.initSelectedFiles(event);
		
		jQuery('#cmtDialogDeleteMultiple').dialog({
			resizable: false,
			width: 400,
			minHeight: 100,
			modal: true
		});
		
		jQuery('.cmtButtonBack', '#cmtDialogDeleteMultiple').on('click', function (ev) {
			jQuery('#cmtDialogDeleteMultiple').dialog('close');
		})
	},
	
	
	/**
	 *
	 * update the list of selected files and dirctories
	 *
	 **/
	
	initSelectedFiles: function(event){
		var files = '';
		jQuery('.cmtSelected').each(function(){
			files = files + $(this).find('.cmtEntryName').html()+',';
		});
		files = files.substr(0, files.length - 1);
		this.selectedFiles = files;
		this.updateSelectedFiles();
			
	},
	
	/**
	 * update the input value with list of selected files
	 * 
	 */
	updateSelectedFiles: function(){
		jQuery('#cmtSelectedEntries').val(this.selectedFiles);
	},
	
	/**
	 * 
	 * 
	 */
	initDragnDrop: function(){
	
		jQuery( ".cmtDraggable" ).draggable({
			helper: 'clone',
			zIndex: 20000,
			appendTo: 'body',
			revert: true
		});

		jQuery( ".cmtDropZone" ).droppable({
			accept: '.cmtDraggable',
			over: function (event, ui) {
				//var draggedElement = ui.draggable;
				var helper = ui.helper;
				//var dropZone = event.target;

				jQuery(helper).toggleClass('cmtDroppableAccept');

			},
			out: function (event, ui) {
//				var draggedElement = ui.draggable;
//				var dropZone = event.target;
				var helper = ui.helper;
				
				jQuery(helper).toggleClass('cmtDroppableAccept');

			},
			drop: function (event, ui) {
//				console.info(ui)		
//				console.info(event.target)
				var helper = ui.helper;
				jQuery(helper).remove();
				
				this.moveFile(jQuery(ui.draggable).data('path'), jQuery(event.target).data('path'));
				
			}.bindScope(this),
			hoverClass: 'cmtDroppableAccept'
		});
		
		
//		onDropOver: function(event, ui) {
//			
//			var draggedNode = jQuery(ui.draggable).parents('tr')[0];
//			var sourceToMove = jQuery(draggedNode).data('directorypath');
//			var node = jQuery(event.target).parents('tr')[0];
//			var moveTo = jQuery(node).data('directorypath');
//			
//			var url = document.URL + '&cmtAction=move&form_checked='+sourceToMove+'&moveTo='+moveTo;
//			jQuery.ajax(url, {
//				dataType: 'html',
//				type:'POST',
//				complete: function(){}
//			})
//		},
	},
	
	moveFile: function(from, to) {
		
		var url = document.URL + '&cmtAction=move&cmtSourcePath=' + from + '&cmtTargetPath=' + to;
		
		jQuery.ajax({
			url: url,
			dataType: 'json',
			type:'POST',
			complete: this._moveFileProceed.bindScope(this) 
		});
	},
	
	_moveFileProceed: function(data) {
		var response = jQuery.parseJSON(data.responseText);
		
		if (response.success) {
			
			jQuery('.cmtDialogVar1', '#cmtMoveSuccess').text(response.sourcePath)
			jQuery('.cmtDialogVar2', '#cmtMoveSuccess').text(response.targetPath)
			var messageText = jQuery('#cmtMoveSuccess').html()
			
			cmtPage.showMessage(messageText, 'success');
			this.refreshDirectoryContent();
			
		} else {
			jQuery('.cmtDialogVar1', '#cmtMoveError').text(response.sourcePath)
			jQuery('.cmtDialogVar2', '#cmtMoveError').text(response.targetPath)
			var messageText = jQuery('#cmtMoveError').html()
			
			cmtPage.showMessage(messageText, 'error');
		}

//		cmtPage.unselectRow(entry);		
	},
	
	refreshDirectoryContent: function() {
		
		var url = document.URL + '&cmtAction=showDirectoryContent&cmtDirectory=' + jQuery('#filesColumn').data('path');
		
		jQuery.ajax({
			url: url,
			dataType: 'html',
			type:'POST',
			complete: function(data) {
				jQuery('tbody', '#filesColumn').html(data.responseText)
				this.initDirectoryContent();
			}.bindScope(this)
		});		
	},
	
	initMessages: function() {
		
		jQuery('.cmtMessage').click( function(ev) {
			jQuery(this).slideUp(400);
		})
	},
	
	
	
	serializeForQuerystring: function(obj, prefix) {
		var str = [];
	    
		for(var p in obj) {

			if (prefix) {
				var k = prefix + "[" + p + "]";
			} else {
				var k = p;
			}
	    	
			var v = obj[p];
	        
			if (typeof v == 'object') {
				str.push(this.serializeForQuerystring(v, k))
			} else {
				//str.push(encodeURIComponent(k) + "=" + encodeURIComponent(v));
				str.push(k + "=" + v);
	        	
			}
		}
		//console.info(str)
		return str.join("&");
	},


	initMultiUpload: function () {
		this.uploadURL = document.getElementById ("cmtUploadURL").value;
		this.dropUploadProgressBar = document.getElementById ("cmtDropUploadProgressBar");

		var uploadInput = document.getElementById ("cmtUploadFiles");
		uploadInput.addEventListener ("change", onChange, false);

		function onChange (event) {
			cmtFileBrowser.uploadFileList (uploadInput.files);
		}

		//document.getElementById ("cmtUploadSubmitButton").style.display = "none";
	},


	initDropUpload: function () {

		var dropZone = document.getElementById("filesColumn");

		dropZone.classList.add ("cmtDropUploadDropZone");
		dropZone.addEventListener ("drop", onDrop, false);
		dropZone.addEventListener ("dragover", onDragOver, false);
		dropZone.addEventListener ("dragenter", onDragEnter, false);
		dropZone.addEventListener ("dragleave", onDragLeave, false);

		function onDragOver (event) {
			event.preventDefault (); // Allow drop!
		}

		function onDragEnter (event) {
			this.classList.add ("cmtDropAccepted");
		}

		function onDragLeave (event) {
			this.classList.remove ("cmtDropAccepted");
		}

		function onDrop (event) {
			event.stopPropagation ();
			event.preventDefault ();

			cmtFileBrowser.uploadFileList (event.dataTransfer.files);
		}
	},

	// Upload a list of files (either from drag and drop event or from file input)
	uploadFileList: function (files) {

		var filelist = [],
			totalSize = 0,
			totalProgress = 0,
			currentUpload = null;

		for (var i = 0; i < files.length; i++) {
			filelist.push (files[i]);
			totalSize += files[i].size;
		}
		startNextUpload ();
		jQuery('#cmtDropUploadProgressBar').addClass("isActive");
		jQuery('#cmtDropUploadSelect').removeClass("isActive");
		jQuery('.cmtDropAccepted').addClass('cmtInProgress');

		function startNextUpload () {
			if (filelist.length > 0) {
				currentUpload = filelist.shift ();
				uploadFile (currentUpload);
			}
			else {
				jQuery('#cmtDropUploadProgressBar').removeClass("isActive");
				jQuery('#cmtDropUploadSelect').addClass("isActive");
				jQuery('.cmtDropUploadDropZone').removeClass('cmtDropAccepted');
				jQuery('.cmtDropUploadDropZone').removeClass('cmtInProgress');
				
			}
		}

		function uploadFile (file) {
			
//			jQuery('#cmtDropUploadProgressBar').addClass("isActive");
			
			var xhr = new XMLHttpRequest ();
			xhr.upload.addEventListener ("progress", onProgress, false);
			xhr.addEventListener ("load", onLoad, false);
			xhr.addEventListener ("error", onError, false);
			xhr.open ("POST", cmtFileBrowser.uploadURL);
			var formdata = new FormData ();
			formdata.append ("cmtUploadFiles", file);
			formdata.append ("is-ajax", 1);
			formdata.append ("cmtDirectory", $('input[name=directory]').val());

			xhr.send (formdata);

			function onProgress (event) {
//				var progress = totalProgress + event.loaded;
//				cmtFileBrowser.dropUploadProgressBar.value = (progress / totalSize);
//				cmtFileBrowser.dropUploadProgressBar.innerText = filelist.length + " files uploading " + progress * 100 + "%)";

				var progress = totalProgress + event.loaded;
				var progressLabelValue = Math.round(progress / totalSize * 100); 
				
				// update progress bar width
				jQuery('.bar', '#progressBar').css({
					width:  progressLabelValue + '%'
				});
				
				jQuery( "#progressBar" ).progressbar({
					 value: progressLabelValue
				 });

			}

			function onLoad (event) {
				try {
					if (event.target.status < 200 || event.target.status >= 400) {
						throw event.target.status;
					}

					var response = JSON.parse (event.target.responseText);
					if (response.success) {
						document.querySelector ('#cmtFilesTable tbody').innerHTML = response.directoryContent;
						cmtFileBrowser.initPreviewFiles();
						cmtFileBrowser.initEntryRename();
						cmtPage.initDialogLinks('#cmtFilesTable');
						//cmtPage.initButtons('#cmtFilesTable');
					}
					else {
						uploadError ();
					}
				}
				catch (e) {
					// JSON.parse() failed most likely because the response does contain unexpected HTML
					// e.g. the login-page when the session has timed-out...
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
			cmtPage.showMessage (errorMessage, "error");	
			console.log (errorMessage);
		}
	}
}

cmtFileBrowser.initialize();
