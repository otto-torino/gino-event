<?php

class eventCtg extends propertyObject {

	public static $_tbl_ctg = "event_ctg";
	private $_gform;

	function __construct($id) {
	
		$this->_tbl_data = self::$_tbl_ctg;
		parent::__construct($this->initP($id));
	}
	
	private function initP($id) {
	
		$db = new db;
		$query = "SELECT * FROM ".self::$_tbl_ctg." WHERE id='$id'";
		$a = $db->selectquery($query);
		if(sizeof($a)>0) return $a[0]; 
		else return array('id'=>null, 'instance'=>null, 'name'=>null, 'description'=>null, 'link'=>null);
	}
	
	public function setInstance($value) {
		
		if($this->_p['instance']!=$value && !in_array('instance', $this->_chgP)) $this->_chgP[] = 'instance';
		$this->_p['instance'] = $value;
		return true;
	}
	
	public static function getAll($instance) {
	
		$ctgs = array();
		$db = new db;
		$query = "SELECT id FROM ".self::$_tbl_ctg." WHERE instance='$instance' ORDER BY name DESC";
		$a = $db->selectquery($query);
		if(sizeof($a))
			foreach($a as $b) $ctgs[$b['id']] = new eventCtg($b['id']);
		return $ctgs;
	}

	public function formCtg($formaction) {
		
		$gform = new Form('ctgform', 'post', true, array("trnsl_table"=>self::$_tbl_ctg, "trnsl_id"=>$this->_p['id']));
		$gform->load('cdataform');

		if($this->_p['id']) {$title = _("Modifica categoria"); $submit = _("modifica");$action='modify';}
		else {$title = _("Nuova categoria");$submit = _("inserisci");$action='insert';}

		$htmlsection = new htmlSection(array('class'=>'admin', 'headerTag'=>'h1', 'headerLabel'=>$title));

		$required = 'name';
		$buffer = $gform->form($formaction, '', $required);
		$buffer .= $gform->hidden('action', $action);
		if($this->_p['id'])
			$buffer .= $gform->hidden('id', $this->_p['id']);
		$buffer .= $gform->cinput('name', 'text', $gform->retvar('name', htmlInput($this->_p['name'])), _("Nome"), array("required"=>true, "size"=>40, "maxlength"=>200, "trnsl"=>true, "field"=>"name"));
		$buffer .= $gform->ctextarea('description', $gform->retvar('description', htmlInput($this->_p['description'])), _("Descrizione"), array("id"=>'description', "rows"=>6, "cols"=>55));
		$buffer .= $gform->cinput('link', 'text', $gform->retvar('link', htmlInput($this->_p['link'])), array(_("Link pagina"), "esempi:<br />- http://otto.to.it/promo.php<br />- event/viewCtgA"), array("size"=>40, "maxlength"=>200));
		
		$buffer .= $gform->cinput('submit_action', 'submit', $submit, '', array("classField"=>"submit"));
		$buffer .= $gform->cform();

		$htmlsection->content = $buffer;

		return $htmlsection->render();
	}
	
	public function formDelCtg($formaction) {
	
		$gform = new Form('gform', 'post', true);
		$gform->load('dataform');
		
		if(!$this->_p['id'])
			return null;
		
		$htmlsection = new htmlSection(array('class'=>'admin', 'headerTag'=>'h1', 'headerLabel'=>_("Elimina categoria")." '".$this->_p['name']."'"));

		$required='id';
		$buffer = $gform->form($formaction, '', $required);
		$buffer .= $gform->hidden('id', $this->_p['id']);
		$buffer .= $gform->cinput('submit_action', 'submit', _("elimina"), array(_("Attenzione!"), _("l'eliminazione è definitiva e NON comporta l'eliminazione degli eventi associati. VERIFICARE")), array("classField"=>"submit"));
		$buffer .= $gform->cform();

		$htmlsection->content = $buffer;

		return $htmlsection->render();
	}
}
?>
