<?php
class Html
{
		public static $tags = array(
		'meta' => '<meta%s/>',
		'metalink' => '<link href="%s"%s/>',
		'link' => '<a href="%s"%s>%s</a>',
		'mailto' => '<a href="mailto:%s" %s>%s</a>',
		'form' => '<form name="%s" %s>',
		'formend' => '</form>',
		'input' => '<input name="%s" %s/>',
		'text' => '<input type="text" name="%s" value="%s" %s/>',
		'textarea' => '<textarea name="%s" %s>%s</textarea>',
		'hidden' => '<input type="hidden" name="%s" value="%s" %s/>',
		'textarea' => '<textarea name="%s" %s>%s</textarea>',
		'checkbox' => '<input type="checkbox" name="%s" %s/>',
		'checkboxmultiple' => '<input type="checkbox" name="%s[]"%s />',
		'radio' => '<input type="radio" name="%s" id="%s" value="%s" %s />%s',
		'selectstart' => '<select name="%s"%s>',
		'selectmultiplestart' => '<select name="%s[]"%s>',
		'selectempty' => '<option value=""%s>&nbsp;</option>',
		'selectoption' => '<option value="%s"%s>%s</option>',
		'selectend' => '</select>',
		'optiongroup' => '<optgroup label="%s"%s>',
		'optiongroupend' => '</optgroup>',
		'checkboxmultiplestart' => '',
		'checkboxmultipleend' => '',
		'password' => '<input type="password" name="%s" %s/>',
		'file' => '<input type="file" name="%s" %s/>',
		'file_no_model' => '<input type="file" name="%s" %s/>',
		'submit' => '<input type="submit" name="%s" value="%s" %s/>',
		'submitimage' => '<input type="image" src="%s" %s/>',
		'button' => '<input type="%s" %s/>',
		'image' => '<img src="%s" %s/>',
		'table' => '<table %s>%s</table>',
		'tablestart' => '<table %s>',
		'tableend' => '</table>',
		'tableheader' => '<th%s>%s</th>',
		'tableheaderrow' => '<tr%s>%s</tr>',
		'tablecell' => '<td%s>%s</td>',
		'tablerow' => '<tr%s>%s</tr>',
		'div' => '<div%s>%s</div>',
		'divstart' => '<div%s>',
		'divend' => '</div>',
		'para' => '<p%s>%s</p>',
		'parastart' => '<p%s>',
		'label' => '<label for="%s"%s>%s</label>',
		'fieldset' => '<fieldset><legend>%s</legend>%s</fieldset>',
		'fieldsetstart' => '<fieldset><legend>%s</legend>',
		'fieldsetend' => '</fieldset>',
		'legend' => '<legend>%s</legend>',
		'css' => '<link rel="%s" type="text/css" href="%s" %s/>',
		'style' => '<style type="text/css"%s>%s</style>',
		'ul' => '<ul%s>%s</ul>',
		'ol' => '<ol%s>%s</ol>',
		'li' => '<li%s>%s</li>',
		'h3' => '<h3%s>%s</h3>'
	);
	public static function append(&$txt, $text, $insertBefore = '', $insertAfter = '')
		{
			$txt .= $insertBefore.$text.$insertAfter;
		}
	
	public static function htmlAttribute($key, $value)
		{
		$attribute = '';
		$attributeFormat = '%s="%s"';
		$minimizedAttributes = array('compact', 'checked', 'declare', 'readonly', 'disabled', 'selected', 'defer', 'ismap', 'nohref', 'noshade', 'nowrap', 'multiple', 'noresize');
		if(is_numeric($key))
			{
			if(in_array($value, $minimizedAttributes))
				{
				$attribute = sprintf($attributeFormat, $value, $value);
				}
			}
		else
			{
			if(in_array($key, $minimizedAttributes))
				{
				if ($value === 1 || $value === true || $value === 'true' || $value == $key) 
					{
					$attribute = sprintf($attributeFormat, $key, $key);
					}
				}
			else
				{
				$attribute = sprintf($attributeFormat, $key, $value);
				}	
			}	
		return $attribute;
		}
	
	public static function htmlAttributes($htmlAttributes, $insertBefore = ' ', $insertAfter = null)
		{
		$out = '';
		if(!empty($htmlAttributes))
		{
		if(is_array($htmlAttributes))
			{
			foreach($htmlAttributes as $key => $value)
				{
				$attributes[] = self::htmlAttribute($key, $value);
				}
			$out = implode(' ', $attributes);
			}
		else 
			{
			$out = $htmlAttributes;
			}
		}		
		return $insertBefore.$out.$insertAfter;	
	}
	
	
	public static function div($innerHTML, $options = array())
		{
		return sprintf(self::$tags['div'], self::htmlAttributes($options), $innerHtml);
		}
	
	public static function divstart($options = array())
		{
		return sprintf(self::$tags['divstart'], self::htmlAttributes($options));
		}
	
	public static function divend()
		{
		return self::$tags['divend'];
		}	
	
	/* START FORM FUNCTIONS*/
	
	public static function label($text, $for="", $options = array())
		{
		return sprintf(self::$tags['label'], $for, self::htmlAttributes($options), $text);
		}
	
	public static function submit($name,$value, $options = array())
		{
		return sprintf(self::$tags['submit'], $name, $value, self::htmlAttributes($options));
		}
	
