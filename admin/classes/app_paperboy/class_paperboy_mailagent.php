<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

 	require ('../../cmt_constants.inc');
	require (PATHTOADMIN."cmt_functions.inc");
    include (PATHTOADMIN."classes/class_dbcex.php");
	include (PATHTOADMIN."classes/class_parser.php");
    include (PATHTOADMIN."classes/class_user.php");
	include (PATHTOADMIN."classes/class_table.php");
	include (PATHTOADMIN."classes/class_form.php");
	include(PATHTOADMIN.'classes/class_mimemailer.php');

// Class paberboy_mailagent{
class paberboy_mailagent{
	protected $debugMode;
	protected $subscribersList=array();
	protected $date;
	protected $tables = array();
	protected $activenewsletters=array();
	protected $news = array();
	protected $condOrderBy = "online_date desc";
	protected $newsletterTemplates = array();
	
	public $daysAgo = 1;
	protected $format = 'both'; // text, html or both
	protected $limit = 5;
	protected $log = array();
	public function getLog(){
		return $this->log;
	}

	/**
	 * Konstruktor
	 */
	public function __construct($debugmode=false) {
		$this->debugMode=$debugmode;
		$this->db = new DBCex();
		$this->date = date('Y-m-d H:i:s');
		//Debug mode log
		if ($this->debugMode) $this->log['date']=$this->date;
		$this->getActiveNewsLetters();
		foreach ($this->activenewsletters as $activeNewsletter){
			$fromDate = ($activeNewsletter['days_ago'])? $activeNewsletter['days_ago']:$this->daysAgo;
			$fromDateF = date ( 'Y-m-d H:i:s' , strtotime ( '-'.$fromDate.' day' , strtotime ( $this->date )) );
			$format =($activeNewsletter['format'])?  $activeNewsletter['format']: $this->format;
			$limit = ($activeNewsletter['search_limit'])? $activeNewsletter['search_limit']:$this->limit;
			$nlid= $activeNewsletter['newsletter_id'];
			$this->getTables($activeNewsletter['tables_wrapper']);
			//Debug mode log
			if ($this->debugMode) $this->log['tables']=$this->tables;
			$this->getNewsLetterSubscribers($fromDate,$nlid);
			$this->getNewsletterTemplates($nlid);
			//Debug mode log
			if ($this->debugMode) $this->log['templates'] = $this->newsletterTemplates;
			$this->getNews($fromDateF,$nlid,$limit='100');
			//Debug mode log
			if ($this->debugMode) $this->log['news']=$this->news;
			$this->sendNewsletter($format);
		}
	}

	/**
	 * protected function getActiveNewsLetters()
	 *
	 * @param void
	 * @return active newsletters in an array
	 */
	protected function getActiveNewsLetters(){
		$res = array();
		$query = 	"SELECT * FROM paperboy_newsletters INNER JOIN paperboy_mailagent_settings
					ON paperboy_newsletters.id = paperboy_mailagent_settings.newsletter_id
					WHERE paperboy_mailagent_settings.active=1";
		$this->db->Query($query);
		while ($r=$this->db->Get(MYSQLI_ASSOC))	$this->activenewsletters[$r['id']] = $r;
	}

	/**
	 * protected function getNewsLetterSubscribers()
	 *
	 * @param $newsletterId
	 * @return Subscriber of active newsletter
	 */
	 protected function getNewsLetterSubscribers($fromDate,$newsletterId){
	 	if (!$newsletterId) return;
		$this->subscribersList = array();
		$fromDateF = date ( 'Y-m-d H:i:s' , strtotime ( '-'.$fromDate.' day + 4 hours' , strtotime ( $this->date )) );
		$query = "SELECT paperboy_subscribers.id, email, firstname, surname,newsletter_id,pages,paperboy_distributed.last_Mailagent_newsletter as lastsend FROM paperboy_subscribers
				 INNER JOIN paperboy_distributed
				 ON paperboy_distributed.subscriber_id = paperboy_subscribers.id
				 WHERE paperboy_distributed.newsletter_id =".$newsletterId." 
				 AND paperboy_distributed.is_mailagent_active='1' 
				 AND (paperboy_distributed.last_Mailagent_newsletter IS null OR paperboy_distributed.last_Mailagent_newsletter <'".$fromDateF."')  
				 AND paperboy_subscribers.is_active='1' 
				 AND paperboy_distributed.pages <> '' ";
		$this->db->Query($query);
		//Debug mode log
		if ($this->debugMode) $this->log['subscribers_query']=$query;
		while ($r=$this->db->Get(MYSQLI_ASSOC)){
		 	if ($r['pages'])	$r['pages'] = $this->getSubscriberTables(safeUnSerialize($r['pages']));
		 	$this->subscribersList[] = $r;
		 }	
	}
	 	
