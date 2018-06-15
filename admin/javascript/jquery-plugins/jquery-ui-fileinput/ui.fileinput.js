/**
 * --------------------------------------------------------------------
 * jQuery customfileinput plugin
 * Author: Scott Jehl, scott@filamentgroup.com
 * Copyright (c) 2009 Filament Group. Updated 2012.
 * licensed under MIT (filamentgroup.com/examples/mit-license.txt)
 * --------------------------------------------------------------------
 */

/**
 * All credits go to the Author of this file, some additional customization was
 * done for theme compat. purposes.
 */
$.fn.customFileInput = function(options){
	
	//Get current element
	var fileInput = $(this);
	
	//Define settings
	var settings	= jQuery.extend({
		button_position 	: 'right',
		classes				: '',		// fileInput.attr('class'),
		feedback_text		: 'No file selected...',
		button_text			: 'hochladen',
		button_change_text	: '&auml;ndern',
		optionalID : ''
	}, options);


	// Falls Input-Feld keine ID hat, dann optionale ID zuweisen, sonst werden alle Input-Felder aufgerufen!
	if (!jQuery(fileInput).prop('id') && settings.optionalID) {
		jQuery(fileInput).prop('id', settings.optionalID)
	}

	//apply events and styles for file input element
	fileInput
		// .addClass('customfile-input') //add class for CSS
		.focus(function(){
			// upload.addClass('ui-state-focus'); 
			fileInput.data('val', fileInput.val());
		})
		.blur(function(){ 
			//upload.removeClass('ui-state-focus');
			$(this).trigger('checkChange');
		 })
		 .bind('disable',function(){
		 	fileInput.attr('disabled',true);
			upload.addClass('ui-state-disabled');
		})
		.bind('enable',function(){
			fileInput.removeAttr('disabled');
			upload.removeClass('ui-state-disabled');
		})
		.bind('checkChange', function(){
			
			if(fileInput.val() && fileInput.val() != fileInput.data('val')){
				fileInput.trigger('change');
			}
		})
		.bind('change',function(){
			//get file name
			var fileName = $(this).val().split(/\\/).pop();
			//get file extension
			var fileExt = 'filetype-' + fileName.split('.').pop().toLowerCase();
			//update the feedback
			uploadFeedback
				.css({ width : '-=21' })
				.text(fileName) //set feedback text to filename
				.removeClass(uploadFeedback.data('fileExt') || '') //remove any existing file extension class
				.addClass(fileExt) //add file extension class
				.data('fileExt', fileExt) //store file extension for class removal on next change
//				.addClass('customfile-feedback-populated'); //add class to show populated state

			uploadInput.attr('value', fileName);
			
			//change text of button	
			//uploadButton.find('.ui-button-text').text(settings.button_change_text);	
		})
		.click(function(event){ //for IE and Opera, make sure change fires after choosing a file, using an async callback
			fileInput.data('val', fileInput.val());

			setTimeout(function(){
				fileInput.trigger('checkChange');
			},100);
			
		});
		
		//create custom control container
		//var upload = $('<div class="input-' + (('right' === settings.button_position)?'append':'prepend') + ' customfile">');
		var upload = $('<div class="cmtCustomFileContainer">');
		
		//create custom control feedback
		var uploadFeedback = $('<span class="cmtCustomFileText ' + settings.classes + '" aria-hidden="true">' + settings.feedback_text + '</span>').appendTo(upload);
		
		//create custom control button
		var labelText = fileInput.data('label-text');
		if (!labelText) {
			labelText = settings.button_text;
		}
		var uploadButton = $('<span class="cmtButton cmtButtonUploadFile" aria-hidden="true">' + labelText + '</span>').css({ float : settings.button_position });
		
		var uploadInput = jQuery(fileInput).parent().find('.cmtFormFile');
		
		if ('right' === settings.button_position) {
			uploadButton.insertAfter(uploadFeedback);
		} else uploadButton.insertBefore(uploadFeedback);
	
	//match disabled state
	if(fileInput.is('[disabled]')){
		fileInput.trigger('disable');
	} else upload.click(function () { fileInput.trigger('click'); });
		
	
	//insert original input file in dom, css if hide it outside of screen
	upload.insertAfter(fileInput);
	fileInput.insertAfter(upload);

	// Upload muss für den IE anders geregelt werden: Da hier ein trigger(#click') wohl aus Sicherheitsgründen nicht reicht und 
	// im IE wirklich auf den "durchsuchen"-Button des File-Input-Felds geklickt werden muss, wird dieser unsichtbar über den
	// Knopf ("hochladen") gelegt.
	if (navigator.userAgent.toLowerCase().indexOf('msie') != -1) {
		uploadButton.on('mousemove', function(e) {
			fileInput.css({
				'top': e.pageY - uploadButton.offset()['top'],
				'left':e.pageX - fileInput.width(),
				'opacity': 0,
				'cursor': 'pointer'
			});
			uploadButton.addClass('ui-state-hover');
	    });
	
		fileInput.on('mousemove', function(e) {
			
			var buttonTop = uploadButton.offset()['top'];
			var buttonLeft = uploadButton.offset()['left'];
			var buttonRight = buttonLeft + uploadButton.width();
			var buttonBottom = buttonTop + uploadButton.height();
			
			if (e.pageX > buttonRight || e.pageX < buttonLeft || e.pageY < buttonTop || e.pageY > buttonBottom) {
				fileInput.css({
					'top': '-9999px',
					'left': '-9999px'
				});
				uploadButton.removeClass('ui-state-hover');
				uploadButton.blur();
				fileInput.blur();
				
			} else {
				fileInput.css({
					'top': e.pageY - buttonTop,
					'right': e.pageX 
				});
				uploadButton.addClass('ui-state-hover');
			}
		});
	}
	
	return $(this);
};