<?php

class eventBox extends propertyObject implements eventInt {

	public static $_tbl_item = "event_box";
	
	public static $extension_media = array('jpg', 'png');
	public static $extension_attach = array('pdf', 'jpg');
	
	private $_gform;

	function __construct($id) {
	
		$this->_tbl_data = self::$_tbl_item;
		parent::__construct($this->initP($id));
	}
	
	private function initP($id) {
	
		$db = db::instance();
		$query = "SELECT * FROM ".$this->_tbl_data." WHERE id='$id'";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) return $a[0];
		else return array('id'=>null, 'instance'=>null, 'name'=>null, 'title'=>null, 'subtitle'=>null, 'content'=>null, 'text_attachment'=>null, 'image'=>null, 'attachment'=>null, 'active'=>null, 'date'=>null);
	}
	
	public function setInstance($value) {
		
		if($this->_p['instance']!=$value && !in_array('instance', $this->_chgP)) $this->_chgP[] = 'instance';
		$this->_p['instance'] = $value;
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
	
	public function setDate($value) {
		
		if($this->_p['date']!=$value && !in_array('date', $this->_chgP)) $this->_chgP[] = 'date';
		$this->_p['date'] = $value;
		return true;
	}
	
	public static function getAll($instance) {
	
		$items = array();
		$db = db::instance();
		$query = "SELECT id FROM ".self::$_tbl_item." WHERE instance='$instance' ORDER BY date DESC";
		$a = $db->selectquery($query);
		if(sizeof($a))
			foreach($a as $b) $items[$b['id']] = new eventBox($b['id']);
		return $items;
	}
	
	public static function getItem($instance) {
	
		$db = db::instance();
		$query = "SELECT id FROM ".self::$_tbl_item." WHERE instance='$instance' AND active='yes' AND 
		date=(SELECT MAX(date) FROM ".self::$_tbl_item." WHERE instance='$instance' AND active='yes')";
		$a = $db->selectquery($query);
		if(sizeof($a) == 1)
			foreach($a as $b) return new eventBox($b['id']);
		else
			return null;
	}
	
	public static function getOrderedItems($instance, $options=array()) {

		$active = array_key_exists('active', $options) ? $options['active'] : null;
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
			if($active) $where[]  = "active='no'";
			if($fromDate) $where[] = "ADDDATE(date, INTERVAL duration-1 DAY)>='$fromDate'";
			if($toDate) $where[] = "date<='$toDate'";
			if(count($where)) $where_query .= " AND ".implode(" AND ", $where);
		}
		if($range) $range = "LIMIT $start,$range";
		
		$db = db::instance();
		$query = "SELECT id FROM ".self::$_tbl_item." $where_query ORDER BY $order $sort $range";
		$a = $db->selectquery($query);
		if(sizeof($a))
			foreach($a as $b) $items[] = new eventBox($b['id']);
		return $items;
	}

	public function formItem($formaction, $interface, $property=null) {
		
		$gform = new Form('bform', 'post', true, array("trnsl_table"=>$this->_tbl_data, "trnsl_id"=>$this->_p['id']));
		$gform->load('bdataform');

		if($this->_p['id'])
		{
			$title = _("Modifica")." '".$this->_p['name']."'";
			$submit = _("modifica");
			$action='modify';
		}
		else
		{
			$title = _("Inserimento nuovo elemento");
			$submit = _("inserisci");
			$action='insert';
		}

		$htmlsection = new htmlSection(array('class'=>'admin', 'headerTag'=>'h1', 'headerLabel'=>$title));

		$required = 'name';
		$buffer = $gform->form($formaction, true, $required);
		$buffer .= $gform->hidden('action', $action);
		if(isset($property['start']) AND !empty($property['start']))
			$buffer .= $gform->hidden('start', $property['start']);
		if($this->_p['id'])
			$buffer .= $gform->hidden('id', $this->_p['id']);
		$buffer .= $gform->hidden('old_image', $this->image);
		$buffer .= $gform->hidden('old_attachment', $this->attachment);
		
		$buffer .= $gform->cinput('name', 'text', $gform->retvar('name', htmlInput($this->_p['name'])), _("Nome"), array("required"=>true, "size"=>40, "maxlength"=>200, "trnsl"=>true, "field"=>"name"));
		$buffer .= $gform->cinput('title', 'text', $gform->retvar('title', htmlInput($this->_p['title'])), _("Titolo"), array("required"=>false, "size"=>40, "maxlength"=>200, "trnsl"=>true, "field"=>"title"));
		$buffer .= $gform->cinput('subtitle', 'text', $gform->retvar('subtitle', htmlInput($this->_p['subtitle'])), _("Sottotitolo"), array("required"=>false, "size"=>40, "maxlength"=>200, "trnsl"=>true, "field"=>"subtitle"));
		
		$buffer .= $gform->fcktextarea('content', $gform->retvar('content', htmlInputEditor($this->_p['content'])), _("Testo"), array("notes"=>false, "img_preview"=>false, "trnsl"=>true, "field"=>"content", "fck_height"=>100));
		
		$buffer .= $gform->cfile('image', $this->image, _("Immagine"), array("extensions"=>self::$extension_media, "del_check"=>true, "preview"=>true, "previewSrc"=>$interface->getEventWWW($this->id, array('dir'=>'box'))."/".$interface->getPrefixThumb().$this->image));
		$buffer .= $gform->cfile('attachment', $this->attachment, _("Allegato"), array("extensions"=>self::$extension_attach, "del_check"=>true));
		$buffer .= $gform->cinput('text_attachment', 'text', $gform->retvar('text_attachment', htmlInput($this->_p['text_attachment'])), _("Testo allegato"), array("required"=>false, "size"=>40, "maxlength"=>200, "trnsl"=>true, "field"=>"text_attachment"));
		
		$buffer .= $gform->cradio('active', $gform->retvar('active', $this->_p['active']), array("no"=>_("no"), "yes"=>_("si")), 'no', _("Attiva"), array("required"=>false));
		
		$buffer .= $gform->cinput('submit_action', 'submit', $submit, '', array("classField"=>"submit"));
		$buffer .= $gform->cform();

		$htmlsection->content = $buffer;

		return $htmlsection->render();
	}
	
	public function formDelItem($formaction) {
	
		$gform = new Form('dform', 'post', true);
		$gform->load('ddataform');
		
		if(!$this->_p['id'])
			return null;
		
		$htmlsection = new htmlSection(array('class'=>'admin', 'headerTag'=>'h1', 'headerLabel'=>$this->textLabel('del')." '".$this->_p['name']."'"));

		$required='id';
		$buffer = $gform->form($formaction, '', $required);
		$buffer .= $gform->hidden('id', $this->_p['id']);
		$buffer .= $gform->cinput('submit_action', 'submit', _("elimina"), array(_("Attenzione!"), _("l'eliminazione è definitiva")), array("classField"=>"submit"));
		$buffer .= $gform->cform();

		$htmlsection->content = $buffer;

		return $htmlsection->render();
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
		if($this->image) $buffer .= "<img class=\"img\" src=\"".$folder.$prefix_img.$this->image."\" />";
		
		if($this->subtitle)
			$buffer .= "<div class=\"subtitle\">".$this->subtitle."</div>";
		$buffer .= "<div class=\"content\">";
		$buffer .= $this->content;
		$buffer .= "</div>";
		
		if($this->attachment)
		{
			if(isset($property['interface']) AND !empty($property['interface']))
			{
				$plink = new Link();
				$link = $plink->aLink($property['interface'], 'downloader', "id={$this->id}&opt=box");
				$text_attachment = $this->text_attachment != '' ? htmlChars($this->text_attachment) : _("scarica allegato");
				$filename = "<a href=\"$link\">$text_attachment</a>";
			}
			else $filename = htmlChars($this->attachment);
			
			$buffer .= "<div class=\"attachment\">$filename</div>";
		}
		
		return $buffer;
	}
}
?>
