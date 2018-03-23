<?php
class Model extends DataSource
{	
	var $_tblColumns = array();
	var $key;
	var $tbl;

	var $_relations = array();
	var $_form = array();
	var $_validator = array();

	private $_relationsClasses = array();
	private $_relationsQuery = array();

	private $_preparedQuery = "";
	/**
	 * @var MySQL
	 */
	var $db;
	var $pageLength = 1;

	public function __construct($id=NULL) {
		global $db;

		if(is_object($db)) {
			$this -> db = $db;
		}

		$tblInfo = $this -> db -> tableInfo($this -> tbl);
		$this -> _tblColumns = $this -> db -> tableColumns($tblInfo);
		if($this -> db -> tableKey($tblInfo)) {
			$this -> key = $this -> db -> tableKey($tblInfo);
		}
		if($id) {
			if(is_array($id)) {
				$this -> fromArray($id);
			}
			elseif(is_numeric($id)) {
				$this -> fromId($id);
			}
			else {
				$this -> fromString($id);
			}
		}
		else {
			$this -> setEmpty();
		}

		foreach($this ->_relations as $key => $rel) {
			if($rel['type'] == "one") {
				$clsname = $rel['model'];
				$this -> _relationsClasses[$key] = new $clsname();
			}
		}

	}

	public function prepareQuery($query) {
		$this -> _preparedQuery = $query;
	}

	/**
	 * returneaza numarul total de randuri pentru query
	 * @return int
	 */
	public function expectedResult() {
		$sql = "
		SELECT count(*) as nr_r 
		FROM `". $this -> tbl ."`
		". $this -> _preparedQuery ."
		";
		$row = $this -> db -> getRow($sql);
		return $row['nr_r'];
	}

	public function nrPages($pageLength) {
		$nr_r = $this -> expectedResult();
		$out = (int)($nr_r/$pageLength);
		if($nr_r%$pageLength != 0) $out++;
		return $out;
	}

	public function getPagedQuery($pageLength, $curentPage) {
		$limit = ($curentPage - 1)*$pageLength;
		if(empty($colums)) {
			$columns = $this -> _tblColumns;
		}
		$sql = "
		SELECT ". $this -> db -> columns($columns, $this -> tbl) ."
		FROM `". $this -> tbl ."`
		". $this -> _preparedQuery ."
		LIMIT $limit, $pageLength
		";
		$arr = $this -> db -> getRows($sql);
		$this -> fromCollection($arr);
	}

	public function validate(&$objResponse)
	{
		$valid = TRUE;
		if(is_array($this -> _validator)) {
			foreach($this -> _validator as $key => $rules) {
				$fieldMsg = "";
				foreach($rules as $rule) {
					if(!$this -> checkRule($rule[0], $this -> $key, $key)) {
						$fieldMsg .= $rule[1].";";
						$valid = FALSE;
					}
				}
				$objResponse -> assign("err_frm_". $key ."", "innerHTML", $fieldMsg);
			}
		}
		return $valid;
	}

	private function checkRule($rule, $value, $key)
	{
		switch($rule)
		{
			case "numeric":
				{
					if(is_numeric($value)) return true;
				}break;
			case "required":
				{
					if(preg_match('/.+/', $value)) return true;
				}break;
			case "unique":
				{
					$nr = $this -> db -> getRow("SELECT COUNT(*) as nr_r FROM `". $this -> tbl ."` WHERE $key = '$value' and ". $this -> key ." <> '". $this -> id ."'");
					if($nr['nr_r'] == 0) return true;
				}
			default :
				{
					return false;
				}break;
		}
		return false;
	}

	/*
		saving data
	*/

	public function insert()
	{
		$this -> fromArray($this -> db -> insertArray($this -> _data,$this -> tbl, $this -> key));
	}

	public function update()
	{
		$this -> fromArray($this -> db -> updateArray($this -> _data,$this -> tbl, $this -> key));
	}

