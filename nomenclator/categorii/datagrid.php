<?php
require_once("cfg.php");

class PagedIterator implements IteratorAggregate, ArrayAccess
{
	var $_model;
	var $_emptyModel;
	var $_collection;
	var $chunks;
	var $length=10;
	var $nr;
	
	function __construct($model, $length=10) {
		$this -> _model = $model;
		$this -> _emptyModel = clone $model;
		$this -> _emptyModel -> clearCollection();
		
		$this -> length = $length;

		$this -> chunks = array_chunk($model -> getCollection(), $this -> length);
		$this -> nr = count($this -> chunks);
	}
	
	public function getIterator() {
		$ret = array();	
		$i = 0;
		foreach($this -> chunks as $d) {
			$ret[$i] = clone $this -> _emptyModel;
			$ret[$i] -> setDataFromCollection($d); 
			$i++;
		} 
		return new ArrayIterator($ret);
	}
	
	
	public function offsetSet($offset, $value) {
      
    }
    public function offsetExists($offset) {
        return isset($this->chunks[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->chunks[$offset]);
    }
    public function offsetGet($offset) {
        if(isset($this->chunks[$offset])){
			$ret = clone $this -> _emptyModel;
			$ret -> setDataFromCollection($this->chunks[$offset]); 
			return $ret;
		}
		else return NULL;
    }
	
}


$produse = new Produse("where 1");
$paged = new PagedIterator($produse, 20);
$i=0;
foreach($paged as $page) {
	$i++;
	echo '<h1>'.$i.' din '.$paged -> nr .'</h1>';
	echo $page -> lista();
}
?>