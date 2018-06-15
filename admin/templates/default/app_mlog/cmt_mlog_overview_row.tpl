{EVAL}
	$posts = new Contentomat\MLog\Posts;
	$commentsNr = $posts->getCommentsCount($cmt_parservars['id']);
{ENDEVAL}
<h3 class="mlog-overview-post-headline clearfix">{VAR:post_online_date} Uhr<span class="mlog-overview-post-headline-id">{VAR:id}</span></h3>
<div class="mlogOverviewRowContainer cmtSelectable clearfix cmtRow{ALTERNATIONFLAG}  cmtHover ">
	<div class="mlogOverviewInfoContainer cmtSelectable">
		<table class="mLogInfoTable">
			<tbody>
				<tr>
					<td class="serviceText">Status: </td>
					<td class="mlog-post-status">
						<span class="mlog-post-status-{FIELD:post_status}" title="{VAR:post_status}" ></span>
					</td>
				</tr>
				<tr>
					<td class="serviceText">Autor:</td>
					<td class="mlogAuthor">{VAR:post_author_id}</td>
				</tr>
				<tr>
					<td class="serviceText">Kategorien:</td>
					<td class="mlogviews">{VAR:post_category}</td>
				</tr>
				<tr>
					<td class="serviceText">Aufrufe:</td>
					<td class="mlogviews">{VAR:post_views}</td>
				</tr>
				<tr>
					<td class="serviceText">Kommentare:</td>
					<td class="mlogviews">{IF ({ISSET:commentsNr:USERVAR})}{USERVAR:commentsNr}{ELSE}--{ENDIF}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="mlogOverviewMainContainer clearfix">
		<div class="clearfix">
			{IF ({ISSET:post_subtitle:VAR})}<h5 class="mlog-overview-subtitle">{VAR:post_subtitle:htmlspecialchars}</h5>{ENDIF}
			<h4 class="mlog-overview-title">{VAR:post_title:htmlspecialchars}</h4>
			<div class="mlog-overview-image-container">
			{IF ({ISSET:post_image:VAR})}
			<figure class="mlog-overview-image">
				<img src="{PATHTOWEBROOT}media/mlog/static/{VAR:post_image}" alt="" />
			</figure>
			{ELSE}
				<div class="mlog-icon mlog-icon-image"></div>
			{ENDIF}
			</div>
			<p class="mlog-overview-teaser">{VAR:post_teaser}</p>
		</div>
	</div>
	<div class="mlogOverviewServiceContainer">
		<a class="cmtButton cmtButtonEditEntry mlogButtonShow" href="{VAR:cmtButtonEditLink}">bearbeiten</a>
		<a class="cmtButton cmtButtonDuplicateEntry" href="{VAR:cmtButtonDuplicateLink}">duplizieren</a>
		{IF ({ISSET:mlogPreviewLink:VAR})}<a class="cmtButton" href="{VAR:mlogPreviewLink}" target="_blank">Vorschau</a>{ENDIF}
		<!-- Preview Link: /de/20/Vorschau,1,10,{VAR:id}.html?sid={SID} -->
		<a class="cmtButton cmtButtonDeleteEntry cmtDialog cmtDialogConfirm" href="Javascript:void(0);" title="Mlog-Artikel löschen" data-dialog-confirm-url="{VAR:cmtButtonDeleteLink}" data-dialog-var="{VAR:id}" data-dialog-content-id="cmtDialogConfirmDeletion">löschen</a>
		{VAR:cmtButtonSelect}
	</div>
</div>
