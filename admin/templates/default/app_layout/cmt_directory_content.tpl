<div class="cmt-box">
	<header class="cmt-box-header cmt-breadcrumbs">
		<a href="#" data-cmt-path="/" class="cmt-breadcrumbs-icon cmt-breadcrumbs-home cmt-select-directory"></a>
		{IF (!{ISSET:isRootLevel:VAR})}<a href="#" data-cmt-path="{VAR:parentPath}" class="cmt-breadcrumbs-icon  cmt-breadcrumbs-up cmt-select-directory">{VAR:parentTitle}</a>{ELSE}
		<span>Root</span>
		{ENDIF}
	</header>
	<div class="cmt-box-content">
		<ul class="cmt-directories">
			{LOOP VAR(directories)}
				<li><a href="#" data-cmt-path="{VAR:directoryPath}" class="cmt-directory cmt-select-directory" title="{VAR:directoryName}">{VAR:directoryName}</a></li>
			{ENDLOOP VAR}
		</ul>
		<ul class="cmt-files">
			{LOOP VAR(files)}
				<li><a href="#" data-cmt-path="{VAR:filePath}" class="cmt-file cmt-file-{VAR:fileType} cmt-select-file {IF ('{VAR:fileName}' == '{VAR:selectedFile}')}cmt-selected{ENDIF}" title="{VAR:fileName}">{VAR:fileName}</a></li>
			{ENDLOOP VAR}
		</ul>
	</div>
</div>
		