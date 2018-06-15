<?php
/**
 * Fileselector
 * Hilfsapplikation zum Hochladen, Anzeigen und Ausw�hlen von Dateien und Ordnern
 * 
 * @version 2007-0329
 * @author J.Hahn <info@contentomat.de>
 * 
 */

// Objekte
$db = new DBCex();
$parser = new Parser();
$form=new Form();
$file = new Filehandler();
$session = new Session();
$serializedSelectedRelationList='';
$countSelectedRelationList=0;

//cmt Action
 if (trim($_REQUEST['searchform'])){
	$cmtAction = 'search'; 
  }
  if (trim($_REQUEST['select'])){
	$cmtAction = 'select'; 
  }
  if (trim($_REQUEST['deselect'])){
	$cmtAction = 'deselect'; 
  }
  if (trim($_REQUEST['submitForm'])){
	$cmtAction = 'submitForm'; 
  }
  
   // GET-Variablen
 $getVars = array ('launch'=> 0,	'action' => '', 'cmt_field' => '','my_id'=>'');
 foreach ($getVars as $varName => $defaultValue) {
 	if (trim($_GET[$varName]) != '') {
 		$getVars[$varName] = trim($_GET[$varName]);
 	}
 }
 extract ($getVars);
 
// sonstige Variablen
$templatePath = CMT_TEMPLATE.'general/appinc_relationselector/';
$incPath = '../';

// Felddaten holen, z.B. Root
$db->Query('SELECT cmt_tablename FROM cmt_tables WHERE id =  "'.intval($launch).'"');
$r = $db->Get(MYSQLI_ASSOC);
$cmtTable = $r['cmt_tablename'];

$db->Query('SELECT cmt_options FROM cmt_fields WHERE cmt_tablename = "'.$cmtTable.'" AND cmt_fieldname = "'.$cmt_field.'"');
$r = $db->Get(MYSQLI_ASSOC);

$cmtOptions = safeUnserialize($r['cmt_options']);

// Update cmt_Options, add id to table array.
// Generate Select field options to limit search in one table only
$dropDownAliases=array('Alle');
$dropDownValues=array('');
if (is_array($cmtOptions['from_table'])){
	foreach($cmtOptions['from_table'] as $key => $relationData){
		$db->query("SELECT * FROM cmt_tables WHERE cmt_tablename =  '".$relationData['name']."' ");
		$r = $db->Get();
		//If no table selected in create field form
		if ($r['id'] == 1){
			continue;
		}
		$cmtOptions['from_table'][$key]['id']= $r['id'];
		//update vaiables to view dropdown list of allowed tables
		$dropDownValues[]=$r['cmt_tablename'];
		$dropDownAliases[]=$r['cmt_showname'];
	}
} 

// Funktionen

