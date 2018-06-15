<?php
/**
 * class_tags.inc
 * Class provides methods to handle tags in blog system MLog
 * 
 * @author A.Alkaissi, J.Hahn
 * @version 2016-09-21
 * @package MLog
 */
namespace Contentomat\MLog;
use Contentomat\DBCex;

Class Tags {

	protected $db;
	private $tagsTable = 'mlog_tags';
	private $postsTable = 'mlog_posts';
	protected $tagSeparator = ',';
	private $tagsArray = array();
	protected $removeTags = array();
	protected $limit;
	protected $date;

	public function __construct() {
		$this->db = new DBCex();
		$this->limit = 20;
		$this->date = date('Y-m-d H:i:s');
	}

	/**
	 * public function setRemoveTag()
	 * 
	 * add a tag to the list of removed tags
	 * 
	 * @param string $tag 
	 */
	public function setRemoveTag($tag) {
		$this->removeTags[] = $this->trimTags($tag);
	}

	/**
	 * public function setRemoveTags()
	 * 
	 * add a set of tags to the list of removed tags
	 * 
	 * @param array $tags 
	 */
	public function setRemoveTags($tags) {
		foreach ($tags as $tag) {
			$this->setRemoveTag($tag);
		}
	}

	/**
	 * public function getRemoveTags()
	 * 
	 * return a list of tags to remove them from final tags cloud
	 * 
	 * @return array 
	 */
	public function getRemoveTags() {
		return $this->removeTags;
	}

	/**
	 * public function setLimit()
	 * 
	 * set the number of tags to be appear in tags cloud
	 * 
	 * @param int $limit 
	 */
	public function setLimit($limit) {
		$this->limit = $limit;
	}

	/**
	 * public function getLimit()
	 * 
	 * get the number of tags to be appear in tags cloud
	 * 
	 * @return int
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * protected function getMax()
	 * 
	 * get the amount of biggst tag amount in tags list
	 * 
	 * @return int 
	 */
	protected function getMax() {

		$max = 0;

		if (!empty($this->tagsArray)) {

			$preAmount = 0;

			foreach ($this->tagsArray as $key => $value) {

				$currentAmount = $value['amount'];
				if ($currentAmount > $preAmount) {
					$max = $currentAmount;
					$preAmount = $currentAmount;
				}
			}
		}
		return $max;
	}

	/**
	 * protected function getMax()
	 * 
	 * get the amount of smallest tag amount in tags list
	 * 
	 * @return int 
	 */
	protected function getMin() {

		$min = 1;

		if (!empty($this->tagsArray)) {

			$preAmount = 999999;

			foreach ($this->tagsArray as $key => $value) {
				$currentAmount = $value['amount'];
				if ($currentAmount < $preAmount) {
					$min = $currentAmount;
					$preAmount = $currentAmount;
				}
			}
		}
		return $min;
	}

	/**
	 * public function getTagsCloud()
	 * 
	 * 
	 * @param array $params
	 * 
	 * @return array 
	 */
	public function getTagsCloud($params = array()) {

		$this->tagsArray = $this->getTagsList($params);

		$this->cleanupTags($params);

		$this->renderTags($params);

		return $this->tagsArray;
	}

	/**
	 * protected function getTagsList()
	 * 
	 * get the list of all tags in tags table to output, order them by tag amount
	 * 
	 * @return array 
	 */
	protected function getTagsList($params=array()) {

		$defaultParams = array(
			'orderBy' => 'mlog_tags.tag_amount DESC',
		);

		$params = array_merge($defaultParams, $params);

		$query = "SELECT mlog_tags.tag_name as tag,mlog_tags.tag_amount as amount FROM mlog_tags";
		if($params['categoryId']){
			$query .= " JOIN mlog_posts ON(find_in_set(mlog_tags.tag_name,replace(mlog_posts.post_tags,', ',',')) AND find_in_set(".$params['categoryId'].",post_category))";
		}
		$query .= " WHERE mlog_posts.post_online_date <= '".$this->date."'";
        $query .= " AND (mlog_posts.post_offline_date >= '".$this->date."'";
        $query .= " OR mlog_posts.post_offline_date = '0000-00-00 00:00:00')";
		$query .=" GROUP BY mlog_tags.tag_name";
		$query .=" ORDER BY ".$params['orderBy'];
		$this->db->query($query);

		while ($tag = $this->db->get()) {

			$tags[] = $tag;
		}

		return $tags;
	}

	/**
	 * protected function cleanupTags()
	 * 
	 * if remove tags defined, delete them from list of tags to appear.
	 * limit the nummber of tags in tags cloude.
	 * 
	 * return void 
	 */
	protected function cleanupTags($params = array()) {

		$helpTagsArray = array();

		if ($params['limit']) {
			$this->setLimit(intval($params['limit']));
		}

		$limit = $this->getLimit();

		$i = 0;

		foreach ($this->tagsArray as $key => $value) {
			if (!in_array($value['tag'], $this->getRemoveTags()) && $i < $limit) {
				$helpTagsArray[$value['tag']] = $value;
				$i++;
			}
		}

		$this->tagsArray = array();
		$this->tagsArray = $helpTagsArray;
	}

	/**
	 * protected function renderTags()
	 * 
	 * @param array $params 
	 * 
	 * @return void
	 */
	protected function renderTags($params = array()) {

		$helpTagsArray = array();

		$defaultParams = array(
			'minAmount' => $this->getMin(),
			'maxAmount' => $this->getMax(),
			'minSize' => 1,
			'maxSize' => 10,
			'shuffle' => true,
		);

		$params = array_merge($defaultParams, $params);

		foreach ($this->tagsArray as $key => $value) {

			$value['fontSize'] = $this->tagFontsizeDistribution($value['amount'], $params['minAmount'], $params['maxAmount'], $params['minSize'], $params['maxSize']);
			$helpTagsArray[$value['tag']] = $value;
		}

		$this->tagsArray = array();

		$this->tagsArray = $helpTagsArray;

		if ($params['shuffle']) {
			$this->tagsShuffle();
		}
	}

	/**
	 * protected  function tagFontsizeDistribution()
	 * 
	 * @param type $amount
	 * @param type $minAmount
	 * @param type $maxAmount
	 * @param type $minSize
	 * @param type $maxSize
	 * @return type 
	 */
	protected function tagFontsizeDistribution($amount, $minAmount, $maxAmount, $minSize, $maxSize) {

		// um "division by zero" zu vermeiden wenn $maxAmount = $minAmount
		if ($maxAmount - $minAmount != 0) {
			$fontSize = ( ( ($maxSize - $minSize) * ($amount - $minAmount) ) / ( $maxAmount - $minAmount ) ) + $minSize;
		} else {
			$fontSize = $minSize;
		}

		return round($fontSize, 0);
	}

	/**
	 * 
	 */
	protected function tagsShuffle() {
		$keys = array_keys($this->tagsArray);
		shuffle($keys);
		if (count($keys) && is_array($keys)) {
			$tmpArray = $this->tagsArray;
			$this->tagsArray = array();
			foreach ($keys as $key => $value)
				$this->tagsArray[$value] = $tmpArray[$value];
		}
	}

	/**
	 * public function saveTags()
	 * Speichert die übergebenen Tags in der dafür vorgesehenen Tabelle ('mlog_tags'), 
	 * erstellt bei Bedarf neue Einträge bei neuen tags oder aktualisiert die Anzahl, 
	 * falls ein tag schon vorhanden ist.
	 *
	 * @param array $params Parameter in assoziativem Array:
	 * tags => String mit den Tags
	 * tagSeparator => String, zeichen, welches die Tags im übergebenen String voneinander trennt
	 * tagTable => String, optional, die Tabelle der Tags. Default ist mlog_tags
	 * addFields => Assoziatives Array mit Feldnamen und Werten bei Neueintrag um zusätzliche Felder zu speichern, optional
	 *
	 * @return boolean
	 */
	public function saveTags($params = array()) {

		if (!is_array($params)) {
			return false;
		}

		$defaultParams = array(
			'tags' => '',
			'tagTable' => $this->tagsTable,
			'addFields' => array(),
			'tagSeparator' => $this->tagSeparator,
			'postID' => 0
		);

		$params = array_merge($defaultParams, $params);

		// alte Tags holen
		$tagsChanged = $this->filterTags(array(
			'tagsOld' => $this->getTags(array('postID' => $params['postID'])),
			'tagsNew' => $this->trimTags(explode($params['tagSeparator'], $params['tags']))
		));

		// Neue Tags hinzufügen oder Anzahl erhöhen
		foreach ($tagsChanged['tagsAdded'] as $key => $tag) {

			$tag = trim($tag);

			if ($this->tagExists($tag)) {
				$this->increaseAmount($tag);
			} else {
				$this->addTag(array(
					'tag' => $tag,
					'addFields' => $params['addFields']
				));
			}
		}

		// Alte Tags Anzahl verringern
		foreach ($tagsChanged['tagsDeleted'] as $key => $tag) {

			$tag = trim($tag);
			$this->decreaseAmount($tag);
		}
		
		return true;
	}

	/**
	 * public function tagExists()
	 * Prüft, ob ein Tag in der Datenbanktabelle der Tags existiert.
	 * 
	 * @param string $tag Tag/ Stichwort so wie es in der DB-Tabelle gespeichert ist.
	 * @return boolean
	 */
	public function tagExists($tag) {

		$tag = trim($tag);

		$this->db->query("
			SELECT * FROM " . $this->tagsTable . " 
			WHERE tag_name = '" . $this->db->dbQuote($tag) . "' 
			LIMIT 1"
		);
		$r = $this->db->get();

		// Tag schon vorhanden, dann Update Amount
		if ($r['tag_name']) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * public function increaseAmount()
	 * Erhöht den Zähler für die Anzahl des Tags in der Datenbanktabelle der Tags
	 * 
	 * @param string $tag Tag/ Stichwort, wie es in der DB-Tabelle gespeichert ist.
	 * @return boolean
	 */
	public function increaseAmount($tag) {

		$amount = $this->getAmount($tag);

		$check = $this->db->query("
			UPDATE " . $this->tagsTable . " 
			SET tag_amount = '" . ($amount + 1) . "'
			WHERE tag_name = '" . $this->db->dbQuote($tag) . "' 
			LIMIT 1"
		);

		if (!$check) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * public function decreaseAmount()
	 * Verringert den Zähler für die Anzahl des Tags in der Datenbanktabelle der Tags
	 * 
	 * @param string $tag Tag/ Stichwort, wie es in der DB-Tabelle gespeichert ist.
	 * @return boolean
	 */
	public function decreaseAmount($tag) {

		$amount = $this->getAmount($tag);

		// TODO: Hier event. dann gleich den Tag löschen!
		if ($amount) {

			$check = $this->db->query("
				UPDATE " . $this->tagsTable . " 
				SET tag_amount = '" . ($amount - 1) . "'
				WHERE tag_name = '" . $this->db->dbQuote($tag) . "' 
				LIMIT 1"
			);
		}

		if (!$check) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * public function getAmount()
	 * Liefert die Anzahl des Tags wie in der Datenbanktabelle gespeichert.
	 * 
	 * @param string $tag Tag/ Stichwort, wie es in der DB-Tabelle gespeichert ist.
	 * @return number
	 */
	public function getAmount($tag) {
		$this->db->query("
			SELECT tag_amount FROM " . $this->tagsTable . " 
			WHERE tag_name = '" . $this->db->dbQuote(trim($tag)) . "'
			LIMIT 1"
		);
		$r = $this->db->get();

		return intval($r['tag_amount']);
	}

	/**
	 * public function addTag()
	 * Fügt ein neues Tag mit zusätzlichen Daten der Datenbanktabelle der Tags hinzu.
	 * 
	 *  @param array Parameter werden in einem assoziativen Array übergeben.
	 *  'tag' => String, Tag/ Stichwort
	 *  'addFields' Assoziatives Array, optional: Enthält zusätzliche Werte als Schlüssel/Wert-Paar, die in der Datenbanktabelle der tags gespeichert werden sollen.
	 *  
	 *  @return boolean
	 */
	public function addTag($params = array()) {


		if (!$params['tag']) {
			return false;
		}

		$fields = array(
			'tag_name' => trim($params['tag']),
			'tag_creation' => date('Y-m-d H:i:s'),
			'tag_amount' => '1'
		);

		if (is_array($params['addFields'])) {
			$fields = array_merge($fields, $params['addFields']);
		}

		$setQuery = $this->db->makeSetQuery($fields);

		$check = $this->db->query("
			INSERT INTO " . $this->tagsTable . " 
			SET " . $setQuery
		);

		if (!$check) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * public function deleteTag()
	 * Löscht ein Tag aus allen Artikeln in der Datenbanktabelle der Artikel (Posts)
	 * 
	 * @param array Parameter in assoziativem Array:
	 * 'tag' => String, das Tag/Stichwort
	 * 'tagID' => Number, optional kann auch die ID des Tags aus der Tabelle der Tags angegeben werden
	 * 'tagTable' => String, optional kann ein abweichender Tabellenname der Tags angegeben werden
	 * 'tagSeparator' => String, optionales Trennzeichen der Tags in der Tabelle der Posts/ Artikel
	 * 
	 * @return void
	 */
	public function deleteTag($params = array()) {

		$defaultParams = array(
			'tag' => '',
			'tagID' => 0,
			'tagTable' => $this->tagsTable,
			'tagSeparator' => $this->tagSeparator
		);

		$params = array_merge($defaultParams, $params);

		if (!$params['tag'] && !$params['tagID']) {
			return false;
		}

		// Das eigentliche Tag muss noch ermittelt werden
		if ($params['tagID']) {
			$this->db->query("SELECT tag_name FROM " . $this->tagsTable . " WHERE id = '" . intval($params['tagID']) . "'");
			$r = $this->db - get();

			$params['tag'] = $r['tag_name'];
		}

		// Tag aus mLog Posts löschen
		$regExp = '^' . $params['tag'] . $params['tagSeparator'] . '[[:blank:]]*|' . // tag, ...
				$params['tagSeparator'] . '[[:blank:]]*' . $params['tag'] . $params['tagSeparator'] . '|' . // ... , tag, ...
				$params['tagSeparator'] . '[[:blank:]]*' . $params['tag'] . '$|' . // ... , tag
				'^' . $params['tag'] . '$'; // tag

		$this->db->query("
			SELECT post_tags, id FROM " . $this->postsTable . " 
			WHERE post_tags REGEXP '" . $regExp . "'"
		);

		$posts = $this->db->getAll();

		$tag = preg_quote($params['tag'], '/');
		$separator = preg_quote($params['tagSeparator'], '/');

		foreach ($posts as $key => $postData) {

			$postData['post_tags'] = preg_replace(
					array(
				'/^' . $tag . $separator . '\s*/',
				'/' . $separator . '\s*' . $tag . $separator . '/',
				'/' . $separator . '\s*' . $tag . '$/',
				'/' . $tag . '/'), array(
				'',
				$separator,
				'',
				''
					), $postData['post_tags']
			);

			$this->db->query("
				UPDATE " . $this->postsTable . " 
				SET post_tags = '" . $postData['post_tags'] . "' 
				WHERE id = '" . $postData['id'] . "'"
			);
		}
		return;
	}

	/**
	 * public function filterTags()
	 * Ermittelt aus zwei Strings, ob Tags hinzugefügt oder entfernt wurden.
	 *
	 * @param unknown_type $params
	 *
	 * @return array Multidimensionales Array: 
	 * 'tagsAdded' => Array mit allen hinzugefügten Tags
	 * 'tagsDeleted' => Array mit allen entfernten Tags
	 */
	public function filterTags($params = array()) {

		$defaultParams = array(
			'tagsOld' => array(),
			'tagsNew' => array(),
			'tagTable' => $this->tagsTable,
			'tagSeparator' => $this->tagSeparator
		);

		$params = array_merge($defaultParams, $params);

		if (!is_array($params['tagsOld'])) {
			$tagsOld = explode($params['tagSeparator'], $params['tagsOld']);
		} else {
			$tagsOld = $params['tagsOld'];
		}

		if (!is_array($params['tagsNew'])) {
			$tagsNew = explode($params['tagSeparator'], $params['tagsNew']);
		} else {
			$tagsNew = $params['tagsNew'];
		}

		$tagsOld = $this->trimTags($tagsOld);
		$tagsNew = $this->trimTags($tagsNew);

		$tagsAdded = array();
		$tagsDeleted = array();

		foreach ($tagsOld as $key => $tagOld) {

			if (!in_array($tagOld, $tagsNew)) {
				$tagsDeleted[] = $tagOld;
			}
		}
		foreach ($tagsNew as $key => $tagNew) {

			if (!in_array($tagNew, $tagsOld)) {
				$tagsAdded[] = $tagNew;
			}
		}

		return array(
			'tagsAdded' => $tagsAdded,
			'tagsDeleted' => $tagsDeleted
		);
	}

	/**
	 * public function getTags()
	 * Liefert die Tags eines Artikels anhand seiner ID.
	 *
	 * @param array $params Assoziatives Array mit folgenden Parametern:
	 * "postID" => Number, ID des Artikels
	 * "tagSeparator" => String, optional, Trennzeichen der Tags 
	 *
	 * @return return_type
	 */
	public function getTags($params = array()) {

		$defaultParams = array(
			'postID' => '',
			'tagSeparator' => $this->tagSeparator
		);

		$params = array_merge($defaultParams, $params);

		$this->db->query("SELECT post_tags FROM " . $this->postsTable . " WHERE id = '" . intval($params['postID']) . "'");
		$r = $this->db->get();

		return $this->trimTags(explode($params['tagSeparator'], $r['post_tags']));
	}

	/**
	 * public function trimTags()
	 * Entfernt führende und abschließende Leerzeichen aus allen Elementen eines Arrays.
	 *
	 * @param array $tags Array, welches die Tags/ Stichworte beinhaltet.
	 *
	 * @return array Bearbeitetes Array der Stichworte
	 */
	public function trimTags($tags = array()) {

		if (!is_array($tags)) {
			return trim($tags);
		}

		foreach ($tags as $key => $tag) {
			$tags[$key] = trim($tag);
		}

		return $tags;
	}

	/**
	 * public function setTagSeparator()
	 * Setzt das Tag-Trennzeichen global für die Klasse.
	 *
	 * @param string $tagSeparator Trennzeichen
	 *
	 * @return void
	 */
	public function setTagSeparator($tagSeparator) {
		$this->tagSeparator = $tagSeparator;
	}

}
?>