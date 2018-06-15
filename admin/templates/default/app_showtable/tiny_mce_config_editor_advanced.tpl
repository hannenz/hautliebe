{IF ("{VAR:editorNr}" == "0")}<script type="text/javascript" src="javascript/tinymce/tinymce.min.js"></script>{ENDIF}
<script type="text/javascript">
	
	tinymce.init({
		
		branding: false,
		selector: "#{VAR:editorID}",
		language: "de",
		
		plugins: 'paste searchreplace autolink directionality visualblocks visualchars fullscreen link charmap hr nonbreaking anchor advlist lists textcolor contextmenu colorpicker textpattern help',
		
		menubar: 'edit insert view format table tools help',
		toolbar1: 'formatselect | bold italic strikethrough removeformat | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | paste',

		width: "{IF ({ISSET:tinymce_width:VAR})}{VAR:tinymce_width}{ELSE}100%{ENDIF}",
		height: "{IF ({ISSET:tinymce_height:VAR})}{VAR:tinymce_height}{ELSE}320px{ENDIF}",
		{IF ({ISSET:tinymce_image_dir:VAR})}image_list : "includes/tinymce_images.php?sid={SID}&dir={VAR:tinymce_image_dir}&type=image",{ENDIF}
		{IF ({ISSET:tinymce_content_css:VAR})}content_css: "{PATHTOWEBROOT}{VAR:tinymce_content_css}",{ENDIF}

		entity_encoding : 'named',
		convert_urls: true,
		relative_urls: false,
		forced_root_block : 'p'
	});

</script>