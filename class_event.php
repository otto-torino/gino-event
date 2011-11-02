<?php

require_once('class_eventCtg.php');
require_once(CLASSES_DIR.OS.'class.selection.php');
require_once(CLASSES_DIR.OS.'class.sort.php');
require_once('class_eventItem.php');

class event extends AbstractEvtClass {

	private $_instanceLabel;
	private $_group_1;

	private $_optionsValue;
	private $_viewList_title, $_card_title, $_searchPage_title, $_viewCal_title;
	private $_firstDayMonday, $_dayChars, $_modeView, $_items_for_page, $_char_summary, $_eventLayer;
	private $_manageCtg, $_manageSel, $_manageNewsl, $_manageSort;
	private $_winWidth, $_winHeight;
	private $_months, $_days, $_daysMonday;
	private $_randomViewer_title, $_randomViewer_num;
	private $_selectedViewerA_title, $_selectedViewerA_num, $_selectedViewerB_title, $_selectedViewerB_num;
	private $_personalizedViewer_title, $_personalizedViewer_num;
	private $_archiveViewer_title;
	private $_ctgViewerA_id, $_ctgViewerA_num, $_ctgViewerA_pag;
	private $_ctgViewerB_id, $_ctgViewerB_num, $_ctgViewerB_pag;
	private $_ctgViewerC_id, $_ctgViewerC_num, $_ctgViewerC_pag;
	private $_ctgViewerD_id, $_ctgViewerD_num, $_ctgViewerD_pag;

	private $_event_dir, $_event_www, $_event_sub, $_js_file, $_css_id;
	private $_view_without_search;
	private $_prefix_img, $_prefix_thumb, $_img_width, $_thumb_width;
	private $_block_sel_a, $_block_sel_b, $_block_newsl;
	private $_list_complete;

	private $_tbl_opt, $_tbl_usr;

	function __construct($mdlId) {

		parent::__construct();
		
		$this->_instance = $mdlId;
		$this->_instanceName = $this->_db->getFieldFromId($this->_tbl_module, 'name', 'id', $this->_instance);
		$this->_instanceLabel = $this->_db->getFieldFromId($this->_tbl_module, 'label', 'id', $this->_instance);

		$this->_tbl_opt = 'event_opt';
		$this->_tbl_usr = 'event_usr';
		
		$this->setAccess();
		$this->setGroups();
		
		// Path/Files
		$this->_event_dir = $this->_data_dir.$this->_os.$this->_instanceName;
		$this->_event_www = $this->_data_www."/".$this->_instanceName;
		$this->_event_sub = array('img', 'doc');	// dir img -> field image, dir doc -> field attachment (pathDirectory())
		$this->_js_file = 'event.js';
		$this->_css_id = 'event';
		
		// Options
		
		// Default values
		$this->_optionsValue = array(
			'viewList_title'=>_("Lista eventi"),
			'card_title'=>_("Dettaglio evento"),
			'searchPage_title'=>_("Ricerca eventi"),
			'first_day_monday'=>1,
			'wide_view_position'=>1, 
			'manage_ctg'=>false, 
			'manage_sel'=>false, 
			'manage_newsl'=>false, 
			'manage_sort'=>false,
			'items_for_page'=>10, 
			'char_summary'=>300, 
			'img_width'=>500, 
			'thumb_width'=>100, 
			'eventLayer'=>true, 
			'winWidth'=>600, 
			'winHeight'=>400, 
			'randomViewer_num'=>3, 
			'selectedViewerA_num'=>3,
			'selectedViewerB_num'=>3,
			'personalizedViewer_num'=>0
		);
		
		$this->_viewList_title = $this->setOption('viewList_title', array('value'=>$this->_optionsValue['viewList_title']));
		$this->_card_title = $this->setOption('card_title', array('value'=>$this->_optionsValue['card_title']));
		$this->_searchPage_title = $this->setOption('searchPage_title', array('value'=>$this->_optionsValue['searchPage_title']));
		$this->_viewCal_title = $this->setOption('viewCal_title');
		$this->_firstDayMonday = $this->setOption('first_day_monday');
		$this->_dayChars = $this->setOption('day_chars');
		$this->_wideView = $this->setOption('wide_view');
		$this->_wideViewPosition = in_array($this->setOption('wide_view_position'), array(1,2,3,4)) ? $this->setOption('wide_view_position') : $this->_optionsValue['wide_view_position'];
		$this->_manageCtg = $this->setOption('manage_ctg', array('value'=>$this->_optionsValue['manage_ctg']));
		$this->_manageSel = $this->setOption('manage_sel', array('value'=>$this->_optionsValue['manage_sel']));
		$this->_manageNewsl = $this->setOption('manage_newsl', array('value'=>$this->_optionsValue['manage_newsl']));
		$this->_manageSort = $this->setOption('manage_sort', array('value'=>$this->_optionsValue['manage_sort']));
		$this->_items_for_page = $this->setOption('items_for_page', array('value'=>$this->_optionsValue['items_for_page']));
		$this->_char_summary = $this->setOption('char_summary', array('value'=>$this->_optionsValue['char_summary']));
		$this->_img_width = $this->setOption('img_width', array('value'=>$this->_optionsValue['img_width']));
		$this->_thumb_width = $this->setOption('thumb_width', array('value'=>$this->_optionsValue['thumb_width']));
		$this->_eventLayer = $this->setOption('eventLayer', array('value'=>$this->_optionsValue['eventLayer']));
		$this->_winWidth = $this->setOption('winWidth', array('value'=>$this->_optionsValue['winWidth']));
		$this->_winHeight = $this->setOption('winHeight', array('value'=>$this->_optionsValue['winHeight']));
		$this->_randomViewer_title = $this->setOption('randomViewer_title', true);
		$this->_randomViewer_num = $this->setOption('randomViewer_num', array('value'=>$this->_optionsValue['randomViewer_num']));
		$this->_selectedViewerA_title = $this->setOption('selectedViewerA_title', true);
		$this->_selectedViewerA_num = $this->setOption('selectedViewerA_num', array('value'=>$this->_optionsValue['selectedViewerA_num']));
		$this->_selectedViewerB_title = $this->setOption('selectedViewerB_title', true);
		$this->_selectedViewerB_num = $this->setOption('selectedViewerB_num', array('value'=>$this->_optionsValue['selectedViewerB_num']));
		$this->_personalizedViewer_title = $this->setOption('personalizedViewer_title', true);
		$this->_personalizedViewer_num = $this->setOption('personalizedViewer_num', array('value'=>$this->_optionsValue['personalizedViewer_num']));
		$this->_archiveViewer_title = $this->setOption('archiveViewer_title', true);
		$this->_ctgViewerA_id = $this->setOption('ctgViewerA_id');
		$this->_ctgViewerA_title = $this->setOption('ctgViewerA_title', true);
		$this->_ctgViewerA_num = $this->setOption('ctgViewerA_num') == 0 ? $this->_items_for_page : $this->setOption('ctgViewerA_num');
		$this->_ctgViewerA_pag = $this->setOption('ctgViewerA_pag');
		$this->_ctgViewerB_id = $this->setOption('ctgViewerB_id');
		$this->_ctgViewerB_title = $this->setOption('ctgViewerB_title', true);
		$this->_ctgViewerB_num = $this->setOption('ctgViewerB_num') == 0 ? $this->_items_for_page : $this->setOption('ctgViewerB_num');
		$this->_ctgViewerB_pag = $this->setOption('ctgViewerB_pag');
		$this->_ctgViewerC_id = $this->setOption('ctgViewerC_id');
		$this->_ctgViewerC_title = $this->setOption('ctgViewerC_title', true);
		$this->_ctgViewerC_num = $this->setOption('ctgViewerC_num') == 0 ? $this->_items_for_page : $this->setOption('ctgViewerC_num');
		$this->_ctgViewerC_pag = $this->setOption('ctgViewerC_pag');
		$this->_ctgViewerD_id = $this->setOption('ctgViewerD_id');
		$this->_ctgViewerD_title = $this->setOption('ctgViewerD_title', true);
		$this->_ctgViewerD_num = $this->setOption('ctgViewerD_num') == 0 ? $this->_items_for_page : $this->setOption('ctgViewerD_num');
		$this->_ctgViewerD_pag = $this->setOption('ctgViewerD_pag');
		
		$this->_options = new options($this->_className, $this->_instance);
		$this->_optionsLabels = array(
			"viewList_title"=>array('label'=>array(_("Titolo pagina elenco eventi"), _("'NULL': nessun titolo")), 'value'=>$this->_optionsValue['viewList_title']),
			"card_title"=>array('label'=>array(_("Titolo scheda evento"), _("'NULL': nessun titolo")), 'value'=>$this->_optionsValue['card_title']),
			"searchPage_title"=>array('label'=>array(_("Titolo ricerca eventi"), _("'NULL': nessun titolo")), 'value'=>$this->_optionsValue['searchPage_title']),
			"viewCal_title"=>array('label'=>array(_("[Calendario] Titolo pagina"), _("'NULL': nessun titolo"))),
			"first_day_monday"=>array('label'=>array(_("[Calendario] Setta primo giorno della settimana a lunedì"), _("'no': setta primo giorno della settimana a domenica")), 'value'=>$this->_optionsValue['first_day_monday']), 
			"day_chars"=>array('label'=>array(_("[Calendario] Numero caratteri giorni"), _("3 = lun, mar, ..."))), 
			"wide_view"=>array('label'=>array(_("[Calendario] Visualizzazione completa"), _("'si': eventi + calendario<br/>'no': solo calendario"))), 
			"wide_view_position"=>array('label'=>array(_("[Calendario] Posizione elementi visualizzazione completa"), _("1: lista eventi sx - calendario dx<br/>2: calendario sx - lista eventi dx<br/>3: lista eventi sopra - calendario sotto<br/>4: calendario sopra - lista eventi sotto")), 'value'=>$this->_optionsValue['wide_view_position']), 
			"manage_ctg"=>array('label'=>_("Abilita categorie"), 'value'=>$this->_optionsValue['manage_ctg']), 
			"manage_sel"=>array('label'=>_("Abilita selezione degli eventi"), 'value'=>$this->_optionsValue['manage_sel']),
			"manage_newsl"=>array('label'=>_("Abilita la newsletter"), 'value'=>$this->_optionsValue['manage_newsl']),
			"manage_sort"=>array('label'=>_("Abilita ordinamento"), 'value'=>$this->_optionsValue['manage_sort']),
			"items_for_page"=>array('label'=>_("Numero di eventi per pagina"), 'value'=>$this->_optionsValue['items_for_page']),
			"char_summary"=>array('label'=>_("Numero caratteri descrizione breve"), 'value'=>$this->_optionsValue['char_summary']),
			"img_width"=>array('label'=>_("Larghezza max immagini (px)"), 'value'=>$this->_optionsValue['img_width']), 
			"thumb_width"=>array('label'=>_("Larghezza max thumbs delle immagini (px)"), 'value'=>$this->_optionsValue['thumb_width']), 
			"eventLayer"=>array('label'=>array(_("Visualizzazione evento completo"), _("'si': apertura in finestra<br/>'no': apertura nella  pagina")), 'value'=>$this->_optionsValue['eventLayer']),
			"winWidth"=>array('label'=>array(_("Larghezza finestra (px)"), _("attiva solo se si setta a 'si' l'opzione precedente")), 'value'=>$this->_optionsValue['winWidth']),
			"winHeight"=>array('label'=>array(_("Altezza finestra (px)"), _("attiva solo se si setta a 'si' l'opzione precedente")), 'value'=>$this->_optionsValue['winHeight']),
			"randomViewer_title"=>array('label'=>array(_("Titolo pagina eventi random"), _("'NULL': nessun titolo"))),
			"randomViewer_num"=>array('label'=>_("Numero eventi random per pagina"), 'value'=>$this->_optionsValue['randomViewer_num']),
			"selectedViewerA_title"=>array('label'=>array(_("Eventi selezionati A - titolo pagina"), _("'NULL': nessun titolo"))),
			"selectedViewerA_num"=>array('label'=>array(_("Eventi selezionati A - numero eventi per pagina"),_("'0': illimitati")), 'value'=>$this->_optionsValue['selectedViewerA_num']),
			"selectedViewerB_title"=>array('label'=>array(_("Eventi selezionati B - titolo pagina"), _("'NULL': nessun titolo"))),
			"selectedViewerB_num"=>array('label'=>array(_("Eventi selezionati B - numero eventi per pagina"),_("'0': illimitati")), 'value'=>$this->_optionsValue['selectedViewerB_num']),
			"personalizedViewer_title"=>array('label'=>array(_("Titolo pagina eventi personalizzati"), _("'NULL': nessun titolo"))),
			"personalizedViewer_num"=>array('label'=>array(_("Numero eventi personalizzati per pagina"),_("'0': illimitati")), 'value'=>$this->_optionsValue['personalizedViewer_num']),
			"archiveViewer_title"=>array('label'=>array(_("Titolo pagina eventi archiviati"), _("'NULL': nessun titolo<br />Visualizza gli eventi a partire dal giorno precedente"))),
			"ctgViewerA_id"=>array('label'=>array(_("Categoria A - id"), _("indicare il codice identificativo della categoria"))),
			"ctgViewerA_title"=>array('label'=>array(_("Categoria A - titolo pagina"), _("'NULL': nessun titolo"))),
			"ctgViewerA_num"=>array('label'=>_("Categoria A - numero eventi per pagina")),
			"ctgViewerA_pag"=>array('label'=>array(_("Categoria A - paginazione eventi"), _("'no': visualizza solo gli eventi indicati nell'opzione precedente"))),
			"ctgViewerB_id"=>array('label'=>array(_("Categoria B - id"), _("indicare il codice identificativo della categoria"))),
			"ctgViewerB_title"=>array('label'=>array(_("Categoria B - titolo pagina"), _("'NULL': nessun titolo"))),
			"ctgViewerB_num"=>array('label'=>_("Categoria B - numero eventi per pagina")),
			"ctgViewerB_pag"=>array('label'=>array(_("Categoria B - paginazione eventi"), _("'no': visualizza solo gli eventi indicati nell'opzione precedente"))),
			"ctgViewerC_id"=>array('label'=>array(_("Categoria C - id"), _("indicare il codice identificativo della categoria"))),
			"ctgViewerC_title"=>array('label'=>array(_("Categoria C - titolo pagina"), _("'NULL': nessun titolo"))),
			"ctgViewerC_num"=>array('label'=>_("Categoria C - numero eventi per pagina")),
			"ctgViewerC_pag"=>array('label'=>array(_("Categoria C - paginazione eventi"), _("'no': visualizza solo gli eventi indicati nell'opzione precedente"))),
			"ctgViewerD_id"=>array('label'=>array(_("Categoria D - id"), _("indicare il codice identificativo della categoria"))),
			"ctgViewerD_title"=>array('label'=>array(_("Categoria D - titolo pagina"), _("'NULL': nessun titolo"))),
			"ctgViewerD_num"=>array('label'=>_("Categoria D - numero eventi per pagina")),
			"ctgViewerD_pag"=>array('label'=>array(_("Categoria D - paginazione eventi"), _("'no': visualizza solo gli eventi indicati nell'opzione precedente")))
		);
		
		$this->_view_without_search = false;	// mostrare gli eventi quando si accede alla pagina di ricerca
		
		$this->_months = array(1=>_("gennaio"), 2=>_("febbraio"), 3=>_("marzo"), 4=>_("aprile"), 5=>_("maggio"), 6=>_("giugno"), 7=>_("luglio"), 8=>_("agosto"), 9=>_("settembre"), 10=>_("ottobre"), 11=>_("novembre"), 12=>_("dicembre"));
		$this->_days = array(0=>_("domenica"), 1=>_("lunedì"), 2=>_("martedì"), 3=>_("mercoledì"), 4=>_("giovedì"), 5=>_("venerdì"), 6=>_("sabato"));
		$this->_daysMonday = array(0=>_("lunedì"), 1=>_("martedì"), 2=>_("mercoledì"), 3=>_("giovedì"), 4=>_("venerdì"), 5=>_("sabato"), 6=>_("domenica"));

		$this->_prefix_img = "img_";
		$this->_prefix_thumb = "thumb_";
		
		$this->_block_sel_a = 'listA';
		$this->_block_sel_b = 'listB';
		$this->_block_newsl = 'newsletter';
		$this->_list_complete = 'sel';	// elenco non paginato di elementi
		
		$this->_action = cleanVar($_REQUEST, 'action', 'string', ''); 
		$this->_block = cleanVar($_REQUEST, 'block', 'string', ''); 
	}

