<script type="text/javascript">
	CMTLayout.setHTMLEditorConfig({
		language: 'de',
	    plugins:[
			'advlist autolink lists charmap hr anchor',
			'searchreplace wordcount visualblocks visualchars code fullscreen',
			'nonbreaking table contextmenu directionality',
			'paste textcolor colorpicker textpattern'
			// , 'media image imagetools link'
		],
		menubar: 'edit insert format table tools view',
		toolbar1: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ',
		toolbar2: 'bullist numlist outdent indent | link | media | forecolor backcolor | removeformat',
		image_advtab: true,
// 		file_browser_callback: function() {
// 			console.info(arguments)
// 		},
// 		file_browser_callback_types: 'image media',
		setup: function(editor) {
			editor.on('init', function(ev) {
				CMTLayout.initLinks(ev.currentTarget, true);
				
				// dirty workaround to steal the focus from tinymce editor (autofocuses itself by default)
				window.setTimeout(function() {$('#cmt-select-object').focus()}, 500);
			});
		}
	});
</script>