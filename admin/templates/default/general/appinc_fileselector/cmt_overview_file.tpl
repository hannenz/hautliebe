<tr class="cmtHover cmtFileSelectorFile" data-filepath="{VAR:fileURL}">
	<td class="cmtfileSelectorColName">
		<!-- <a class="cmtFileSelectorFile" href="{VAR:fileURL}'" title="{VAR:fileName}" data-filepath="{VAR:fileURL}"><img src="{CMT_TEMPLATE}general/img/filetypes/{VAR:fileIconSize}px/{VAR:fileIcon}" class="fileIcon" alt="{VAR:fileName}">&nbsp;{VAR:fileName}</a>-->
		<span ><img src="{CMT_TEMPLATE}general/img/filetypes/{VAR:fileIconSize}px/{VAR:fileIcon}" class="fileIcon" alt="">&nbsp;{VAR:fileName}</span>
	</td>
	<td class="cmtfileSelectorColType">
		{VAR:extension}
	</td>
	<td class="cmtfileSelectorColSize">
		{VAR:fileSize} {VAR:fileSizeUnit}
	</td>
	<td class="cmtfileSelectorColDate">
		{VAR:lastChangeDate}, {VAR:lastChangeTime}
	</td>
</tr>