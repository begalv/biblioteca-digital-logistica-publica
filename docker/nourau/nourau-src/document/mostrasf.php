<?php
/*
* Arquivo: mostrasf.php
* data da última modificação: 21/10/2010
* Após o arquivo suplementar ser acrescentado ou excluído atualiza a página de edição do documento
*/

require_once '../include/start.php';
require_once BASE . 'include/html.php';

$did = $_REQUEST['did'];
$tid = $_REQUEST['tid'];
$sqlsf = "SELECT supplementary_files.id, supplementary_files.filename, supplementary_files.remote, nr_document.topic_id, status FROM supplementary_files INNER JOIN nr_document on supplementary_files.document_id = nr_document.id WHERE supplementary_files.document_id =$did";
$qsf = db_query($sqlsf);

 echo "<p>";
 echo "<table class ='arqsupl'>";
  if (db_rows($qsf)==0) {
	 echo "<tr><td><b>Nennhum arquivo encontado</b></td></tr>";
 } else {
	 
	$i = 1;

    	while ($rowsf = db_fetch_array($qsf)){
			echo "<tr>";
			echo "<td>$i. ".$rowsf['filename']."</a></td>";
			echo "<td><a href =\"javascript:janelaArquivoSuplementar('arquivosuplementar.php?did=".$did."&id=".$rowsf['id']."&tid=".$tid."&op=d&status=".$rowsf['status']."',330,450)\">"._('Remove')."</a></td>";
			echo "</tr>";
			$i += 1;
     }
  }
echo "</table>";
echo "<p>\n";

?>