	   public function getSubscriberTables($pages) {
	   	if ($pages) {
	   		return $pages;
	 	 	}
	 	 	return false;
	 	}
	
	/**
	 * protected function getNewsletterTemplates()
	 *
	 * @param $nlid [newsletter id]
	 * @return get newsletter template from paperboy_templates table
	 */
	 protected function getNewsletterTemplates($nlid){
		$query = "SELECT * FROM paperboy_templates where template_linkwithnewsletter='".$nlid."'";
		$this->db->Query($query);
		$this->newsletterTemplates[$nlid]=$r=$this->db->Get(MYSQLI_ASSOC);
	}

	/**
	 * protected function getTables()
	 *
	 * @param $wrappers
	 * @return get tables names, id and aliases from mailagent_settings wrapper
	 */
	protected function getTables($wrappers){
		$tables = explode ('|',$wrappers);
		foreach ($tables as $table){
			if (!$table) continue;
			$data = explode (';',$table);
			$filds = (trim($data[3]))?trim($data[3]):'';
			$this->tables[trim($data[0])] = array("table"=>trim($data[0]),"table_name"=>trim($data[1]),"table_id"=>trim($data[2]), "fields"=>$filds);
		}
	}

	/**
	 * protected function getNews()
	 *
	 * @param void
	 * @return get all news since (datefrom) date for all tables in $this->tables
	 */
	 protected function getNews($fromDate,$nlid,$limit=500){
	 	
		foreach  ($this->tables as $table){
			if (!$table) continue;
			$query = "SELECT id,title,online_date".$table['fields']." from ".$table['table']." 
			WHERE online_date >='".$fromDate."' 
			AND status = '3' 
			AND online_date <= '".$this->date."' 
			AND (offline_date >= '".$this->date."' OR offline_date = '0000-00-00 00:00:00') 
			ORDER BY ".$this->condOrderBy." LIMIT ".$limit;
			//print $query;
			$this->db->Query($query);
			//Debug mode log
			if ($this->debugMode) $this->log['tables_query'][$table['table'].'_query']=$query;
			unset($aktnews);
			while ($r=$this->db->Get(MYSQLI_ASSOC))	$aktnews[] = $r;
			unset($r);
			$this->news[$nlid][$table['table']]=$aktnews;
		}
	}

