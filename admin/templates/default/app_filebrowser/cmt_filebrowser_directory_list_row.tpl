{IF({ISSET:dirReadable:VAR})}
<tr id="cmtDirectoryNode-{VAR:nodeID}" data-path="{VAR:directoryPath}" class="cmtFileRow cmtHover cmtNode cmtChildOf-{VAR:directoryParent} {IF({ISSET:dirWriteable:VAR})}cmtDropZone{ENDIF} {IF({ISSET:dirSelected:VAR})}cmtSelected{ENDIF}" data-children="{VAR:hasChildren}">
	<td>
		<div class="cmtNodeContent {IF({ISSET:dirWriteable:VAR})}cmtDraggable{ENDIF}" >
			<a href="{SELFURL}&cmtDirectory={VAR:directoryPath}&dirID={VAR:nodeID}" class="cmtDirectory  {IF ("{VAR:dirSize}" == "0")}cmtDirectoryEmpty{ENDIF}"></a>
			<span id="entry{VAR:entryNr}" class="fileName">{VAR:dirName}</span>
		</div>
	</td>
</tr>
{ELSE}
<tr id="cmtDirectoryNode-{VAR:nodeID}" data-directorypath="{VAR:directoryPath}" class="cmtFileRow cmtNode cmtChildOf-{VAR:directoryParent}" data-children="0">
	<td>
		<span class="cmtIcon cmtDisabled cmtDirectory cmtDirectoryEmpty"></span>
		<span id="entry{VAR:entryNr}" class="">{VAR:dirName}</span>
	</td>
</tr>
{ENDIF}