	public function save()
	{
		if(!$this -> _data[$this -> key])
		{
			$this -> insert();
		}
		else
		{
			$this -> update();
		}
	}

	public function delete()
	{
		$key = $this -> key;
		$this -> db -> query("DELETE FROM `". $this -> tbl ."` WHERE `". $this -> key ."` = '". $this -> $key ."'");
	}

	/*
		data manipulation
	*/

	public function pageLength($length)
	{
		if(is_numeric($length)) $length = (int) $length;
		if($length > 0 && is_int($length)) {
			$this -> pageLength = $length;
		}
	}

	public function fromString($obj, $columns = array()) {
		if(!count($columns)) {
			$columns = $this -> _tblColumns;		
			$sql = "SELECT ". $this -> db -> columns($columns, $this -> tbl) ." FROM `". $this -> tbl ."` $obj;";
		} else {
			
			$sql = "SELECT ". $this -> db -> columns($columns) ." FROM `". $this -> tbl ."` $obj;";
		}
		
		$this -> fromCollection($this -> db -> getRows($sql));
	}

	public function fromId($id, $columns = array()) {
		if(empty($colums)) {
			$columns = $this -> _tblColumns;
		}
		$this -> fromString("WHERE `". $this -> key ."` = '$id';");
	}
	
	public function fromArrayOfId($array, $columns = array()) {
		$in = "'".implode("','", $array)."'";
		if(empty($colums)) {
			$columns = $this -> _tblColumns;
		}
		$this -> fromString(" where `". $this -> key ."` in ($in)");
	}

	public function find($key, $value) {
		$this -> findKey($key, $value);
		$columns = $this -> _tblColumns;
		$this -> fromString("WHERE `". $key ."` = '$value'");
	}
	
	public function fromArray($array, $prefix="") {
		$this -> setEmpty();
		parent::fromArray($array, $prefix);
	}
	

	public function fromArrayReset($array, $prefix="") {
		$this -> setEmpty();
		parent::fromArrayReset($array, $prefix);
	}

	public function setEmpty() {
		$this -> _data = array_fill_keys($this -> _tblColumns, NULL);
	}
	/*
		end data manipulation
	*/


	/*
		start form functions
	*/

	public function frm($options = array()) {
		$options = array_merge(array("method" => "post", "action" => "", "id" => "frm_".$this -> tbl), $options);
		return Html::form("frm_".$this -> tbl, $options);
	}


	function frmContent($form = array()) {
		$out = ''; 
		if($form) {
			$frm = $form;
		}
		else {
			$frm = $this -> _defaultForm;
		}
		if($this -> _defaultForm) {
			foreach($frm as $key => $f) {
				if(array_key_exists($key,$this -> _data) || array_key_exists($key, $this -> _relations)) {

					if($f['label']) {
						Html::append($out, Html::label($f['label'], "",array("id" => 'lbl_frm_'. $key .'')));
						Html::append($out, '<br/>');
					}
					Html::append($out, '<div id="div_frm_'. $key .'">');
					Html::append($out, $this -> $key($f));
					if($f['type'] != 'hidden') { Html::append($out, '<span id="err_frm_'. $key .'" class="error"></span>'); }
					Html::append($out, '</div>');
				}
				else {
					switch($f['type']) {
						case "fieldstart": {
							Html::append($out, "<fieldset><legend>". $f['label'] ."</legend>");
						}break;
						case "fieldend": {
							Html::append($out, "</fieldset>");
						}break;
						default: {
							Html::append($out, $f);
						}break;
					}
				}
			}
		}
		return $out;
	}
	function frmDefault($form = array(), $frmOptions = array()) {
		$out = '';
		Html::append($out, $this -> frm($frmOptions));
		Html::append($out, $this -> frmContent($form));
		Html::append($out, $this -> frmEnd());
		return $out;
	}