/** function getSelected()
 * get currintly selected relations items data, and update its list if any addes or deleted
 * save selected items list as serilized string in session after modifing 
 * 
 * @param $relTables array of allowed tables with detail about it (table id, table name ...)
 * @param $mode	there are three mode to call this function(default: get items only, 
 * 				select: add new items, deselect: delete items) from selected items list
 * @return array() list of selected relations with all related details 
 */ 
 function getSelected($relTables,$mode=false){
 	Global $session;
 	Global $serializedSelectedRelationList;
 	Global $countSelectedRelationList;
 	
 	$db = new DBCex();
 	
 	//get relations list saved in session
 	$relationsList=array();
 	$relations = safeUnserialize($session->GetSessionVar($_REQUEST['rel']));
 	
 	//if no relation selected, create new empty array 
 	if (!is_array($relations)){
 		$relations = array();
 	}
 	
 	//modify relation list according to mode 
 	switch($mode){
 		//add new selected items to list
 		case 'select':
	 		$addRelations = getAddedRelations();
	 		$relations = array_merge($relations,$addRelations);
 			break;
 		//delete items from list
 		case 'deselect':
	 		$deleteRelations = getDeletedRelations();
	 		foreach($deleteRelations as $delItem){
	 			foreach ($relations as $key=>$Item){
	 				if ($delItem == $Item){
	 					unset($relations[$key]);
	 				}
	 			}
	 		}
 			break;
 	}
	$newRelations=array();
 	if (!empty($relations)){
 		// get relations data from database
 		
 		foreach ($relations as $article){
 			//assign relation table id as current table id
 			$relationTableId=$article[0];
 			
 			//assign relation article id  as current article id
 			$relationArticleId=$article[1];
 			
 			// get table info for current article
 			$table=array();
 			
 			foreach($relTables as $relTable){
 				if ($relationTableId == $relTable['id']){
 					$table = $relTable;	
 				}
 			}
 			
 			//if relation table id is not in allowd tables list, go to next relation
 			if (!$table) {
 				continue;
 			}

 			$query = " SELECT '".$table['name']."' as tableName , '".$table['id']."' as tableId, ";
 			$query .= $table['alias_field']." as title, ".$table['value_field']." as id";
 			$query .= " FROM ".$table['name'];
 			$query .= " WHERE id = '".$relationArticleId."' ";
 			$db->query($query);
 			$r = $db->get();
 			if ($r){
 				$relationsList[] = $r;	
 				$newRelations[] = $article;
 			}
 		}
 	}
 	 
 	
 	//assign new list to global variable, using this to return value with js to opener window
 	$serializedSelectedRelationList = safeSerialize($newRelations);
 	
 	//assign the nummber of relations in list to variable, using this to return value with js to opener window
 	$countSelectedRelationList = count($newRelations);
 	
 	//save modified list to session again
 	$session->SetSessionVar($_REQUEST['rel'],$serializedSelectedRelationList);
 	$session->SaveSessionVars();
 	
 	return $relationsList;
 }

/**
 * function search()
 * 
 * search in selected tables for related articles or media files
 * 
 * @ToDo smart limit search		 
 * @param $relTables array of allowed tables with detail about it (table id, table name ...)
 * @param $searchText	string,
 * @return array() list of funded relations with its related details
 */
 function search($relTables,$launch,$searchText='',$myid=''){
 	Global $serializedSelectedRelationList;

 	if(!$relTables || empty($relTables)) return array();
 	
 	//show last 5 items from tables when no searchText
 	$limit = 5;
 	
 	//search 50 items in search mode, you can change the result order by assigning
 	//(add_sql) parameter for this field 
	$maxLimit=50;
	
 	$db = new DBCex();
	
 	//get a list of current selected relations
 	$relations = safeUnserialize($serializedSelectedRelationList);
 	
 	//if no relation selected, create new empty array
 	if (!is_array($relations)){
 		$relations = array();
 	}
 	
 	$queryTable=array();
 	foreach($relTables as $key => $table){

 		//if table have no name (application) go to next table
 		if (!$table['id'] || !$table['name']){
 			continue;
 		}
 		
 		// if a table name selected in dropdown field, search only in this table
 		if ($_REQUEST['ddf'] && $_REQUEST['ddf'] != $table['name']){
 			continue;
 		}
 		$query = "(SELECT '".$table['name']."' as tableName , '".$table['id']."' as tableId, ";
 		$query .= $table['alias_field']." as title, ".$table['value_field']." as id";
 		$query .= " FROM ".$table['name'];
 		
 		//if user give search text
 		if ($searchText){
 			$andQuery []= $table['alias_field']." LIKE '%".$db->mysqlQuote($searchText)."%'";
 		}
 		
 		if($_REQUEST['ddf']){
 			$limit=$maxLimit;
 		}
 		
 		if ($andQuery){
 			$query .= " WHERE ".join(" AND ",$andQuery);
 		}
 		
 		if ($table['add_sql']){
 			$query .= " ".$table['add_sql'];
 		}
 		
 		$query .= " LIMIT ".$limit;
 		
 		$query .= " )";
 		$queryTable [] = $query;
 	}
 	$query = join(" UNION ", $queryTable);
 	#print($query);
 	$db->query($query);
 	$res= array();
 	while ($r = $db->get()){
 		$valueDupplicated = false;
 		
 		//dont allow selected relation to appear in search result list 
 		foreach($relations as $item){
 			if ($item[0]== $r['tableId'] && $item[1]==$r['id']){
 				$valueDupplicated = true;
 			}
 		}
 		// dont allow current article to appear in search result list
 		if ($myid && ($launch == $r['tableId'] && $myid ==$r['id'])){
 			$valueDupplicated = true;
 		}
 		
 		//if item not in selected list, add to search result array
 		if (!$valueDupplicated) {
 			$res[]=$r;
 		}
 	}
 	return $res;
 }
 
 /**
  *function  getAddedRelations()
  *
  * @return array(), list of new relation to add to selected relations list
  */
