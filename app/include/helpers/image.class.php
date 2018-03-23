<?php
class Image
{
	var $filename;
	var $path;
	var $width;
	var $height;
	var $format;
	var $thumb;
	
	function __construct($filename, $path="")
		{
		$this -> setFilename($filename);
		$this -> setPath($path);
		$this -> getImgFormat();
		$this -> getImgSize();
		}
		
	function setPath($path)
		{
		$this -> path = $path;
		}	
	
	function setFilename($filename)
		{
		$this -> filename = $filename;
		}

	function getImgSize()
		{
		if(!$this -> format) die("NU ESTE IMAGINE");
		list($width, $height) = getimagesize($this -> path.$this -> filename);
		$this -> width = $width;
		$this -> height = $height;
		}

	function getImgFormat()
		{
		$format='';
		$filename = $this -> filename;
   		if(preg_match("/.jpg/i", "$filename"))
   		{
        $format = 'image/jpeg';
   		}
  		if (preg_match("/.gif/i", "$filename"))
   		{
        $format = 'image/gif';
   		}
   		if(preg_match("/.png/i", "$filename"))
   		{
       	$format = 'image/png';
   		}
		$this -> format = $format;
		}
	
	function copyImage($filename, $path="")
		{
		if (!copy($this -> path.$this -> filename, $path.$filename)) {
   			 die("nu am putut copia");
			}
		$this -> setFilename($filename);
		$this -> setPath($path);
		}	
	
	function resizeProc($procent)
		{
		$newwidth = number_format($this -> width*$procent/100);
		$newheight = number_format($this -> height*$procent/100);
		$this -> resize($newwidth,$newheight);
		}
		
	function resizeMaxSize($size)
		{
		if($this -> width > $this -> height)
			{
			$this -> resize($size, $this -> height*$size/$this -> width);
			}
		else
			{
			$this -> resize($this -> width*$size/$this -> height, $size);
			}	
		}
	
	function resize($newwidth, $newheight)
		{
       switch($this -> format)
       {
           case 'image/jpeg':
           $source = imagecreatefromjpeg($this -> path.$this -> filename);
           break;
           case 'image/gif';
           $source = imagecreatefromgif($this -> path.$this -> filename);
           break;
           case 'image/png':
           $source = imagecreatefrompng($this -> path.$this -> filename);
           break;
       }
       $this -> thumb = imagecreatetruecolor($newwidth,$newheight);
       imagealphablending($this -> thumb, false);
       imagecopyresampled($this -> thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $this -> width, $this -> height);
		}
	
	function mark($WatermarkImage, $Opacity=50)
		{
       switch($this -> format)
       		{
           case 'image/jpeg':
           $canvas_src = imagecreatefromjpeg($this -> path.$this -> filename);
           break;
           case 'image/gif';
           $canvas_src = imagecreatefromgif($this -> path.$this -> filename);
           break;
           case 'image/png':
           $canvas_src = imagecreatefrompng($this -> path.$this -> filename);
           break;
       		}
   		$canvas_img = imagecreatetruecolor($this -> width, $this -> height);
   		imagecopy($canvas_img, $canvas_src, 0,0,0,0, $this -> width, $this -> height);
   		imagedestroy($canvas_src);    // no longer needed

   // create true color overlay image:
   		$overlay_src = imagecreatefrompng($WatermarkImage);
		list($overlay_w, $overlay_h) = getimagesize($WatermarkImage);
   		$overlay_img = imagecreatetruecolor($overlay_w, $overlay_h);
   		imagecopy($overlay_img, $overlay_src, 0,0,0,0, $overlay_w, $overlay_h);
   		imagedestroy($overlay_src);    // no longer needed
   		$dest_x = $this -> width - $overlay_w - 5; 
   		$dest_y = $this -> height - $overlay_h - 5; 
   // setup transparent color (pick one):
   		$black  = imagecolorallocate($overlay_img, 0x00, 0x00, 0x00);
   		$white  = imagecolorallocate($overlay_img, 0xFF, 0xFF, 0xFF);
   		$magenta = imagecolorallocate($overlay_img, 0xFF, 0x00, 0xFF);   
   // and use it here:
   		imagecolortransparent($overlay_img, $black);

   		imagecopymerge($canvas_img, $overlay_img, $dest_x,$dest_y,0,0, $overlay_w, $overlay_h, $Opacity);
		$this -> thumb = $canvas_img;
		imagedestroy($overlay_img);
		}	
	
	function uniqueName()
		{
		$this -> filename = time()."_".$this -> filename;
		}
	
	function save($quality=90)
		{
		if($this -> thumb)
		{
		switch($this -> format)
       	   {
           case 'image/jpeg':
           @imagejpeg($this -> thumb, $this -> path.$this -> filename, $quality);
           break;
           case 'image/gif';
           @imagegif($this -> thumb, $this -> path.$this -> filename, $quality);
           break;
           case 'image/png':
           @imagepng($this -> thumb, $this -> path.$this -> filename);
           break;
       	   }
		}
		else
		{
		die("NIMIC IN thumb");
		}   
		$this -> getImgSize();
		}
	
	function __toString()
		{
		return Html::img($this -> path.$this -> filename, array("width" => $this -> width, "height" => $this -> height));
		}		
}
?>