<?php
class Dialog
{
	var $name;
	var $width;
	var $height;
	var $title = '';
	var $modal = false;
	var $draggable = true;
	var $resizable = false;
	var $close = true;
	var $onCloseDestroy = true;
	var $buttons = array();
	var $focus;
	var $innerHTML;
	
	function __construct($width=600, $height=400, $innerHTML="",$name="")
	{
		$this -> name = $name;
		$this -> innerHTML = $innerHTML;
		if($this -> name) $this -> name = $this -> name;
		else $this -> name = mt_rand();
		$this -> setWidth($width);
		$this -> setHeight($height);
	}
	
	function addButton($value, $function="") 
	{
		switch($value) {
			case "Renunta":
			case "Inchide":{
				$function = $function."\$(this).dialog('close');";
			}break;
		}
		$function = str_replace('<%close%>', "\$(this).dialog('close');", $function);
		$this -> buttons[$value] = $function;
	}
	
	function getHtml()
	{
		return '<div id="'. $this -> name .'">'.$this -> innerHTML.'</div>';
	}
	
	function script()
	{
		$options = array();
		if($this -> modal) {
			$options[] = "modal:true";
		}
		$options[] = "width:". $this -> width ."";
		$options[] = "height:". $this -> height ."";
		$options[] = "title:'". $this -> title ."'";
		
		if(!$this -> draggable) {
			$options[] = "draggable:false";
		}
		
		if(!$this -> resizable) {
			$options[] = "resizable:false";
		}
		
		if($this -> buttons) {
			$b = array();
			foreach($this -> buttons as $key => $value) {
				$b[] = "'$key': function() {". $value ."}";
			}
			$options[] = "buttons: {". implode(',', $b) ."}";
		}
		
		if($this -> onCloseDestroy) { 
			$options[] = "close: function() { \$('#". $this -> name ."').remove() }";
		}
		
		$script = "
		\$(document).ready(function() {
		\$('#". $this -> name ."').dialog({". implode(',', $options) ."});
		})
		";
		return $script;
	}
	
	function scriptTag()
	{
		return '
		<script type="text/javascript">'. $this -> script() .'</script>
		';
	}
	
	function setWidth($width)
	{
		if($width)
			$this -> width = $width;
		else
			$this -> width = 400;
	}	

	function setHeight($height)
	{
		if($height) 
			$this -> height = $height;
		else 
			$this -> height = 400;	
	}
		
	function append($txt)
	{
		$this -> innerHTML .= $txt; 
	}	
	
	function open() {
		$objResponse = new xajaxResponse();
		$objResponse -> append("windows", "innerHTML", $this -> getHtml());
		$objResponse -> script($this -> script());
		if(!$this -> close) {
			$objResponse -> script("$('.ui-dialog-titlebar-close').hide();");
		}
		return $objResponse;
	}
}
?>