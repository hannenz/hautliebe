<style type="text/css">
</style>
<div class="cmt-box">
	<div class="cmt-box-header cmt-images-view-options">
		<input type="radio" name="cmt-images-view-style" value="list" id="cmt-images-view-style--list" checked><label for="cmt-images-view-style--list"><span class="cmt-flat-button cmt-button-list" data-cmt-tooltip="Listenansicht"></span></label>
		<input type="radio" name="cmt-images-view-style" value="grid" id="cmt-images-view-style--grid"><label for="cmt-images-view-style--grid"><span class="cmt-flat-button cmt-button-grid" data-cmt-tooltip="Kachelansicht"></span></label>
		<input type="range" name="cmt-images-view-size" min="30" max="270" value="130">
		<form action="?action=upload" method="post" enctype="multipart/form-data">
			<input type="file" multiple name="cmtUploadFiles[]" id="cmtUploadFiles"><label for="cmtUploadFiles"><span class="cmt-flat-button cmt-button-upload" data-cmt-tooltip="Bilder hochladen"></span></label>
			<input type="hidden" name="cmtUploadURL" id="cmtUploadURL" value="{SELFURL}&cmtAction=uploadFiles">
			<input type="hidden" name="cmtUploadPath" value="{VAR:cmtPath}">
		</form>
	</div>
	<!-- <div class="cmt&#45;box&#45;header"> -->
	<!-- </div> -->
	<header class="cmt-box-header cmt-breadcrumbs">
		<a href="#" data-cmt-path="/" class="cmt-breadcrumbs-icon cmt-breadcrumbs-home cmt-select-directory"></a>
		{IF (!{ISSET:isRootLevel:VAR})}<a href="#" data-cmt-path="{VAR:parentPath}" class="cmt-breadcrumbs-icon  cmt-breadcrumbs-up cmt-select-directory">{VAR:parentTitle}</a>{ELSE}
		<span>Root</span>
		{ENDIF}
		{LOOP VAR(breadcrumbs)}
	<!-- 	<a href="#" data-cmt-path="{VAR:link}" class="cmt-select-directory">{VAR:breadcrumb}</a> &gt; -->
		{ENDLOOP VAR}
	</header>
	<div class="cmt-box-content">
		<ul class="cmt-directories">
			{LOOP VAR(directories)}
				<li><a href="#" data-cmt-path="{VAR:directoryPath}" class="cmt-directory cmt-select-directory" title="{VAR:directoryName}">{VAR:directoryName}</a></li>
			{ENDLOOP VAR}
		</ul>
		<ul class="cmt-images cmt-images-view-style--list">
			{LOOP VAR(images)}
				<li>
					<figure class="cmt-select-image {IF ('{VAR:fileName}' == '{VAR:selectedFile}')}cmt-selected{ENDIF}">
						<div>
							<img data-cmt-image-path="{VAR:filePath}" title="{VAR:fileName}" src="{VAR:imageSource}" alt="{VAR:fileName}" data-cmt-image-width="{VAR:imageWidth}" data-cmt-image-height="{VAR:imageHeight}" />
						</div>
						<figcaption>
							{VAR:fileName}					
						</figcaption>
					</figure>
				</li>
			{ENDLOOP VAR}
		</ul>
	</div>
</div>

