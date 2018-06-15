{IF (!{ISSET:cmtDialog})}
<!DOCTYPE HTML>
<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset={CONSTANT:CHARSET}">
		<meta name="author" content="www.contentomat.de">
		<meta name="robots" content="noindex, nofollow">
		<meta http-equiv="cache-control" content="no-cache" />
		<title>content-o-mat: {CONSTANT:WEBNAME}</title>
		<script src="{PATHTOADMIN}javascript/jquery/jquery.js" type="text/javascript"></script>
		<script src="{PATHTOADMIN}javascript/jquery-ui/jquery-ui.js" type="text/javascript"></script>
		<script src="{PATHTOADMIN}javascript/jquery-ui/lang/ui.datepicker-de.js" type="text/javascript"></script>
		<script src="{PATHTOADMIN}javascript/jquery-plugins/jquery-ui-multiselect/ui.multiselect.js" type="text/javascript"></script>
		<script src="{PATHTOADMIN}javascript/jquery-plugins/jquery-ui-multiselect/lang/ui.multiselect-de.js" type="text/javascript"></script>
		<script src="{PATHTOADMIN}javascript/jquery-plugins/jquery-ui-selectmenu/ui.selectmenu.js" type="text/javascript"></script>
		<script src="{PATHTOADMIN}javascript/jquery-plugins/jquery-ui-fileinput/ui.fileinput.js" type="text/javascript"></script>
		<script src="{PATHTOADMIN}javascript/jquery-plugins/jquery-base64/jquery.base64.js" type="text/javascript"></script>
		<script src="{PATHTOADMIN}javascript/jquery-plugins/jquery-json/jquery.json.js" type="text/javascript"></script>
		<script src="{PATHTOADMIN}javascript/jquery-plugins/jquery-iframe-post-form/jquery.iframe-post-form.js" type="text/javascript"></script>
		<script src="{CMT_TEMPLATE}app_showtable/javascript/cmt_showtable_functions.js" type="text/javascript"></script>
		<script src="{PATHTOADMIN}javascript/jquery-plugins/jquery-iframe-post-form/jquery.iframe-post-form.js" type="text/javascript"></script>
		<script src="{PATHTOADMIN}javascript/js-cookie/js.cookie.js" type="text/javascript"></script>
		<script src="{CMT_TEMPLATE}administration/javascript/cmt_template_functions.js" type="text/javascript"></script>
		<script src="{PATHTOADMIN}javascript/jquery-plugins/jquery-ui-extendedaccordion/ui.extendedaccordion.js" type="text/javascript"></script>
		<script src="{CMT_TEMPLATE}administration/javascript/cmt_administration_navigation.js" type="text/javascript"></script>
		<script src="{PATHTOADMIN}javascript/jquery-plugins/jquery-cookie/jquery.cookie.js" type="text/javascript"></script>
		
		<link rel="Stylesheet" href="{CMT_TEMPLATE}administration/css/jquery-ui/jquery-ui.css" type="text/css" />
		<link rel="Stylesheet" href="{CMT_TEMPLATE}administration/css/jquery-ui/cmt_custom_jquery-ui.css" type="text/css" />
		<link rel="Stylesheet" href="{CMT_TEMPLATE}administration/css/cmt_fileselector_style.css" type="text/css" />
		<link rel="Stylesheet" href="{CMT_TEMPLATE}administration/css/tinymce/cmt_tinymce_overrides.css" type="text/css" />
		<link rel="Stylesheet" href="{CMT_TEMPLATE}administration/css/cmt_style.css" type="text/css" />
    </head>
	<body>
		<div class="st-container" id="cmtPageWrapper">
			<header id="cmtMainHeader">
				<div class="cmtPositionContainer">
					<div id="cmtLogoContainer" class="cmtMainNavigationToggler">
						<h2 id="cmtMainNavigationToggler">content-o-mat&nbsp;<span>v{VAR:cmtVersion}</span></h2>
						<h5>cms // application framework</h5>
					</div>
					<h1 id="cmtWebTitle">{CONSTANT:WEBNAME}</h1>
					<nav id="cmtServiceNavigationContainer">
						<a href="index.php?sid={SID}&amp;action=logout" class="cmtClose">{IF ("{VAR:cmtUserType}" == "admin")}Administrator{ELSE}Benutzer{ENDIF} <strong>'{VAR:cmtUserName}'</strong> abmelden</a>
					</nav>
				</div>
			</header>
			<div class="st-pusher">
				<nav class="st-menu" id="cmtMainNavigation">
					{VAR:cmtNavigation}
				</nav>

				<div class="st-content" id="cmtMainContent" role="main">
					<div class="st-content-inner">
						{IF ({ISSET:cmtHasSystemMessages:VAR})}<div class="cmt-system-messages">
						{LOOP VAR(cmtSystemMessages)}
							<div class="cmtMessage cmtMessage{VAR:message_type:ucfirst} cmt-system-message {IF ({ISSET:is_pinned:VAR})}cmtIgnore cmt-system-message-pinned{ENDIF}" data-cmt-message-id="{VAR:id}">
								{VAR:message_text}
							</div>
						{ENDLOOP VAR}						
						</div>{ENDIF}
						{VAR:cmtContent}
					</div>
				</div>
			</div>
		</div>
		<div id="cmtDialogContainer" class="cmtDialogContainer" style="display: none;"></div>
	</body>
</html>
{ELSE}
	{VAR:cmtContent}
{ENDIF}