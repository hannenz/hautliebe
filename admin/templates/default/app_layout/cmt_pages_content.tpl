<div class="cmt-box">
	<header class="cmt-box-header cmt-breadcrumbs">
		<a href="#" data-cmt-internal-page-id="0" class="cmt-breadcrumbs-icon cmt-breadcrumbs-home cmt-select-parent"></a>
		{IF (!{ISSET:isRootLevel:VAR})}<a href="#" data-cmt-internal-page-id="{VAR:parentID}" class="cmt-breadcrumbs-icon  cmt-breadcrumbs-up cmt-select-parent">{VAR:parentTitle}</a>{ELSE}
		<span>Root</span>
		{ENDIF}
	</header>
	<div class="cmt-box-content">
		<ul class="cmt-pages">
			{LOOP VAR(pages)}
				<li>
				<a href="#" data-cmt-internal-page-id="{VAR:id}" class="cmt-{VAR:cmt_type} cmt-select-page" title="{VAR:cmt_title:htmlentities} / ID: {VAR:id}">{VAR:cmt_title}&nbsp;<span>({VAR:id})</span></a>
				{IF ({ISSET:hasChildren})}<a href="#" data-cmt-internal-parent-id="{VAR:id}" class="cmt-flat-button cmt-select-children" title="{VAR:hasChildren} Unterelement(e)"></a>{ENDIF}
				</li>
			{ENDLOOP VAR}
		</ul>
	</div>
</div>