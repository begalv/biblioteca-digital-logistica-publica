<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

require_once '../include/start.php';
require_once BASE . 'include/html.php';
require_once BASE . 'include/control.php';
require_once BASE . 'include/defs_d.php';
require_once BASE . 'include/format.php';
require_once BASE . 'include/page_d.php';
require_once BASE . 'include/util.php';
require_once BASE . 'include/util_d.php';

global $cid, $did, $op, $id, $filename, $tid;

html_header(_('Arquivos Suplementares'), "", "", false);


 if($_SERVER["REQUEST_METHOD"] == "POST") {
      
	  $sent = $_POST['sent'];	
      $op = $_POST['op'];
	  $id = $_POST['id'];
	  $tid = $_POST['tid'];	  
	  $did = $_POST['did'];	  
	  $status = $_POST['status'];
    }
 else {
      $op = $_GET['op'];
	  $did = $_GET['did']; 
	  $tid = $_GET['tid'];
	  $id = isset($_GET['id'])?$_GET['id']:0;
	  $status = $_GET['status'];
 }
	
// validate input
if (!valid_int($did))
   message($cfg_site,"Parâmetro inválido", "failure");	

// check access rights
  check_user_rights();

if (empty($sent)) {
	form();
}
else if ($sent == 'Sim' || $sent == 'Salvar') {
 
	
	if ($op == 'd'){

		//Apaga o arquivo suplementar
  	
		$format_id = $_POST['format_id'];
	
       //Apaga o fisicamnete arquivo anterior
	   $qDel = db_query("SELECT extension,compress FROM nr_format WHERE id=".$format_id);
	   $aDel = db_fetch_array($qDel);
	   
	   if ($status == 'i' ||$status == 'w') {
		   $fDel = "$cfg_dir_incoming/S"."$id.{$aDel['extension']}";
		   if ($aDel['compress'] == 'y')
				$fDel .= '.gz';
		   
          @unlink($fDel);   
       }  
       else { 
	   
	   	   $fDel = "$cfg_dir_archive/$tid/S"."$id.{$aDel['extension']}";
			if ($aDel['compress'] == 'y')
				$fDel .= '.gz';
			@unlink($fDel);
	   }
     
       db_command("DELETE FROM supplementary_files WHERE id = $id");
	}
	else if ($op == 'i'){
		
		if (file_exists($_FILES['file']['tmp_name'])) {
			$file = $_FILES["file"]["tmp_name"];
			$file_type = $_FILES["file"]["type"];
			$file_name = trim($_FILES['file']['name']);
			$file_size = $_FILES["file"]["size"];
			$parts = explode('.',$file_name);
			$file_ext = end($parts);
			list('fid' => $fid, 'cid' => $cid) = find_format($file_type);
		}
		
		if (empty($sent))
		  form();
	  
		if (!$fid){
	    	form("O tipo de arquivo não é aceito nesta coleção.");
		}
        
       //  insert supplementary document
		db_command("INSERT INTO supplementary_files (filename,size,document_id,category_id,format_id, owner_id, topic_id) values ('$file_name','$file_size',$did,'$cid','$fid','{$_SESSION['suid']}','$tid')");
		$id = db_simple_query("SELECT CURRVAL('supplementary_files_seq')");
		//Grava o arquivo no servidor
		$qSave = db_query("SELECT extension,compress FROM nr_format WHERE id=".$fid);
		$aSave = db_fetch_array($qSave);
		
       
	   if ($status == 'i' ||$status == 'w') {
           chmod($file, 0644);
		   $new = "$cfg_dir_incoming/S"."$id.{$aSave['extension']}";
           copy($file, $new); 
		   
		   if ($aSave['compress'] == 'y') {
				// verifica se o arquivo compacta existe para apaga-lo
				$filecompact = $new. '.gz';
				//compacta o arquivo
				exec("gzip -9 $new");
			}
		   
       }  
       else { 
            chmod($file, 0644);
			$new = "$cfg_dir_archive/$tid/S"."$id.{$aSave['extension']}";

			copy($file, $new);
   
			if ($aSave['compress'] == 'y') {
				// verifica se o arquivo compacta existe para apaga-lo
				$filecompact = $new. '.gz';
				//compacta o arquivo
				exec("gzip -9 $new");
			}
	   }

	}
	
	//add_log('n', 'di', "did=$did&from=$REMOTE_ADDR $HTTP_USER_AGENT");
	
	echo "<script language='JavaScript'>";
    echo "arquivosuplementar($did,$tid);";
    echo "setTimeout('window.close();', 100);";
    echo "</script>";
	
}
else if ($sent == "Cancelar" || $sent =="Não" ) {
  // abort editing
   echo "<script language='JavaScript'>";
   echo "setTimeout('window.close();', 100);";
   echo "</script>";
  exit();
}

