<?php
/*
*
* Mostra o campo URL
*
*
*
*/

require_once '../include/start.php';
require_once BASE . 'include/html.php';

$tipo = $_REQUEST['tipo'];

if ($tipo == 'rmt') {
	
	$remote = $_REQUEST['remote'];
	$url = $_REQUEST['url'];

    if ($remote == 's'){
	   html_form_text("Endereço Remoto", 'url', 80, $url, 150, false, '', 'URL do conteúdo hospedado remotamente');
	} else{ 
		html_form_hidden('url', $url);
	}

} else {
	$archieve = $_REQUEST['archieve'];
	
    if ($archieve == 's'){
		
	  // show categories for this topic
		 if (empty($category))
			$category = array();
			$q = db_query("SELECT id,name,description FROM nr_category ORDER BY name");
		 
		  echo html_b("Tipo de Documentos:") . "<br>\n";
		  
		  echo "<select name=\"category[]\" multiple size=\"4\">\n";
		  while ($a = db_fetch_array($q)) {
			if (in_array($a['id'], $category))
			  echo '<option selected ';
			else
			  echo '<option ';
			echo "value=\"{$a['id']}\">" . _($a['name']) . ' - ' . _($a['description']) . "</option>\n";
		  }
		  echo "</select><p>\n";	
	
	} else {
       html_form_hidden('category', null);
	}   
}

?>