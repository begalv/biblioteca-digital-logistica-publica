<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.
//document action: /document/action.php

require_once '../include/start.php';
require_once BASE . 'include/defs_d.php';
require_once BASE . 'include/util_d.php';


if(isset($_GET)) {
	
 if (isset($_GET['op']))
 	$op = $_GET['op'];

 if (isset($_GET['did']))
 	$did =  $_GET['did'];	

if (isset($_GET['tidold']))
		$tidold = $_GET['tidold'];
	
	if (isset($_GET['tid']))
		$tid = $_GET['tid'];
	
}


if(isset($_POST)) {
	
	if (isset($_POST['conf']))
		$conf = $_POST['conf'];

	if (isset($_POST['did']))
		$did = $_POST['did'];
	 
	if (isset($_POST['op']))
		$op = $_POST['op'];
	
	if (isset($_POST['tidold']))
		$tidold = $_POST['tidold'];
	
	if (isset($_POST['tid']))
		$tid = $_POST['tid'];

} 
  

// validate input
if (!valid_int($did))
   message("{$cfg_site}document/?code=" . rawurlencode($a['code']),"Parâmetro Inválido !", "failure");

$a = get_document($did);

if ($op == 'v') { // ---------------- accept document after verification
  //validate access
   check_administrator_rights();
  check_user_rights();
  if ($a['status'] != 'v')
    error('Acesso Negado');
  if (substr($a['code'],0,4)!='vtls') {
      // update document
      db_command("UPDATE nr_document SET status='w' WHERE id='$did'");
      add_log('n', 'dv', "did=$did");

      // notify maintainer
      $title = $a['title'];
      $topic = get_topic($a['topic_id'], 'name');
      $email = get_user($a['owner_id'], 'email');
      send_mail($email, _('Document received'), _M("The document with title '@1' was received on the topic '@2'.", $title, $topic));
  }
  else { // tese - arquivar imediatamente
      // move file to archive, renaming it and compressing if necessary
      $q2 = db_query("SELECT extension,compress FROM nr_format WHERE id='{$a['format_id']}'");
      $a2 = db_fetch_array($q2);
      $old = "$cfg_dir_incoming/{$a['filename']}";
      if (!file_exists("$cfg_dir_archive/{$a['topic_id']}"))
        mkdir("$cfg_dir_archive/{$a['topic_id']}");
      $new = "$cfg_dir_archive/{$a['topic_id']}/$did.{$a2['extension']}";
      $filename = substr($a['filename'], 5); // remove random prefix
      if (!@rename($old, $new))
	  error(_('Rename failed')); // FIXME: notify admin?
      if ($a2['compress'] == 'y') {
	  // compress it
	  exec("gzip -9 $new");
      }

      // update document
      db_command("UPDATE nr_document SET status='a',filename='$filename',visits='0',downloads='0' WHERE id='$did'");
      add_log('n', 'dv', "did=$did");

      // add document to search index
      db_command("INSERT INTO nr_document_queue (op,document_id) VALUES ('i','$did')");

      // notify owner
      $title = $a['title'];
      $topic = get_topic($a['topic_id'], 'name');
      $email = get_user($a['owner_id'], 'email');
      send_mail($email, _('Document accepted'), _M("The document with title '@1' has been accepted in the topic '@2'.", $title, $topic));
  }
  // finish
  message(_('Document accepted'), "{$cfg_site}document/manage.php");
}
else if ($op == 'a') { // ---------------- approve document
  	// validate access
  	 $tid = $a['topic_id'];
  	check_maintainer_rights($tid);
   	
	if ($a['status'] != 'w')
    	  print "acesso_negado";
  
  	// ask confirmation
  	if (empty($conf)) {
    	$title = $a['title'];
    	remove("Você deseja aprovar o documento com título $title ?", "{$_SERVER['PHP_SELF']}?did=$did&op=$op", false);
  	}

 	if ($conf == 'Sim') {

		if ($a['remote'] == 'n') {	 
		
		if (strlen($a['filename']) > 0 ) {   
			// move file to archive, renaming it and compressing if necessary
			$q2 = db_query("SELECT extension,compress FROM nr_format WHERE id='{$a['format_id']}'");
			$a2 = db_fetch_array($q2);
			$old = "$cfg_dir_incoming/{$a['filename']}";
			
			//echo "$cfg_dir_archive/{$a['topic_id']}";

		
		
			if (!file_exists("$cfg_dir_archive/{$a['topic_id']}"))
				mkdir("$cfg_dir_archive/{$a['topic_id']}");
	

			$new = "$cfg_dir_archive/{$a['topic_id']}/$did.{$a2['extension']}";
			$filename = substr($a['filename'], 5); // remove random prefix
		

			if (file_exists($old)) 
				rename($old, $new);
			else {
				print "error";
				exit (); 
			}
			
			if ($a2['compress'] == 'y') 
				// compress it
				exec("gzip -9 $new");
			
			/*Arquivos Anexos*/
						
			$qsf = db_query("SELECT * FROM supplementary_files WHERE document_id=$did");
				
			if (db_rows($qsf)>=1){

				db_command("UPDATE supplementary_files SET topic_id = $tid WHERE document_id=$did");
			
				while ($asf = db_fetch_array($qsf)){
							
					$qformat = db_query("SELECT extension,compress FROM nr_format WHERE id='{$asf['format_id']}'");
					$asf2 = db_fetch_array($qformat);
					$old = "$cfg_dir_incoming/S{$asf['id']}.{$asf2['extension']}";
									
					if ($asf2['compress'] == 'y')
						$old .= '.gz';
										
					$new = "$cfg_dir_archive/{$a['topic_id']}/S{$asf['id']}.{$asf2['extension']}";
					if ($asf2['compress'] == 'y')
					$new .= '.gz';	
									
					//echo "<br>$old<br>$new"; 
					//exit();
									
					if(file_exists($old))	
					rename($old, $new);
					else{
						print 'error' ;
						exit();
					}										
								
				}
			}		
		} else{
				print 'error_arquivo' ;
			exit();
		}

		} else {
			$filename =  $a['acesso_eletronico'];
		}
  
   db_command("UPDATE nr_document SET status='a',filename='$filename',visits='0',downloads='0' WHERE id='$did'");
   add_log('n', 'dv', "did=$did");
   print "sucesso";
 }
  else
    redirect("{$cfg_site}document/manage.php"); 
}
else if ($op == 'r') { // ---------------- reject document
  // validate access
  $tid = $a['topic_id'];
  check_maintainer_rights($tid);
  if ($a['status'] != 'v' && $a['status'] != 'w')
    print "error";

  // ask confirmation
  if (empty($conf)) {
    $title = $a['title'];
    remove("Você deseja rejeitar o documento com título ' $title ' ?", "{$_SERVER['PHP_SELF']}?did=$did&op=$op", false);
  }

  if ($conf == 'Sim') {
	  
	if ($a['remote'] == 'n') {	  
		// remove file and document entry
		//@unlink("$cfg_dir_incoming/{$a['filename']}");
		
		//$qsf = db_query("SELECT * FROM supplementary_files WHERE document_id=$did");
			
		//		  if (db_rows($qsf)>=1){
		//			db_command("UPDATE supplementary_files SET topic_id = $tid WHERE document_id=$did");
					
		//				while ($asf = db_fetch_array($qsf)){
							
		//						$qformat = db_query("SELECT extension,compress FROM nr_format WHERE id='{$asf['format_id']}'");
		//						$asf2 = db_fetch_array($qformat);
		//						$old = "$cfg_dir_incoming/S{$asf['id']}.{$asf2['extension']}";
								
		//						if ($asf2['compress'] == 'y')
		//							$old .= '.gz';
									
		//						$new = "$cfg_dir_archive/{$a['topic_id']}/S{$asf['id']}.{$asf2['extension']}";
		//						if ($asf2['compress'] == 'y')
		//							  $new .= '.gz';	
								  
								// echo "<br>$old<br>$new"; 
									
		//						if (!@rename($old, $new))
		//						  print 'error' ;
	         //					}	 
		//		}	
	}
    db_command("DELETE FROM nr_document WHERE id='$did'");
    add_log('n', 'dr', "did=$did");

     print "sucesso";
 
    // finish
   // message("Dcoumento Rejeitado", "{$cfg_site}document/manage.php");
  }
  else
    redirect("{$cfg_site}document/manage.php");
}
else if ($op == 'd') { // ---------------- remove document
  // validate access
  if ($a['status'] != 'a')
    print "error";

  // ask confirmation
  if (empty($conf)) {
    $title = $a['title'];
    remove(" Você deseja remover o documento com título: ' $title ' ?" , "{$_SERVER['PHP_SELF']}?did=$did&op=$op", true);
  }

  if ($conf == 'Sim') {
	 
  if ($a['remote'] == 'n') {
   // remove file and document entry
    $q2 = db_query("SELECT extension,compress FROM nr_format WHERE id='{$a['format_id']}'");
    $a2 = db_fetch_array($q2);
    $file = "$cfg_dir_archive/{$a['topic_id']}/$did.{$a2['extension']}";
    
	
		if ($a2['compress'] == 'y')
			$file .= '.gz';
   
		$new = "$cfg_dir_remove/$did.{$a2['extension']}";
		if ($a2['compress'] == 'y')
			$new .= '.gz';
    

		if (!@rename($file, $new))
			//	error(_('Rename failed')); // FIXME: notify admin?
		    print 'error' ;
		
      
    //@unlink($file);
   

    // remove document from search index
    //db_command("INSERT INTO nr_document_queue (op,document_id,flag) VALUES ('d','$did',0)");

	//Remove os arquivos suplementares
	 $qsf = db_query("SELECT * FROM supplementary_files WHERE document_id=$did");
  		if (db_rows($qsf)>=1){
  			while ($asf = db_fetch_array($qsf)){
			 	$qformat = db_query("SELECT extension,compress FROM nr_format WHERE id='{$asf['format_id']}'");
   			    $asf2 = db_fetch_array($qformat);
   				$file = "$cfg_dir_archive/{$asf['topic_id']}/S{$asf['id']}.{$asf2['extension']}";
				
    			if ($asf2['compress'] == 'y')
      		 		$file .= '.gz';
					
				$new = "$cfg_dir_remove/S{$asf['id']}.{$asf2['extension']}";
       			 if ($asf2['compress'] == 'y')
        			  $new .= '.gz';	
					
    		   if (!@rename($file, $new))
				     print 'error' ;
			     
			  //@unlink($file);
    		  db_command("DELETE FROM supplementary_files WHERE id = {$asf['id']}");
		  }
		}
	}	
	
	 db_command("UPDATE nr_document SET status='d', updated='NOW' WHERE id='$did'");
    add_log('n', 'dd', "did=$did");

   print "sucesso";
   
  }
  else
    redirect("{$cfg_site}document/?code=" . rawurlencode($a['code']));
}
else if ($op == 't') { // ---------------- Troca de Tópico 
  
  // validate access
  if ($a['status'] != 'a' ){
	   print 'error';
	   exit();
  }
  
	// update document
		if($tidold != $tid){

  		
		if ($a['remote'] == 'n') {
			
			// move file to the new topic
			 $q2 = db_query("SELECT extension,compress FROM nr_format WHERE id='{$a['format_id']}'");
			 $a2 = db_fetch_array($q2);

			 $old = "$cfg_dir_archive/$tidold/$did.{$a2['extension']}";
			 if ($a2['compress'] == 'y')
				$old .= '.gz';

           

			 if (!file_exists("$cfg_dir_archive/$tid"))
				mkdir("$cfg_dir_archive/$tid");

			 $new = "$cfg_dir_archive/$tid/$did.{$a2['extension']}";
			  if ($a2['compress'] == 'y')
				$new .= '.gz';
			 
			   
			 if (!@rename($old, $new))
			   print 'error'; 
			/* else 
			   @unlink($old);*/
			  
			 /*Arquivos Anexos*/
					
			  $qsf = db_query("SELECT * FROM supplementary_files WHERE document_id=$did");
			
				  if (db_rows($qsf)>=1){
					db_command("UPDATE supplementary_files SET topic_id = $tid WHERE document_id=$did");
					
						while ($asf = db_fetch_array($qsf)){
							
								$qformat = db_query("SELECT extension,compress FROM nr_format WHERE id='{$asf['format_id']}'");
								$asf2 = db_fetch_array($qformat);
								$old = "$cfg_dir_archive/$tidold/S{$asf['id']}.{$asf2['extension']}";
								
								if ($asf2['compress'] == 'y')
									$old .= '.gz';
								
									
								$new = "$cfg_dir_archive/$tid/S{$asf['id']}.{$asf2['extension']}";
								if ($asf2['compress'] == 'y')
									  $new .= '.gz';	
								  							
								if (!@rename($old, $new))
								  print 'error' ;
								//else 			  
								//  @unlink($old);
						}	 
				}	
		}
		
		
		   db_command("UPDATE nr_document SET topic_id = $tid, updated='now' WHERE id='$did'");
		   add_log('n', 'du', "did=$did");

            $code = get_document($did, 'code');
			
			/*atualiza a colecao na tabela de visitas e download */
			 $update = "UPDATE visitas_downloads SET topic_id =".$tid."  WHERE code = '".$code."'";
	         db_command($update);
	      
		  print 'sucesso';
  

    }
	else
	      print 'error';
}
else if ($op == 'dc') {
  check_user_rights();
 /*Descartat documento*/
 // validate access
  if ($a['status'] != 'i' ){
    print 'error';
    exit();
  }
 if ($conf == 'Sim') {
	  
	/*if ($a['remote'] == 'n') {	  
		// remove file and document entry
		//@unlink("$cfg_dir_incoming/{$a['filename']}");
		
		/*$qsf = db_query("SELECT * FROM supplementary_files WHERE document_id=$did");
			
				  if (db_rows($qsf)>=1){
					db_command("UPDATE supplementary_files SET topic_id = $tid WHERE document_id=$did");
					
						while ($asf = db_fetch_array($qsf)){
							
								$qformat = db_query("SELECT extension,compress FROM nr_format WHERE id='{$asf['format_id']}'");
								$asf2 = db_fetch_array($qformat);
								$old = "$cfg_dir_incoming/S{$asf['id']}.{$asf2['extension']}";
								
								if ($asf2['compress'] == 'y')
									$old .= '.gz';
									
								$new = "$cfg_dir_archive/{$a['topic_id']}/S{$asf['id']}.{$asf2['extension']}";
								if ($asf2['compress'] == 'y')
									  $new .= '.gz';	
								  
								// echo "<br>$old<br>$new"; 
									
								if (!@rename($old, $new))
								  print 'error' ;
	         					}	 
				}	
	}*/
    db_command("DELETE FROM nr_document WHERE id='$did'");
    add_log('n', 'dr', "did=$did");

     print "sucesso";
 
    // finish
   // message("Dcoumento Rejeitado", "{$cfg_site}");
  }
  else
    redirect("{$cfg_site}document/manage.php");
}	

?>