function find_format ($file_type)
{
  $type = explode('/', $file_type);
 $format_id = 0;
  $q = db_query("SELECT C.id,C.type,C.subtype FROM nr_format C,nr_category_format CC WHERE  CC.format_id=C.id");
  while ($a = db_fetch_array($q)) {
    if ($a['type'] == 'any') {
      // match against all types
      $q2 = db_query("SELECT id,type,subtype FROM nr_format WHERE subtype<>'any'");
      while ($a2 = db_fetch_array($q2))
        if (!strcasecmp($a2['type'], $type[0]) &&
            !strcasecmp($a2['subtype'], $type[1])) {
          $format_id = $a2['id'];
          break;
        }
		
    }
    else if ($a['subtype'] == 'any') {
      // match against all subtypes with the given type
      $q2 = db_query("SELECT id,type,subtype FROM nr_format WHERE type='{$a['type']}' AND subtype<>'any'");
      while ($a2 = db_fetch_array($q2))
        if (!strcasecmp($a2['type'], $type[0]) &&
            !strcasecmp($a2['subtype'], $type[1])) {
          $format_id = $a2['id'];
          break;
        }
    }
    else {
      // match against specified type and subtype
      if (!strcasecmp($a['type'], $type[0]) &&
          !strcasecmp($a['subtype'], $type[1]))
        $format_id = $a['id'];  
      }
	
    if ($format_id)
      break;
  }
  
  if ($format_id > 0) {
   
    $q = db_query("SELECT category_id FROM  nr_category_format  WHERE  format_id=".$format_id );
	$a = db_fetch_array($q);
    $category_id = $a['category_id'];	
  }
  else {
    $format_id = 0; 
	$category_id = 0;
  }	  

  return ['fid' => $format_id, 'cid' =>  $category_id ];
}


function form ($msg = ""){
	global $cfg_site;
	global $cid, $did, $op, $id, $tid, $status;
	
	echo "<div class='arqsuplementar'>";

	if ($op == 'i') {
		echo html_h(html_b('Adicionar Anexo'));
		format_warning($msg);
		
		echo "<br>";

		html_form_begin("{$_SERVER['PHP_SELF']}", true, 'multipart/form-data');
		
		html_form_file('Documento', 'file', true, "Selecione o arquivo.", true);
		

    	echo "<input type='hidden' name='did' value=$did>";
		echo "<input type='hidden' name='op' value=$op>";
		echo "<input type='hidden' name='tid' value=$tid>";
		echo "<input type='hidden' name='id' value=$id>";
		echo "<input type='hidden' name='status' value=$status>";

	   echo "<div class=\"botao\">";
		html_form_submit('Salvar', 'sent');
		echo "</div>";
		echo "<div class=\"botao\">";
		html_form_submit('Cancelar', 'sent', false);
		echo "</div>";
	
	}
    else if ($op == 'd') {

     echo html_h("Excluir Anexo");

     $q = db_query("SELECT filename, format_id FROM supplementary_files WHERE id =$id");
	 $a = db_fetch_array($q);

   
     $msg = "Você deseja remover o arquivo ". $a['filename']. "?";

	 echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
	 echo "<input type='hidden' name='did' value=$did>";
	 echo "<input type='hidden' name='op' value=$op>";
	 echo "<input type='hidden' name='tid' value=$tid>";
	 echo "<input type='hidden' name='id' value=$id>";
	 echo "<input type='hidden' name='format_id' value={$a['format_id']}>";
	 echo "<input type='hidden' name='status' value=$status>";



       echo "<div class=\"botao\">";
		html_form_submit('Sim', 'sent');
		echo "</div>";
		echo "<div class=\"botao\">";
		html_form_submit('Não', 'sent', false);
		echo "</div>";

	
    }
	
  echo "</div>";
  html_footer();
  exit();
}

?>
