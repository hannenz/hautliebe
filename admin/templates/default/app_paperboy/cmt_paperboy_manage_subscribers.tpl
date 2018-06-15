<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=ISO 8859-1">
		<meta name="author" content="www.contentomat.de">
		<meta http-equiv="content-language" content="de">
		<meta name="robots" content="NOFOLLOW">
		<meta http-equiv="pragma" content="no-cache">
		<title>content-o-mat: Paperboy - Abonnenten bearbeiten</title>
		<link rel="Stylesheet" href="{CMT_TEMPLATE}cmt_style.css" type="text/css">
		<link rel="Stylesheet" href="{CMT_TEMPLATE}app_paperboy/cmt_paperboy_style.css" type="text/css">
		<script  type="text/javaScript" src="javascript/cmt_functions.js"></script>
		<script type="text/javaScript" src="{CMT_TEMPLATE}app_paperboy/javascript/cmt_paperboy_functions.js"></script>
		<script type="text/javascript" src="{PATHTOADMIN}javascript/tiny_mce/tiny_mce.js"></script>
	</head>
	<body>

		<form name="editNewsletter" action="{SELFURL}" method="POST" enctype="multipart/form-data">
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
				Paperboy: Newsletter-Abonnenten
			</div>
		</div>
		<div class="appDropshadow"></div>
		<div class="serviceContainer clearfix">
			<a href="{SELFURL}&action="><img class="linkIcon" src="{CMT_TEMPLATE}app_paperboy/img/icon_edit_newsletter.gif">Bearbeiten</a>
			<img class="imageLinked" src="{CMT_TEMPLATE}app_paperboy/img/icon_separator.gif" />
			<a href="#" onClick="submitForm({formName: 'editNewsletter', action: 'previewNewsletter'})"><img class="linkIcon" src="{CMT_TEMPLATE}app_paperboy/img/icon_preview_newsletter.gif">Vorschau</a>
			<img class="imageLinked" src="{CMT_TEMPLATE}app_paperboy/img/icon_separator.gif">
			<a href="#" onClick="submitForm({formName: 'editNewsletter', action: 'showTestNewsletter'})"><img class="linkIcon" src="{CMT_TEMPLATE}app_paperboy/img/icon_test_newsletter.gif">Testversand</a>
			<img class="imageLinked" src="{CMT_TEMPLATE}app_paperboy/img/icon_separator.gif">
			<a href="#" onClick="submitForm({formName: 'editNewsletter', action: 'sendNewsletter'})"><img class="linkIcon" src="{CMT_TEMPLATE}app_paperboy/img/icon_send_newsletter.gif">Newsletterversand</a>
			<img class="imageLinked" src="{CMT_TEMPLATE}app_paperboy/img/icon_separator.gif">
			<a href="#" onClick="submitForm({formName: 'editNewsletter', action: 'newsletterArchive'})"><img class="linkIcon" src="{CMT_TEMPLATE}app_paperboy/img/icon_newsletter_archive.gif">Archiv</a>
			<img class="imageLinked" src="{CMT_TEMPLATE}app_paperboy/img/icon_separator.gif">
			<a class="paperboyCategorySelected" href="#" onClick="submitForm({formName: 'editNewsletter', action: 'manageSubscribers'})"><img class="linkIcon" src="{CMT_TEMPLATE}app_paperboy/img/subscribers.png">Abonnenten</a>
		</div>
		<br/>
		<!-- Layer 1: Absender und Liste -->
		<div id="pbLayer1" style="display: none">
			<div class="sliderBackground">
				<div class="sliderActive">{VAR:layer1Title}</div>
				<div class="sliderInactive"><a href="Javascript: void(0);" onClick="changeAllLayers('close', 'pbLayer'); changeLayer('pbLayer2');">{VAR:layer2Title}</a></div>
				<div class="sliderInactive"><a href="Javascript: void(0);" onClick="changeAllLayers('close', 'pbLayer'); changeLayer('pbLayer3');">{VAR:layer3Title}</a></div>
				<div class="sliderInactive"><a href="Javascript: void(0);" onClick="changeAllLayers('close', 'pbLayer'); changeLayer('pbLayer4');">{VAR:layer4Title}</a></div>
				<div class="clear"></div>
			</div>
{IF ({ISSET:addSubscriberMessage})}
				<div  class="{VAR:messageStyleClass}">{VAR:addSubscriberMessage}</div>
{ENDIF}
			<div class="serviceContainer">
				<div class="serviceTextHead">Neue Abonnenten hinzuf&uuml;gen:</div>
				<p>
					<span class="serviceText">1. E-Mail Adresse</span><br />				
					<input type="text" name="email" value="{VAR:email}" class="input_email_text" />
				</p>
				<p><span class="serviceText">2. Newsletter ausw&auml;hlen</span></p>
				{VAR:newsletterListContent}
				<div class="buttonContainer">
					<button type="submit" name="subaction"  value="addSubscriber">hinzuf&uuml;gen</button>
				</div>
			</div>
		</div>
		<!-- Layer 2: HTML-Editor -->
		<div id="pbLayer2" style="display: none">
			<div class="sliderBackground">
				<div class="sliderActivePre"><a href="Javascript: void(0);" onClick="changeAllLayers('close', 'pbLayer'); changeLayer('pbLayer1');">{VAR:layer1Title}</a></div>
				<div class="sliderActive">{VAR:layer2Title}</div>
				<div class="sliderInactive"><a href="Javascript: void(0);" onClick="changeAllLayers('close', 'pbLayer'); changeLayer('pbLayer3');">{VAR:layer3Title}</a></div>
				<div class="sliderInactive"><a href="Javascript: void(0);" onClick="changeAllLayers('close', 'pbLayer'); changeLayer('pbLayer4');">{VAR:layer4Title}</a></div>
				<div class="clear"></div>
			</div>
{IF ({ISSET:deleteSubscriberMessage})}
			<div  class="{VAR:messageStyleClass}">{VAR:deleteSubscriberMessage}</div>
{ENDIF}
			<div class="serviceContainer">	
				<div class="serviceTextHead">Newsletter-Abonnenten l&ouml;schen:</div>
				<div class="pbLayerCol1">
					<span class="serviceText">Suchbegriff</span><br />
					<input type="text" name="subscriberSearchDelete" value="{POSTVAR:subscriberSearchDelete}" class="search_text" />
					<button type="submit" name="search"  value="search">Suchen</button>
				</div>
				<div class="pbLayerCol2">
					{IF({ISSET:foundedSearchDelete:VAR})}
						<span class="serviceText"><b>{VAR:foundedSearchDelete}</b> Eintr&auml;ge gefunden</span>
					{ENDIF}<br />
					{VAR:subscriberTemplateDelete}
					<button type="submit" name="subaction"  value="deleteSubscriber" onClick="return confirmDeleteSubscriber(document.editNewsletter.subscriberEmailDelete.value)">l&ouml;schen</button>
				</div>
				<div class="clear"></div>
			</div>
		</div>
			
		<!-- Layer 3: Texteingabe -->
		<div id="pbLayer3" style="display: none">			
			<div class="sliderBackground">
				<div class="sliderInactive"><a href="Javascript: void(0);" onClick="changeAllLayers('close', 'pbLayer'); changeLayer('pbLayer1');">{VAR:layer1Title}</a></div>
				<div class="sliderActivePre"><a href="Javascript: void(0);" onClick="changeAllLayers('close', 'pbLayer'); changeLayer('pbLayer2');">{VAR:layer2Title}</a></div>
				<div class="sliderActive">{VAR:layer3Title}</div>
				<div  class="sliderInactive"><a href="Javascript: void(0);" onClick="changeAllLayers('close', 'pbLayer'); changeLayer('pbLayer4');">{VAR:layer4Title}</a></div>
				<div class="clear"></div>
			</div>
{IF ({ISSET:editSubscriberMessage})}
			<div class="{VAR:messageStyleClass}">{VAR:editSubscriberMessage}</div>
{ENDIF}
			<div class="serviceContainer">	
				<div class="serviceTextHead">Newsletter-Abonnenten bearbeiten:</div>
				<div class="pbLayerCol1">
					<span class="serviceText">Suchbegriff</span><br />
					<input type="text" name="subscriberSearchEdit" value="{POSTVAR:subscriberSearchEdit}" class="search_text" />
					<button type="submit"  name="search"  value="search">Suchen</button>
				</div>
				<div class="pbLayerCol2">
					{IF({ISSET:foundedSearchEdit:VAR})}
						<span class="serviceText"><b>{VAR:foundedSearchEdit}</b> Eintr&auml;ge gefunden</span>
					{ENDIF}<br />
					{VAR:subscriberTemplateEdit}
					<button type="submit" name="subaction"  value="editSubscriber">Bearbeiten</button>		
				</div>
				<div class="clear"></div>
				{IF({ISSET:subscribedNewsletterListContent:VAR})}
					<p>&nbsp;<br />Der User <b>{VAR:subscriberEmail}</b> hat folgende Newsletter abonniert:</p>
					{VAR:subscribedNewsletterListContent}
					<div class="formfieldCheckboxOuterContainer saveChangesButton">
						<button type="submit" name="subaction"  value="updateSubscriber">Speichern</button>
					</div>
				{ENDIF}
			</div>
		</div>
		
		<!-- Layer 4: Abonnenten importieren -->
		<div id="pbLayer4" style="display: none">			
			<div class="sliderBackground">
				<div class="sliderInactive"><a href="Javascript: void(0);" onClick="changeAllLayers('close', 'pbLayer'); changeLayer('pbLayer1');">{VAR:layer1Title}</a></div>
				<div class="sliderInactive"><a href="Javascript: void(0);" onClick="changeAllLayers('close', 'pbLayer'); changeLayer('pbLayer2');">{VAR:layer2Title}</a></div>
				<div class="sliderActivePre"><a href="Javascript: void(0);" onClick="changeAllLayers('close', 'pbLayer'); changeLayer('pbLayer3');">{VAR:layer3Title}</a></div>
				<div class="sliderActive">{VAR:layer4Title}</div>
				<div class="clear"></div>
			</div>
{IF ({ISSET:importSubscribersMessage})}
			<div  class="{VAR:messageStyleClass}">{VAR:importSubscribersMessage}</div>
{ENDIF}
			<div class="serviceContainer">	
				<div class="serviceTextHead">Abonnentenliste importieren:</div>
{IF(!{ISSET:importLog:VAR})}
				<p>
					<b>Hinweis:</b> Die CSV-Textdatei darf nur aus einer Spalte mit ausschlieﬂlich den E-Mailadressen darin bestehen.
				</p>
				<p>
					<span class="serviceText">1. CSV Datei ausw&auml;hlen</span><br />
					<input type="file" name="importListFile">	 	
				</p>
					<p><span class="serviceText">2. Newsletter abonnieren</span></p>
				{VAR:importNewsletterListContent}
				<div class="buttonContainer">
					<button type="submit"  name="subaction"  value="importSubscribers">Abonnentenliste importieren</button>
				</div>
			</div>
{ELSE}				
			<div  class="serviceLog">
				Insgesamt ({VAR:importStateSumme}) E-Mail Adressen in Datei ({VAR:importStateFileName}) <br />
				{VAR:importStateNew} <span class="importStateNew"> neu angelegt</span><br />
				{VAR:importStateImported} <span class="importStateImported">abonniert</span><br />
				{VAR:importStateNotImported} <span class="importStateNotImported">Newsletter bereits abonniert</span><br />
				{VAR:importStateError} <span class="importStateError"> ung&uuml;ltige E-Mail Adresse, nicht importiert!</span><br />---<br /><br />
				{VAR:importLog}
			</div>
{ENDIF}
		</div>
		<input type="hidden" name="action" value="manageSubscribers">
		<input type="hidden" name="deleteFile" value="">
		</form>
		<script type="text/javascript">
		
		initPbLayers();


		</script>
		

	</body>
</html>