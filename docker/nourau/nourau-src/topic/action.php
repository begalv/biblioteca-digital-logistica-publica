<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.
//Topic Action: /topic/action.php

require_once '../include/start.php';
require_once BASE . 'include/control.php';
require_once BASE . 'include/page.php';
require_once BASE . 'include/util.php';
require_once BASE . 'include/util_t.php';


if (isset($_GET['op']))
 	$op = $_GET['op'];

if (isset($_GET['tid']))
 	$tid =  $_GET['tid'];

if (isset($_GET['pid']))
 	$pid =  $_GET['pid'];
else 
	$pid = 0;

if (isset($_POST['conf']))
   $conf = $_POST['conf'];
   
$back = "{$cfg_site}document/list.php?tid=$pid";   
   

if (!is_administrator() && !is_responsable() )
  message($back,"Acesso Negado!", "failure");





if ($op == 'd') { // ---------------- remove topic
  // validate input
  if (!valid_int($tid))
    message($back,"Parâmetro Inválido !", "failure");

  // check if topic is empty
 /*  $doc = db_simple_query("SELECT COUNT(ID) FROM nr_document WHERE topic_id='$tid' AND status <> 'd'");
  $top = db_simple_query("SELECT COUNT(ID) FROM topic WHERE parent_id='$tid'");
 
 if ($doc + $top)
      message(_('A non-empty topic cannot be removed'), $back);
 */

  // ask confirmation 
  if (empty($conf)) {
    $topic = get_topic($tid, 'name');
    confirm_topic("Você deseja remover a Coleção $topic ?", "{$_SERVER['PHP_SELF']}?op=$op&tid=$tid&pid=$pid");
  }


  if ($conf =='Sim') {
    // remove topic
    $pid = get_topic($tid, 'parent_id');
    db_command("DELETE FROM topic WHERE id='$tid'");
    db_command("DELETE FROM nr_topic_category WHERE topic_id='$tid'");
    db_command("DELETE FROM topic_users WHERE topic_id='$tid'");
	$doc = db_simple_query("SELECT COUNT(ID) FROM nr_document WHERE topic_id='$tid' AND status = 'a'");
	if ($doc)
	   db_command("DELETE FROM nr_document WHERE topic_id='$tid'");

    add_log('c', 'td', "tid=$tid");
	
  //  message('Coleção removido', );
      message($back,"Coleção removido.", "success");
    //redirect("{$cfg_site}document/list.php" . (($pid) ? "?tid=$pid" : ''));
  }
 else
   redirect($back);
}



if ($op == 'l') {
	
	if (isset($_POST['tid'])&& is_numeric($_POST['tid']))
		$tid = $_POST['tid'];
	
    $qCL = db_query("SELECT id, name, parent_id, archieve FROM topic WHERE parent_id = '$tid' order by name");   
	//echo "<option selected='selected'  value=''>-- escolha uma das coleções --</option>";
	
	$i =0;
	$response = array();
	while ($aCL = db_fetch_array($qCL)) {
		   
		$response[$i] = ['id'=>$aCL['id'],'name'=>$aCL['name'],'archieve' =>$aCL['archieve'], 'parent_id'=>$aCL['id']];
		
		$qCL1 = db_query("SELECT id,name,parent_id,archieve FROM topic WHERE parent_id = {$aCL['id']}  ORDER BY name");
			while ($aCL1 = db_fetch_array($qCL1)) {
				$i++;
				$response[$i] = ['id'=>$aCL1['id'],'name'=>trim($aCL1['name']),'archieve' =>$aCL1['archieve'], 'parent_id'=>$aCL1['parent_id']];
			}
		$i++;
		//$data[$i] = ['PaÃ­ses'=>$flag, 'Total de Visitas'=>$total];
   
		
		  /* if ($aCL['archieve']=='n')
			   
		   
					echo "<optgroup label = '".$aCL['name']."'>";
			else 		   
				echo "<option value='".$aCL['id']."'>{$aCL['name']}</option>";   
			
			$qCL1 = db_query("SELECT id,name,parent_id,archieve FROM topic WHERE parent_id = {$aCL['id']}  ORDER BY name");
			while ($aCL1 = db_fetch_array($qCL1)) {
				echo "<option value='".$aCL1['id']."'>{$aCL1['name']}</option>";
			}*/
			
    }
	
	//	echo "</optgroup>";
	
	print json_encode($response);

}


if ($op == 'TI') {
	
	if (isset($_POST['tid']))
		$tid = $_POST['tid'];
	
        $qTI = db_query("SELECT  ti.id, ti.name,  count(nr.id) as qtde FROM nr_document nr inner join type_information ti on nr.typeinform_id = ti.id Where status = 'a' and topic_id = '$tid'  group by ti.id, ti.name  ORDER by ti.name");   

		/*echo "<option selected='selected'  value=''>-- escolha um Tipo de Informação --</option>";
		while ( $aTI = db_fetch_array( $qTI)) {
				echo "<option value='".$aTI['id']."'>{$aTI['name']}({$aTI['qtde']})</option>";   
		}	*/
		$response = array();
		$i=0;
		while ( $aTI = db_fetch_array( $qTI)) {
				$response[$i] = ['id'=>$aTI['id'],'name'=>$aTI['name'],'quantidade' =>$aTI['qtde']];
				$i++;
		}

		print json_encode($response);
}


