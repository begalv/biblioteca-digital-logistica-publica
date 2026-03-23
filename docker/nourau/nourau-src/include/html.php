<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

/*-------------- local variables --------------*/

$html_table_align;
$html_table_border;
$html_table_row_align;

global $lg;


/*-------------- basic functions --------------*/

function html_header ($title = '', $head = '', $body = '', $tag=false, $result='')
{
  global $cfg_site;
  static $sent = false;

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  if (!$sent) {
   // echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n";
     echo "<!DOCTYPE html>";
	 echo "<html lang='pt-BR'>\n";
        
	if ($tag) {
	    echo "<head>\n<title>".$result['title']."</title>\n";
		echo "<meta name='robots' content='index, follow' />\n";
		echo "<meta name='author' content='".trim($result['author'])."' />\n";
		echo "<meta name='description' content='".substr($result['abstract'],0,200)."...' />\n";
		echo "<meta name='keywords' content='".$result['keywords'] ."' />\n";
		echo "<meta name='generator' content='Biblioteca Digital da UNICAMP' />\n";
		echo "<meta name='date' content='".$result['created']."' />\n";
	}
	else
	  echo "<head>\n<title>$title</title>\n";

	echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=8\" />";
	echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />";
	//echo "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css\">\n";
	/*-- Boxicons CDN Link --*/
     echo "<link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>\n";
	   echo "<link href=\"{$cfg_site}layout/jquery-ui-1.8.9.custom.css\" rel=\"stylesheet\" type=\"text/css\"/ media=\"screen\">\n";
     echo "<link rel=\"stylesheet\" href=\"{$cfg_site}layout/estilo.css\" rel=\"StyleSheet\" type=\"text/css\" media=\"screen\">\n";

   //Não comentar a linha do javaScript funcs.js e jquery.js, utilizado utilizada no site
     
    echo "<script LANGUAGE=\"JavaScript\" type='text/javascript' src='{$cfg_site}script/funcs.js'  charset='utf-8'></script>\n";
    echo "<script type='text/javascript' src='{$cfg_site}script/js/jquery-1.4.4.min.js'></script>\n";
	  echo "<script type='text/javascript' src='{$cfg_site}script/js/jquery.validate.js'></script>\n";
    echo "<script type='text/javascript' src='{$cfg_site}script/js/jquery-ui-1.8.8.custom.min.js'></script>\n";
	  echo "<script type='text/javascript' src='{$cfg_site}script/js/jquery.maskedinput-1.2.2.js'></script>\n";
    echo "<script type='text/javascript' src='https://cdn.jsdelivr.net/npm/clappr@latest/dist/clappr.min.js'> </script>\n";


   if ($tag) {
         echo "<link rel='schema.DCTERMS' href='http://purl.org/dc/terms/' >\n";
         echo "<link rel='schema.DC' href='http://purl.org/dc/elements/1.1/' >\n";
         echo "<meta name='DC.language' content='por' xml:lang='pt' scheme='DCTERMS.RFC1766' />\n";
   	  echo "<meta name='DC.creator' content'".trim($result['author'])."' xml:lang='pt'>\n";
   	  echo "<meta name='DC.contributor' content='".trim($result['author'])."' xml:lang='pt'>\n";
   	  echo "<meta name='DC.title' content='".$result['title']."' xml:lang='pt'>\n";
   	  if ($result['title_en'] != '')
   	  echo "<meta name='DC.title' content='".$result['title_en']."' xml:lang='en'>\n";

         $keywords = explode(",",$result['keywords']);

         foreach ($keywords as $word) {
            if ($word!=' ')
            echo "<meta name='DC.subject' content='".trim($word)."' xml:lang='pt'>\n";
         }

   	  echo "<meta name='DC.subject' content='Constitutional and consumerists proving garantees' xml:lang='en'>\n";

   	  if ($result['keywords_en']!='') {

   		  $keywords_en = explode(",",$result['keywords_en']);
   		  foreach ($keywords_en as $word_en) {
   		    if ($word_en!=' ')
   		   	  echo "<meta name='DC.subject' content='".trim($word_en)."' xml:lang='en'>\n";
   		  }
         }


   	  echo "<meta name='DC.description' content='".trim(substr(str_replace(array('<','>'),array('&lt;','&gt;'),$result['abstract']),8,strpos($result['abstract'],'Abstract:')))."' xml:lang='pt'>\n";
   	  echo "<meta name='DC.publisher' content='Biblioteca Digital da Unicamp' xml:lang='pt'>\n";
   	  echo "<meta name='DC.date' content='".$result['date_defense']."' xml:lang='pt'>\n";
         echo "<meta name='DC.identifier' content='{$cfg_site}document/?code=".$result['code']."' xml:lang='pt'>\n";

    }



    echo "$head</head>\n<body>\n\n";
	
	
	$sent = true;
    return true;
  }
  else
    return false;
}

