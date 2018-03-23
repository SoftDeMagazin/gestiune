<?php
require_once("../../db/mysql.php");
$db = new MySQL();
class Model implements ArrayAccess, IteratorAggregate, Countable
{
	private $_data = array();
	private $_dataSource = array();
	private $_tblColumns = array();
	
	public $key;
	public $tbl;
	
	var $_relations = array();
	var $_form = array();
	var $_rules = array();
	private $_relationsClasses = array();
	private $_relationsQuery = array();	
	
	var $db;
	var $pageLength = 1;
	
	public function __construct($id=NULL) {
		global $db;
		
		if(is_object($db)) {
			$this -> db = $db;	
		}
		
		$tblInfo = $this -> db -> tableInfo($this -> tbl);
		$this -> _tblColumns = $this -> db -> tableColumns($tblInfo);
		$this -> key = $this -> db -> tableKey($tblInfo);
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
	
	/*
		saving data
	*/
	
	public function insert()
	{
		$this -> setDataFromArray($this -> dv -> insertArray($this -> _data,$this -> tbl, $this -> key));
	}
		
	public function update()
	{
		$this -> setDataFromArray($this -> db -> updateArray($this -> _data,$this -> tbl, $this -> key));
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
			$this -> db -> query("DELETE FROM ". $this -> useTable ." WHERE ". $this -> primaryKey ." = '". $this -> $key ."'");
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
			
	private function fromString($obj, $columns = array()) {
		if(empty($colums)) {
			$columns = $this -> _tblColumns;
		}
		$sql = "SELECT ". $this -> db -> columns($columns) ." FROM ". $this -> tbl ." $obj;";
		$this -> fromCollection($this -> db -> getRows($sql));
	}
	
	private function fromId($id, $columns = array()) {
		if(empty($colums)) {
			$columns = $this -> _tblColumns;
		}	
		$sql = "SELECT ". $this -> db -> columns($columns) ." FROM ". $this -> tbl ." WHERE ". $this -> key ." = '$id';";
		$this -> _data = $this -> db -> getRow($sql);
		$this -> resetDataSource();
	}
	
	private function fromCollection($collection) {
		$this -> _data = $collection[0];
		$this -> _dataSource = $collection;
	}
	
	public function fromArray($array, $prefix="") {
		$this -> setEmpty();
		foreach($this -> _data as $key => $value) {
			if(array_key_exists($key, $array)) {
				$this -> _data[$key] = $array[$prefix.$key];
			}
		}
		$this -> resetDataSource();
	}

	private function resetDataSource() {
		$this -> _dataSource = array();
		$this -> _dataSource[0] = $this -> _data;
	}

	private function clearCollection() {
		$this -> _dataSource = array();
	}
	
	private function setEmpty() {
		$this -> _data = array_fill_keys($this -> _tblColumns, NULL);
	}
	
	/*
		end data manipulation
	*/
	
	
	/*
		form functions 
	*/		
		
	public function frm($options = array()) {
		$options = array_merge(array("method" => "post", "action" => "", "id" => "frm_".$this -> useTable), $options);
		return Html::form("frm_".$this -> useTable, $options);
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
						Html::append($out, '<div id="div_frm_'. $key .'">');
						if($f['label']) {
							Html::append($out, Html::label($f['label']));
							Html::append($out, '<br/>');
						}
						Html::append($out, $this -> $key());	
						Html::append($out, '</div>');
					}
					else {
						Html::append($out, FormField::field($key, $f));
					}	
				}
			}
		return $out;	
	 }	
	 function frmDefault($form = array(), $frmOptions = array()) {	 	
		$out = '';
		Html::append($out, $this -> frm($frmOptions));
		Html::append($out, $this -> frmContent($form));
		//Html::append($out, $this -> frmButton());
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
				case "one":
				{
					if(!array_key_exists("sql",$rel))
					{
					$key = $rel['key'];
					$value = $rel['model_key'];
					if(!$value) $value = $key;
					$this -> _relationsClasses[$name] -> setDataFromString("where $key = '". $this -> $value ."'");
					$out = clone $this -> _relationsClasses[$name];
					return $out;
					}
					else
					{
					$this -> _relationsClasses[$name] -> setDataFromString($this -> stringReplace($rel['sql']));
					$out = clone $this -> _relationsClasses[$name];
					return $out;
					}
				}break;
				
				case "many":
				{
					if(!array_key_exists("sql", $rel))
					{
					$clsname = $rel['model'];
					$key = $rel['key'];
					$cls = new $clsname("where $key = '". $this -> $key ."' ". $this -> _relationsQuery[$name] .""); 
					return $cls;
					}
					else
					{
					$clsname = $rel['model'];
					$key = $rel['key'];
					$cls = new $clsname($this -> stringReplace($rel['sql'])); 
					return $cls;
					}
				}
			}			
		}
		switch($name)
		{
			case "collection":
			{
				$out = array();
				for($i = 0; $i< count($this -> _dataCollection);$i++)
					{
					$out[$i] = clone $this -> collection($i);
					$out[$i] -> clearCollection();
					}
				return $out;					
			}break;
			
			case "nr_r":
			{
				return count($this -> _dataCollection); 
			}break;
			case "id":
			{
				$key = $this -> primaryKey;
				return $this -> $key;
			}break;
		}
		
		if(array_key_exists($name, $this ->_data))
			{
			return $this -> _data[$name];
			}
		
		$trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
		return NULL;
	}
	}
	
	public function __set($name, $value) {
		if(array_key_exists($name, $this -> _data)) {
			$this -> _data[$name] = $value;	
		}
		
		if(array_key_exists($name, $this -> _relations)) {
			$this -> _relationsQuery[$name] = $value;
		}

	}
	
	function __call($method, $arguments)
	{		
		if(array_key_exists($method, $this -> _data))
			{
				if(is_array($arguments[0]))
				{
					$options = $arguments[0];	
				}
				else
				{
					$this -> _form = array_merge($this -> _form, $this -> _defaultForm);				
		  			$options = $this -> _form[$method];
		   			
				}	
				$options['value'] = $this -> $method;
           		return new FormField($method, $options);
			}
		
		if(array_key_exists($method, $this -> _relations))
		{
			$rel = $this -> _relations[$method];			
			$cls = $this -> _relationsClasses[$method];
			$cls -> setDataFromString("where 1 order by ". $rel['value'] ." asc");
			$opt = $cls -> getCollectionForm(array($rel['key'], $rel['value']));
			if(is_array($arguments[0]))
			{
				$options = $arguments[0];	
			}
			else
			{
				$this -> _form = array_merge($this -> _form, $this -> _defaultForm);				
				$options = $this -> _form[$method];
				
			}	
			$options['type'] = "select";
			$options['value'] = $this -> $rel['key'];
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
	
	public function offsetGet($offset) {
        if(isset($this -> _dataSource[$offset])) {
			$out = clone $this;
			$out -> fromArray($this -> _dataSource[$offset]);
			return  $out;
		}
		return null;
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
	
	public function getIterator() {
		$ret = array();	
		$i = 0;
		if($this -> pageLength == 1) {
			foreach($this -> _dataSource as $d) {
				$ret[$i] = clone $this; 
				$ret[$i] -> fromArray($d);
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
	
	public function count() {
		$nr_r = count($this -> _dataSource);
		$out = (int)($nr_r/$this -> pageLength);
		if($nr_r%$this -> pageLength != 0) $out++;
		return $out;
	}
}


class Produse extends Model
{
	var $tbl = "produse";
}
$m = new Produse("WHERE 1");
$m -> pageLength(10);
echo count($m);
foreach($m as $a) {
echo count($a);
echo '<br>';
var_dump($a);
echo '<hr>';
echo '<br>';
}
?>