	private function setGroups(){
		
		// visualizza eventi privati
		$this->_group_1 = array($this->_list_group[0], $this->_list_group[1]);
	}
	
	public static function getClassElements() {

		return array("tables"=>array('event', 'event_ctg', 'event_opt', 'event_grp', 'event_usr', 'event_sel', 'event_sel_b'),
			     "css"=>array('event.css'),
			     "folderStructure"=>array(
				     CONTENT_DIR.OS.'event'=>array()
	     		)
			);
	}
	
	public function deleteInstance() {

		$this->accessGroup('');

		/*
		 * delete records and translations from table events
		 */
		$query = "SELECT id FROM ".eventItem::$_tbl_item." WHERE instance='$this->_instance'";
		$a = $this->_db->selectquery($query);
		if(sizeof($a)>0) 
			foreach($a as $b) 
				language::deleteTranslations(eventItem::$_tbl_item, $b['id']);
		
		$query = "DELETE FROM ".eventItem::$_tbl_item." WHERE instance='$this->_instance'";	
		$result = $this->_db->actionquery($query);
		
		/*
		 * delete record and translations from table events categories
		 */
		$query = "SELECT id FROM ".eventCtg::$_tbl_ctg." WHERE instance='$this->_instance'";
		$a = $this->_db->selectquery($query);
		if(sizeof($a)>0) {
			foreach($a as $b) {
				language::deleteTranslations(eventCtg::$_tbl_ctg, $b['id']);
			}
		}
		$query = "DELETE FROM ".eventCtg::$_tbl_ctg." WHERE instance='$this->_instance'";	
		$result = $this->_db->actionquery($query);

		/*
		 * delete record and translation from table calendar_opt
		 */
		$opt_id = $this->_db->getFieldFromId($this->_tbl_opt, "id", "instance", $this->_instance);
		language::deleteTranslations($this->_tbl_opt, $opt_id);
		
		$query = "DELETE FROM ".$this->_tbl_opt." WHERE instance='$this->_instance'";	
		$result = $this->_db->actionquery($query);
		
		/*
		 * delete group users association
		 */
		$query = "DELETE FROM ".$this->_tbl_usr." WHERE instance='$this->_instance'";	
		$result = $this->_db->actionquery($query);

		/*
		 * delete css files
		 */
		$classElements = $this->getClassElements();
		foreach($classElements['css'] as $css) {
			unlink(APP_DIR.OS.$this->_className.OS.baseFileName($css)."_".$this->_instanceName.".css");
		}

		/*
		 * delete folder structure
		 */
		foreach($classElements['folderStructure'] as $fld=>$fldStructure) {
			$this->deleteFileDir($fld.OS.$this->_instanceName, true);
		}

		return $result;
	}

	public static function outputFunctions() {

		$list = array(
			"viewCal" => array("label"=>_("Calendario"), "role"=>'1'),
			"viewList" => array("label"=>_("Lista eventi futuri o in via di svolgimento"), "role"=>'1'),
			"viewRandom" => array("label"=>_("Lista eventi random"), "role"=>'1'),
			"viewSelected" => array("label"=>_("A - Lista eventi selezionati"), "role"=>'1'),
			"viewSelectedB" => array("label"=>_("B - Lista eventi selezionati"), "role"=>'1'),
			"viewPersonalized" => array("label"=>_("Lista eventi della settimana"), "role"=>'1'),
			"viewArchive" => array("label"=>_("Lista eventi archiviati"), "role"=>'1'),
			"searchItems" => array("label"=>_("Ricerca eventi"), "role"=>'1'),
			"viewCtgA" => array("label"=>_("A - Lista eventi per categoria"), "role"=>'1'),
			"viewCtgB" => array("label"=>_("B - Lista eventi per categoria"), "role"=>'1'),
			"viewCtgC" => array("label"=>_("C - Lista eventi per categoria"), "role"=>'1'),
			"viewCtgD" => array("label"=>_("D - Lista eventi per categoria"), "role"=>'1')
		);

		return $list;
	}
	
	public function getInstance() {

		return $this->_instance;
	}

	public function getEventWWW($id) {

		return $this->pathDirectory($id, 'rel', 'image');
	}
	
	public function getPrefixImg() {

		return $this->_prefix_img;
	}
	
	public function getPrefixThumb() {

		return $this->_prefix_thumb;
	}
	
	private function pathBaseDir($id, $type){
		
		if($type == 'abs')
			$directory = $this->_event_dir.$this->_os.$id.$this->_os;
		elseif($type == 'rel')
			$directory = $this->_event_www.'/'.$id.'/';
		else $directory = '';
		
		return $directory;
	}
	
	/**
	 * Percorso delle directory
	 *
	 * @param integer $id			record ID
	 * @param string $type			abs | rel
	 * @param string $field			field name
	 * @return string
	 */
	private function pathDirectory($id, $type, $field=''){
	
		$directory = $this->pathBaseDir($id, $type);
		
		if($field == 'image') $sub = $this->_event_sub[0];
		elseif ($field == 'attachment') $sub = $this->_event_sub[1];
		else $sub = '';
		
		if($type == 'abs')
		{
			if(!empty($sub)) $sub = $sub.$this->_os;
			$directory = $directory.$sub;
		}
		elseif($type == 'rel')
		{
			if(!empty($sub)) $sub = $sub.'/';
			$directory = $directory.$sub;
		}
		
		return $directory;
	}
	
