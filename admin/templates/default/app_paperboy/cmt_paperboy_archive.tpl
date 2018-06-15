<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=ISO 8859-1">
		<meta name="author" content="www.contentomat.de">
		<meta http-equiv="content-language" content="de">
		<meta name="robots" content="NOFOLLOW">
		<meta http-equiv="pragma" content="no-cache">
		<title>content-o-mat: Paperboy - Newslettervorschau</title>
		<link rel="Stylesheet" href="{CMT_TEMPLATE}cmt_style.css" type="text/css">
		<link rel="Stylesheet" href="{CMT_TEMPLATE}app_paperboy/cmt_paperboy_style.css" type="text/css">
		<script  type="text/javaScript" src="javascript/cmt_functions.js"></script>
	</head>
	<body>
		<link rel="Stylesheet" href="{CMT_TEMPLATE}cmt_style.css" type="text/css">
		<script type="text/javaScript" src="{PATHTOADMIN}javascript/cmt_functions.js"></script>
		<div class="appHeadlineContainer">
			<div id="appIcon">
				<script type="text/javascript">
					if (document.all) {
						var bg = document.getElementById('appIcon');
						var bgImg = bg.currentStyle.backgroundImage.match(/url\(\"?(.*)\"\)/);
						bg.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, src='"+bgImg[1].toString()+"')";
					}
				</script>
			</div>
			<div class="appHeadline">
				Paperboy Newsletterversand: Archivierte Newsletter
			</div>
		</div>
		<div class="appDropshadow"></div>
		<div class="serviceContainer">
			<a href="{SELFURL}&action="><img class="imageLinked" src="{CMT_TEMPLATE}app_paperboy/img/icon_edit_newsletter.gif">Bearbeiten</a>
			<img class="imageLinked" src="{CMT_TEMPLATE}app_paperboy/img/icon_separator.gif">
			<a href="{SELFURL}&amp;action=previewNewsletter"><img class="linkIcon" src="{CMT_TEMPLATE}app_paperboy/img/icon_preview_newsletter.gif">Vorschau</a>
			<img class="imageLinked" src="{CMT_TEMPLATE}app_paperboy/img/icon_separator.gif">
			<a href="{SELFURL}&amp;action=showTestNewsletter"><img class="linkIcon" src="{CMT_TEMPLATE}app_paperboy/img/icon_test_newsletter.gif">Testversand</a>
			<img class="imageLinked" src="{CMT_TEMPLATE}app_paperboy/img/icon_separator.gif">
			<a href="{SELFURL}&amp;action=sendNewsletter"><img class="linkIcon" src="{CMT_TEMPLATE}app_paperboy/img/icon_send_newsletter.gif">Newsletterversand</a>
			<img class="imageLinked" src="{CMT_TEMPLATE}app_paperboy/img/icon_separator.gif">
			<a class="paperboyCategorySelected" href="{SELFURL}&amp;action=newsletterArchive"><img class="linkIcon" src="{CMT_TEMPLATE}app_paperboy/img/icon_newsletter_archive.gif">Archiv</a>
			<img class="imageLinked" src="{CMT_TEMPLATE}app_paperboy/img/icon_separator.gif">
			<a href="{SELFURL}&amp;action=manageSubscribers"><img class="linkIcon" src="{CMT_TEMPLATE}app_paperboy/img/subscribers.png">Abonnenten</a>
		</div>
		{IF ({ISSET:failedNewsletterTable:VAR})}
		<div class="serviceContainer"><span class="serviceText" style="font-weight: bold">Fehlgeschlagene und abgebrochene Versendeversuche</span></div>
		<table id="archivedFailedNewsletters" width="100%">
			<thead>
			<tr>
				<td class="tableDataHeadCell" width="50%">Newslettername</td>
				<td class="tableDataHeadCell" width="50%">verschickt am</td>
				<td class="tableActionHeadCell" nowrap="nowrap">Versand wiederaufnehmen</td>
			</tr>
			</thead>
			<tbody>
				{VAR:failedNewsletterTable}
			</tobdy>
		</table>
		{ENDIF}
		{IF ({ISSET:newsletterTable:VAR})}
		<div class="serviceContainer"><span class="serviceText" style="font-weight: bold">Versendete Newsletter</span></div>
		<table id="archivedFailedNewsletters" width="100%">
			<thead>
			<tr>
				<td class="tableDataHeadCell" width="50%">Newslettername</td>
				<td class="tableDataHeadCell" width="50%">verschickt am</td>
				<td class="tableActionHeadCell" nowrap="nowrap">Versand wiederaufnehmen</td>
			</tr>
			</thead>
			<tbody>
				{VAR:newsletterTable}
			</tobdy>
		</table>
		{ENDIF}
	</body>
</html>