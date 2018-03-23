<?php
class FormField extends Html
{
	public static $out;

	function __construct($name, $options = array())
	{
		self::$out = $this -> field($name, $options);
	}

	function __toString()
	{
		return self::$out;
	}

	public static function field($name, $options = array())
	{
		if(!array_key_exists("type", $options)) $options['type'] = "text";
		if(!array_key_exists("attributes", $options)) $options['attributes'] = array();
		if(!array_key_exists("id", $options['attributes'])) $options['attributes']['id'] = $name;
		switch($options['type']) {
			case "text": {
				$out = parent::text($name, $options['value'], $options['attributes']);
			}break;
			case "textarea": {
				$out = parent::textarea($name, $options['value'], $options['attributes']);
			}break;
			case "input": {
				$options['attributes']['value'] = $options['value'];
				$out = parent::input($name, $options['attributes']);
			}break;
			case "password": {
				$options['attributes']['value'] = $options['value'];
				$options['attributes']['type'] = 'password';
				$out = parent::input($name, $options['attributes']);
			}break;
			case "hidden": {
				$out = parent::hidden($name, $options['value'] , $options['attributes']);
			}break;
			case "radiogroup": {
				if(is_array($options['options'])) {
					foreach($options['options'] as $key => $value) {
						if($options['value'] == $key) {
							$options['attributes']['checked']="checked";
						} else {
							unset($options['attributes']['checked']);
						}
						$out .= Html::radio($name, $name."_".$key, $key, $options['attributes'], $value);
					}
				}
				return $out;	
			}break;
			case "select": {
				$out = '';
				$options['selected'] = $options['value'];
				parent::append($out, parent::selectstart($name, $options['attributes']));
					
				if($options['default']) {
					if($options['selected']==$options['default_value']) {
						$selected = array("selected");
					}
					parent::append($out, parent::selectoption($options['default_value'], $options['default'], $selected));
				}
					
				if(is_array($options['options'])) {
					foreach($options['options'] as $key => $value) {
						if(!empty($options['selected']) && isset($options['selected']) && $options['selected'] == $key) {
							$selected = array("selected");
						}
						else {
							$selected = array();
						}
						parent::append($out, parent::selectoption($key, $value, $selected));
					}
				}
				else {
					global $db;
					$rows = $db -> getRowsNum($options['options']);
					if(isset($rows)) {
						foreach($rows as $row) {
							if(!empty($options['selected']) && isset($options['selected']) && $options['selected'] == $row[0]) {
								$selected = array("selected");
							}
							else {
								$selected = array();
							}
							parent::append($out, parent::selectoption($row[0], $row[1], $selected));
						}
					}
				}
				parent::append($out, parent::selectend());
			}break;
			case "multiselect":{
				$out = '';
				$options['selected'] = $options['value'];
				$name.="multiple";
				parent::append($out, parent::selectstart($name, $options['attributes']));
					
				if(is_array($options['options'])) {
					foreach($options['options'] as $key => $value) {
						if(!empty($options['selected']) && isset($options['selected']) && $options['selected'] == $key) {
							$selected = array("selected");
						}
						else {
							$selected = array();
						}
						parent::append($out, parent::selectoption($key, $value, $selected));
					}
				}
				else {
					global $db;
					$rows = $db -> getRowsNum($options['options']);
					if(isset($rows)) {
						foreach($rows as $row) {
							if(!empty($options['selected']) && isset($options['selected']) && $options['selected'] == $row[0]) {
								$selected = array("selected");
							}
							else {
								$selected = array();
							}
							parent::append($out, parent::selectoption($row[0], $row[1], $selected));
						}
					}
				}
				parent::append($out, parent::selectend());
					
			}break;
		}
		self::$out =  $out;
		return $out;
	}
}

class Form extends Html
{
	var $name;
	var $frmOptions = array();
	var $frmText = "";
	function __construct($name, $options = array())
	{
		$this -> name = $name;
		if(!array_key_exists("id", $options)) {
			$options["id"] = $this -> name;
		}

		if(array_key_exists("submit", $options)) {
			if($options['submit'] == false) {
				unset($options['submit']);
				$options['onSubmit'] = "return false;";
			}
		}
		$this -> frmOptions = $options;
	}

	function addField($name, $options = array())
	{
		$this -> frmText .= FormField::field($name, $options);
	}

	function addLabel($text, $for="", $options = array())
	{
		$this -> frmText .= parent::label($text, $for, $options);
		$this -> frmText .= "<br />";
	}

	function addText($txt)
	{
		$this -> frmText .= $txt;
	}

	function get()
	{
		$out = parent::form($this -> name, $this -> frmOptions);
		$out .= $this -> frmText;
		$out .= parent::formEnd();
		return $out;
	}
}
?>