function html_footer ()
{	
	
  echo "\n</body>\n</html>\n";
}

function html_a ($content, $url, $target = '')
{
  if (empty($target))
    return "<a href=\"$url\">$content</a>";
  else
    return "<a href=\"$url\" target=\"$target\">$content</a>";
}

function html_b ($content)
{
  return "<b>$content</b>";
}


function html_button ($content)
{
  return "<div class=\"button\">$content</div>";	
}

function html_big ($content)
{
  return "<big>$content</big>";
}

function html_error ($content)
{
  return "<span class=\"emphasis\"><b>$content</b></span>";
}

function html_h ($content)
{
  return "<div class=\"title\">$content</div>\n\n";
  	
}

function html_p ($content, $align = 'center')
{
  return "<p align=\"$align\">$content</p>\n";
}

function html_small ($content)
{
  return "<small>$content</small>";
}


function html_h1 ($content)
{
  return "<h1>$content</h1>\n\n";
}




/*-------------- table functions --------------*/

function html_table_begin ($border = false, $align = 'center', $auto = false)
{
  global $html_table_align, $html_table_auto, $html_table_border;

  $html_table_align = $align;
  $html_table_auto = ($auto) ? 1 : 0;
  if ($html_table_border = $border) {
    echo "<table width=\"100%\" border=\"0\" cellpadding=\"1\" cellspacing=\"0\">\n";
    echo "<tr class=\"border\"><td>";
  }
  echo "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\n";
}

function html_table_item ($content, $class = '', $align = '')
{
  global $html_table_align, $html_table_auto;

  if (empty($class)) {
    if ($html_table_auto)
      $class = ($html_table_auto & 1) ? 'odd' : 'even';
    else
      $class = 'base';
  }
  if (empty($align))
    $align = $html_table_align;
  echo "<tr class=\"$class\"><td align=\"$align\">$content</td></tr>\n";
}

function html_table_row_begin ($class = '', $align = '')
{
  global $html_table_align, $html_table_auto, $html_table_row_align;

  if (empty($class)) {
    if ($html_table_auto)
      $class = ($html_table_auto++ & 1) ? 'odd' : 'even';
    else
      $class = 'base';
  }
  if (empty($align))
    $html_table_row_align = $html_table_align;
  echo "<tr class=\"$class\">\n";
}

function html_table_row_item ($content, $align = '', $width = '')
{
  global $html_table_row_align;

  if (empty($align))
    $align = $html_table_row_align;
  if (!empty($width))
    echo "<td align=\"$align\" width=\"$width\">$content</td>\n";
  else
    echo "<td align=\"$align\">$content</td>\n";
}

function html_table_row_end ()
{
  echo "</tr>\n";
}

function html_table_end ()
{
  global $html_table_border;

  echo "</table>\n";
  if ($html_table_border)
    echo "</td></tr></table>\n";
}


/*-------------- form functions --------------*/

function html_form_begin ($action, $post = true, $enctype = '', $target = '')
{
  echo '<form method="' . (($post) ? 'post' : 'get') . "\" action=\"$action\"";
  if (!empty($enctype))
    echo " enctype=\"$enctype\"";
  if (!empty($target))
    echo " target=\"$target\"";
  echo ">\n";
}

function html_form_area ($title, $name, $rows, $content, $max = 0, $required, $placeHolder, $info='') {

  $asteristico = (($required==true)?'*':'');
  $content = htmlentities(stripslashes((string) $content, ));
  echo "<p>";
  if (!empty($title)) {
    if ($max){
      echo "<label for=\"$name\">$title <span  class=\"spanAsteristico\">$asteristico</span>:</label>\n";
	  if ($info<> '')
		 echo "<br><span class=\"spanForm\">$info</span>";  
    }
    else
      echo "<label for=\"$name\">$title:</label>\n";
  }
  /*if ($max > 0 )
	$ph = $placeHolder. "&#013;(Limitado em {$max} caracteres)";
  else 
	$ph = "Limitado em {$max} caracteres";*/
  
  $ph = $placeHolder;

 $required = (($required==true)?'required ="required"':'');
  echo "<textarea name=\"$name\" rows=\"$rows\" cols=\"80\" wrap=\"soft\" placeholder=\"$ph\"  $required >$content</textarea>\n";
  echo "</p>";
}