	public function downloader(){
		
		$this->accessType($this->_access_base);
		
		$id = cleanVar($_GET, 'id', 'int', '');
		$field = cleanVar($_GET, 'fld', 'string', '');
		
		if(!empty($id) AND !empty($field))
		{
			$query = "SELECT $field FROM ".eventItem::$_tbl_item." WHERE id='$id'";
			$a = $this->_db->selectquery($query);
			if(sizeof($a) > 0)
			{
				foreach($a AS $b)
				{
					$filename = $b[$field];
					$full_path = $this->pathDirectory($id, 'abs', $field).$filename;
					download($full_path);
					exit();
				}
			}
			else exit();
		}
		else exit();
	}
	
	private function listLine($count, $tot) {

		if($count >= $tot) return '';
		$buffer = "<div class=\"listLine\">";
		$buffer .= "<div class=\"lineLeft\"></div>";
		$buffer .= "<div class=\"lineCenter\"></div>";
		$buffer .= "<div class=\"lineRight\"></div>";
		$buffer .= "<div class=\"null\"></div>";
		$buffer .= "</div>\n";

		return $buffer;
	}
	
	/*
	 * SEZIONE definizione delle query
	 */
	private function valueOption($name, $array, $default=null){
		
		if(isset($_REQUEST[$name]) AND !empty($_REQUEST[$name]))
			return $_REQUEST[$name];
		elseif(is_array($array) AND isset($array[$name]))
			return $array[$name];
		else
			return $default;
	}
	
	// Opzioni -> per aggiungere una condizione: $options['new'] = $new;
	private function setValueOptions($options=array()){
		
		$array = array();
		
		$array['fromDate'] = $this->valueOption('fromDate', $options);
		$array['toDate'] = $this->valueOption('toDate', $options);
		$array['sort'] = $this->valueOption('sort', $options, 'DESC');
		$array['start'] = $this->valueOption('start', $options, 0);
		$array['range'] = $this->valueOption('range', $options, $this->_items_for_page);
		
		$array['enc'] = $this->valueOption('enc', $options, 0);
		if($array['enc'] == 1)
			$array['where'] = base64_decode($this->valueOption('where', $options));
		else
			$array['where'] = $this->valueOption('where', $options);
		
		return $array;
	}
	
	// parametri per i link (ad es. per paginazione) -> implode('&amp;', $postvar)
	private function setPostVar($options=array()){
		
		$postvar = array();
		
		if(isset($options['where']) AND !empty($options['where'])) {
			$postvar[] = 'where='.base64_encode(($options['where']));
			$postvar[] = 'enc=1';
		}
		else {
			if(isset($options['fromDate']) AND !empty($options['fromDate']))
				$postvar[] = 'fromDate='.$options['fromDate'];
			if(isset($options['toDate']) AND !empty($options['toDate']))
				$postvar[] = 'toDate='.$options['toDate'];
		}
		
		if(isset($options['sort']) AND !empty($options['sort']))
			$postvar[] = 'sort='.$options['sort'];
		
		return $postvar;
	}
	// Fine SEZIONE
	
	public function viewItem(){
	
		$this->accessType($this->_access_base);
		
		$id = cleanVar($_GET, 'id', 'int', '');

		$evt = new eventItem($id);
		$title = htmlChars($evt->ml('name'));

		if((!$this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_1) && $evt->private=='yes') || !$evt->id) exit('');

		$htmlsection = new htmlSection(array('id'=>"item_".$this->_css_id."_".$this->_instanceName,'class'=>'public', 'headerTag'=>'header', 'headerLabel'=>$title));

		$GINO = $this->scriptAsset($this->_css_id."_".$this->_instanceName.".css", "calCSS$this->_instance", 'css');
		$GINO .= $this->scriptAsset($this->_js_file, "calJs", 'js');
		$GINO .= $evt->show(array("interface"=>$this->_instanceName, "folder"=>$this->pathDirectory($id, 'rel', 'image'), "prefix_img"=>$this->_prefix_img, "prefix_thumb"=>$this->_prefix_thumb, "manage_ctg"=>$this->_manageCtg));

		$htmlsection->content = $GINO;