	/**
	 * protected function sendNewsletter()
	 *
	 * @param void
	 * @return main function of generating newsletter content and sending it
	 */
	protected function sendNewsletter($format){
		//Debug mode log
		if ($this->debugMode) $this->log['all_subscribers']=count($this->subscribersList);
		$mimemailer = new mimemailer();
		$mimemailer->eol = "\n";
		$parser = new Parser();
		foreach ($this->subscribersList as $subscriber){
			$nlid = $subscriber['newsletter_id'];
			if (!$body = $this->getRubrikNews($subscriber['pages'],$nlid)) continue;
			$newsletterHtml = $this->addHtmlBody($nlid, $body['html'], $this->newsletterTemplates[$nlid]['template_name']);
			$newsletterText = $this->addTextBody($nlid, $body['text'], $this->newsletterTemplates[$nlid]['template_name']);
			if ($format != 'text' && $format != 'both') $newsletterText = '';
			if ($format != 'html' && $format != 'both') $newsletterHtml = '';
			$newsletterSubject = $this->newsletterTemplates[$nlid]['template_subject'];
			$newsletterSenderMail=$this->newsletterTemplates[$nlid]['template_sendermail'];
			$newsletterSenderName=$this->newsletterTemplates[$nlid]['template_sendername'];
			$newsletterReplyTo=$this->newsletterTemplates[$nlid]['template_sendermail'];
			#$attachedFiles=false;
			#$addParams = '';
			$mimemailer->createMail(array (	
											'html' => $newsletterHtml,
											'text' => $newsletterText,
											'attachments' => $attachedFiles,
											'senderMail' => $newsletterSenderMail,
											'senderName' => $newsletterSenderName,
											'replyTo' => $newsletterReplyTo
											)
										);
			
			//Debug mode log
			 if ($this->debugMode){
				$this->log ['send'][$subscriber['id']] = 'OK';
				$this->log ['send'][$subscriber['id']] = 'OK';
				$this->log ['send'][$subscriber['id']] = $mimemailer->mailBody;
			 }
			 else
			 {
				$check = @mail($subscriber['email'], $newsletterSubject, $mimemailer->mailBody, $mimemailer->mailHeader); 
				//no email sended
				
				if (!$check) {
					//print_r($subscriber);
					//print $mimemailer->mailBody;
					$this->addLog($subscriber['id'],$nlid,$this->date,$subscriber['email'],$this->date);
				}
				//sucsessufly email sending
				else {
					//print $mimemailer->mailBody;
					$this->updateUserLastNewsletter($subscriber['id'],$nlid);	
				}
			 }
		}
	}

	protected function updateUserLastNewsletter($subscriberId,$newsLetterId){
		$query = "UPDATE paperboy_distributed SET last_Mailagent_newsletter='".date('Y-m-d H:i:s')."' WHERE subscriber_id='".$subscriberId."' AND newsletter_id='".$newsLetterId."'";
		$this->db->Query($query);
	}
	
	/**
	 * protected function getRubrikNews()
	 *
	 * @param $page $nlid
	 * @return generate newsletter content of all news of subscribed tables for current subscriber
	 */
	protected function getRubrikNews($page,$nlid){
		$tables = $page; #explode (';',$page);
		$rowHtmlTemplate = $this->newsletterTemplates[$nlid]['template_mailagent_html_row'];
		$rowTextTemplate = $this->newsletterTemplates[$nlid]['template_mailagent_text_row'];
		$htmlDocumentContent = '';
		$textDocumentContent = '';

		foreach($tables as $key=>$table){
			$body = $this->getRowNews($key,$table,$rowHtmlTemplate,$rowTextTemplate,$nlid);
			if (!$body) continue;
			$htmlDocumentContent .= join ('',$body['html']);
			$textDocumentContent .= join ('',$body['text']);
		}
		if (!$textDocumentContent) return false;
		return array('html'=>$htmlDocumentContent,'text'=>$textDocumentContent);
	}

	/**
	 * protected function  getRowNews()
	 *
	 * @param $table,$rowTemplate,$nlid
	 * @return generate all news rows or one table and return them as an array
	 */
	protected function getRowNews($key,$table, $htmlTemplate,$textTemplate,$nlid){
		$lmt = 0;
		$body = array('html'=>array(),'text'=>array());
		if (is_array($table)){
			$topics = $table;
			if (!$this->news[$nlid][trim($key)]) return; // to down
		}
		else
		{
			$topics='';
			if (!$this->news[$nlid][trim($table)]) return; // delete
		} 

		foreach ($this->news[$nlid][trim($key)] as $newsItem){
			$topic='';
			if ($topics) {
				if (!in_array($newsItem['topics'], $topics) && $newsItem['topics']) continue;
				$topic = '|'.$newsItem['topics'];
			}
			if ($lmt >= $this->limit) continue;
			$lmt++;
			$datede = date ( 'd.m.Y' , strtotime ($newsItem['online_date']  ));
			$hrow = str_replace('{DATE}', $datede, $htmlTemplate);
			$hrow = str_replace('{RUBRIK}', $this->tables[$key]['table_name'].$topic, $hrow);
			$hrow = str_replace('{TITLE}', $newsItem['title'], $hrow);
			$hrow = str_replace('{LINK}', $this->getArticleLink ($this->tables[$key],$nlid,$newsItem['title'],$newsItem['id'],'html'), $hrow);
			$trow = str_replace('{DATE}', $datede, $textTemplate);
			$trow = str_replace('{RUBRIK}', $this->tables[$key]['table_name'].$topic, $trow);
			$trow = str_replace('{TITLE}', $newsItem['title'], $trow);
			$trow = str_replace('{LINK}', $this->getArticleLink ($this->tables[$key],$nlid,$newsItem['title'],$newsItem['id'],'text'), $trow);
			$trow = ereg_replace ("/\r\n|\n\r|\r|\n/", " ", $trow);
			$trow .="\r\n";
			$body['html'][] = $hrow;
			$body['text'][] = $trow;
		}
		return $body;
	}