	public static function formFile($name, $options = array())
		{
		return sprintf(self::$tags['file'], $name, self::htmlAttributes($options));
		}
	
	public static function form($name, $options = array()){
		if(!array_key_exists("id", $options)) $options['id'] = $name;
		return sprintf(self::$tags['form'], $name, self::htmlAttributes($options));
	}
	
	public static function formEnd()
		{
		return self::$tags['formend'];
		}
	
	public static function input($name, $options = array())
		{
		return sprintf(self::$tags['input'], $name, self::htmlAttributes($options));
		}
		
	public static function textarea($name, $value, $options = array())
		{
		if(!array_key_exists("cols", $options)) $options['cols'] = "30";
		if(!array_key_exists("rows", $options)) $options['rows'] = "7";
		return sprintf(self::$tags['textarea'], $name, self::htmlAttributes($options), $value);
		}
		
	public static function button($type, $options = array())
		{
		return sprintf(self::$tags['button'], $type, self::htmlAttributes($options));
		}
		
	public static function hidden($name, $value, $options = array())
		{
		return sprintf(self::$tags['hidden'], $name, $value, self::htmlAttributes($options));
		}
		
	public static function text($name, $value, $options = array())
		{
		return sprintf(self::$tags['text'], $name, $value, self::htmlAttributes($options));
		}	
	public static function radio($name, $id, $value, $options = array(), $label = null) 
		{
		return '<label>'.sprintf(self::$tags['radio'], $name, $id, $value, self::htmlAttributes($options), $label).'</label>';
		}	
	public static function selectstart($name, $options = array())
		{
		return sprintf(self::$tags['selectstart'], $name, self::htmlAttributes($options));
		}
		
	public static function selectend()
		{
		return self::$tags['selectend'];
		}
			
	public static function selectoption($value, $text, $options = array())
		{
		return sprintf(self::$tags['selectoption'], $value, self::htmlAttributes($options), $text);
		}	
		
	public static function checkbox($name, $options = array())
		{
		return sprintf(self::$tags['checkbox'], $name, self::htmlAttributes($options));
		}
	/* END FORM FUNCTIONS*/
		
	public static function image($url, $options = array())
		{
		return sprintf(self::$tags['image'], $url, self::htmlAttributes($options));
		}
			
	/* START TABLE FUNCTIONS*/	
	public static function tablecell($innerHTML, $options = array())	
		{
		if($innerHTML === NULL) $innerHTML = "&nbsp;";
		return sprintf(self::$tags['tablecell'], self::htmlAttributes($options), $innerHTML);
		}
	public static function tablerow($innerHTML, $options = array())
		{
		return sprintf(self::$tags['tablerow'], self::htmlAttributes($options), $innerHTML);
		}	
	public static function tableheader($innerHTML, $options = array())
		{
		return sprintf(self::$tags['tableheader'], self::htmlAttributes($options), $innerHTML);
		}
				
	public static function table($innerHtml, $options = array())
		{
		return sprintf(self::$tags['table'],self::htmlAttributes($options), $innerHtml);
		}
	public static function tableStart($options = array())
		{
		return sprintf(self::$tags['tablestart'],self::htmlAttributes($options));
		}
	public static function tableEnd()
		{
		return self::$tags['tableend'];
		}
	/* END TABLE FUNCTIONS*/	
		
	public static function link($url, $value, $options = array())
	{
		return sprintf(self::$tags['link'], $url, self::htmlAttributes($options), $value);
	}
	
	public static function onClickLink($value, $fn, $options = array()) {
		$options['onClick'] = $fn.";return false;";
		return sprintf(self::$tags['link'], "#", self::htmlAttributes($options), $value);
	}
	
	public static function img($url, $options = array())
	{
		return sprintf(self::$tags['image'], $url, self::htmlAttributes($options));
	}
	
	/**
	 * returneaza div div cu scrollbar pe dreapta
	 * @param object $innerHTML
	 * @param object $h [optional] - inaltime css
	 * @param object $w [optional] - latime css
	 * @param object $options [optional] - html array attributes 
	 * @return 
	 */
	public static function overflowDiv($innerHTML, $h="400px", $w="100%", $options = array())
	{
		if(!isset($options['style'])) {
			$options['style'] = '';
		}
		
		$options['style'] .= "overflow:scroll; overflow-x:hidden;width:$w;height:$h;";
		return sprintf(self::$tags['div'], self::htmlAttributes($options), $innerHTML);
	}
	
	public static function imglink($url, $img, $olink = array(), $oimg = array())
	{
		$oimg['border'] = 0;
		return self::link($url, self::img($img, $oimg), $olink);
	}
	
	public static function redirect($url)
		{
		return '
		<script>
		window.location.href = \''.$url.'\';
		</script>
		';
		}
		
	public static function h3($title,$options=array())
	{
		return sprintf(self::$tags['h3'],self::htmlAttributes($options),$title);
	}
	
	public static function ul($innerHtml,$options=array())
	{
		return sprintf(self::$tags['ul'],self::htmlAttributes($options),$innerHtml);
	}
	
	public static function li($innerHtml,$options=array())
	{
		return sprintf(self::$tags['li'],self::htmlAttributes($options),$innerHtml);
	}
}
?>