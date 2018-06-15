<table class="cmtTable">
	<thead>
		<tr>
			<td class="extensionNameColumn">Extension</td>
			<td class="extensionVersionColumn">Version</td>
		</tr>
	</thead>
	<tbody>
{LOOP VAR(extensions)}
<tr class="cmtHover">
	<td class="extensionName">{VAR:extension}</td>
	<td class="extensionVersion">{VAR:version}</td>
</tr>
{ENDLOOP VAR}
</tbody>
</table>