		return $htmlsection->render();
	}
	
	public function viewList(){
	
		$this->accessType($this->_access_base);
		
		$GINO = $this->scriptAsset($this->_css_id."_".$this->_instanceName.".css", "calCSS$this->_instance", 'css');
		$GINO .= $this->scriptAsset($this->_js_file, "calJs", 'js');

		$htmlsection = new htmlSection(array('id'=>"list_".$this->_css_id."_".$this->_instanceName,'class'=>'public', 'headerTag'=>'header', 'headerLabel'=>($this->_viewList_title!='NULL'?$this->_viewList_title:"")));

		$options = $this->setValueOptions(array('fromDate'=>date("Y-m-d")));
		$GINO .= "<div id=\"cal_list$this->_instance\">".$this->ajaxList($options)."</div>";

		$htmlsection->content = $GINO;

		return $htmlsection->render();
	}
	
	public function ajaxList($options=array()) {
		
		$GINO = '';
		
		if(empty($options)) $options = $this->setValueOptions();
		
		$private = ($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_1))?true:false;
		$options['private'] = $private;	// aggiungo la condizione
		
		$postvar = $this->setPostVar();
		
		$tot_items = eventItem::getTotItems($this->_instance, $options);
		$list = new PageList($this->_items_for_page, $tot_items, 'array');
		
		if($tot_items > 0)
		{
			$htmlList = new htmlList(array("numItems"=>$tot_items, "separator"=>true));
			$GINO .= $htmlList->start();
			
			$items = eventItem::getOrderedItems($this->_instance, $options);
			
			foreach($items as $item) {
		
				$GINO .= $htmlList->item($this->itemList($item), null, null, true);
			}
			$GINO .= $htmlList->end();
			
			$GINO .= $list->listReferenceGINO("pt[$this->_instanceName-ajaxList]", true, implode('&amp;', $postvar), "cal_list$this->_instance", "cal_list$this->_instance", true, 'updateTooltips');
		}
		else {
			$GINO .= _("La ricerca non ha prodotto risultati.");
		}
		
		return $GINO;
	}
	
	public function searchItems(){
		
		$this->accessType($this->_access_base);
		
		$GINO = $this->scriptAsset($this->_css_id."_".$this->_instanceName.".css", "calCSS$this->_instance", 'css');
		$GINO .= $this->scriptAsset($this->_js_file, "calJs", 'js');

		$htmlsection = new htmlSection(array('id'=>"list_".$this->_css_id."_".$this->_instanceName,'class'=>'public', 'headerTag'=>'header', 'headerLabel'=>($this->_searchPage_title!='NULL'?$this->_searchPage_title:"")));

		$item = new eventItem(null);
		$where = $item->dataSearch($this->_instanceName);
		$options = $this->setValueOptions(array('where'=>$where));
		
		$GINO .= $item->formSearch($this->_instance, $this->_home."?evt[{$this->_instanceName}-searchItems]", array('section'=>true));
		
		$GINO .= "<div id=\"cal_list$this->_instance\">";
		if($this->_view_without_search)
		{
			$GINO .= $this->ajaxList($options);
		}
		else
		{
			if(!empty($where)) $GINO .= $this->ajaxList($options);
		}
		$GINO .= "</div>";

		$htmlsection->content = $GINO;

		return $htmlsection->render();
	}
	
	public function viewRandom() {
		
		$this->accessType($this->_access_base);

		$htmlsection = new htmlSection(array('id'=>"rnd_".$this->_css_id."_".$this->_instanceName,'class'=>'public', 'headerTag'=>'header', 'headerLabel'=>($this->_randomViewer_title!='NULL'?$this->_randomViewer_title:"")));

		$GINO = $this->scriptAsset($this->_css_id."_".$this->_instanceName.".css", "calCSS$this->_instance", 'css');
		$GINO .= javascript::abiMapLib();
		
		$rnd_evts = array();
		$choosen = array();
		$evts = eventItem::getOrderedItems($this->_instance, array('fromDate'=>date("Y-m-d")));
		if(count($evts)<$this->_randomViewer_num) $this->_randomViewer_num = count($evts);
		$c = 0;
		while($c<$this->_randomViewer_num) {
		
			$rnd_evts[$c] = $evts[rand(0,count($evts)-1)];
			if(!in_array($rnd_evts[$c]->id, $choosen)) {$choosen[] = $rnd_evts[$c]->id; $c++;}
		}

		$i=0;
		foreach($rnd_evts as $rnd_evt) {
			
			$GINO .= $this->itemCard($rnd_evt);
			$i++;
		}

		$htmlsection->content = $GINO;

		return $htmlsection->render();
	}
	
	public function viewSelected() {
		
		$this->accessType($this->_access_base);
		
		$htmlsection = new htmlSection(array('id'=>"rnd_".$this->_css_id."_".$this->_instanceName,'class'=>'public', 'headerTag'=>'header', 'headerLabel'=>($this->_selectedViewerA_title!='NULL'?$this->_selectedViewerA_title:"")));

		$GINO = $this->scriptAsset($this->_css_id."_".$this->_instanceName.".css", "calCSS$this->_instance", 'css');
		$GINO .= javascript::abiMapLib();
		
		$sels = new selection(0, 0, $this->_instance, eventItem::$_tbl_selection);
		$events = $sels->getListItems(array('class'=>'eventItem'));
		if(empty($this->_selectedViewerA_num)) $end = count($events);
		elseif(count($events)<$this->_selectedViewerA_num) $end = count($events);
		else $end = $this->_selectedViewerA_num;
		
		for($i=0;$i<$end;$i++)
		{
			if(!isset($events[$i])) break;
			$evt = $events[$i];
			$GINO .= $this->itemCard($evt);
			
			$GINO .= $this->listLine($i+1, $end);
		}

		$htmlsection->content = $GINO;

		return $htmlsection->render();
	}
	
	public function viewSelectedB() {

		$this->accessType($this->_access_base);
		
		$htmlsection = new htmlSection(array('id'=>"rndSelB_".$this->_css_id."_".$this->_instanceName,'class'=>'public', 'headerTag'=>'header', 'headerLabel'=>($this->_selectedViewerB_title!='NULL'?$this->_selectedViewerB_title:"")));

		$GINO = $this->scriptAsset($this->_css_id."_".$this->_instanceName.".css", "calCSS$this->_instance", 'css');
		$GINO .= javascript::abiMapLib();
		
		$sels = new selection(0, 0, $this->_instance, eventItem::$_tbl_selection_b);
		$events = $sels->getListItems(array('class'=>'eventItem'));
		if(empty($this->_selectedViewerB_num)) $end = count($events);
		elseif(count($events)<$this->_selectedViewerB_num) $end = count($events);
		else $end = $this->_selectedViewerB_num;
		
		if($end > 0)
		{
			$GINO .= $this->listLine(0, 1);
			$GINO .= "<table>\n";
			$GINO .= "<tr>";
			$width = (int)(100/$end);
			
			for($i=0;$i<$end;$i++)
			{
				if(!isset($events[$i])) break;
				
				$mod_style = $i == $end-1 ? "style=\"border: none;\"" : '';
				$GINO .= "<td width=\"$width\" $mod_style>";
				
				$evt = $events[$i];
				$GINO .= $this->itemCard($evt, array('view_ctg'=>false, 'resize'=>80));
				
				$GINO .= "</td>\n";
			}
			$GINO .= "</tr>\n";
			$GINO .= "</table>\n";
		}

		$htmlsection->content = $GINO;

		return $htmlsection->render();
	}
	
	// Eventi della settimana
	public function viewPersonalized() {
		
		$this->accessType($this->_access_base);
		
		$htmlsection = new htmlSection(array('id'=>"list_".$this->_css_id."_".$this->_instanceName,'class'=>'public', 'headerTag'=>'header', 'headerLabel'=>($this->_personalizedViewer_title!='NULL'?$this->_personalizedViewer_title:"")));

		$GINO = $this->scriptAsset($this->_css_id."_".$this->_instanceName.".css", "calCSS$this->_instance", 'css');
		
		//$nextWeek = time() + (7*24*60*60);
		//$nextWeek = date('Y-m-d', $nextWeek);
		// or using strtotime():
		$nextWeek = date('Y-m-d', strtotime('+1 week'));
		
		$events = eventItem::getOrderedItems($this->_instance, array('fromDate'=>date("Y-m-d"), 'toDate'=>$nextWeek));
		if(empty($this->_personalizedViewer_num)) $end = count($events);
		elseif(count($events)<$this->_personalizedViewer_num) $end = count($events);
		else $end = $this->_personalizedViewer_num;
		
		$GINO .= "<div id=\"cal_list$this->_instance\">";
		if(count($events) > 0)
		{
			$htmlList = new htmlList(array("numItems"=>$end, "separator"=>true));
			$GINO .= $htmlList->start();

			for($i=0;$i<$end;$i++)
			{
				if(!isset($events[$i])) break;
				$evt = $events[$i];
				
				$GINO .= $htmlList->item($this->itemList($evt), null, null, true);
			}
			
			$GINO .= $htmlList->end();
		}
		else
		{
			$GINO .= _("Non risultano elementi registrati");
		}
		$GINO .= "</div>";
		
		$htmlsection->content = $GINO;

		return $htmlsection->render();
	}
	
	public function viewArchive() {
		
		$this->accessType($this->_access_base);
		
		$GINO = $this->scriptAsset($this->_css_id."_".$this->_instanceName.".css", "calCSS$this->_instance", 'css');
		
		$htmlsection = new htmlSection(array('id'=>"list_".$this->_css_id."_".$this->_instanceName,'class'=>'public', 'headerTag'=>'header', 'headerLabel'=>($this->_archiveViewer_title!='NULL'?$this->_archiveViewer_title:"")));

		$prevDay = date('Y-m-d', strtotime('-1 day'));
		$options = $this->setValueOptions(array('toDate'=>$prevDay, 'sort'=>'DESC'));
		$GINO .= "<div id=\"cal_list$this->_instance\">".$this->ajaxList($options)."</div>";

		$htmlsection->content = $GINO;

		return $htmlsection->render();
	}
	
	/*
	 * Opzioni
	 * ---------------------
	 * view_ctg		boolean		visualizzare la categoria di un evento
	 * link_ctg		boolean		mostrare il link della categoria
	 * resize		integer		0: nessun ridimensionamento
	 */
	private function itemCard($evt, $options=array()){
		
		$view_ctg = array_key_exists('view_ctg', $options) ? $options['view_ctg'] : true;
		$link_ctg = array_key_exists('link_ctg', $options) ? $options['link_ctg'] : true;
		$resize = array_key_exists('resize', $options) ? $options['resize'] : 0;
		
		if(!$this->_manageCtg)
			$view_ctg = false;
		
		$GINO = '';
		
		if($evt->image)
		{
			if($resize > 0)
			{
				$image = $this->pathDirectory($evt->id, 'abs', 'image').$this->getPrefixThumb().$evt->image;
				list($im_width, $im_height, $type) = getimagesize($image);
				
				if($im_width > $im_height AND $im_width > $resize)
				{
					$width = $resize;
					$height = ($im_height / $im_width) * $resize;
					$add = "width=\"$width\" height=\"$height\"";
				}
				elseif($im_height > $im_width AND $im_height > $resize)
				{
					$width = ($im_width / $im_height) * $resize;
					$height = $resize;
					$add = "width=\"$width\" height=\"$height\"";
				}
				elseif($im_width == $im_height AND $im_width > $resize)
				{
					$width = $resize;
					$height = $resize;
					$add = "width=\"$width\" height=\"$height\"";
				}
				else
				{
					$width = $im_width;
					$height = $im_height;
					$add = '';
				}
			}
			else $add = '';
			
			$GINO .= "<div class=\"HL_media\">";
			$GINO .= "<div class=\"HL_evt_image\"><img src=\"".$this->getEventWWW($evt->id)."/".$this->getPrefixThumb().$evt->image."\" $add /></div>";
			$GINO .= "</div>";
		}
		
		$GINO .= "<div class=\"HL_content\">";
		
		if($view_ctg)
		{
			$ctg = new eventCtg($evt->ctg);
			
			if($link_ctg)
				$link_ctg = "<a href=\"".$ctg->link."\">".htmlChars($ctg->ml('name'))."</a>";
			else
				$link_ctg = htmlChars($ctg->ml('name'));
			
			if(!empty($link_ctg))
				$GINO .= "<div class=\"HL_evt_ctg\">$link_ctg</div> - ";
		}
		
		if($evt->hours != '00:00:00') $hours = " - "._("ore")." ".dbTimeToTime($evt->hours); else $hours = '';
		$GINO .= "<div class=\"HL_evt_date\">".dbDateToDate($evt->date, "/").$hours;
		$GINO .= "</div>";
		
		$GINO .= "<div class=\"HL_evt_name\">";
		
		if($this->_eventLayer) {
			$title_window = ($this->_card_title!='NULL') ? $this->_card_title : '';
			$url = $this->_home."?pt[$this->_instanceName-viewItem]&amp;id=".$evt->id;
			$close_button = $this->_img_www.'/ico_close2.gif';
			$GINO .= "<span class=\"link\" onclick=\"if(!window.myWin$evt->id || !window.myWin$evt->id.showing) {window.myWin$evt->id = new layerWindow({'title':'$title_window', 'url':'$url', 'bodyId':'event_$evt->id', 'width':$this->_winWidth, 'height':$this->_winHeight, 'destroyOnClose':true, 'closeButtonUrl':'$close_button'});window.myWin$evt->id.display($(this), {'left':getViewport().cX-".$this->_winWidth."/2, 'top':getViewport().cY-".$this->_winHeight."/2});}\" >".htmlChars($evt->ml('name'))."</span>";
		}
		else {
			$link = $this->_plink->aLink($this->_instanceName, 'viewItem', "id={$evt->id}");
			$GINO .= "<a href=\"$link\"><span>".htmlChars($evt->ml('name'))."</span></a>";
		}
		$GINO .= "</div>";
		
		if($evt->informations) $GINO .= "<div class=\"HL_evt_informations\">".htmlChars($evt->ml('informations'))."</div>";
		if($evt->summary) $GINO .= "<div class=\"HL_evt_description\">".htmlChars($evt->ml('summary'))."</div>";
		$GINO .= "</div>";
		$GINO .= "<div class=\"null\"></div>";
		
		return $GINO;
	}
	
	// evento in un elenco
	private function itemList($item){
		
		$ctg = new eventCtg($item->ctg);
		$ctg_name = htmlChars($ctg->ml('name'));
		if($ctg_name != '')
		{
			if($ctg->link != '') $ctg_name = "<a href=\"".$ctg->link."\">$ctg_name</a>";
			$ctg_name = ' - '.$ctg_name;
		}
		
		$evt_name = htmlChars($item->ml('name'));
		
		$title = "title=\"$evt_name\"";
		if($this->_eventLayer) {
			$title_window = ($this->_card_title!='NULL') ? $this->_card_title : '';
			$url = $this->_home."?pt[$this->_instanceName-viewItem]&amp;id=".$item->id;
			$close_button = $this->_img_www.'/ico_close2.gif';
			$link = "<span $title class=\"link tooltip\" onclick=\"if(!window.myWin$item->id || !window.myWin$item->id.showing) {window.myWin$item->id = new layerWindow({'title':'$title_window', 'url':'$url', 'bodyId':'event_$item->id', 'width':$this->_winWidth, 'height':$this->_winHeight, 'destroyOnClose':true, 'closeButtonUrl':'$close_button'});window.myWin$item->id.display($(this), {'left':getViewport().cX-".$this->_winWidth."/2, 'top':getViewport().cY-".$this->_winHeight."/2});}\" >$evt_name</span>";
		}
		else {
			$link = $this->_plink->aLink($this->_instanceName, 'viewItem', "id={$item->id}");
			$link = "<a href=\"$link\"><span $title class=\"tooltip\">$evt_name</span></a>";
		}

		$label = "<p><span class=\"date\">".dbDateToDate($item->date, "/")."</span>$ctg_name - $link</p>";
		return $label;
	}
	
	public function viewCtgA(){
	
		return $this->viewCtg("A");
	}
	
	public function viewCtgB(){
	
		return $this->viewCtg("B");
	}
	
	public function viewCtgC(){
	
		return $this->viewCtg("C");
	}
	
	public function viewCtgD(){
	
		return $this->viewCtg("D");
	}

	public function viewCtg($type){
	
		$this->accessType($this->_access_base);

		$ctg_id = $this->{'_ctgViewer'.$type.'_id'};
		$ctg = new eventCtg($ctg_id);

		$htmlsection = new htmlSection(array('id'=>"ctg".$type."_".$this->_css_id."_".$this->_instanceName,'class'=>'public', 'headerTag'=>'header', 'headerLabel'=>($this->{'_ctgViewer'.$type.'_title'}!='NULL'?$this->{'_ctgViewer'.$type.'_title'}:"")));

		$GINO = $this->scriptAsset($this->_css_id."_".$this->_instanceName.".css", "calCSS$this->_instance", 'css');
		$GINO .= javascript::abiMapLib();
		
		$GINO .= "<div id=\"cal_ctg".$type."_list$this->_instance\">".$this->ajaxCtgList($type)."</div>";

		$htmlsection->content = $GINO;

		return $htmlsection->render();
	}

	public function ajaxCtgList($type=null) {

		if(!$type) $type = cleanVar($_POST, 'type', 'string', '');
		
		$ctg_id = $this->{'_ctgViewer'.$type.'_id'};
		$ctg = new eventCtg($ctg_id);

		$private = ($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_1))?true:false;
		$evts = eventItem::getOrderedItems($this->_instance, array('ctg'=>$ctg, 'private'=>$private, 'fromDate'=>date("Y-m-d")));
		
		if(count($evts)) {
			$list = new pagelist($this->{'_ctgViewer'.$type.'_num'}, $evts, 'array');
			
			$end = $list->start()+$list->rangeNumber > count($evts) ? count($evts) : $list->start()+$list->rangeNumber;
			$htmlList = new htmlList(array("numItems"=>($end-$list->start()), "separator"=>true));
			$GINO = $htmlList->start();
			for($i=$list->start(); $i<$end; $i++) {
				$evt = $evts[$i];
				
				$ctg = new eventCtg($evt->ctg);
				$title = "title=\"".htmlChars($ctg->ml('name'))."\"";
				if($this->_eventLayer) {
					$url = $this->_home."?pt[$this->_instanceName-viewItem]&amp;id=".$evt->id;
					$close_button = $this->_img_www.'/ico_close2.gif';
					$link = "<span $title class=\"link tooltip\" onclick=\"if(!window.myWin$evt->id || !window.myWin$evt->id.showing) {window.myWin$evt->id = new layerWindow({'title':'"._("Dettagli evento")."', 'url':'$url', 'bodyId':'event_$evt->id', 'width':$this->_winWidth, 'height':$this->_winHeight, 'destroyOnClose':true, 'closeButtonUrl':'$close_button'});window.myWin$evt->id.display($(this), {'left':getViewport().cX-".$this->_winWidth."/2, 'top':getViewport().cY-".$this->_winHeight."/2});}\" >".htmlChars($evt->ml('name'))."</span>";
				}
				else {
					$link = $this->_plink->aLink($this->_instanceName, 'viewItem', "id={$evt->id}");
					$link = "<a href=\"$link\"><span $title class=\"tooltip\">".htmlChars($evt->ml('name'))."</span></a>";
				}

				$label = "<p><span class=\"date\">".dbDateToDate($evt->date, "/")."</span> - $link</p>";
				$GINO .= $htmlList->item($label, null, null, true);
			}
			$GINO .= $htmlList->end();
			
			if($this->{'_ctgViewer'.$type.'_pag'})
				$GINO .= $list->listReferenceGINO("pt[$this->_instanceName-ajaxCtgList]", true, "type=$type", "cal_ctg".$type."_list$this->_instance", "cal_ctg".$type."_list$this->_instance", true, 'updateTooltips');
		}
		else {
			$GINO .= "<p class=\"message\">"._("Non risultano elementi nella categoria")."</p>";
		}
		return $GINO;
	}
	
	// Calendario
	
	public function viewCal(){
	
		$this->accessType($this->_access_base);
		
		$htmlsection = new htmlSection(array('id'=>"cal_calendar_".$this->_instanceName,'class'=>'public', 'headerTag'=>'header', 'headerLabel'=>($this->_viewCal_title!='NULL'?$this->_viewCal_title:"")));

		$GINO = $this->scriptAsset($this->_css_id."_".$this->_instanceName.".css", "calCSS$this->_instance", 'css');
		$GINO .= $this->scriptAsset($this->_js_file, "calJs", 'js');
		$GINO .= javascript::abiMapLib();
		$GINO .= $this->render();

		$htmlsection->content = $GINO;

		return $htmlsection->render();
	}

	private function getDate($type='viewed') {
		date_default_timezone_set('Europe/Rome');
		$date_p = cleanVar($_REQUEST, 'date', 'string', '');
		if($date_p) {
			$datetime = new DateTime($date_p.' 00:00:00');
			$date = getDate($datetime->format('U'));
		}
		else $date = getDate();

		$vdate_p = cleanVar($_REQUEST, 'vdate', 'string', '');
		if($vdate_p) {
			$vdatetime = new DateTime($vdate_p.' 00:00:00');
			$vdate = getDate($vdatetime->format('U'));
		}
		else {
			$vdatetime = new DateTime(date('Y').'-'.date('m').'-01'.' 00:00:00');
			$vdate = getDate($vdatetime->format('U'));
		}

		return ($type=='selected')?$date:$vdate;
	}

	private function render() {
	
		$GINO = '';

		$l = "<td class=\"calendarLeft\">".$this->renderLeft()."</td>";
		$r = "<td class=\"calendarRight\">".$this->renderRight()."</td>";

		$GINO .= "<div class=\"calContainer\">";
		$GINO .= "<table class=\"calendarContainer\">";
		$GINO .= "<tr>";
		if(!$this->_wideView) $GINO .= $r;
		else {
			$GINO .= ($this->_wideViewPosition==1 || $this->_wideViewPosition == 3) ? $l:$r;
			if($this->_wideViewPosition==4 || $this->_wideViewPosition == 3) $GINO .= "</tr><tr>";
			$GINO .= ($this->_wideViewPosition==1 || $this->_wideViewPosition == 3) ? $r:$l;
		}
		$GINO .= "</tr>";
		$GINO .= "</table>";
		$GINO .= "</div>";

		return $GINO;
	}

	public function renderLeft() {

		$gform = new Form('gform', 'post', true, array("tblLayout"=>false));
		$selctg = cleanVar($_POST, 'ctg', 'int', '');

		$date = $this->getDate('selected');
		$GINO = "<div class=\"calendarActualDay\">";
		$GINO .= "<table>";
		$GINO .= "<tr>";
		$GINO .= "<td class=\"dayLetter\">".$this->_days[$date['wday']]."</td><td class=\"dayNumber\">".$date['mday']."</td>";
		$GINO .= "</tr>";
		$GINO .= "</table>";
		$GINO .= "</div>";

		$GINO .= "<div class=\"eventItems\">";
		$GINO .= "<div class=\"eventItemsTitle\">";
		if($this->_manageCtg) {
			$ctgs = eventCtg::getAll($this->_instance);
			if(count($ctgs)>0) {
				$select_ctg = array();
				foreach($ctgs as $ctg) $select_ctg[$ctg->id] = htmlInput($ctg->name);
				$month = ($date['mon']<10)?"0".$date['mon']:$date['mon'];
				$day = ($date['mday']<10)?"0".$date['mday']:$date['mday'];
				$onchange = "onchange=\"ajaxRequest('post', '$this->_home?pt[$this->_instanceName-getDateEvents]', 'ctg='+$(this).value+'&date={$date['year']}-$month-$day', this.getParent('div').getNext('div'), {'script':true, 'cache':true, 'callback':updateTooltips})\"";
				$GINO .= $gform->select('selctg', $selctg, $select_ctg, array("id"=>"selctg", "js"=>$onchange, "firstVoice"=>_("tutti"), "firstValue"=>"", "noFirst"=>true));
			}
			else 
				$GINO .= $gform->hidden('selctg', '', array("id"=>"selctg"));
		}
		else {
			$GINO .= $gform->hidden('selctg', '', array("id"=>"selctg"));
			$GINO .= _("eventi:");
		}
		$GINO .= "</div>";
		$GINO .= "<div class=\"eventItemsFieldWide\">";
		$GINO .= $this->getDateEvents();
		$GINO .= "</div>";
		$GINO .= "</div>";

		return $GINO;
	}

	public function renderRight() {
		
		$GINO = $this->topRender();
		$GINO .= $this->bodyRender();
		$GINO .= $this->bottomRender();

		return $GINO;
	}

	private function topRender() {
		
		$vdate = $this->getDate();
		$vdatetime = new DateTime(date("Y-m-d H:i:s", $vdate['0']));
		$vdatetime->modify("-1 month");
		$prev_date = $vdatetime->format("Y-m-d");
		$vdatetime->modify("+2 month");
		$next_date = $vdatetime->format("Y-m-d");
		$GINO = '';

		$GINO .= "<div>";
		$GINO .= "<table class=\"calendarTop\"><tr>\n";
		$onclick = "ajaxRequest('post', '$this->_home?pt[$this->_instanceName-renderRight]', 'vdate=".$prev_date."', $$('#cal_calendar_".$this->_instanceName." td[class=calendarRight]')[0], {'script':true, 'cache':true})";
		$GINO .= "<td class=\"previousMonth\" onclick=\"$onclick\"></td>\n";
		$GINO .= "<td>".$this->_months[$vdate['mon']]." ".$vdate['year']."</td>\n";
		$onclick = "ajaxRequest('post', '$this->_home?pt[$this->_instanceName-renderRight]', 'vdate=".$next_date."', $$('#cal_calendar_".$this->_instanceName." td[class=calendarRight]')[0], {'script':true, 'cache':true})";
		$GINO .= "<td class=\"nextMonth\" onclick=\"$onclick\"></td>\n";
		$GINO .= "</tr></table>";
		$GINO .= "</div>";

		return $GINO;
	}

	private function bodyRender() {

		$GINO = '';
	
		$GINO .= "<table class=\"calendarBody\">";
		$GINO .= $this->topBodyRender();
		$GINO .= $this->monthRender();
		$GINO .= "</table>";

		return $GINO;
	}

	private function bottomRender() {

		$GINO = '';

		$GINO .= "";

		return $GINO;
	}

	private function topBodyRender() {

		$GINO = '';

		$GINO .= "<tr>";
		$day_array = ($this->_firstDayMonday)?$this->_daysMonday:$this->_days;
		$i=0;
		foreach($day_array as $d) {$GINO .= (!$i)? "<td style=\"padding-top:0px;border-left:none\">".substr(ucfirst($d), 0, $this->_dayChars)."</td>\n":"<td style=\"padding-top:0px;\">".substr(ucfirst($d), 0, $this->_dayChars)."</td>\n";$i++;}
		$GINO .= "</tr>";
		return $GINO;
	}

	private function monthRender() {

		$date = $this->getDate('selected');
		$vdate = $this->getDate();
		$daysNumber = $this->getDaysNumber($vdate['mon'], $vdate['year']);
		$month_p = ($vdate['mon']<10)?"0".$vdate['mon']:$vdate['mon'];
		if($this->_firstDayMonday) 
			$start = ($vdate['wday']==0)?6:$vdate['wday']-1;
		else $start = $vdate['wday'];

		$GINO = "<tr>";
		for($i=0;$i<$start;$i++) $GINO .= (!$i)? "<td style=\"border-left:none\"></td>\n":"<td></td>\n";
		for($i=0;$i<7-$start;$i++) {
			$class = $this->classDate($i+1, $date, $vdate);
			$day_p = ($i+1<10)?"0".($i+1):($i+1); 
			$onclick = "onclick=\"onDayClickAction('".$this->_wideView."', '".$vdate['year']."-$month_p-$day_p', $(this), '$this->_instanceName')\"";
			$GINO .= ($start==0 && $i==0)? "<td style=\"border-left:none\"><div><div class=\"$class\" $onclick>".($i+1)."</div><div class=\"".$class."_h\"></div></div></td>\n":"<td><div><div class=\"$class\" $onclick>".($i+1)."</div><div class=\"".$class."_h\"></div></div></td>\n";
		}
		$GINO .= "</tr>";
		$GINO .= "<tr>";
		$chgrow = false;
		for($l=$i+1; $l<$daysNumber+1; $l++) {
			$chgrow = (($l-$i-1)%7==0)?true:false;
			$class = $this->classDate($l, $date, $vdate);
			$day_p = ($l<10)?"0".($l):$l; 
			$onclick = "onclick=\"onDayClickAction('".$this->_wideView."', '".$vdate['year']."-$month_p-$day_p', $(this), '$this->_instanceName')\"";
			if(($l-$i-1)%7==0 && $l!=$i+1) $GINO .= "</tr><tr>";
			$GINO .= ($chgrow)?"<td style=\"border-left:none\"><div><div class=\"$class\" $onclick>$l</div><div class=\"".$class."_h\"></div></div></td>\n":"<td><div><div class=\"$class\" $onclick>$l</div><div class=\"".$class."_h\"></div></div></td>\n";
		}
		for($ii=0;$ii<(7-($l-$i-1)%7);$ii++) $GINO .= ((7-($l-$i-1)%7)!=7)?"<td>&nbsp;</td>\n":"";
		$GINO .= "</tr>";

		return $GINO;
	}

	private function classDate($iter, $date, $vdate) {
		$private = ($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_1))?true:false;
		$type = array();
		if($iter==date('j') && $vdate['mon']==date('n') && $vdate['year']==date('Y'))  $type[] = 'today'; // data odierna
		$day = ($iter<10)?"0".$iter:$iter;
		$month = ($vdate['mon']<10)?"0".$vdate['mon']:$vdate['mon'];
		if(eventItem::getDateItems($this->_instance, $vdate['year']."-$month-$day", null, $private, true)) $type[] = 'event'; // eventi presenti
		if($iter==$date['mday'] && $date['mon']==$vdate['mon'] & $date['year']==$vdate['year'])	$type[] = 'sel'; // data selezionata
		$class = "day";
		foreach($type as $t) $class .= "_$t";
		return $class;
	}

	private function getDaysNumber($m, $y) {

		if(in_array($m, array(1,3,5,7,8,10,12))) return 31;
		elseif(in_array($m, array(4,6,9,11))) return 30;
		else return (($y%4==0 && $y%100!=0) || $y%400==0)? 29:28;	
	}

	// Eventi che sono in programma in un dato giorno
	public function getDateEvents() {

		$ctg = cleanVar($_POST, 'ctg', 'int', '');
		$date = cleanVar($_POST, 'date', 'string', '');
		if(!$date) $date = date("Y-m-d");

		$private = ($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_1))?true:false;
		$evts = eventItem::getDateItems($this->_instance, $date, $ctg, $private);

		if(count($evts)) {
			$GINO = "<ul>";
			foreach($evts as $evt) {
				$ctg = new eventCtg($evt->ctg);
				$title = "title=\"".htmlChars($ctg->ml('name'))."\"";
				if($this->_eventLayer) {
					
					$url = $this->_home."?pt[$this->_instanceName-viewItem]&amp;id=".$evt->id;
					$close_button = $this->_img_www.'/ico_close2.gif';
					$link = "<span $title class=\"link tooltip\" onclick=\"if(!window.myWin$evt->id || !window.myWin$evt->id.showing) {window.myWin$evt->id = new layerWindow({'title':'"._("Dettagli evento")."', 'url':'$url', 'bodyId':'event_$evt->id', 'width':$this->_winWidth, 'height':$this->_winHeight, 'destroyOnClose':true, 'closeButtonUrl':'$close_button', reloadZindex:true});window.myWin$evt->id.display($(this), {'left':getViewport().cX-".$this->_winWidth."/2, 'top':getViewport().cY-".$this->_winHeight."/2});}\" >".htmlChars($evt->ml('name'))."</span>";
				}
				else {
					$link = $this->_plink->aLink($this->_instanceName, 'viewItem', "id={$evt->id}");
					$link = "<a href=\"$link\"><span $title class=\"tooltip\">".htmlChars($evt->ml('name'))."</span></a>";
				}

				$GINO .= "<li>$link</li>";
			}
			$GINO .= "</ul>";
		}
		else {
			$GINO = "<p class=\"message\">"._("Non risultano elementi per la data selezionata")."</p>";
		}

		return $GINO;
	}
	// Fine Calendario
	
	public function manageDoc() {

		$this->accessGroup('');

		$htmltab = new htmlTab(array("linkPosition"=>'right', "title"=>$this->_instanceLabel));
		$link_admin = "<a href=\"".$this->_home."?evt[$this->_instanceName-manageDoc]&block=permissions\">"._("Permessi")."</a>";
		$link_css = "<a href=\"".$this->_home."?evt[$this->_instanceName-manageDoc]&block=css\">"._("CSS")."</a>";
		$link_options = "<a href=\"".$this->_home."?evt[$this->_instanceName-manageDoc]&block=options\">"._("Opzioni")."</a>";
		$link_newsletter = "<a href=\"".$this->_home."?evt[$this->_instanceName-manageDoc]&block=".$this->_block_newsl."\">"._("Newsletter")."</a>";
		$link_listA = "<a href=\"".$this->_home."?evt[$this->_instanceName-manageDoc]&block=".$this->_block_sel_a."\">"._("Selezione A")."</a>";
		$link_listB = "<a href=\"".$this->_home."?evt[$this->_instanceName-manageDoc]&block=".$this->_block_sel_b."\">"._("Selezione B")."</a>";
		$link_dft = "<a href=\"".$this->_home."?evt[".$this->_instanceName."-manageDoc]\">"._("Contenuti")."</a>";
		$sel_link = $link_dft;
		
		$ctg = cleanVar($_GET, 'ctg', 'int', '');
		$id = cleanVar($_GET, 'id', 'int', '');
		
		if($this->_block == 'css') {
			$GINO = sysfunc::manageCss($this->_instance, $this->_className);		
			$sel_link = $link_css;
		}
		elseif($this->_block == 'permissions' && $this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, '', '')) {
			$GINO = sysfunc::managePermissions($this->_instance, $this->_className);		
			$sel_link = $link_admin;
		}
		elseif($this->_block == 'options') {
			$GINO = sysfunc::manageOptions($this->_instance, $this->_className);		
			$sel_link = $link_options;
		}
		else {
			
			if($this->_block == $this->_block_newsl)
			{
				$form = $this->formList($this->_block);
				$sel_link = $link_newsletter;
			}
			elseif($this->_block == $this->_block_sel_a)
			{
				$form = $this->formList($this->_block);
				$sel_link = $link_listA;
			}
			elseif($this->_block == $this->_block_sel_b)
			{
				$form = $this->formList($this->_block);
				$sel_link = $link_listB;
			}
			else
			{
				if(($this->_action == $this->_act_insert || $this->_action == $this->_act_modify) && $this->_block == 'ctg') $form = $this->formCtg(new eventCtg($ctg)); 
				elseif($this->_action == $this->_act_delete && $this->_block == 'ctg') $form = $this->formDelCtg(new eventCtg($ctg));
				elseif($this->_action == $this->_act_insert || $this->_action==$this->_act_modify) $form = $this->formEvent(new eventItem($id)); 
				elseif($this->_action == $this->_act_delete) $form = $this->formDelEvent(new eventItem($id));
				else $form = $this->info();
			}

			$GINO = "<div class=\"vertical_1\">\n";
			if($this->_manageCtg)
				$GINO .= $this->listCtg();
			$GINO .= $this->listItems();
			$GINO .= "</div>\n";

			$GINO .= "<div class=\"vertical_2\">\n";
			$GINO .= $form;
			$GINO .= "</div>\n";
			$GINO .= "<div class=\"null\"></div>\n";
		}
		
		if($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, '', ''))
		{
			$links_array = array($link_admin, $link_css, $link_options);
			if($this->_manageNewsl)
				$links_array[] = $link_newsletter;
			if($this->_manageSel)
			{
				$links_array[] = $link_listB;
				$links_array[] = $link_listA;
			}
			
			$links_array[] = $link_dft;
		}
		else
		{
			$links_array = array($link_css, $link_options);
			if($this->_manageNewsl)
				$links_array[] = $link_newsletter;
			if($this->_manageSel)
			{
				$links_array[] = $link_listB;
				$links_array[] = $link_listA;
			}
			
			$links_array[] = $link_dft;
		}

		$htmltab->navigationLinks = $links_array;
		$htmltab->selectedLink = $sel_link;
		$htmltab->htmlContent = $GINO;
		return $htmltab->render();
	}

	private function formCtg($ctg) {

		$GINO = "<div class=\"area\">\n";
		$GINO .=  $ctg->formCtg($this->_home."?evt[$this->_instanceName-actionCtg]");
		$GINO .= "</div>\n";

		return $GINO;

	}

	private function formDelCtg($ctg) {

		$GINO = "<div class=\"area\">";
		$GINO .= $ctg->formDelCtg($this->_home."?evt[$this->_instanceName-actionDelCtg]");
		$GINO .= "</div>";

		return $GINO;
	}

	private function formEvent(eventInt $event) {

		$start = cleanVar($_GET, 'start', 'int', '');
		
		$GINO = "<div class=\"area\">\n";
		$GINO .=  $event->formItem($this->_home."?evt[$this->_instanceName-actionEvent]", $this, array('max_char'=>$this->_char_summary, 'start'=>$start));
		$GINO .= "</div>\n";

		return $GINO;
	}
	
	private function formDelEvent(eventInt $event) {

		$GINO = "<div class=\"area\">";
		$GINO .= $event->formDelItem($this->_home."?evt[$this->_instanceName-actionDelEvent]");
		$GINO .= "</div>";

		return $GINO;
	}
	
	private function listCtg() {
		
		$gform = new Form('gformc', 'post', true);
		$sel_ctg = cleanVar($_GET, 'ctg', 'int', '');

		$link_insert = "<a href=\"$this->_home?evt[$this->_instanceName-manageDoc]&amp;action=$this->_act_insert&amp;block=ctg\">".$this->icon('insert', _("nuova categoria"))."</a>";

		$htmlsection = new htmlSection(array('class'=>'admin', 'headerTag'=>'header', 'headerLabel'=>_("Gestione categorie"), 'headerLinks'=>$link_insert));

		$ctgs = eventCtg::getAll($this->_instance);

		if(count($ctgs)>0) {
			$select_ctg = array();
			foreach($ctgs as $ctg) $select_ctg[$ctg->id] = htmlChars($ctg->ml('name'))." - id:".$ctg->id;
			$GINO = "<div>";
			$GINO .= "<div class=\"left\">";
			$GINO .= $gform->select('formctg', $sel_ctg, $select_ctg, array("id"=>"formctg", 'maxChars'=>40, 'cutWords'=>false));
			$GINO .= "</div>";
			$GINO .= "<div class=\"right\">";
			$link_modify = "<span class=\"link\" onclick=\"if(!$('formctg').value) alert('"._("Selezionare la categoria da modificare")."');else location.href='".$this->_home."?evt[".$this->_instanceName."-manageDoc]&block=ctg&action=".$this->_act_modify."&ctg='+$('formctg').value\">".$this->icon('modify')."</span>";
			$link_delete = "<span class=\"link\" onclick=\"if(!$('formctg').value) alert('"._("Selezionare la categoria da eliminare")."');else location.href='".$this->_home."?evt[".$this->_instanceName."-manageDoc]&block=ctg&action=".$this->_act_delete."&ctg='+$('formctg').value\">".$this->icon('delete')."</span>";
			$GINO .= $link_modify." ".$link_delete;
			$GINO .= "</div>";
			$GINO .= "<div class=\"null\"></div>";
			$GINO .= "</div>";
		}
		else 
			$GINO = "<p>"._("Non risultano categorie registrate")."</p>\n";

		$htmlsection->content = $GINO;

		return $htmlsection->render();
	}

	private function listItems($form=false) {
		
		$link_insert = "<a href=\"$this->_home?evt[$this->_instanceName-manageDoc]&amp;action=$this->_act_insert\">".$this->icon('insert', _("nuovo evento"))."</a>";

		$htmlsection = new htmlSection(array('class'=>'admin', 'headerTag'=>'header', 'headerLabel'=>_("Gestione eventi"), 'headerLinks'=>$link_insert));
		
		$GINO = $this->scriptAsset($this->_css_id."_".$this->_instanceName.".css", "calCSS$this->_instance", 'css');
		
		$item = new eventItem(null);
		$where = $item->dataSearch($this->_instanceName);
		$options = $this->setValueOptions(array('where'=>$where));
		$options['form'] = $form;
		
		$GINO .= $item->formSearch($this->_instance, $this->_home."?evt[{$this->_instanceName}-manageDoc]", array('admin'=>true, 'ctg'=>$this->_manageCtg));
		
		$GINO .= "<div id=\"cal_list$this->_instance\">";
		$GINO .= $this->ajaxAdminItems($options);
		$GINO .= "</div>";
		
		$htmlsection->content = $GINO;

		return $htmlsection->render();
	}
	
	public function ajaxAdminItems($options=array()) {

		$GINO = '';
		
		// Opzioni
		if(empty($options)) $options = $this->setValueOptions();
		
		$form = array_key_exists('form', $options) ? $options['form'] : false;
		// End
		
		$postvar = $this->setPostVar($options);
		
		// from pagination (AjaxRequest)
		$start = cleanVar($_POST, 'start', 'int', '');
		$sel_id = cleanVar($_POST, 'id', 'int', '');
		
		$tot_items = eventItem::getTotItems($this->_instance, $options);
		
		if($tot_items > 0)
		{
			if(!$form)
			{
				if($this->_manageSel OR $this->_manageNewsl)
				{
					$form = true;
					$mcheck = ($this->_manageSel AND $this->_manageNewsl) ? true : false;
				}
			}
			else $mcheck = false;
			
			if($form)
			{
				$gform = new Form('gform', 'post', true, array('tblLayout'=>false));
				$gform->load('dataform');
				
				$submit = _("aggiorna");
				$GINO .= $gform->form($this->_home."?evt[$this->_instanceName-actionList]", true, '');
				
				if($this->_manageNewsl)
				{
					$newsl = new newsletter();
					$newsl_id = $newsl->activeId();
					if($newsl_id)
						$GINO .= $gform->hidden('refid', $newsl_id);
				}
				$GINO .= $gform->hidden('action', $this->_act_modify);
			}
			
			$list = new PageList($this->_items_for_page, $tot_items, 'array');
			
			$items = eventItem::getOrderedItems($this->_instance, $options);
			
			$end = $list->start()+$list->rangeNumber > count($items) ? count($items) : $list->start()+$list->rangeNumber;
			
			$htmlList = new htmlList(array("numItems"=>($end-$list->start()), "separator"=>true));
			$GINO .= $htmlList->start();
			
			foreach($items as $item) {
		
				$selected = $item->id == $sel_id ? true:false;

				$link_modify = "<a href=\"$this->_home?evt[$this->_instanceName-manageDoc]&amp;id={$item->id}&amp;action=$this->_act_modify&amp;start=$start\">".$this->icon('modify')."</a>";
				$link_delete = "<a href=\"$this->_home?evt[$this->_instanceName-manageDoc]&amp;id={$item->id}&amp;action=$this->_act_delete&amp;start=$start\">".$this->icon('delete')."</a>";
				
				$check = '';
				
				if($form)
				{
					if($mcheck)
					{
						$sels = new selection(0, $item->id, $this->_instance, eventItem::$_tbl_selection, array('input_name'=>'s_check'));
						$check .= $sels->fCheck($gform, array('text'=>_("A")));
						$check .= "&nbsp;";
						
						$sels_b = new selection(0, $item->id, $this->_instance, eventItem::$_tbl_selection_b, array('input_name'=>'b_check'));
						$check .= $sels_b->fCheck($gform, array('text'=>_("B")));
						$check .= "&nbsp;";
						
						$sels_newsl = new selection(0, $item->id, $this->_instance, eventItem::$_tbl_newsletter_item, array('input_name'=>'n_check'));
						$disabled = !$newsl_id ? true : false;
						$check .= $sels_newsl->fCheck($gform, array('text'=>_("newsletter"), 'disabled'=>$disabled, 'where'=>array("aggregator='$newsl_id'")));
					}
					else
					{
						if($this->_manageSel)
						{
							$sels = new selection(0, $item->id, $this->_instance, eventItem::$_tbl_selection, array('input_name'=>'s_check'));
							$check .= $sels->fCheck($gform, array('text'=>_("A")));
							$check .= "&nbsp;";
							$sels_b = new selection(0, $item->id, $this->_instance, eventItem::$_tbl_selection_b, array('input_name'=>'b_check'));
							$check .= $sels_b->fCheck($gform, array('text'=>_("B")));
						}
						
						if($this->_manageNewsl)
						{
							$sels_newsl = new selection(0, $item->id, $this->_instance, eventItem::$_tbl_newsletter_item, array('input_name'=>'n_check'));
							$disabled = !$newsl_id ? true : false;
							$check .= $sels_newsl->fCheck($gform, array('text'=>_("newsletter"), 'disabled'=>$disabled, 'where'=>array("aggregator='$newsl_id'")));
						}
					}
				}
				
				$name = dbDateToDate($item->date, "/", 2).' - '.htmlChars($item->ml('name'));
				
				$GINO .= $htmlList->item($name, array($link_modify, $link_delete), $selected, true, $check, '', 'checkbox');
			}
			$GINO .= $htmlList->end();
			
			if($form)
			{
				$GINO .= "<p>".$gform->input('submit_action', 'submit', $submit, array("classField"=>"submit"))."</p>";
				$GINO .= $gform->cform();
			}

			$GINO .= $list->listReferenceGINO("pt[$this->_instanceName-ajaxAdminItems]", true, implode('&amp;', $postvar), "cal_list$this->_instance", "cal_list$this->_instance", true, 'updateTooltips');
		}
		else {
			$GINO .= _("La ricerca non ha prodotto risultati.");
		}
		
		return $GINO;
	}
	
	private function info() {
		
		$htmlsection = new htmlSection(array('class'=>'admin', 'headerTag'=>'h1', 'headerLabel'=>_("Informazioni")));
		
		$buffer = "<p>"._("Prima di cominciare ad utilizzare la classe impostare le corrette opzioni dell'istanza 
		facendo particolare attenzione all'abilitazione o meno delle categorie.")."</p>\n";
		
		$htmlsection->content = $buffer;

		return $htmlsection->render();
	}
	
	public function actionCtg() {
	
		$this->accessGroup('');

		$gform = new Form('ctgform', 'post', false);
		$gform->save('cdataform');
		$req_error = $gform->arequired();

		$id = cleanVar($_POST, 'id', 'int', '');
		
		$link_error = $this->_home."?evt[$this->_instanceName-manageDoc]&block=ctg&action=$this->_action";
		if($id) $link_error .= "&id=$id";

		if($req_error > 0) 
			exit(error::errorMessage(array('error'=>1), $link_error));

		$ctg = new eventCtg($id);
		$ctg->instance = $this->_instance;

		foreach($_POST as $k=>$v) {
			$ctg->{$k} = $k;
		}

		$ctg->updateDbData();

		EvtHandler::HttpCall($this->_home, $this->_instanceName.'-manageDoc', '');
	}

	public function actionDelCtg() {
	
		$this->accessGroup('');
		$gform = new Form('gform', 'post', false);
		$gform->save('dataform');
		$req_error = $gform->arequired();

		$id = cleanVar($_POST, 'id', 'int', '');

		if($req_error > 0) 
			exit(error::errorMessage(array('error'=>9), $this->_home."?evt[$this->_instanceName-manageDoc]"));

		$ctg = new eventCtg($id);
		$ctg->deleteDbData();
		
		$delete_event = false;	// elimina tutti gli eventi legati alla categoria
		if($ctg->deleteDbData() && $delete_event)
		{
			$query = "SELECT id FROM ".eventItem::$_tbl_item." WHERE ctg='$id' AND instance='$this->_instance'";
			$a = $this->_db->selectquery($query);
			if(sizeof($a) > 0)
			{
				foreach($a AS $b)
				{
					$item = $b['id'];
					$query_del = "DELETE FROM ".eventItem::$_tbl_item." WHERE id='$item'";
					if($this->_db->actionquery($query_del))
					{
						$dir = $this->pathBaseDir($item, 'abs');
						$this->deleteFileDir($dir);
					}
				}
			}
		}

		EvtHandler::HttpCall($this->_home, $this->_instanceName.'-manageDoc', '');
	}

	public function actionEvent() {

		$this->accessGroup('');

		$gform = new Form('eform', 'post', false);
		$gform->save('dataform');
		$req_error = $gform->arequired();

		$id = cleanVar($_POST, 'id', 'int', '');
		$start = cleanVar($_POST, 'start', 'int', '');

		$link_error = $this->_home."?evt[$this->_instanceName-manageDoc]&action={$this->_action}&start=$start";
		if($id) $link_error .= "&id=$id";
		
		if($req_error > 0) 
			exit(error::errorMessage(array('error'=>1), $link_error));

		$event = new eventItem($id);
		$event->instance = $this->_instance;

		foreach($_POST as $k=>$v) {
			$event->{$k} = $k;
		}
	
		$old_img = cleanVar($_POST, 'old_image', 'string', '');
		$old_attach = cleanVar($_POST, 'old_attachment', 'string', '');
		
		if($event->updateDbData())
			$directory = $this->pathDirectory($event->id, 'abs');

		$gform->manageFile('image', $old_img, true, eventItem::$extension_media, $directory.$this->_event_sub[0], $link_error, eventItem::$_tbl_item, 'image', 'id', $event->id, 
			array('prefix_file'=>$this->_prefix_img, 'prefix_thumb'=>$this->_prefix_thumb, 'width'=>$this->_img_width, 'thumb_width'=>$this->_thumb_width));
		
		$gform->manageFile('attachment', $old_attach, false, eventItem::$extension_attach, $directory.$this->_event_sub[1], $link_error, eventItem::$_tbl_item, 'attachment', 'id', $event->id);

		EvtHandler::HttpCall($this->_home, $this->_instanceName.'-manageDoc', "start=$start");
	}
	
	public function actionDelEvent() {
	
		$this->accessGroup('');
		$gform = new Form('gform', 'post', true);
		$gform->save('dataform');
		$req_error = $gform->arequired();

		$id = cleanVar($_POST, 'id', 'int', '');

		if($req_error > 0) 
			exit(error::errorMessage(array('error'=>9), $this->_home."?evt[$this->_instanceName-manageDoc]"));

		$dir = $this->pathBaseDir($id, 'abs');
		
		$event = new eventItem($id);
		if($event->deleteDbData())
			$this->deleteFileDir($dir);

		EvtHandler::HttpCall($this->_home, $this->_instanceName.'-manageDoc', '');
	}
	
	/*
	 * Selezioni
	 */
	
	private function formList($block) {
		
		$GINO = "<div class=\"area\">\n";
		
		$gform = new Form('gform', 'post', true, array('tblLayout'=>false));
		$gform->load('dataform');

		if($block == $this->_block_newsl)
		{
			$table = eventItem::$_tbl_newsletter_item;
			$newsl = new newsletter();
			$newsl_id = $newsl->activeId();
			
			if($newsl_id)
			{
				$input_name = 'n_check';
				$input_hidden = $gform->hidden('refid', $newsl_id);
				
				$query = "SELECT date, object FROM ".eventItem::$_tbl_newsletter." WHERE id='$newsl_id'";
				$a = $this->_db->selectquery($query);
				if(sizeof($a) > 0)
				{
					foreach($a AS $b)
					{
						$newsl_date = dbDateToDate(htmlChars($b['date']), '/', 2);
						$newsl_obj = htmlChars($b['object']);
						
						$add_title = _("per la newsletter").": '$newsl_obj' - $newsl_date";
					}
				}
			}
			else
			{
				$add_title = _("per la newsletter");
			}
		}
		elseif($block == $this->_block_sel_a OR $block == $this->_block_sel_b)
		{
			if($block == $this->_block_sel_a)
			{
				$table = eventItem::$_tbl_selection;
				$add_title = _("A");
				$input_name = 's_check';
			}
			else
			{
				$table = eventItem::$_tbl_selection_b;
				$add_title = _("B");
				$input_name = 'b_check';
			}
			
			$newsl_id = 0;
			$input_hidden = '';
		}
		
		$title = _("Selezione eventi"); $submit = _("aggiorna");

		$htmlsection = new htmlSection(array('class'=>'admin', 'headerTag'=>'h1', 'headerLabel'=>$title.' '.$add_title));

		if($block == $this->_block_newsl AND !$newsl_id)
		{
			$GINO .= _("Non è stata trovata una newsletter 'attiva' e che non sia stata ancora 'inviata'.");
		}
		else
		{
			$GINO .= $gform->form($this->_home."?evt[$this->_instanceName-actionList]", true, '');
			$GINO .= $input_hidden;
			$GINO .= $gform->hidden('action', $this->_act_modify);
			$GINO .= $gform->hidden('block', $block);
			$GINO .= $gform->hidden('list', $this->_list_complete);
			
			$sels = new selection(0, 0, $this->_instance, $table, array('aggregator'=>$newsl_id));
			$events = $sels->getListItems(array('class'=>'eventItem'));
			
			if($this->_manageSort)
				$sort = new sort(array('table'=>$table, 'instance'=>$this->_instance, 'aggregator'=>$newsl_id));
			
			if(count($events) > 0)
			{
				$htmlList = new htmlList(array("numItems"=>count($events), "separator"=>false, "id"=>'priorityList'));
				$GINO .= $htmlList->start();
				for($i=0, $end=sizeof($events);$i<$end;$i++)
				{
					if(!isset($events[$i])) break;
					$evt = $events[$i];
					$content = dbDateToDate($evt->date, "/");
					if($evt->hours != "00:00:00") $content .= " (".dbTimeToTime($evt->hours).")";
					if($evt->location != '') $content .= " - {$evt->location}";
					
					$sels = new selection(0, $evt->id, $this->_instance, $table, array('input_name'=>$input_name));
					$input = $sels->fCheck($gform);
					if($this->_manageSort) $link_sort = $sort->link(); else $link_sort = '';
					
					$name = $input." ".htmlChars($evt->ml('name'));
					$GINO .= $htmlList->item($name, array($link_sort), '', true, $content, "id$evt->id", "sortable");
				}
				$GINO .= $htmlList->end();
				
				if($this->_manageSort)
					$GINO .= $sort->jsLib($this->_home."?pt[{$this->_className}-actionUpdateOrder]");
				
				$GINO .= "<p>".$gform->input('submit_action', 'submit', $submit, array("classField"=>"submit"))."</p>";
			}
			
			$GINO .= $gform->cform();
		}
		
		$GINO .= "</div>\n";
		
		$htmlsection->content = $GINO;

		return $htmlsection->render();
	}
	
	public function actionUpdateOrder() {
	
		$this->accessGroup('');
		
		$order = cleanVar($_POST, 'order', 'string', '');
		$table = cleanVar($_POST, 'tbl', 'string', '');
		
		$items = explode(",", $order);
		$i=1;
		foreach($items as $item) {
			$sort = new sort(array('id'=>$item, 'instance'=>$this->_instance, 'table'=>$table, 'field_id'=>'reference'));
			$sort->priority = $i;
			$sort->updateDbData();
			$i++;
		}
	}
	
	public function actionList() {
	
		$this->accessGroup('');

		$gform = new Form('gform', 'post', false);
		$gform->save('dataform');
		$req_error = $gform->arequired();
		
		$complete = cleanVar($_POST, 'list', 'string', '');

		$link_error = $this->_home."?evt[$this->_instanceName-manageDoc]&block={$this->_block}&action={$this->_action}";
		if($this->_block == $this->_block_newsl OR $this->_block == $this->_block_sel_a OR $this->_block == $this->_block_sel_b)
			$link = "block=".$this->_block;
		else
			$link = '';
		
		if($req_error > 0)
			exit(error::errorMessage(array('error'=>1), $link_error));

		if($this->_action != $this->_act_modify)
			exit(error::errorMessage(array('error'=>9), $link_error));
		
		if($this->_manageNewsl)
		{
			$refid = cleanVar($_POST, 'refid', 'int', '');
			$this->updateSelection($complete, eventItem::$_tbl_newsletter_item, array('input_name'=>'n_check', 'aggregator'=>$refid));
		}
		
		if($this->_manageSel)
		{
			$this->updateSelection($complete, eventItem::$_tbl_selection, array('input_name'=>'s_check'));
			$this->updateSelection($complete, eventItem::$_tbl_selection_b, array('input_name'=>'b_check'));
		}

		EvtHandler::HttpCall($this->_home, $this->_instanceName.'-manageDoc', $link);
	}
	
	private function updateSelection($list_complete, $table, $options=array()){
		
		$sels = new selection(0, 0, $this->_instance, $table, $options);
		$sels->instance = $this->_instance;
		if($this->_list_complete == $list_complete)
		{
			if($this->_manageSort) $sels->sortNumber(1); else $sels->sortNumber(0);
			$sels->updateListDbData();
		}
		else
		{
			if($this->_manageSort)
			{
				$aggregator = array_key_exists('aggregator', $options) ? $options['aggregator'] : 0;
				$sort = new sort(array('table'=>$table, 'instance'=>$this->_instance, 'aggregator'=>$aggregator));
				$new = $sort->newPriority();
			}
			else $new = 0;
			$sels->sortNumber($new);
			$sels->updateDbData();
		}
	}
	
	/*
	 * Newsletter
	 */
	
	public function newsletter($instanceName=''){
		
		$link = $instanceName != '' ? "index.php?evt[{$instanceName}-manageDoc]&block=newsletter" : '';
		
		$list = array(
			"include" => 'event/class_event.php',
			"classData" => 'event',
			"link" => $link
		);

		return $list;
	}
	
	// list | html
	public function printForNewsletter($id, $options=array()){
		
		$view = array_key_exists('view', $options) ? $options['view'] : 'html';
		$font_family = array_key_exists('font', $options) ? $options['font'] : '';
		
		$item = new eventItem($id);
		
		if(is_null($item->id))
			return null;
		
		if($view == 'list')
		{
			return $item->ml('name')." - ".dbDateToDate($item->date, "/");
		}
		
		$GINO = '';
		
		$style_img = "border:1px solid #000; float:left; margin:5px 10px 0px 0px;";
		$style_td1 = "font: bold 12px/15px $font_family; color:#FFFFFF; background-color:#E21969; text-align:right; padding:1px 3px;";
		$style_td2 = "font: normal 12px/15px $font_family; padding:1px 3px;";
		$style_td3 = "font: bold 13px/18px $font_family; color:#9D916F; margin-top:1px; margin-bottom:3px; padding:1px 3px;";
		
		$style_title = "font: bold 13px/18px $font_family; color:#9D916F; margin-top:1px; margin-bottom:3px; padding-left:4px;";
		$style_descr = "font: normal 11px/14px $font_family; padding-left:4px;";
		
		$GINO .= "<div style=\"text-align:left; font-size:10px;\">";
		
		if($item->image)
		{
			$link_to_image = $this->_url_root.$this->pathDirectory($item->id, 'rel', 'image');
			$GINO .= "<img style=\"$style_img\" src=\"$link_to_image".$this->_prefix_thumb.$item->image."\" />";
		}
		
		$link_event = $this->_url_root.$this->_plink->alink($this->_instanceName, 'viewItem', "id=".$item->id);
		$name = "<a href=\"$link_event\"><font color=\"#666\">".htmlChars($item->ml('name'))."</font></a>";
		
		$GINO .= "<table>";
		$GINO .= "<tr><td style=\"$style_td1\">"._("Evento")."</td><td style=\"$style_td3\">$name</td></tr>";
		$GINO .= "<tr><td style=\"$style_td1\">"._("Luogo")."</td><td style=\"$style_td2\">".$item->location."</td></tr>";
		
		if($item->hours != '00:00:00') $hours = " - "._("ore")." ".dbTimeToTime($item->hours); else $hours = '';
		$GINO .= "<tr><td style=\"$style_td1\">"._("Data")."</td><td style=\"$style_td2\">".dbDateToDate($item->date, "/").$hours."</td></tr>";
		$GINO .= "</table>";
		
		if($item->summary) $GINO .= "<div style=\"$style_descr\">".htmlChars($item->ml('summary'))."</div>";
		
		$GINO .= "<div style=\"clear:both;\"></div>";
		$GINO .= "</div>";
		
		return $GINO;
	}
}
?>
