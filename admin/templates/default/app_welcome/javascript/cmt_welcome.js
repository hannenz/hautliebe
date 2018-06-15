
cmtDashboard = {
	
	initialize: function() {
		jQuery(document).ready(this.initDashboard.bind(this));
	},
	
	initDashboard: function() {
		
	},
	
	initPasswordChange: function(params) {

		var formId = '#' + params.formId;
		
		// init close button
		$('.cmtButtonBack', formId).on('click', function(ev) {
			
			ev.preventDefault();
			$('#cmtDialogContainer').dialog('close');
		});
		
		// init submit button
		$('.cmtButtonSave', formId).on('click', function(ev) {
			
			ev.preventDefault();
			
			$.ajax({
				url: $(formId).attr('action'),
				type: 'post',
				data: $(formId).serialize(),
				complete: function(response) {
					$('#cmtDialogContainer').html(response.responseText);
					console.info(response);
				}
			})
		})
	}
		
}

cmtDashboard.initialize();