if ($OP == 'TC') {
  
    $tidNew = 873;
	$tidOld = 729;
	$tipo_material = 14;
  
	$cons = "Select nr_document.id, nr_document.code, nr_document.topic_id, remote, format_id, status from nr_document where nr_document.topic_id = ".$tidOld."  and typeinform_id = ".$tipo_material."  order by id";
	echo  $cons . "\n\r";
	$qCons = pg_query($conexao, $cons);
	$qtde= pg_num_rows($qCons);

	while($a= pg_fetch_array($qCons)){

		echo $a['id'] ." | ".  $a['code']    ." | ".  $a['topic_id'] ." | ". $a['remote']." | ". $a['format_id']." | ". $a['status'].   "\n\r";
		$did = $a['id'] ;
		$code = $a['code'];
		
		/*Atualizar o tópico*/
		$update = "UPDATE nr_document SET topic_id =".$tidNew."  WHERE code = '".$code."'";
		echo  $update . "\n\r";
		
		$result_Update = pg_query($conexao, $update );
	  
		if (!$result_Update)
		 $linha = "An error occurred.\n";
		
		/*Atualizar o tópico- Visitas e downloads*/
		$updateZ = "UPDATE z_visitas SET topic_id =".$tidNew."  WHERE code = '".$code."'";
		echo  $updateZ . "\n\r";

	   $result_UpdateZ = pg_query($conexao_zeus, $updateZ );
	  
	   if (!$result_UpdateZ)
		 $linha = "An error occurred.\n";



		/*Troca os arquivos de pastas*/

	   if ($a['status'] == 'a' && $a['remote'] == 'n') {
		   // remove file and document entry
		   $cons2 = "SELECT extension,compress FROM nr_format WHERE id=".$a['format_id'];
		   $qCons2 = pg_query($conexao, $cons2);
		   $a2 = pg_fetch_array($qCons2);
		   $file = "$cfg_dir_archive/{$a['topic_id']}/$did.{$a2['extension']}";
			
			
			if ($a2['compress'] == 'y')
				$file .= '.gz';
		   
			$old = "$cfg_dir_archive/$tidOld/$did.{$a2['extension']}";
			if ($a2['compress'] == 'y')
			  $old .= '.gz';
			
			if (!file_exists("$cfg_dir_archive/$tidNew"))
				mkdir("$cfg_dir_archive/$tidNew");

			$new = "$cfg_dir_archive/$tidNew/$did.{$a2['extension']}";
			if ($a2['compress'] == 'y')
				$new .= '.gz';

			echo " OLD: ". $old ." | \n\r NEW:". $new.  "\n\r";
			
			if (!rename($old, $new)) {
				$linha =" OLD: ". $old ." | \n\r NEW:". $new.  "\n";
				fwrite ($arq1, $linha);

			}
			

		}
				
			 /*Arquivos Anexos*/
						
			$cons3 ="SELECT * FROM supplementary_files WHERE document_id=$did";
			$qCons3 = pg_query($conexao, $cons3);
			$qtd= pg_num_rows($qCons3);
					
			
			   if ($qtd >=1){				
					
					$qtdaf += $qtd;
					
					while ($asf = pg_fetch_array($qCons3)){
							
						$qformat = "SELECT extension,compress FROM nr_format WHERE id=".$asf['format_id'];
						$qConsF = pg_query($conexao, $qformat);
						$asf2 = pg_fetch_array($qConsF);
						
						$old = "$cfg_dir_archive/$tidOld/S{$asf['id']}.{$asf2['extension']}";
									
						if ($asf2['compress'] == 'y')
							$old .= '.gz';
									
										
						$new = "$cfg_dir_archive/$tidNew/S{$asf['id']}.{$asf2['extension']}";
						if ($asf2['compress'] == 'y')
						  $new .= '.gz';	
						

						echo " OLD: ". $old ." | \n\r NEW:". $new.  "\n\r";	
													
						if (!@rename($old, $new))
						 $linha =" OLD: ". $old ." | \n\r NEW:". $new.  "\n";
						fwrite ($arq1, $linha);

						
					}	 
				}
		
		
		/*atualiza a colecao na tabela de visitas */
		
		$insert = "INSERT INTO troca_colecao (code,topic_id_old, topic_id_new, flag, date ) VALUES ('$code',$tidOld,$tidNew,0, CURRENT_DATE )";
		 echo "Insert: ". $insert ."\n\r";
		
		$result_Insert = pg_query($conexao, $insert);
		
		if (!$result_Insert)
		 $linha = "An error occurred.\n";

		
	}	  
	
	
}





function confirm_topic ($msg, $url, $back = '') {	
		
	$yes = 'Sim';
	$no = 'Não';

echo <<<HTML
	<html>
	<form method="post" action="$url">
	<table align="center" border="0" cellpadding="8" cellspacing="0">
	<tr><td align="center" colspan="2"><b>$msg</b></td></tr>
	<tr><td align="center"><input type="submit" name="conf" value="$yes"></td>
	<td align="center"><input type="submit" name="conf" value="$no"></td></tr>
	</table>
	</form>
	</html>
HTML;
	
  exit();
}





?>
