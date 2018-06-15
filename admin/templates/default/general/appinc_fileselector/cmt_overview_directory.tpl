<tr class="cmtHover {IF ({ISSET:isClickable:VAR})}cmtFileSelectorDirectory{ENDIF}" data-filepath="{VAR:actDirectory}{VAR:dirURL}" data-url="{SELFURL}&cmt_extapp=fileselector&showOnlyDirContent=true&cmt_field={VAR:cmt_field}&actDirectory={VAR:actDirectory}{VAR:dirURL}">
	<td class="cmtfileSelectorColName">
		<!--
		{IF ({ISSET:isClickable:VAR})}
		<a class="cmtFileSelectorDirectory" href="{SELFURL}&cmt_extapp=fileselector&showOnlyDirContent=true&cmt_field={VAR:cmt_field}&actDirectory={VAR:actDirectory}{VAR:dirURL}"  data-filepath="{VAR:actDirectory}{VAR:dirURL}" title="Verzeichnis: {VAR:dirName:htmlentities}"><img class="cmtFileSelectorIcon directoryIcon" src="{CMT_TEMPLATE}general/img/filetypes/{VAR:fileIconSize}px/directory{VAR:imageApp}.png" class="imageLinked" alt="Verzeichnis: {VAR:dirName}">{VAR:dirName}</a>
		{ELSE}
		<span class="cmtFileSelectorDirectory" data-filepath="{VAR:actDirectory}{VAR:dirURL}" title="Verzeichnis: {VAR:dirName:htmlentities}"><img class="cmtFileSelectorIcon directoryIcon" src="{CMT_TEMPLATE}general/img/filetypes/{VAR:fileIconSize}px/directory{VAR:imageApp}.png" class="imageLinked" alt="Verzeichnis: {VAR:dirName}">{VAR:dirName}</span>
		{ENDIF}
		-->
		<span class="" title="Verzeichnis: {VAR:dirName:htmlentities}"><img class="cmtFileSelectorIcon directoryIcon" src="{CMT_TEMPLATE}general/img/filetypes/{VAR:fileIconSize}px/directory{VAR:imageApp}.png" class="imageLinked" alt="Verzeichnis: {VAR:dirName}">{VAR:dirName}</span>
	</td>
	<td class="cmtfileSelectorColType">
		
	</td>
	<td class="cmtfileSelectorColSize">
		
	</td>
	<td class="cmtfileSelectorColDate">
		{VAR:lastChangeDate}, {VAR:lastChangeTime}
	</td>
</tr>