/**
 * cmt_mlog_functions.js
 * Javascript-Datei für den Mlog ('app_mlog.inc')
 * 
 * @author J.Hahn <info@content-o-mat.de>
 * @version 2014-07-14
 *
 */
 
cmtMedia = {

	currentDialog: null,
	mediaContentID: '#cmtMediaSortableContainer',
	addMediaButtonID: '#cmtAddMediaButton',
	addMediaTypeID: '#cmtAddMediaType',
		
	/**
	* function initialize()
	* 
	* @param void
	* @return void
	*/
	initialize: function() {
		jQuery(document).ready(this.initMedia.bind(this));
	},
		
	/**
	* function initMlog()
	* 
	*  @param void
	*  @return void
	*/
	initMedia: function(event) {
		this.initAddMediaButton();
		this.initSortableMedia();
		this.initSaveButtons();
	},
		
		
	/**
	* function initUpdateMediaPositions()
	* Initalisiert die Sortierbarkeit von Medien und das Aktualisieren der Reihenfolgen in einem Formularfeld.
	* 
	* @param void
	* @return void
	*/
	initSortableMedia: function(){
			
		jQuery(this.mediaContentID).sortable({
			handle: '.cmtMediaHandle',
			placeholder: 'ui-sortable-placeholder',
			forcePlaceholderSize: true
		});
			
		jQuery(this.mediaContentID).disableSelection();
			
		jQuery(this.mediaContentID).on( "sortupdate", function(event, ui) {
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

		jQuery(this.mediaContentID).find('.cmtMediaContainer').each(function(index,element){
			var id = element.id.match('[0-9]+');
			mediaList[index] = id;
		});
		jQuery('#cmtMediaPositions').attr('value',mediaList);
	},
	
	
	initSaveButtons: function() {
		jQuery('.cmtButton.cmtButtonSave, .cmtButton.cmtButtonSaveShowNext, .cmtButton.cmtButtonSaveShowPrev').on('click.cmtMedia', this.submitMediaOrder).bind(this);
		
	},
	
	submitMediaOrder: function() {
		jQuery.ajax({
			type: 'POST',
			data: {
				cmtAction: 'saveMediaPositions',
				cmtMediaPositions: jQuery('#cmtMediaPositions').val(),
				cmtEntryID: jQuery('#cmtEntryID').val(),
				cmtTableID: jQuery('#cmtTableID').val()
			},
			complete: function(response) {
				//console.info(response);
			}
		});
		
		setTimeout(function() {jQuery('#cmtEditEntryForm').submit();}, 100);
		return false;
	},
		
	/**
	* function initMediaEditForm()
	* Initialisiert ein Eingabeformular für Medien das in einem Dialogfenster geöffnet wird.
	*  
	* @param string formID ID des Formulars inklusive führendem "#"
	* @return void 
	*/
	initMediaEditForm: function(formID) {
		
		var self = this;
		
		jQuery(formID).iframePostForm({
			json:true,
			post: function(){},
			complete : function (response){	
				
				//console.log(response);
				
				if (response.errorMessage){
					alert(response.errorMessage);	
				} else {
					jQuery(cmtPage.dialogContainerID).dialog('close');
					
					if (response.mediaIsNew) {
						
						// Eintrag ist neu: anhängen
						jQuery(self.mediaContentID).append(jQuery.base64Decode(response.mediaContent));
						var newElement = jQuery('.cmtMediaContainer', self.mediaContentID).last();

						jQuery(newElement).hide();
						jQuery(newElement).fadeIn(800);
							
					} else {
						// Eintrag wurde bearbeitet: ersetzen
						jQuery('#mediaContainer_' + response.mediaID).replaceWith(jQuery.base64Decode(response.mediaContent));
						//jQuery('#mediaContainer_' + response.mediaID).replaceWith(response.mediaContent);

						var newElement = jQuery('#mediaContainer_' + response.mediaID);
							
						jQuery(newElement).effect('pulsate', {
							times: 2
						}, 800);

					}
						
					// GUI-Elemente in neuem HTML initialisieren und Elementreihenfolgen aktualisieren
					cmtPage.initGUIElements(jQuery(newElement));
					cmtMedia.initSortableMedia();
				}
			}
				
		});
			
	},
	

	/**
	* function initAddMediaButton()
	* 
	*/
	initAddMediaButton: function(){
			
		var self = this;
		jQuery(this.addMediaButtonID).data('href', jQuery(this.addMediaButtonID).attr('href'));

		jQuery(this.addMediaButtonID).on('mousedown.cmtMedia', function(event) {

			var selectField = jQuery(self.addMediaTypeID);
			var url = jQuery(this).data('href') + '&cmtMediaType=' + jQuery(selectField).val();
			url += '&cmtAction=newMedia';
				
			if (!url){
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
	* @param number mediaID ID des Medieneintrags
	* @param string url URL des Ajaxaufrufes 
	*/
	deleteMedia: function(mediaID) {

		mediaID = parseInt(mediaID);
			
		jQuery.ajax({  
			type: 'post',
			data: {
				cmtMediaID: mediaID,
				cmtAction: 'deleteMedia'
			},
			dataType: 'json',
				
			success: function(data, state, response) {
					
				// Element wurde in DB gelöscht, dann auch aus dem DOM löschen
				if (data.mediaDeleted) {

					jQuery('#mediaContainer_' + data.cmtMediaID).animate({
						opacity: 0, 
						width: 'toggle'
					}, 
					800, 
					function(el) {
						jQuery(this).remove();

						// abschließend Reihenfolge aktualisieren
						cmtMedia.updateMediaPositions();
					});
				}
			}
		});
	}
};

cmtMedia.initialize();
