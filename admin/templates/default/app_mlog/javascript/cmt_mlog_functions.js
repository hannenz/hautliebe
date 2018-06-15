/**
 * cmt_mlog_functions.js
 * Javascript-Datei für den Mlog ('app_mlog.inc')
 * 
 * @author A. Al-Kaissi <info@content-o-mat.de>, J.Hahn <info@content-o-mat.de>
 * @version 2016-09-21
 *
 */
 
Mlog = {
	// eigene Klassenvariablen
	currentDialog: null,
	relationsContentID: null,
	selftUrl: '',
	postMediaPositions: 'input[name=post_media_positions]',
		
	/**
	* function initialize()
	* 
	* @param void
	* @return void
	*/
	initialize: function() {
		jQuery(document).ready(this.initMlog.bind(this));
	},
		
	/**
	* function initMlog()
	* 
	*  @param void
	*  @return void
	*/
	initMlog: function(event) {
		this.initAddMediaButton();
		this.initSortableMedia();
		this.initAbortButtons();
		//this.initEditPostForm();
	},
		
		
	/**
	* facebookAuthentication()
	*/
	facebookAuthentication: function(){
		jQuery(document).on('click', '#facebookFeed', function(){
			console.log('clicked');
			var authUrl = jQuery('#facebookAuthenticationUrl').attr('value');
			if(authUrl){
				jQuery(window.location).attr('href', authUrl);
			}
		});
	},
	
	initAbortButtons: function() {

		jQuery('a.cmtButtonBack').on('mousedown', function(ev) {
			
			var link = ev.currentTarget;

			jQuery(link).attr('href', jQuery(link).attr('href') + '&deleteMediaIds=' + jQuery(this.postMediaPositions).attr('value'));
//			alert(jQuery(link).attr('href'));
			//ev.preventDefault();
			//location.href = jQuery(link).attr('href');
		}.bindScope(this));
	},
		
	/**
	* function initUpdateMediaPositions()
	* Initalisiert die Sortierbarkeit von Medien und das Aktualisieren der Reihenfolgen in einem Formularfeld.
	* 
	* @param void
	* @return void
	*/
	initSortableMedia: function(){
			
		jQuery("#mlogMediaSortableContainer" ).sortable({
			handle: '.mlogMediaHandle'
		});
			
		jQuery("#mlogMediaSortableContainer" ).disableSelection();
			
		jQuery("#mlogMediaSortableContainer").on( "sortupdate", function(event, ui) {
			this.updateMediaPositions();
		}.bind(this));
			
		this.updateMediaPositions();
	},
	/**
	 *	updateMediaPositions()
	 *
	 */
	updateMediaPositions: function() {
		var mediaList = [];
		var index = 1;
		jQuery("#mlogMediaSortableContainer").find('.mlogMediaContainer').each(function(index,element){
			var id = element.id.match('[0-9]+');
			mediaList[index] = id;
			index++;
		});
		jQuery(this.postMediaPositions).attr('value', mediaList.join(','));

	},
	
		
	/**
	* function initMediaEditForm()
	* Initialisiert ein Eingabeformular für Medien das in einem Dialogfenster geöffnet wird.
	*  
	* @param string formID ID des Formulars inklusive führendem "#"
	* @return void 
	*/
	initMediaEditForm: function(formID) {
			
		jQuery(formID).iframePostForm({
			json:true,
			post: function(){

			},
			complete : function (response){

				if(response.errorMessage){
					console.info(response)
					alert($.base64Decode(response.errorMessage));	
				} else {
					jQuery(cmtPage.dialogContainerID).dialog('close');
					
					if (response.mediaIsNew) {
						// Eintrag ist neu: anhängen
						jQuery('#mlogMediaSortableContainer').append($.base64Decode(response.mediaContent));
						var newElement = jQuery('.mlogMediaContainer', '#mlogMediaSortableContainer').last();

						jQuery(newElement).hide();
						jQuery(newElement).fadeIn(800)
							
					} else {
						// Eintrag wurde bearbeitet: ersetzen
						jQuery('#mediaContainer_' + response.mediaId).replaceWith($.base64Decode(response.mediaContent));

						var newElement = jQuery('#mediaContainer_' + response.mediaId);
							
						jQuery(newElement).effect('pulsate', {
							times: 2
						}, 500);

					}
						
					// GUI-Elemente in neuem HTML initialisieren und Elementreihenfolgen aktualisieren
					cmtPage.initGUIElements(jQuery(newElement));
					Mlog.updateMediaPositions();
				}
			}
				
		});
			
	},
	
	/**
	*
	*/	
	saveMediaEditForm: function(formID, response, ev){
		jQuery(cmtPage.dialogContainerID).dialog('close');
			
		var form = ev.currentTarget;

		// Inhalte per Ajax aktualisieren
		jQuery.ajax({  
			type: jQuery(form).attr('method'),  
			url: jQuery(form).attr('action'),  
			data: jQuery(form).serialize(),
			dataType: 'json',
					
			success: function(data) {
				console.log(data);
						
				if (data.mediaIsNew) {
					// Eintrag ist neu: anhängen
					jQuery('#mlogMediaSortableContainer').append(data.mediaContent);
					var newElement = jQuery('.mlogMediaContainer', '#mlogMediaSortableContainer').last();

					jQuery(newElement).hide();
					jQuery(newElement).fadeIn(800)
							
				} else {
					// Eintrag wurde bearbeitet: ersetzen
					jQuery('#mediaContainer_' + data.mediaId).replaceWith(data.mediaContent);

					var newElement = jQuery('#mediaContainer_' + data.mediaId);
							
					jQuery(newElement).effect('pulsate', {
						times: 2
					}, 500);

				}
						
				// GUI-Elemente in neuem HTML initialisieren und Elementreihenfolgen aktualisieren
				cmtPage.initGUIElements(jQuery(newElement));
				Mlog.updateMediaPositions();

			}
		});
				
	},
		
	/**
	* function initAddMediaButton()
	* 
	*/
	initAddMediaButton: function(){
			
		var self = this;

		jQuery('#mlogAddNewMediaButton').on('mousedown.mlog', function(event) {

			var selectField = jQuery('#mlogAddNewMedia');
			var url = jQuery(this).attr('href') + '&mediaType=' + jQuery(selectField).val();
			//				var uploadUrl = url;
				
			url += '&action=mlogNewMedia';
			//				uploadUrl += '&action=mlogUploadFile'
				
			if(!url){
				return false;
			}
			jQuery(this).attr('href', url);
			return false;
			
		}); 
	},
		
		
	/**
	* function deleteMedia()
	* Löscht einen Medieneintrag per Ajax aus der Datenbank und bei Erfolg auch aus dem DOM.
	* 
	* @param number mediaId ID des Medieneintrags
	* @param string url URL des Ajaxaufrufes 
	*/
	deleteMedia: function(mediaId, url) {

		mediaId = parseInt(mediaId);
		
		jQuery.ajax({  
			type: 'post',  
			url: url + '&mediaId=' + mediaId, 
			dataType: 'json',
				
			success: function(data, state, response) {
					
				// Element wurde in DB gelöscht, dann auch aus dem DOM löschen
				if (data.actionResult == '1') {

					jQuery('#mediaContainer_' + data.mediaId).animate({
						opacity: 0, 
						width: 'toggle'
					}, 
					500, 
					function(el) {
						jQuery(this).remove();

						// abschließend Reihenfolge aktualisieren
						Mlog.updateMediaPositions();
					}
					)
				}
			}
		});
	}
	
//	initEditPostForm: function() {
//return;		
//		jQuery('#cmtEditEntryForm').on('submit.Mlog', function(ev) {
//			ev.stopPropagation()
//			Mlog.submitEditPostForm(ev);
//			
//			// Wait 1 second to give the previous request a chance to store the mediaPositions in a session variable, then submit the form
//			window.setTimeout(function() {
//				jQuery('#cmtEditEntryForm').off('submit.Mlog');
//				jQuery('#cmtEditEntryForm').submit()
//			}, 1000)
//			return false;
//		})
//	},
	
//	submitEditPostForm: function(ev) {
//		
//		var positions = jQuery('#mediaPositions').attr('value'); 
//		var posArray = positions.split(',');
//		
//		jQuery.ajax({  
//			type: 'post',  
//			url: this.selfUrl + '&action=mlogSaveMediaPositions', 
//			dataType: 'json',
//			data: {
//				mediaPositions: posArray,
//				action: 'mlogSaveMediaPositions'
//			}
////			success: function(data, state, response) {
////
////				}
//			});
//		
//		return false;
//	}
	
	
}

Mlog.initialize();
