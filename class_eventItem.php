<?php

require_once('interface.event.php');

class eventItem extends propertyObject implements eventInt {

	public static $_tbl_item = "event";
	public static $_tbl_selection = "event_sel";
	public static $_tbl_selection_b = "event_sel_b";
	public static $_tbl_newsletter = "newsletter";
	public static $_tbl_newsletter_item = "newsletter_item";
	
	private static $_fck_toolbar = 'Full';
	private static $_fck_height = 200;
	private static $_fck_width = '100%';
	public static $extension_media = array('jpg', 'png');
	public static $extension_attach = array('pdf', 'jpg');

	private $_gform;

	function __construct($id) {
		
		$this->_tbl_data = self::$_tbl_item;
		parent::__construct($this->initP($id));
	}
	
	private function initP($id) {
	
		$db = new db;
		$query = "SELECT * FROM ".self::$_tbl_item." WHERE id='$id'";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) return $a[0]; 
		else return array('id'=>null, 'instance'=>null, 'ctg'=>null, 'name'=>null, 'date'=>null, 'hours'=>null, 'location'=>null, 'duration'=>null, 'informations'=>null, 'description'=>null, 'summary'=>null, 'image'=>null, 'attachment'=>null, 'private'=>null, 'lng'=>null, 'lat'=>null);
	}

	public function setInstance($value) {
		
		if($this->_p['instance']!=$value && !in_array('instance', $this->_chgP)) $this->_chgP[] = 'instance';
		$this->_p['instance'] = $value;
		return true;
	}

	public function setCtg($postLabel) {
		
		$value = cleanVar($_POST, $postLabel, 'int', '');
		if($this->_p['ctg']!=$value && !in_array('ctg', $this->_chgP)) $this->_chgP[] = 'ctg';
		$this->_p['ctg'] = $value;
		return true;
	}

	public function setDate($postLabel) {

		$value = cleanVar($_POST, $postLabel, 'string', '');
		if($this->_p['date']!=$value && !in_array('date', $this->_chgP)) $this->_chgP[] = 'date';
		$this->_p['date'] = dateToDbDate($value, "/");
		return true;
	}
	
	public function setHours($postLabel) {

		$value = cleanVar($_POST, $postLabel, 'string', '');
		if($this->_p['hours']!=$value && !in_array('hours', $this->_chgP)) $this->_chgP[] = 'hours';
		$this->_p['hours'] = timeToDbTime($value);
		return true;
	}
	
	public function setDuration($postLabel) {

		$value = cleanVar($_POST, $postLabel, 'int', '');
		if($this->_p['duration']!=$value && !in_array('duration', $this->_chgP)) $this->_chgP[] = 'duration';
		$this->_p['duration'] = $value;
		return true;
	}
	
	public function setInformations($postLabel) {

		$value = cleanVarEditor($_POST, $postLabel, '');
		if($this->_p['informations']!=$value && !in_array('informations', $this->_chgP)) $this->_chgP[] = 'informations';
		$this->_p['informations'] = $value;
		return true;
	}
	
	public function setDescription($postLabel) {

		$value = cleanVarEditor($_POST, $postLabel, '');
		if($this->_p['description']!=$value && !in_array('description', $this->_chgP)) $this->_chgP[] = 'description';
		$this->_p['description'] = $value;
		return true;
	}
	
	public function setSummary($postLabel) {

		$value = cleanVarEditor($_POST, $postLabel, '');
		if($this->_p['summary']!=$value && !in_array('summary', $this->_chgP)) $this->_chgP[] = 'summary';
		$this->_p['summary'] = $value;
		return true;
	}
	
	public function setImage($value) {
		
		if($this->_p['image']!=$value && !in_array('image', $this->_chgP)) $this->_chgP[] = 'image';
		$this->_p['image'] = $value;
		return true;
	}
	
	public function setAttachment($value) {
		
		if($this->_p['attachment']!=$value && !in_array('attachment', $this->_chgP)) $this->_chgP[] = 'attachment';
		$this->_p['attachment'] = $value;
		return true;
	}
	
	/*
	 * Per le ricerche 
	 */
	private function sessionVar($interface, $name) {

		return isset($_SESSION[$interface][$name]) ? $_SESSION[$interface][$name] : null;
	}
	
	private function actionSearch($interface) {

		$_SESSION[$interface]['name'] = cleanVar($_POST, 'name', 'string', '');
		$_SESSION[$interface]['ctg'] = cleanVar($_POST, 'ctg', 'int', '');
		$_SESSION[$interface]['location'] = cleanVar($_POST, 'location', 'string', '');
		$_SESSION[$interface]['sfromdate'] = cleanVar($_POST, 'sfromdate', 'string', '');
		$_SESSION[$interface]['stodate'] = cleanVar($_POST, 'stodate', 'string', '');
		$_SESSION[$interface]['range'] = cleanVar($_POST, 'range', 'string', '');
	}

	private function setWhere($interface) {

		$where = array();
		if($this->sessionVar($interface, 'name')) {
			if(preg_match("#^\\\(\")(.*?)\\\(\")$#", $this->sessionVar($interface, 'name'), $match)) $where[] = "name = '".$match[2]."'";
			elseif(preg_match("#^\\\(\")(.*)$#", $this->sessionVar($interface, 'name'), $match)) $where[] = "name LIKE '".$match[2]."%'";
			else $where[] = "name LIKE '%".$this->sessionVar($interface, 'name')."%'";
		}
		if($this->sessionVar($interface, 'ctg')) $where[] = "ctg='".$this->sessionVar($interface, 'ctg')."'";
		
		if($this->sessionVar($interface, 'location')) {
			if(preg_match("#^\\\(\")(.*?)\\\(\")$#", $this->sessionVar($interface, 'location'), $match)) $where[] = "location = '".$match[2]."'";
			elseif(preg_match("#^\\\(\")(.*)$#", $this->sessionVar($interface, 'location'), $match)) $where[] = "location LIKE '".$match[2]."%'";
			else $where[] = "location LIKE '%".$this->sessionVar($interface, 'location')."%'";
		}
		
		if($this->sessionVar($interface, 'sfromdate')) {
			$where[] = "date>='".dateToDbDate($this->sessionVar($interface, 'sfromdate'), "/")."'";
		}
		
		if($this->sessionVar($interface, 'stodate')) {
			$where[] = "date<='".dateToDbDate($this->sessionVar($interface, 'stodate'), "/")."'";
		}
		
		return implode(" AND ", $where);
	}
	
	// $interface: instance name
	public function dataSearch($interface){
		
		$this->actionSearch($interface);
		$where = $this->setWhere($interface);
		return $where;
	}
	
	/*
	 * Opzioni:
	 * ----------------
	 * ctg			boolean		segnala se le categorie sono abilitate
	 * admin		boolean		nella parte amministrativa
	 * section		boolean		trattato come sezione
	 */
	public function formSearch($instance, $url, $options=array()){
		
		$ctg = array_key_exists('ctg', $options) ? $options['ctg'] : true;
		$admin = array_key_exists('admin', $options) ? $options['admin'] : false;
		$section = array_key_exists('section', $options) ? $options['section'] : false;
		
		$db = new DB();
		$interface = $db->getFieldFromId('sys_module', 'name', 'id', $instance);
		
		if($section)
		{
			$htmlsection = new htmlSection(array('class'=>'admin', 'headerTag'=>'h1'));
			$size = 30;
			$max_chars = 60;
			$cut_words = false;
		}
		else
		{
			$size = 10;
			$max_chars = 20;
			$cut_words = true;
		}

		$gform = new Form('sform', 'post', true, array('tblLayout'=>false));
		
		$buffer = $gform->form($url, '', '');
		$buffer .= "<fieldset>";

		// Input search
		$search_from = $gform->cinput_date('sfromdate', $this->sessionVar($interface, 'sfromdate'), '', array('inputClickEvent'=>true));
		$search_to = $gform->cinput_date('stodate', $this->sessionVar($interface, 'stodate'), '', array('inputClickEvent'=>true));
		
		if($ctg)
		{
			$selctg = array();
			foreach(eventCtg::getAll($instance) as $c) $selctg[$c->id] = htmlChars($c->name);
			$search_ctg = $gform->select('ctg', $this->sessionVar($interface, 'ctg'), $selctg, array('maxChars'=>$max_chars, 'cutWords'=>$cut_words));
		}
		$search_loc = $gform->input('location', 'text', htmlInput($this->sessionVar($interface, 'location')), array("size"=>$size, "maxlength"=>200));
		$search_submit = $gform->input('submit_action', 'submit', _("cerca"), array("classField"=>"submit"));
		$search_zero = "<input type=\"button\" class=\"generic\" value=\""._("cancella")."\" onclick=\"$$('input[type=text]').set('value', '');$$('select').set('value', '');$$('input[type=radio]').set('checked', '');$('sform').submit();\"/>";
		// End
		
		$buffer .= "<table style=\"margin-top:0px; width:100%;\">";
		if($admin)
		{
			$buffer .= "<tr>";
			$buffer .= "<td>"._("da")."</td>";
			$buffer .= "<td>$search_from</td>";
			$buffer .= "</tr>";
			$buffer .= "<tr>";
			$buffer .= "<td>"._("a")."</td>";
			$buffer .= "<td>$search_to</td>";
			$buffer .= "</tr>";
			if($ctg)
			{
				$buffer .= "<tr>";
				$buffer .= "<td>"._("Categoria")."</td>";
				$buffer .= "<td>$search_ctg</td>";
				$buffer .= "</tr>";
			}
			$buffer .= "<tr>";
			$buffer .= "<td colspan=\"2\">$search_submit $search_zero</td>";
			$buffer .= "</tr>";
		}
		else
		{
			$buffer .= "<tr>";
			$buffer .= "<td>"._("da")."</td>";
			$buffer .= "<td>$search_from</td>";
			if($ctg)
			{
				$buffer .= "<td>"._("Categoria")."</td>";
				$buffer .= "<td>$search_ctg</td>";
			}
			else
			{
				$buffer .= "<td colspan=\"2\"></td>";
			}
			$buffer .= "</tr>";
			$buffer .= "<tr>";
			$buffer .= "<td>"._("a")."</td>";
			$buffer .= "<td>$search_to</td>";
			$buffer .= "<td>"._("Città")."</td>";
			$buffer .= "<td>$search_loc</td>";
			$buffer .= "</tr>";
			$buffer .= "<tr>";
			$buffer .= "<td colspan=\"3\">&nbsp;</td>";
			$buffer .= "<td>$search_submit $search_zero</td>";
			$buffer .= "</tr>";
		}
		$buffer .= "</table>";
		$buffer .= "</fieldset>";
		$buffer .= $gform->cform();
		
		if($section)
		{
			$htmlsection->content = $buffer;
			return $htmlsection->render();
		}
		else return $buffer;
	}

	/**
	 * Visualizzazione
	 *
	 * @param array $property
	 * @return string
	 * 
	 * interface		nome dell'istanza, se presente attiva il download del file allegato
	 * folder			percorso della directory del file immagine
	 * prefix_img
	 * prefix_thumb
	 * manage_ctg		indica se le categorie sono attivate
	 */
	public function show($property=array()) {
		
		$manage_ctg = array_key_exists('manage_ctg', $property) ? $property['manage_ctg'] : false;
		$folder = array_key_exists('folder', $property) ? $property['folder'] : '';
		$prefix_img = array_key_exists('prefix_img', $property) ? $property['prefix_img'] : '';
		
		$buffer = '';
		if($this->image) $buffer .= "<img class=\"card_img\" src=\"".$folder.$prefix_img.$this->image."\" />";
		$buffer .= "<div class=\"card_mainInfo\">\n";
		$buffer .= "<table>";
		
		if($manage_ctg AND $this->ctg) {
			
			$ctg = new eventCtg($this->ctg);
			$buffer .= "<tr><td class=\"card_ctg\">"._("Categoria:")."</td><td class=\"card_value\">".htmlChars($ctg->ml('name'))."</td></tr>";
		}
		
		$buffer .= "<tr><td class=\"card_loc\">"._("Luogo:")."</td><td class=\"card_value\">".$this->location."</td></tr>";
		$buffer .= "<tr><td class=\"card_start\">"._("Data:")."</td><td class=\"card_value\">".dbDateToDate($this->date, "/");
		
		$hours = dbTimeToTime($this->hours);
		if(!empty($hours)) $buffer .= " - "._("ore").' '.htmlChars($hours);
		$buffer .= "</td></tr>";
		if($this->duration>1)
		{
			$buffer .= "<tr><td class=\"card_duration\">"._("Durata:")."</td><td class=\"card_value\">".htmlChars($this->duration);
			$buffer .= ($this->duration>1)?_(" giorni"):_(" giorno");
			$buffer .= "</td></tr>";
		}
		$buffer .= "</table>";
		$buffer .= "</div>";
		$buffer .= "<div class=\"null\"></div>";
		
		if($this->lat && $this->lng) {
			$mapPoints = array();
			$jdesc = cutHtmlText($this->ml('description'), 500, "...", false, false, true);
			$jdesc = preg_replace("#'#", "&quot;", $jdesc);
			$jdesc = preg_replace("#\n#", "", $jdesc);
			$jdesc = preg_replace("#\r#", "", $jdesc);
			$jdesc = preg_replace("#\r\n#", "", $jdesc);
			$jdesc = "<b>".htmlspecialchars($this->ml('name'))."</b>".preg_replace("#\"#", "&#039;", $jdesc);
			$mapPoints[] = array("id"=>"evt_$this->id", "lat"=>$this->lat, "lng"=>$this->lng, "label"=>htmlspecialchars($this->ml('name'), ENT_QUOTES), "description"=>$jdesc);
			$buffer .= "<script type=\"text/javascript\">\n";
			$buffer .= "function showMap".$this->instance."() {
				window.map".$this->instance." = new AbidiMap('".json_encode($mapPoints)."', {'canvasPosition': 'over', 'dftType':'roadmap', 'dftCtrlType':'default', 'dftCtrlNav':'default', 'closeButtonLabel':'"._("chiudi")."', 'resize':true, 'canvasW':'600px', 'canvasH':'400px', 'destroyOnClose': false, 'zoom':10, 'title':'<b>".htmlspecialchars(htmlChars($this->ml('name')))."</b>', 'zindex':'100000'});
			}";
			$buffer .= "Asset.javascript('http://maps.google.com/maps/api/js?sensor=true&callback=showMap".$this->instance."');";
			$buffer .= "</script>";

			$onclick = "onclick=\"window.map".$this->instance.".showMap($(this).getParent()); window.map".$this->instance.".openInfoWindow('evt_$this->id');\"";
			$buffer .= "<p><span class=\"link\" $onclick>"._("Visualizza mappa evento")."</span></p>";
		}
		$buffer .= "<div class=\"card_info\">".htmlChars($this->ml('informations'))."</div>";
		$buffer .= "<div class=\"card_desc\">".htmlChars($this->ml('description'))."</div>";
		
		if($this->attachment)
		{
			if(isset($property['interface']) AND !empty($property['interface']))
			{
				$plink = new Link();
				$link = $plink->aLink($property['interface'], 'downloader', "id={$this->id}");
				$filename = "<a href=\"$link\">"._("scarica allegato")."</a>";
			}
			else $filename = htmlChars($this->attachment);
			
			$buffer .= "<div class=\"card_value\">$filename</div>";
		}
		
		return $buffer;
	}
	
	public function formItem($formaction, $interface, $property=null) {

		$manage_ctg = (isset($property['manage_ctg']) AND !empty($property['manage_ctg'])) ? $property['manage_ctg'] : false;

		$gform = new Form('eform', 'post', true, array("trnsl_table"=>self::$_tbl_item, "trnsl_id"=>$this->id));
		$gform->load('dataform');

		if($this->_p['id'])
		{
			$title = _("Modifica")." '".$this->_p['name']."'";
			$submit = _("modifica");
			$action='modify';
		}
		else
		{
			$title = _("Inserimento evento");
			$submit = _("inserisci");
			$action='insert';
		}

		$htmlsection = new htmlSection(array('class'=>'admin', 'headerTag'=>'h1', 'headerLabel'=>$title));

		$required = 'name,date,duration,private';
		if($manage_ctg)
			$required .= ',ctg';
		$buffer = javascript::abiMapLib();
		
		$buffer .= "<script type=\"text/javascript\">";
		$buffer .= "function convert() {
				var addressConverter = new AddressToPointConverter('map_coord', 'lat', 'lng', $('map_address').value, {'canvasPosition':'over'});
				addressConverter.showMap();
			}";
		$buffer .= "</script>";
		$onclick = "onclick=\"Asset.javascript('http://maps.google.com/maps/api/js?sensor=true&callback=convert')\"";

		$buffer .= $gform->form($formaction, true, $required);
		if($this->_p['id'])
			$buffer .= $gform->hidden('id', $this->id);
		$buffer .= $gform->hidden('action', $action);
		if(isset($property['start']) AND !empty($property['start']))
			$buffer .= $gform->hidden('start', $property['start']);
		$buffer .= $gform->hidden('old_image', $this->image);
		$buffer .= $gform->hidden('old_attachment', $this->attachment);

		if($manage_ctg)
		{
			$select_ctg = array();
			foreach(eventCtg::getAll($interface->getInstance()) as $ctg) $select_ctg[$ctg->id] = htmlInput($ctg->name);
			$buffer .= $gform->cselect('ctg', $gform->retvar('ctg', $this->_p['ctg']), $select_ctg,  _("Categoria"), array("required"=>true));
		}
		$buffer .= $gform->cinput('name', 'text', $gform->retvar('name', htmlInput($this->_p['name'])), _("Titolo"), array("required"=>true, "size"=>40, "maxlength"=>200, "trnsl"=>true, "field"=>"name"));
		$buffer .= $gform->cinput_date('date', $gform->retvar('date', dbDateToDate($this->_p['date'], "/")), _("Data"), array("required"=>true));
		$buffer .= $gform->cinput('hours', 'text', $gform->retvar('hours', dbTimeToTime($this->_p['hours'])), _("Ora"), array("size"=>6, "maxlength"=>5, "field"=>"hours"));
		
		$default_duration = ($action == 'insert' AND empty($this->_p['duration'])) ? 1 : $this->_p['duration'];
		$buffer .= $gform->cinput('duration', 'text', $gform->retvar('duration', $default_duration), array(_("Durata"), _("1: si conclude in giornata")), array("required"=>true, "size"=>2, "maxlength"=>3));
		$buffer .= $gform->cinput('location', 'text', $gform->retvar('location', htmlInput($this->_p['location'])), _("Luogo"), array("size"=>40, "maxlength"=>200, "field"=>"location"));

		$buffer .= $gform->fcktextarea('informations', $gform->retvar('informations', htmlInputEditor($this->_p['informations'])), array(_("Informazioni"), _("chi vi partecipa, etc...")), array("notes"=>false, "img_preview"=>false, "trnsl"=>true, "field"=>"informations", "fck_height"=>100));
		$buffer .= $gform->fcktextarea('description', $gform->retvar('description', htmlInputEditor($this->_p['description'])), _("Descrizione"), array("notes"=>false, "img_preview"=>true, "trnsl"=>true, "field"=>"description", "fck_toolbar"=>self::$_fck_toolbar));
		
		if(isset($property['max_char']) AND !empty($property['max_char']))
			$maxlength = $property['max_char'];
		else $maxlength = 200;
		$buffer .= $gform->ctextarea('summary', $gform->retvar('summary', htmlInput($this->_p['summary'])), _("Descrizione sintetica"), array("id"=>'summary', "rows"=>6, "cols"=>55, 'maxlength'=>$maxlength));

		$buffer .= $gform->cfile('image', $this->image, _("Immagine"), array("extensions"=>self::$extension_media, "del_check"=>true, "preview"=>true, "previewSrc"=>$interface->getEventWWW($this->id)."/".$interface->getPrefixThumb().$this->image));
		$buffer .= $gform->cfile('attachment', $this->attachment, _("Allegato"), array("extensions"=>self::$extension_attach, "del_check"=>true));

		$buffer .= $gform->cradio('private', $gform->retvar('private', $this->_p['private']), array("no"=>_("pubblico"), "yes"=>_("privato")), 'no', array(_("Tipo evento"), _("privato: visibile solo dal relativo gruppo")), array("required"=>true));

		$buffer .= $gform->cinput('map_address', 'text', '', array(_("Indirizzo evento"), _("es: torino, via mazzini 37<br />utilizzare 'converti' per calcolare latitudine e longitudine")), array("size"=>40, "maxlength"=>200, "id"=>"map_address"));
		$buffer .= $gform->cinput('map_coord', 'button', _("converti"), '', array("id"=>"map_coord", "classField"=>"generic", "js"=>$onclick));
		$buffer .= $gform->cinput('lat', 'text', $gform->retvar('lat', htmlInput($this->_p['lat'])), _("Latitudine"), array("size"=>20, "maxlength"=>200, "id"=>"lat"));
		$buffer .= $gform->cinput('lng', 'text', $gform->retvar('lng', htmlInput($this->_p['lng'])), _("Longitudine"), array("size"=>20, "maxlength"=>200, "id"=>"lng"));

		$buffer .= $gform->cinput('submit_action', 'submit', $submit, '', array("classField"=>"submit"));
		$buffer .= $gform->cform();

		$htmlsection->content = $buffer;

		return $htmlsection->render();
	}
	
	public function formDelItem($formaction) {
	
		$gform = new Form('gform', 'post', false);
		
		$title = _("Elimina")." '".$this->name."'";
		
		$htmlsection = new htmlSection(array('class'=>'admin', 'headerTag'=>'h1', 'headerLabel'=>$title));

		$required = 'id';
		$buffer = $gform->form($formaction, '', $required);
		$buffer .= $gform->hidden('id', $this->id);
		$buffer .= $gform->cinput('submit_action', 'submit', _("elimina"), _("Attenzione! l'eliminazione è definitiva"), array("classField"=>"submit"));
		$buffer .= $gform->cform();

		$htmlsection->content = $buffer;

		return $htmlsection->render();
	}

	public function deleteDbData(){
		
		$id = intval($this->id);
		if(empty($id)) return false;
		
		$db = new db;
		$query = "DELETE FROM ".self::$_tbl_item." WHERE id='$id'";
		$result = $db->actionquery($query);
		if($result)
		{
			$query_sel = "DELETE FROM ".self::$_tbl_selection." WHERE reference='$id'";
			$db->actionquery($query_sel);
			
			$query_sel = "DELETE FROM ".self::$_tbl_selection_b." WHERE reference='$id'";
			$db->actionquery($query_sel);
		}
		return $result;
	}
	
	public static function getTotItems($instance, $options=array()) {

		$private = array_key_exists('private', $options) ? $options['private'] : false;
		$ctg = array_key_exists('ctg', $options) ? $options['ctg'] : null;
		$fromDate = array_key_exists('fromDate', $options) ? $options['fromDate'] : null;
		$toDate = array_key_exists('toDate', $options) ? $options['toDate'] : null;
		$where_opt = array_key_exists('where', $options) ? $options['where'] : null;
		
		$where = array();
		$where_query = "WHERE instance='$instance'";
		
		if($where_opt) $where_query .= " AND $where_opt";
		else {
			if(!$private) $where[]  = "private='no'";
			if($fromDate) $where[] = "ADDDATE(date, INTERVAL duration-1 DAY)>='$fromDate'";
			if($toDate) $where[] = "date<='$toDate'";
			if(count($where)) $where_query .= " AND ".implode(" AND ", $where);
		}
		
		$db = new db;
		$query = "SELECT COUNT(id) AS tot FROM ".self::$_tbl_item." $where_query";
		$a = $db->selectquery($query);
		return sizeof($a)>0 ? $a[0]['tot'] : 0;
	}
	
	public static function getOrderedItems($instance, $options=array()) {

		$private = array_key_exists('private', $options) ? $options['private'] : false;
		$ctg = array_key_exists('ctg', $options) ? $options['ctg'] : null;
		$fromDate = array_key_exists('fromDate', $options) ? $options['fromDate'] : null;
		$toDate = array_key_exists('toDate', $options) ? $options['toDate'] : null;
		$order = (array_key_exists('order', $options) AND !empty($options['order'])) ? $options['order'] : 'date';
		$sort = array_key_exists('sort', $options) ? $options['sort'] : 'DESC';
		$start = array_key_exists('start', $options) ? $options['start'] : 0;
		$range = array_key_exists('range', $options) ? $options['range'] : null;
		$where_opt = array_key_exists('where', $options) ? $options['where'] : null;
		
		$items = array();
		$where = array();
		$where_query = "WHERE instance='$instance'";
		
		if($where_opt) $where_query .= " AND $where_opt";
		else {
			if(!$private) $where[]  = "private='no'";
			if($fromDate) $where[] = "ADDDATE(date, INTERVAL duration-1 DAY)>='$fromDate'";
			if($toDate) $where[] = "date<='$toDate'";
			if(count($where)) $where_query .= " AND ".implode(" AND ", $where);
		}
		if($range) $range = "LIMIT $start,$range";
		
		$db = new db;
		$query = "SELECT id FROM ".self::$_tbl_item." $where_query ORDER BY $order $sort $range";
		$a = $db->selectquery($query);
		if(sizeof($a))
			foreach($a as $b) $items[] = new eventItem($b['id']);
		return $items;
	}

	// Eventi che sono in programma in un dato giorno
	public static function getDateItems($instance, $date, $ctg=null, $private=false, $bool=false) {
	
		$evts = array();
		$db = new db;
		$where_ctg = ($ctg)?"AND ctg='$ctg'":"";
		$where_private = ($private)?"":"AND private='no'";
		$date = "date<='$date' AND ADDDATE(date, INTERVAL duration-1 DAY)>='$date'";
		
		$query = "SELECT id FROM ".self::$_tbl_item." WHERE instance='$instance' AND $date $where_ctg $where_private ORDER BY date ASC";
		$a = $db->selectquery($query);
		if(sizeof($a)) {
			if($bool) return true;
			else foreach($a as $b) $evts[] = new eventItem($b['id']);
		}
		return ($bool)? false:$evts;
	}
}
?>
