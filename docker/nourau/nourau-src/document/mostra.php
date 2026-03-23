<?php
/*
*
* CarregaSubTopic.php
*
* carrega os meses que podem ser preenchidos pela unidades
*
*
*
*/

require_once '../include/start.php';
require_once BASE . 'include/html.php';


$remote = $_REQUEST['remote'];
$nomearq = $_REQUEST['nomearq'];
$status = $_REQUEST['docstatus'];


if ($remote == 'n'){
	       

  			echo "<p><label for=\"nomearq\">Trocar o documento principal:</label>\n";
  			echo " <input type=\"file\" id=\"file\" name=\"file\" onchange=\"nome_arquivo(this.value);\"></p>\n";
			
		    echo "<p><label for=\"nomearq\">Nome do arquivo: </label>";
  			echo "<input type=text id=\"nomearq\" name=\"filename\" value='".$nomearq."' size ='80' ".($status=='i'?" Readonly":"")." >";
  			echo "</p>\n";
}
else
   html_form_text("Endereço Digital", "filename", 80, $nomearq, 150, false, "", "");

?>