	function frmInnerHtml($innerHTML, $frmOptions = array()) {
		$out = '';
		Html::append($out, $this -> frm($frmOptions));
		Html::append($out, $innerHTML);
		Html::append($out, $this -> frmEnd());
		return $out;
	}

	function frmButton($value="Salveaza", $options = array()) {
		return Html::submit("submit_".$this -> useTable, $value, $options);
	}

	function frmButtonScript($value="Salveaza", $options = array()) {
		$options["onClick"] = "document.getElementById('frm_". $this -> useTable ."').submit()";
		return Html::submit("submit_".$this -> useTable, $value, $options);
	}

	function frmEnd() {
		return Html::formEnd();
	}


	/*
		magic methods
	*/
	public function __get($name) {

		if(array_key_exists($name, $this -> _relations))
		{
			$rel = $this -> _relations[$name];
			switch($rel['type'])
			{
				case "one": {
						if(!array_key_exists("sql", $rel))
						{
							$key = $rel['key'];
							$value = $rel['model_key'];
							if(!$value) $value = $key;
							$this -> _relationsClasses[$name] -> find($value, $this -> $key);
							$out = clone $this -> _relationsClasses[$name];
							return $out;
						}
						else
						{
							$this -> _relationsClasses[$name] -> fromString($this -> stringReplace($rel['sql']));
							$out = clone $this -> _relationsClasses[$name];
							return $out;
						}
				}break;

				case "many": {
						if(!array_key_exists("sql", $rel)) {
							$clsname = $rel['model'];
							$key = $rel['key'];
							$cls = new $clsname("where $key = '". $this -> $key ."' ". $this -> _relationsQuery[$name] ."");
							return $cls;
						}
						else {
							$clsname = $rel['model'];
							$key = $rel['key'];
							$cls = new $clsname($this -> stringReplace($rel['sql']));
							return $cls;
						}
				}break;
				case "manytomany": {
					$clsname = $rel['model'];
					
				}break;	
			}
		}


		switch($name) {
			case "collection": {
				$out = array();
				for($i = 0; $i< count($this -> _dataCollection);$i++)
				{
					$out[$i] = clone $this -> collection($i);
					$out[$i] -> clearCollection();
				}
				return $out;
			}break;
					
			case "nr_r": {
				return count($this -> _dataSource);
			}break;
					
			case "id": {
				$key = $this -> key;
				return $this -> $key;
			}break;
		}
		return parent::__get($name);
	}

	public function __set($name, $value) {
		if(array_key_exists($name, $this -> _relations)) {
			$this -> _relationsQuery[$name] = $value;
			return;
		}
		parent::__set($name, $value);
	}

	function __call($method, $arguments)
	{
		if(array_key_exists($method, $this -> _data)) {
			if(is_array($arguments[0])) {
				$options = $arguments[0];
			}
			else {
				$this -> _form = array_merge($this -> _form, $this -> _defaultForm);
				$options = $this -> _form[$method];
			}
			
			if($this -> $method) {
				$options['value'] = $this -> $method;
			} else {
				$options['value'] = ($this -> id) ? $this -> $method : $options['value'];
			}
 			/*
			$options['value'] = ($this -> id) ? $this -> $method : $options['value'];
			if($this -> $method && !$this -> id) $options['value'] = $this -> $method;
			if(!$options['value']) $options['value'] = $this -> $method;
			*/
			return new FormField($method, $options);
		}

		if(array_key_exists($method, $this -> _relations)) {
			$rel = $this -> _relations[$method];
			$cls = $this -> _relationsClasses[$method];
			if(!$rel['conditions']) {
				$cls -> fromString("where 1 order by ". $rel['value'] ." asc");
			}
			else {
				$cls -> fromString($rel['conditions']);
			}
			
			if($rel['model_key']) $key = $rel['model_key'];
			else $key = $rel['key'];
			
			$opt = $cls -> getCollectionForm(array($key, $rel['value']));
			if(is_array($arguments[0])) {
				$options = $arguments[0];
			}
			else {
				$this -> _form = array_merge($this -> _form, $this -> _defaultForm);
				$options = $this -> _form[$method];
			}
			
			
			$atrs = $arguments['attributes'];
			if($atrs['multiple']) {
					$options['type'] = "multiselect";					
				}
			else {
				$options['type'] = "select";
			}
			$val_key = $rel['key'];
			$options['value'] = $this -> $val_key;
			$options['options'] = $opt;
			return FormField::field($rel['key'], $options);
		}

		$trace = debug_backtrace();
		trigger_error(
            'Undefined function via __call(): ' . $method .
            '() in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
		E_USER_NOTICE);
		return NULL;
	}

