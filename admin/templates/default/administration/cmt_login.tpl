<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="author" content="www.contentomat.de">
	<meta http-equiv="content-language" content="de">
	<meta name="robots" content="noindex, nofollow">
	<meta http-equiv="pragma" content="no-cache">
	<title>{CONSTANT:WEBNAME} // login</title>
	<link rel="stylesheet" type="text/css" href="templates/default/administration/css/cmt_style_login.css">
</head>
<body>
	<div id="contentWrapper">
		<div id="content">
			<div id="head"><span class="name">content-o-mat</span>&nbsp;<span class="version">{VAR:cmtVersion}</span></div>
			<div id="headSubhead">content management system // web application framework</div>
			<div id="contentContainer">
				<div id="headBarImage"> </div>
				<div id="headBar">Login</div>
				{IF ({ISSET:error})}
				<div class="cmtMessage cmtMessageError">
					Die angegeben Zugangsdaten sind falsch!
				</div>	
				{ENDIF}
				<div id="contentText">
					<form method="POST" action="{SELF}" name="login">
						<p id="nameContainer">
							<label class="labelText">Name</label>
							<input type="text" name="user">
							<script type="text/javaScript">document.login.user.focus();</script>
						</p>
						<p id="pwContainer">
							<label class="labelText">Passwort</label>
							<input type="password" name="pw" class="inputText">
						</p>
						<p id="buttonsContainer">
							<input id="buttonLogin" type="submit" value="Login">
							<input type="hidden" name="sid" value="{SID}">
							<input type="hidden" name="firstlogin" value="1">
						</p>
					</form>
				</div>		
			</div>
		</div>
	</div>
</body>
</html>