	/**
	 * protected function  getArticleLink()
	 *
	 * @param $table,$nlid,$title,$aid
	 * @return generate an article link
	 */
	protected function getArticleLink($table,$nlid,$title,$aid,$type){
		$tmp = $this->newsletterTemplates[$nlid]['template_homepage_link'];
		$url = str_replace('{HOMEPAGE}', $this->activenewsletters[$nlid]['homepage'], $tmp);
		$url = str_replace('{LANGUAGE}', $this->activenewsletters[$nlid]['language'], $url);
		$url = str_replace('{SUBJECT}', $table['table_id'], $url);
		#$url = str_replace('{PAGE}', $this->makeNameWebSave($title).'.html', $url);
		$url = str_replace('{PAGE}', 'newsletter_'.$table['table'].'.html', $url);
		$url = str_replace('{AID}', $aid, $url);
		if ($type=='text')	$link = '<'.$url.'>';
		else $link = '<a href="'.$url.'">'.$url.'</a>';
		return $link;
	}

	/**
	 * protected function  addBody()
	 *
	 * @param $template, $htmlBody, $htmlTitle=''
	 * @return get newsletter content template
	 */
	protected function addHtmlBody($template, $htmlBody, $htmlTitle='') {
		$htmlBody = utf8_decode($htmlBody);
		if (!empty ($template)){
				$this->db->Query("SELECT template_html FROM paperboy_templates WHERE id='$template'");
				$r = $this->db->Get();
				$htmlFrame = utf8_decode(stripslashes(trim($r['template_html'])));
				if ($htmlFrame) {
					$htmlFrame = str_replace('{HTML_DOCUMENT_TITLE}', $htmlTitle, $htmlFrame);
					return str_replace('{HTML_DOCUMENT_BODY}', $htmlBody, $htmlFrame);
				} else 	return '<html><head><title>'.$htmlTitle.'</title></head><body>'.$htmlBody.'</body></html>';
			}
		else return '<html><head><title>'.$htmlTitle.'</title></head><body>'.$htmlBody.'</body></html>';
		}

	/**
	 * protected function  addTextBody()
	 *
	 * @param $template, $htmlBody, $htmlTitle=''
	 * @return get newsletter content template
	 */
	protected function addTextBody($template, $textBody, $textTitle='') {
		$textBody = utf8_decode($textBody);
		if (!empty ($template)){
				$this->db->Query("SELECT template_text FROM paperboy_templates WHERE id='$template'");
				$r = $this->db->Get();
				$textFrame = utf8_decode(stripslashes(trim($r['template_text'])));
				if ($textFrame) {
					$textFrame = str_replace('{HTML_DOCUMENT_TITLE}', $textTitle, $textFrame);
					return str_replace('{HTML_DOCUMENT_BODY}', $textBody, $textFrame);
				} else 	return $textTitle.'\n\n'.$textBody.'\n\n';
			}
		else return '\n\n'.$textTitle.'\n\n'.$textBody.'\n\n';
		}

	/**
	 * protected function  addLog()
	 *
	 * @param void
	 * @return handling error logs
	 */	 
	 protected function addLog($subId,$nlId,$date,$subEmail,$tdate){
		 $query = "INSERT INTO paperboy_errorlog SET subscriber_id='".$subId."', newsletter_id='".$nlId."', error_datetime ='".$date."', error_email='".$subEmail."', transmission_datetime='".$tdate."'";
		 $this->db->Query($query);
	 }
}

?>
