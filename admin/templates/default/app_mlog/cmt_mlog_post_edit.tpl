<div id="postContainer">
	
<form enctype="multipart/form-data" method="POST" action="{SELFURL}&amp;mlogAction=savePost&amp;postID={VAR:id}" id="editEntryForm" name="editEntryForm">
	<div>
	
	<div class="editEntryRow1">
			<div class="editEntryRowHead">Kategorie</div>
			<div class="editEntryRowField">
			
				{VAR:categoryListContent}
			
			</div>
		</div>
		
		
		<div class="editEntryRow0">
			<div class="editEntryRowHead">Titel</div>
			<div class="editEntryRowField"><input type="text" size="40" value="{VAR:postTitle}" name="postTitle"></div>
		</div>
		<div class="editEntryRow1">
			<div class="editEntryRowHead">Text</div>
			<div class="editEntryRowField">
			<textarea rows="8" cols="60" name="postText" id="htmlEditor_0">{VAR:postText}</textarea></div>
		</div>
		
		<div class="editEntryRow1">
			<div class="editEntryRowHead">Stichworte</div>
			<div class="editEntryRowField"><input type="text" size="40" value="{VAR:postTags}" name="postTags"></div>
			<div class="editEntryRowDescription"></div>	
		</div>
		
		
			<div class="editEntryRow1">
			<div class="editEntryRowHead"><input type="checkbox" value="share" name="facebookFeed" id="facebookFeed" > Share on Facebook page
			<!-- 
			{IF ({ISSET:facebookLoginUrl:VAR})}<a href="{VAR:facebookLoginUrl}"> Login with Facebook</a>{ENDIF}
			{IF ({ISSET:facebookLogoutUrl:VAR})}<a href="{VAR:facebookLogoutUrl}"> Logout</a>{ENDIF}
			 -->
			<input type="hidden" name="facebookAuthenticationUrl" id="facebookAuthenticationUrl" value="{VAR:facebookLoginUrl}" />
			</div>
			
			<div class="editEntryRowDescription"></div>	
			
			
			
		</div>
		
	</div>
	
		
		
		<div class="editEntryRow0">
			<input type="hidden" value="{VAR:cmtRelations}" name="cmtRelations">
			<input type="hidden" value="" name="mediaPositions" id="mediaPositions">
			<input type="hidden" value="{VAR:postID}" name="postID">
		</div>
		
		
		
</form>
	
	<div id="relatedPostsSideBar" class="clearfix">
		{VAR:articleRelatedContent}
	</div>
</div>

<div>
	<script type="text/javascript" src="javascript/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
	
		tinyMCE.init({
				mode: "exact",
				elements: "htmlEditor_0", 
				language : "de", 
				plugins : "safari,style,table,save,advhr,advimage,advlink,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template", 
				theme: "advanced",
				extended_valid_elements : "hr[class|width|size|noshade],style",
				external_image_list_url : "includes/tinymce_images.php?sid=221b7e26d50e8c008c11f1f97f65b129&dir=",
				width: "475px",
				height: "200px",
				apply_source_formatting : false,
				cleanup: true,
				cleanup_on_startup : false,
				convert_fonts_to_spans : false,
				verify_html : false,
				convert_urls : false,
				content_css : "../",
				charset: 'UTF-8',
				forced_root_block : false,
				doctype : '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
				theme_cmtHtmlEditor_resizing : true,
				entity_encoding : 'numeric',
				entities : '160,nbsp,38,amp,39,apos,34,quot,162,cent,8364,euro,163,pound,165,yen,169,copy,174,reg,8482,trade,8240,permil,181,micro,183,middot,8226,bull,8230,hellip,8242,prime,8243,Prime,167,sect,182,para,223,szlig,8249,lsaquo,8250,rsaquo,171,laquo,187,raquo,8216,lsquo,8217,rsquo,8220,ldquo,8221,rdquo,8218,sbquo,8222,bdquo,60,lt,62,gt,8804,le,8805,ge,8211,ndash,8212,mdash,175,macr,8254,oline,164,curren,166,brvbar,168,uml,161,iexcl,191,iquest,710,circ,732,tilde,176,deg,8722,minus,177,plusmn,247,divide,8260,frasl,215,times,185,sup1,178,sup2,179,sup3,188,frac14,189,frac12,190,frac34,402,fnof,8747,int,8721,sum,8734,infin,8730,radic,8764,sim,8773,cong,8776,asymp,8800,ne,8801,equiv,8712,isin,8713,notin,8715,ni,8719,prod,8743,and,8744,or,172,not,8745,cap,8746,cup,8706,part,8704,forall,8707,exist,8709,empty,8711,nabla,8727,lowast,8733,prop,8736,ang,180,acute,184,cedil,170,ordf,186,ordm,8224,dagger,8225,Dagger,192,Agrave,194,Acirc,195,Atilde,196,Auml,197,Aring,198,AElig,199,Ccedil,200,Egrave,202,Ecirc,203,Euml,204,Igrave,206,Icirc,207,Iuml,208,ETH,209,Ntilde,210,Ograve,212,Ocirc,213,Otilde,214,Ouml,216,Oslash,338,OElig,217,Ugrave,219,Ucirc,220,Uuml,376,Yuml,222,THORN,224,agrave,226,acirc,227,atilde,228,auml,229,aring,230,aelig,231,ccedil,232,egrave,234,ecirc,235,euml,236,igrave,238,icirc,239,iuml,240,eth,241,ntilde,242,ograve,244,ocirc,245,otilde,246,ouml,248,oslash,339,oelig,249,ugrave,251,ucirc,252,uuml,254,thorn,255,yuml,914,Beta,915,Gamma,916,Delta,917,Epsilon,918,Zeta,919,Eta,920,Theta,921,Iota,922,Kappa,923,Lambda,924,Mu,925,Nu,926,Xi,927,Omicron,928,Pi,929,Rho,931,Sigma,932,Tau,933,Upsilon,934,Phi,935,Chi,936,Psi,937,Omega,945,alpha,946,beta,947,gamma,948,delta,949,epsilon,950,zeta,951,eta,952,theta,953,iota,954,kappa,955,lambda,956,mu,957,nu,958,xi,959,omicron,960,pi,961,rho,962,sigmaf,963,sigma,964,tau,965,upsilon,966,phi,967,chi,968,psi,969,omega,8501,alefsym,982,piv,8476,real,977,thetasym,978,upsih,8472,weierp,8465,image,8592,larr,8593,uarr,8594,rarr,8595,darr,8596,harr,8629,crarr,8656,lArr,8657,uArr,8658,rArr,8659,dArr,8660,hArr,8756,there4,8834,sub,8835,sup,8836,nsub,8838,sube,8839,supe,8853,oplus,8855,otimes,8869,perp,8901,sdot,8968,lceil,8969,rceil,8970,lfloor,8971,rfloor,9001,lang,9002,rang,9674,loz,9824,spades,9827,clubs,9829,hearts,9830,diams,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm,173,shy,233,eacute,237,iacute,243,oacute,250,uacute,193,Aacute,225,aacute,201,Eacute,205,Iacute,211,Oacute,218,Uacute,221,Yacute,253,yacute'
			});
	
	</script>

</div>
