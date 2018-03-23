<?php
class Window
{
	var $name = "";
	var $title = "&nbsp;";
	var $innerHTML = "";
	var $width;
	var $height;
	var $top=0;
	var $left=0;
	var $center = true;
	var $modal = true;
	var $head = true;
	var $closeClick = "xajax_close_window";
	function __construct($width=400, $height=300, $innerHTML="", $name="")
		{
		$this -> name = $name;
		$this -> innerHTML = $innerHTML;
		if($this -> name) $this -> name = $this -> name."_".time()."_".mt_rand();
		else $this -> name = time()."_".mt_rand();
		$this -> setWidth($width);
		$this -> setHeight($height);
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
	
	function render()
		{
		$mleft = $this -> width/2;
		$mtop = $this -> height/2;
		
	$out = '
	<div id="'. $this -> name .'" class="window"'; 
	if($this -> center)
		{
		//
		$out .= 'style="position:absolute;z-index:100;  width:'.$this -> width.'px;height:'.$this -> height.'px;  border:1px solid #000; left:50%; top:50%; padding:0px; background-color: #CCCCCC;margin-left:-'.$mleft.'px;margin-top:-'.$mtop.'px;" >';
		}
	else 
		{
		$out .= 'style="position:absolute;z-index:100;height:'.$height.'px;width:'.$width.'px;border:1px solid #000; left:'. $this -> left .'px; top:'. $this -> top .'px; padding:0px; background-color: #CCCCCC;" >';
		}	
	
	if($this -> head)
		{
			$out .= '
	<div style="width:100%; height:18px; background-image:url('. DOC_ROOT .'i/dialog-title.gif); border-bottom:1px solid #000;padding-top 2px;" class="window-head">
	<div style="width:70%;float:left">'. $this -> title .'</div>
	<div style="width:25%;float:right;text-align:right;"><img src="'. DOC_ROOT .'i/dialog-titlebar-close.png" onClick="'. $this -> closeClick .'(\''.$this -> name.'\')"></div>
	</div>
	';
		}	
	$this -> innerHTML = str_replace('<%closeok%>','<div align="center"><input id="close" name="close" type="button" value="Ok" onClick="'. $this -> closeClick .'(\''.$this -> name.'\')" class="btnTouch"></div>', $this -> innerHTML);
	$out .= '<div id="window-content-'.$this -> name.'" style="padding:5px;">'. $this -> innerHTML .'
	</div>
	</div>
	';
	return $out;
		}	

}
?>