function html_form_check ($title, $name, $options, $array = '', $break = true) {
  if (!empty($title)) {
    echo "<b>$title:</b>\n";
    if ($break)
      echo '<br>';
  }
  
  foreach (array_keys($options) as $key) {
	  
    if (empty($array[$key]))
      echo '<input ';
    else
      echo '<input checked ';
    echo "type=\"checkbox\" name=\"$name" . "[$key]\" value=\"$key\"> {$options[$key]}\n";
  }
}

function html_form_file ($title, $name, $break = true,  $info='',$required = false, $accept='')
{
 $asteristico = (($required==true)?'*':''); 	
  if (!empty($title)) {
       echo "<label for=\"$name\">$title <span  class=\"spanAsteristico\" >$asteristico</span> :</label>\n";
	  if ($info<> '')
		 echo "<br><span class=\"spanForm\">$info</span>";  
    if ($break)
      echo '<br>';
  }
    $required = (($required==true)?'required':'');
	
	
   echo "<input type=\"file\" id=\"file\" name=\"$name\" $required  >\n";
}

function html_form_hidden ($name, $content)
{
  $content = htmlentities(stripslashes((string)$content));
  echo "<input type=\"hidden\" name=\"$name\" value=\"$content\">\n";
}

function html_form_password ($title, $name, $size, $content, $max, $required, $placeHolder, $break = true)
{
  $content = htmlentities(stripslashes((string)$content));
  echo "<p>";
  if (!empty($title)) {
     echo "<label for=\"$name\">$title:</label>\n";
    if ($break)
      echo '<br>';
  }
    $required = (($required==true)?'required ="required"':'');
  echo "<input id=\"$name\" type=\"password\" name=\"$name\" value=\"$content\" maxlength=\"$max\" size=\"$size\" $required  placeholder=\"$placeHolder\"  autocomplete=\"new-password\" >\n";
  echo "</p>";
}

function html_form_radio ($title, $name, $options, $default = '', $showIcone='',$javacode='', $break = true)
{
  if (!empty($title)) {
	echo "<p>";  
    echo "<label for=\"$name\">$title:</label>\n";
	if (!empty($showIcone))
	  echo "<span id='ajuda'>".$showIcone."</span>";
    if ($break)
      echo '<br>';
  }

  foreach (array_keys($options) as $key) {
       
	echo "<span class=\"spanAcesso\"><input type=\"radio\" name=\"$name\" value=\"$key\" ".($key == $default?"checked":"") ."> {$options[$key]}</span>\n";

  }
  echo "</p>";
}

function html_form_select ($title, $name, $options, $default = '',$required, $break = true)
{
  $asteristico = (($required==true)?'*':'');  	
  if (!empty($title)) {
   echo "<label for=\"$name\">$title <span  class=\"spanAsteristico\" >$asteristico</span> :</label>\n";
    if ($break)
      echo '<br>';
  }
   $required = (($required==true)?'required':'');
  echo "<select name=\"$name\" id=\"$name\" size=\"1\" $required>\n";
  foreach (array_keys($options) as $key) {
    if ($key == $default)
      echo '<option selected ';
    else
      echo '<option ';
    echo "value=\"$key\">{$options[$key]}</option>\n";
  }
  echo "</select>\n";
}

function html_form_submit ($label, $name = '',$validar=true)
{
 echo "<p>";	
  echo '<input type="submit"';
  if (!empty($name))
    echo " name=\"$name\"";

  if(!$validar)
   echo " formnovalidate ";	  
  echo " value=\"$label\">\n";
  echo "</p>";
}

function html_form_text ($title, $name, $size, $content, $max, $required, $placeHolder, $info='', $break = true)
{
  $content = htmlentities(stripslashes((string)$content));
  echo "<p>";
  if (!empty($title)) {
	$asteristico =  (($required==true)?'*':'');    
	echo "<label for=\"$name\">$title  <span class=\"spanAsteristico\">$asteristico</span> :</label>\n";
	
	if ($info<> '')
	   echo "<br><span class=\"spanForm\">$info</span>";
    if ($break)
      echo '<br>';
  }  
   $required = (($required==true)?'required':'');
  
  echo "<input id=\"$name\" type=\"text\" id=\"$name\" name=\"$name\" value=\"$content\" maxlength=\"$max\" size=\"$size\" $required placeholder=\"$placeHolder\" >\n";
  echo "</p>";
}


function html_form_end ()
{
  echo "</form>\n";
}

?>