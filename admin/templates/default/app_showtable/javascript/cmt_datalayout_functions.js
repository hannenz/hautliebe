 
cmtDataLayout = {

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
		jQuery(document).ready(this.init.bind(this));
	},
		
	/**
	* function initMlog()
	* 
	*  @param void
	*  @return void
	*/
	init: function(event) {
		this.initSaveButtons();
		this.initTemplates();
		this.initObjectDeleteButtons();
	},
		
		
	initTemplates: function(ev) {
		
		jQuery('#cmt-templates .cmt-object-template').draggable({
			revert: 'invalid',
			helper: 'clone',
			connectToSortable: jQuery('#cmt-layout'),
			stop: function(ev, ui) {
				console.info(ev);
				console.info(ui)
				this.addDeleteButton(ev.currentTarget);
			}.bindScope(this)
		});
		
		jQuery('#cmt-layout').sortable({
			accept: '.cmt-object-template'
		})
	},
	
	initObjectDeleteButtons: function() {

		this.addDeleteButton(jQuery('#cmt-layout .cmt-object-template'));
		
		jQuery('#cmt-layout').on('click', '.cmt-delete-layout-object', function(ev) {
			
			var layoutObject = jQuery(ev.currentTarget).closest('.cmt-object-template');
			
			jQuery(layoutObject).fadeOut({
				done: function() {
					
					jQuery(layoutObject).css({
						display: 'block',
						visibility: 'hidden'
					});
					
					jQuery(layoutObject).animate({
						height: '0px'
					}, {
						done: function() {
							jQuery(layoutObject).remove();
						}
					})
				}
			})
		})
	},
	
	initSaveButtons: function() {
		
		// workaround: CMT app_showtable does not allow action 'editSave' in own controller right now :-( 
		jQuery('.cmtButtonSave').on('click', function() {
			
			var templateIds = jQuery('#cmt-layout').sortable('toArray', {
				attribute: 'data-id'
			});
			
			//jQuery('#cmtTemplatePositions').attr('value', templateIds.join(','));
			
			var cmtTemplatePositions = templateIds.join(',');
			var cmtEntryId = jQuery('#cmtEntryId').attr('value');
			var cmtTableId = jQuery('#cmtTableId').attr('value');
			
			jQuery.ajax({
				url: window.location.href + '&cmtAction=saveDataLayout&cmtTableId=' + cmtTableId + '&cmtEntryId=' + cmtEntryId + '&cmtTemplatePositions=' + cmtTemplatePositions
			})
			
//			return confirm('dd')
//			return false;
		})
	},
	
	addDeleteButton: function(elements) {
return
		jQuery(elements).each(function (index, el) {
			
			jQuery(el).append('<button type="button" class="cmt-delete-layout-object">X</button>');
		})
	}
	
	
};

cmtDataLayout.initialize();
