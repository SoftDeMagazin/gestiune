<?php
class Upload
{
	var $field;
	var $path;
	var $filename;
	
	function __construct($field, $path="")
		{
		$this -> setField($field);
		$this -> setPath($path);
		}

	function uniqueName()
		{
		$this -> filename = time()."_".$this -> filename;
		}
	
	function check()
		{
		if(isset($_FILES[$this -> field]))
			{
			return true;
			}
		return false;
		}
	
	function getFilename()
		{
		$this -> filename = $_FILES[$this -> field]['name'];
		}	
		
	function save()
		{
		$this -> getFilename();
		$test = move_uploaded_file($_FILES[$this -> field]['tmp_name'], $this -> path.$this -> filename);
		return $test;
		}
	
	function setField($field)
		{
		$this -> field = $field;
		}
	function setPath($path)
		{
		$this -> path = $path;
		}
	
	
	function frm($options =  array())
		{
		return Html::formFile($this -> field, $options);
		}	
	
	function getLink()
		{
		return Html::link($this -> path.$this -> filename, $this -> filename);
		}	
}
?>
