<?php
class Model implements IteratorAggregate {
	public $_data;
	public $_dataCollection = array();
	
	public function get()
		{
		echo $this -> _data;
		echo '<br>';
		}
	
	public function getIterator() {
		$ret = array();	
		$i = 0;
		foreach($this -> _dataCollection as $d) {
			$this -> _data = $d;
			$ret[$i] = clone $this; 
			$ret[$i] -
		}
		return new ArrayIterator($ret);
	}
}

$m = new Model();
$m -> _dataCollection = array("a", "b", "c");
foreach($m as $key) {
	var_dump($key);	
} 
?>