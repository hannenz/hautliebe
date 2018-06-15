/**
 * cmt_paperboy_functions.js
 * Provides all methods for newsletter app "Paperboy"
 * 
 * @author J.Hahn <info@contentomat.de>
 * @version 2016-01-21
 * 
 */


cmtPaperboy = {

	cacheTemplateID: 0,
	cachePageID: 0,
	ajaxURL: '',				// URL for ajax requests (without querystring parameters
	deliveryObserver: null,
	deliveryTimeOut: 30,		// Fire delivery restart dialog after X Seconds in case of a server error
	deliveryTimeOutAdd: 5,
	deliveryData: {},			// Data object received from PHP script in delivery process
	textareaID: null,			// paste loaded recipient list in this textarea

	/**
	* function initialize()
	* 
	* @param void
	* @return void
	*/
   
	initialize: function() {
		
		// Funktionswerweiterungen und Grundlegendes
		this.initBasics();
		
		// Initialisierungen nach dem Laden des DOM starten
		jQuery(document).ready( function(ev) {
			this.initPaperboy();
		}.bindScope(this));	
	},
		
	/**
	 * function initBasics()
	 * Initalisiert grundlegende, globale Funktionen
	 * 
	 * @param void
	 * @return void
	 */
	initBasics: function() {
		Function.prototype.bindScope = function(scope) {
			var _function = this;

			return function() {
				return _function.apply(scope, arguments);
			};
		};
	},
	
	
	/**
	* function initPage()
	* 
	*  @param void
	*  @return void
	*/	
   
	initPaperboy: function(event) {

		this.ajaxURL = document.location.href.replace(/(cmtAction|cmt_action)=[^&]*/, '');

		this.initTabs();
		this.initSelect();
		this.initButtons();
		this.initHTMLEditor();
		this.initProgressBar();
		this.initRecipientTextareas();
		this.initRecipientTextareaButtons();
	},

	initProgressBar: function() {
		
	},

	/**
	 * function initButtons()
	 * Calls all methods to init functional buttons.
	 * 
	 * @param void
	 * @return void
	 */
	initButtons: function() {
		
		this.initDeliveryStartButton();
		this.initWebsitePageRefreshButton();
	},
	
	/**
	 * function initWebsitePageRefreshButton()
	 * Inits the button for refreshing the website page selection.
	 * 
	 * @param void
	 * @return void
	 */
	initWebsitePageRefreshButton: function() {
		jQuery('#refreshWebsitePage').on('click', function(ev) {
			ev.preventDefault();
			
			this.selectPage({
				currentTarget: jQuery('#newsletterPageID')
			});
			
		}.bindScope(this));
		
	},
	
	/**
	 * function initDeliveryStartButton()
	 * Inits the button to start newsletter delivery.
	 * 
	 * @param void
	 * @return void
	 */
	initDeliveryStartButton: function() {
		
		// init delivery start button
		jQuery('#startDelivery').on('click', function(ev) {
			ev.preventDefault();
			
			jQuery(ev.currentTarget).button('option', 'disabled', true);

			this.sendNewsletter();
		}.bindScope(this));
	},
	
	/**
	 * function initTabs()
	 * Initializes tabs and panels
	 * 
	 *  @param void
	 *  @return void
	 */
	initTabs: function() {
		
		jQuery('a', '#cmtPageTabs').each(function(index, a) {
			
			jQuery(a).data('tab-nr', index + 1);

			jQuery(a).on('click', function(ev) {
				
				var a = ev.currentTarget;
				
				if (jQuery('#cmtEditNewsletterForm')[0]) {
					jQuery('#cmtChangeAction').val(jQuery(a).data('change-action'));
					jQuery('#cmtTab').val(jQuery(a).data('tab-nr'));
					jQuery('#cmtEditNewsletterForm').submit();
					
					ev.preventDefault();
					return false;
				}
				
			}.bindScope(this));
		}.bindScope(this));
		
		jQuery('#' + jQuery('a', '#cmtTabs').first().data('for-panel')).show();
	},
	
	/**
	 * function initHTMLEditor
	 * Initializes the HTML Editor
	 * 
	 * @param void
	 * @return void
	 */
	initHTMLEditor: function() {

		// ACHTUNG: Hier noch die Makros austauschen!!!
		tinymce.init({	
			mode: "exact",
			button_tile_map : true,
			cleanup : true,
			elements: "newsletterHTML",
			language : "de", 
			
			plugins: 'paste code toc tabfocus print preview searchreplace autolink directionality visualblocks visualchars fullscreen image imagetools link media template codesample table charmap hr nonbreaking anchor advlist lists textcolor contextmenu colorpicker textpattern help',
			
			menubar: 'file edit insert view format table tools help',
			toolbar1: 'formatselect | bold italic strikethrough removeformat | link image | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | paste',
			removed_menuitems: 'newdocument',
			 
			image_advtab: true,
			width: 800,
			height: 600,
			remove_script_host : false,
			verify_html : false,
			remove_linebreaks : false,
			convert_urls : false, 
			convert_newlines_to_brs : false,
			convert_fonts_to_spans : false,
			forced_root_block : false,
			entity_encoding : "named",
			charset: 'utf-8',
//			entities : "160,nbsp,38,amp,34,quot,162,cent,8364,euro,163,pound,165,yen,169,copy,174,reg,8482,trade,8240,permil,181,micro,183,middot,8226,bull,8230,hellip,8242,prime,8243,Prime,167,sect,182,para,223,szlig,8249,lsaquo,8250,rsaquo,171,laquo,187,raquo,8216,lsquo,8217,rsquo,8220,ldquo,8221,rdquo,8218,sbquo,8222,bdquo,60,lt,62,gt,8804,le,8805,ge,8211,ndash,8212,mdash,175,macr,8254,oline,164,curren,166,brvbar,168,uml,161,iexcl,191,iquest,710,circ,732,tilde,176,deg,8722,minus,177,plusmn,247,divide,8260,frasl,215,times,185,sup1,178,sup2,179,sup3,188,frac14,189,frac12,190,frac34,402,fnof,8747,int,8721,sum,8734,infin,8730,radic,8764,sim,8773,cong,8776,asymp,8800,ne,8801,equiv,8712,isin,8713,notin,8715,ni,8719,prod,8743,and,8744,or,172,not,8745,cap,8746,cup,8706,part,8704,forall,8707,exist,8709,empty,8711,nabla,8727,lowast,8733,prop,8736,ang,180,acute,184,cedil,170,ordf,186,ordm,8224,dagger,8225,Dagger,192,Agrave,194,Acirc,195,Atilde,196,Auml,197,Aring,198,AElig,199,Ccedil,200,Egrave,202,Ecirc,203,Euml,204,Igrave,206,Icirc,207,Iuml,208,ETH,209,Ntilde,210,Ograve,212,Ocirc,213,Otilde,214,Ouml,216,Oslash,338,OElig,217,Ugrave,219,Ucirc,220,Uuml,376,Yuml,222,THORN,224,agrave,226,acirc,227,atilde,228,auml,229,aring,230,aelig,231,ccedil,232,egrave,234,ecirc,235,euml,236,igrave,238,icirc,239,iuml,240,eth,241,ntilde,242,ograve,244,ocirc,245,otilde,246,ouml,248,oslash,339,oelig,249,ugrave,251,ucirc,252,uuml,254,thorn,255,yuml,914,Beta,915,Gamma,916,Delta,917,Epsilon,918,Zeta,919,Eta,920,Theta,921,Iota,922,Kappa,923,Lambda,924,Mu,925,Nu,926,Xi,927,Omicron,928,Pi,929,Rho,931,Sigma,932,Tau,933,Upsilon,934,Phi,935,Chi,936,Psi,937,Omega,945,alpha,946,beta,947,gamma,948,delta,949,epsilon,950,zeta,951,eta,952,theta,953,iota,954,kappa,955,lambda,956,mu,957,nu,958,xi,959,omicron,960,pi,961,rho,962,sigmaf,963,sigma,964,tau,965,upsilon,966,phi,967,chi,968,psi,969,omega,8501,alefsym,982,piv,8476,real,977,thetasym,978,upsih,8472,weierp,8465,image,8592,larr,8593,uarr,8594,rarr,8595,darr,8596,harr,8629,crarr,8656,lArr,8657,uArr,8658,rArr,8659,dArr,8660,hArr,8756,there4,8834,sub,8835,sup,8836,nsub,8838,sube,8839,supe,8853,oplus,8855,otimes,8869,perp,8901,sdot,8968,lceil,8969,rceil,8970,lfloor,8971,rfloor,9001,lang,9002,rang,9674,loz,9824,spades,9827,clubs,9829,hearts,9830,diams,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm,173,shy,233,eacute,237,iacute,243,oacute,250,uacute,193,Aacute,225,aacute,201,Eacute,205,Iacute,211,Oacute,218,Uacute,221,Yacute,253,yacute",
			extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color|style]",
			external_image_list_url : "includes/tinymce_images.php?sid={SID}&dir={VAR:imageDir}",
			doctype : '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',

		});
	},
	
	/**
	 * function initSelect()
	 * Initializes select fields with functions.
	 * 
	 * @param void
	 * @return void
	 */
	initSelect: function() {

		jQuery('#newsletterTemplateID').on('change', this.selectTemplate.bindScope(this));
		jQuery('#newsletterPageID').on('change', this.selectPage.bindScope(this));
		jQuery('#newsletterID').on('change', this.selectRecipientsList.bindScope(this));
		
		this.cachePageID = jQuery('#newsletterPageID').val();
		this.cacheTemplateID = jQuery('#newsletterTemplateID').val();
	},
	
	initRecipientTextareas: function() {
		
		jQuery('#newsletterTo, #newsletterCC, #newsletterBCC').on('keyup', this.resetRecipientsList.bindScope(this));
		
	},
	
	initRecipientTextareaButtons: function() {
		jQuery('#newsletterToLoad, #newsletterCCLoad, #newsletterBCCLoad').on('click', this.loadRecipientsListInTextarea.bindScope(this));
		jQuery('#newsletterToEmpty, #newsletterCCEmpty, #newsletterBCCEmpty').on('click', this.clearRecipientsTextarea.bindScope(this));
	},
	
	/**
	 * function selectTemplate()
	 * Method is called by an 'onchange' event of a select field. Expects jQuery Event object as parameter.
	 * 
	 *  @param {object} JQuery event object.
	 *  @return void
	 */
	selectTemplate: function(ev) {
		
		var select = ev.currentTarget;
		this.templateID = jQuery('option:selected', select).val();

		cmtPage.showConfirm({
			title: jQuery('#confirmChangeTemplateID').attr('title'),
			contentContainerID: 'confirmChangeTemplateID',
			onConfirm: this.proceedSelectTemplate.bindScope(this),
			onCancel: this.cancelSelectTemplate.bindScope(this)
		});
		
		return false;

	},
	
	proceedSelectTemplate: function() {
		this.loadTemplate({
			newsletterTemplateID: jQuery('#newsletterTemplateID').val()
		});
	},

	cancelSelectTemplate: function() {
		
		// reset origin select and jQuery UI selectmenu
		jQuery('#newsletterTemplateID').val(this.cacheTemplateID);
		jQuery('#newsletterTemplateID').selectmenu('value', this.cacheTemplateID );
	},
	
	selectRecipientsList: function(ev) {
		this.clearRecipientTextareas();	
	},
	
	resetRecipientsList: function(ev) {
		jQuery('#newsletterID').selectmenu('value', 0);
	},
	
	clearRecipientTextareas: function() {
		jQuery('#newsletterTo').val('');
		jQuery('#newsletterCC').val('');
		jQuery('#newsletterBCC').val('');
	},
	
	clearRecipientsTextarea: function(ev) {
		jQuery('#' + jQuery(ev.currentTarget).data('field')).val('');
	},
	
	loadRecipientsListInTextarea: function(ev) {

		var button = ev.currentTarget;
		this.textareaID = '#' + jQuery(button).data('field');
		
		this.paperboyWorking();
		
		jQuery.ajax({
			url: this.ajaxURL,
			data: {
				newsletterID: jQuery(this.textareaID + 'Select').val(),
				cmtAction: 'loadRecipientList'
			},
			type: 'POST'
		})
		.done(this.pasteRecipientsListInTextarea.bindScope(this));		
	},
	
	pasteRecipientsListInTextarea: function(response) {
		
		if (typeof response == 'string') {
			response = jQuery.parseJSON(response);
		}
		
		jQuery(this.textareaID).val(response.list);
		
		this.resetRecipientsList();
		this.paperboyLazy();
	},
	
	/**
	 * function loadTemplate()
	 * Performs an Ajax request and loads the data for a newly selected template
	 * 
	 * @params {object} data Object with the property 'newsletterTemplateID' : Number
	 * @return void 
	 */
	loadTemplate: function(data) {
		
		this.paperboyWorking();
		
		jQuery.ajax({
			url: this.ajaxURL,
			data: {
				newsletterTemplateID: data.newsletterTemplateID,
				cmtAction: 'loadTemplate'
			},
			type: 'POST'
		})
		.done(this.showTemplate.bindScope(this));

	},
	
	/**
	 * function showTemplate()
	 * Expects an JSON formated string(!) or object containing the data.
	 * 
	 * @param {string} From Ajax request received string.
	 * @return void
	 */
	showTemplate: function(response) {
		
		// reset page select menu
		this.resetSelectPage();

		if (typeof response == 'string') {
			response = jQuery.parseJSON(response);
		}

		// 1. Absendermail
		jQuery('#newsletterID').selectmenu('value', parseInt(response.newsletterID));

		// 2. Absendermail
		jQuery('#newsletterSenderName').val(response.newsletterSenderName);
		
		// 3. Absendermail
		jQuery('#newsletterSenderEmail').val(response.newsletterSenderEmail);			
		
		// 4. Betreffzeile
		jQuery('#newsletterSubject').val(response.newsletterSubject);
		
		// 5. HTML
		jQuery('#htmlArea').val(response.newsletterHTML);
		//tinyMCE.updateContent('htmlArea');
		tinyMCE.execInstanceCommand('newsletterHTML', 'mceSetContent', false, response.newsletterHTML, false);

		// 6. Textinhalte
		jQuery('#newsletterText').val(response.newsletterText);
		
		this.paperboyLazy();

	},
	
	/**
	 * function selectPage()
	 * Method is called by an 'onchange' event of a select field. Expects jQuery Event object as parameter.
	 * 
	 *  @param {object} JQuery event object.
	 *  @return void
	 */
	selectPage: function(ev) {

		var select = ev.currentTarget;
		this.pageID = jQuery('option:selected', select).val();

		cmtPage.showConfirm({
			title: jQuery('#confirmChangePageID').attr('title'),
			contentContainerID: 'confirmChangePageID',
			onConfirm: this.proceedSelectPage.bindScope(this),
			onCancel: this.cancelSelectPage.bindScope(this)
		});
		
		return false;
	},
	
	/**
	 * function proceedSelectPage()
	 * Executed when the dialog was confirmed.
	 * 
	 */
	proceedSelectPage: function() {
		this.loadPage({
			newsletterPageID: this.pageID
		});
	},
	
	/**
	 * function cancelSelectPage()
	 * Called when a page selection is canceled. Resets the HTML and the UI select menu.
	 */
	cancelSelectPage: function() {
		
		// reset origin select and jQuery UI selectmenu
		jQuery('#newsletterPageID').val(this.cachePageID);
		jQuery('#newsletterPageID').selectmenu('value', this.cachePageID );
	},

	
	resetSelectPage: function() {
		jQuery('#newsletterPageID').val();
		jQuery('#newsletterPageID').selectmenu('value', '');
	},
	
	/**
	 * function loadPage()
	 * Performs an Ajax request and loads the data for a newly selected page.
	 * 
	 * @params {object} data Object with the property 'newsletterPageID' : Number
	 * @return void 
	 */
	loadPage: function(data) {

		this.paperboyWorking();
		
		jQuery.ajax({
			url: this.ajaxURL,
			data: {
				newsletterPageID: data.newsletterPageID,
				action: 'loadPage'
			},
			type: 'POST'
		})
		.done(this.showPage.bindScope(this));
	},
	
	/**
	 * function showPage()
	 * Copies the received HTML and text in the editors. Expects an JSON formated string(!) or object containing the data.
	 * 
	 * @param {string} From Ajax request received string.
	 * @return void
	 */
	showPage: function(response) {

		if (typeof response == 'string') {
			response = jQuery.parseJSON(response);
		}

		// 1. HTML
		jQuery('#newsletterHTML').val(response.newsletterHTML);

		// 2. Textinhalte
		jQuery('#newsletterText').val(response.newsletterText);
		
		//tinyMCE Update
		tinymce.get('newsletterHTML').setContent(response.newsletterHTML);

		jQuery('#newsletterPageID').val(this.pageID);
		
		this.paperboyLazy();
	},
	
	/**
	 * function sendNewsletter()
	 * Starts an newsletter delivery interval via Ajax request.
	 * 
	 * @param void
	 * @return void
	 */
	sendNewsletter: function(data) {
		
		var params = {
			cmtAction: 'sendNewsletter'
		};
		
		if (typeof data == 'object') {
			jQuery.extend(params, data);
		};
		
		this.initDeliveryObserver();
	
		jQuery.ajax({
			url: this.ajaxURL,
			data: params,
			type: 'POST'
		})
		.done(this._sendNewsletterResponse.bindScope(this));
		
	},

	/**
	 * function sendNewsletter()
	 * Proceeds newsletter delivery after an Ajax response.
	 * 
	 * @param void
	 * @return void
	 */
	_sendNewsletterResponse: function(response) {
		
		if (typeof response == 'string') {
			try {
				response = jQuery.parseJSON(response);
			} catch(error) {
				this.resumeDelivery();
				return;
			}
		}
		
		if (typeof response.proceedDelivery == 'undefined' || (!response.proceedDelivery && !response.deliveryComplete)) {
			this.resumeDelivery();
			return;
		}
		
		this.deliveryData = response;
		this.showDeliveryData();
		
		// update progress bar width
		jQuery('.bar', '#progressBar').css({
			width: response.currentPercent + '%'
		});
		
		jQuery( "#progressBar" ).progressbar({
			 value: response.currentPercent
		 });
		
		if (!response.deliveryComplete) {
			this.sendNewsletter({
				newsletterDeliveryCurrentPos: response.nextPosition
			});
		} else {
			this.deliveryComplete(response.currentPosition);
		}
		
	},

	/**
	 * function setAjayURL()
	 * SETTER: Set URL for Ajax reuqests
	 * 
	 * @param void
	 * @return void
	 */
	setAjaxURL: function(url) {
		this.ajaxURL = url;
	},
	
	/**
	 * function initDeliveryObserver()
	 * Inits the timeout for newsletter delivery observation.
	 * 
	 * @param void
	 * @return void
	 */
	initDeliveryObserver: function() {
		this.clearDeliveryObserver();
		this.deliveryObserver = setTimeout(this.resumeDelivery.bindScope(this), (parseInt(this.deliveryTimeOut) * 1000 + parseInt(this.deliveryTimeOutAdd) * 1000));
	},

	/**
	 * function clearDeliveryObserver()
	 * Deletes the timeout for newsletter delivery observation.
	 * 
	 * @param void
	 * @return void
	 */
	clearDeliveryObserver: function() {
		clearTimeout(this.deliveryObserver);
	},
	
	/**
	 * function resumeDelivery()
	 * Restarts the newsletter delivery at aborted position.
	 * 
	 * @param void
	 * @return void
	 */
	resumeDelivery: function() {
		
		this.clearDeliveryObserver();
		var co = confirm('Der Server hat falschen Daten geliefert oder antwortet nicht mehr. Der Newsletterversand scheint abgebrochen zu sein. Soll er an der abgebrochenen Stelle wiederaufgenommen werden?');
		
		if (co) {
			this.sendNewsletter({
				resumeDelivery: 1
			});
		} else {
			this.abortDelivery();
		}
	},

	/**
	 * function abortDelivery()
	 * Aborts the newsletter delivery (after user's choice)
	 * TODO: Auch in PHP Script Abbruch melden!? 2 Kn�pfe: Wiederaufnahme und Neuversand
	 * 
	 * @param void
	 * @return void
	 */
	abortDelivery: function() {
		this.clearDeliveryObserver();
		this.showDeliveryData();

	},

	/**
	 * function deliveryComplete()
	 * Delivery completed, shows summary.
	 * TODO: Ausgabe der Informationen
	 * 
	 * @param void
	 * @return void
	 */
	deliveryComplete: function() {
		
		if (!this.deliveryData.deliveryErrors) {
			jQuery('#progressBar').addClass('cmtSuccess');
		} else {
			jQuery('#progressBar').addClass('cmtError');
		}
		
		this.clearDeliveryObserver();
		this.showDeliveryData();
	},

	/**
	 * function showDeliveryData()
	 * Displays information about the delivery process.
	 * 
	 * @param void
	 * @return void
	 */
	showDeliveryData: function() {
		
		jQuery('#startDelivery').button('option', 'disabled', false);
		
		jQuery('#totalNewsletters').text(this.deliveryData.totalNewsletters);
		jQuery('#newslettersSent').text(this.deliveryData.deliverySuccessful);
		jQuery('#newsletterErrors').text(this.deliveryData.deliveryErrors);
		jQuery('#deliveryInterval').text(this.deliveryData.deliveryInterval);
	},
	
	/**
	 * function setVar()
	 * Sets a variable for cmtPaperboy
	 * 
	 * @param {String} varName name of the variable
	 * @param {Mixed} value Variable's value
	 * 
	 * @returns {Boolean}
	 */
	setVar: function(varName, value) {
		
		if (typeof this[varName] != 'undefined' && typeof this[varName] != 'function') {
			this[varName] = value;
			return true;
		}
		return false;

	},
	
	paperboyWorking: function() {
		jQuery('body').addClass('cmtWorking');
	},
	
	paperboyLazy: function() {
		jQuery('body').removeClass('cmtWorking');
	}
};

cmtPaperboy.initialize();