function getAddedRelations(){
	$newRelations = array();
	foreach ($_POST as $varName => $varValue) {
 			if (strstr($varName, 'add_') && $varValue == 'on') {
  				$varParts = explode('_', $varName);
 				$newRelations[]=array($varParts[1],$varParts[2]);
 			}
 		}
	return $newRelations;
} 	  

/**
 * function getDeletedRelations()
 * @return array(), list of selected relations, to delete them from selected relations list
 */
function getDeletedRelations(){
	$deleteRelations = array();
	foreach ($_POST as $varName => $varValue) {
 			if (strstr($varName, 'del_') && $varValue == 'on') {
  				$varParts = explode('_', $varName);
 				$deleteRelations[]=array($varParts[1],$varParts[2]);
 			}
 		}
	return $deleteRelations;
}

// End Funktionen	
	
	
/*
 * Hauptteil: Aktionen
 */
switch ($cmtAction) {
 	// Verzechnisinhalt anzeigen
 	case 'search':
 		$selectedRelations = getSelected($cmtOptions['from_table']);
 		break;
 	case 'select':
	 	$selectedRelations = getSelected($cmtOptions['from_table'],'select');
	 	break;
 	case 'deselect':
 		$selectedRelations = getSelected($cmtOptions['from_table'],'deselect');
 		break;
 	default:
 		// Send search paramerters, and get results
		$selectedRelations = getSelected($cmtOptions['from_table']);
 		break;
}

$result = search($cmtOptions['from_table'],$launch,$_REQUEST['searchText'],$my_id);

//create html content for items in search box	
if ($result){
	$searchContentResults = '';
	foreach($result as $item){
		$parser->setMultipleParserVars($item);
		$searchContentResults .= $parser->parseTemplate($templatePath.'cmt_relationselector_search_resultes_items_row.tpl');
	}	
}

//create html content for currintly selected items in  selected relations box
if ($selectedRelations){
	$searchContentSelectedItems = '';
	foreach($selectedRelations as $item){
		$parser->setMultipleParserVars($item);
		$searchContentSelectedItems .= $parser->parseTemplate($templatePath.'cmt_relationselector_selected_items_row.tpl');
	}	
}

//DropDown select Field to select searching in allowed tables list
$dropDownField = $form->select( 
							// ddf = drop down field
							array ('name' => 'ddf',
									'id' =>'ddf',
									'optionsOnly' => false,
									'values' => $dropDownValues,
									'aliases' => $dropDownAliases,
									'selected' => $_REQUEST['ddf'],
								));

//ToDo change order of selected relations
//ToDo Reset values button	
								
//assign parser variables 								
$parser->setParserVar('searchContentResults',$searchContentResults);
$parser->setParserVar('searchContentSelectedItems',$searchContentSelectedItems);
$parser->setParserVar('dropDownField',$dropDownField);
$parser->setParserVar('fieldName',$cmt_field);
$parser->setParserVar('fieldValue',htmlentities($serializedSelectedRelationList));
$parser->setParserVar('countFieldName','count'.$cmt_field);
$parser->setParserVar('countFieldValue',$countSelectedRelationList);

//show html content
$html = $parser->parseTemplate($templatePath.'cmt_relationselector_add_html.tpl');
$html .= $parser->parseTemplate($templatePath.'cmt_relationselector.tpl'); 
echo $html;
exit;
 
?>
