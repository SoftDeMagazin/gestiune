<?php
class DataSource implements Countable, IteratorAggregate, ArrayAccess{

	var $_data = array();
	var $_dataSource = array();
	
	function getCollectionForm($columns = array())
	{
		if(empty($columns))
		{
			return $this -> dataSource;
		}
		else
		{
			$out = array();
			foreach($this -> _dataSource as $data)
			{
				$out[$data[$columns[0]]] = $data[$columns[1]];
			}
			return $out;
		}
	}
	
	public function stringReplace($str) {
		foreach($this -> _data as $key => $value) {
			$str = str_replace('<%'. $key .'%>', $value, $str);
		}
		return $str;
	}
	
	public function fromArray($array, $prefix="") {
		foreach($this -> _data as $key => $value) {
			if(array_key_exists($key, $array)) {
				$this -> _data[$key] = $array[$prefix.$key];
			}
		}
		//$this -> resetDataSource();
	}
	
	public function fromDataSource($key) {
		$this -> _data = $this -> _dataSource[$key];
	}

	public function findKey($key, $value) {
		$i=0;
		foreach($this -> _dataSource as $ds) {
			if($ds[$key] == $value) {
				$this -> fromDataSource($i);
				return true;
			}
			$i++;
		}
	}

	public function fromCollection($collection) {
		$this -> _data = $collection[0];
		$this -> _dataSource = $collection;
	}

	public function fromArrayReset($array, $prefix="") {
		$this -> setEmpty();
		foreach($this -> _data as $key => $value) {
			if(array_key_exists($key, $array)) {
				$this -> _data[$key] = $array[$prefix.$key];
			}
		}
		$this -> resetDataSource();
	}

	public function resetDataSource() {
		$this -> _dataSource = array();
		$this -> _dataSource[0] = $this -> _data;
	}
	
	public function clearCollection() {
		$this -> _dataSource = array();
	}
	
	public function __get($name) {
		if(array_key_exists($name, $this -> _data))
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
	
	public function __set($name, $value) {
		if(array_key_exists($name, $this -> _data)) {
			$this -> _data[$name] = $value;
		}
		return;
	}	
	
	public function count() {
		return count($this -> _dataSource);
	}
	
	/*
		interfaces
	*/	
		
	public function getIterator() {
		$ret = array();
		$i = 0;
		foreach($this -> _dataSource as $d) {
			$ret[$i] = clone $this;
			$ret[$i] -> _data = $d;
			$ret[$i] -> _dataSource[0] = $d;
			$i++;
		}
		return new ArrayIterator($ret);
	}
	
	public function offsetGet($offset) {
		if(isset($this -> _dataSource[$offset])) {
			$out = clone $this;
			$out -> fromArray($this -> _dataSource[$offset]);
			return  $out;
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
}
?>