	/*
		interface functions
	*/

	//array acces
	public function offsetGet($offset) {
		if($this -> pageLength == 1) {
			if(isset($this -> _dataSource[$offset])) {
				$out = clone $this;
				$out -> fromArray($this -> _dataSource[$offset]);
				return  $out;
			}
		}
		else {
			if($this -> _dataSource) {
				$chunks = array_chunk($this -> _dataSource, $this -> pageLength);
				$ret = clone $this;
				$ret -> fromCollection($chunks[$offset]);
				$ret -> pageLength = 1;
			}
			else {
				$ret = clone $this;
				$ret -> setEmpty();
				$ret -> clearCollection();
			}
			return $ret;
		}
	}
	
	public function offsetExists($offset) {
		return isset($this -> _dataSource[$offset]);
	}
	public function offsetUnset($offset) {
		unset($this->container[$offset]);
	}
	public function offsetSet($offset, $value) {
		return isset($this->container[$offset]) ? $this->container[$offset] : null;
	}


	//iterator agregate
	public function getIterator() {
		$ret = array();
		$i = 0;
		if($this -> pageLength == 1) {
			foreach($this -> _dataSource as $d) {
				$ret[$i] = clone $this;
				$ret[$i] -> fromArrayReset($d);
				$ret[$i] -> pageLength = 1;
				$i++;
			}
			return new ArrayIterator($ret);
		}
		else {
			$chunks = array_chunk($this -> _dataSource, $this -> pageLength);
			foreach($chunks as $ds) {
				$ret[$i] = clone $this;
				$ret[$i] -> fromCollection($ds);
				$ret[$i] -> pageLength = 1;
				$i++;
			}
			return new ArrayIterator($ret);
		}
	}

	//countable
	public function count() {
		$nr_r = count($this -> _dataSource);
		$out = (int)($nr_r/$this -> pageLength);
		if($nr_r%$this -> pageLength != 0) $out++;
		return $out;
	}
	
	function listaDefault($columns = array(),$click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		foreach($columns as $key => $value) {
			if(is_array($value)) {
				$dg -> addHeadColumn($value['label']);
			}
			else {
				$dg -> addHeadColumn($value);
			}
					
		}
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
				foreach($columns as $key => $value) {
					if(is_array($value)) {
						$dg -> addColumn($this -> stringReplace($value['content']));
					}
					else {
						$dg -> addColumn($this -> $key);
					}			
				}
			if($this -> id == $selected) $class="rowclick";
			else $class="";
			$ck = $this -> stringReplace($click);
			$dck = $this -> stringReplace($dblClick);
			$dg -> setRowOptions(array(
			"class" => $class,
			"onMouseOver"=>"$(this).addClass('rowhover')", 
			"onMouseOut"=>"$(this).removeClass('rowhover')",
			"onClick"=>"". $ck ."$('#selected_". $this -> key ."').val('". $this -> id ."');$('#tbl_". $this -> tbl ." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');",
			"onDblClick"=>"$dck"
			));
			$dg -> index();
			}
		$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		$out .= $dg -> getDataGrid();
		return $out;	
	}
}
?>