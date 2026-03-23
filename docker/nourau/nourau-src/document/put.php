<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

require_once '../include/start.php';
require_once BASE . 'include/defs_d.php';
require_once BASE . 'include/page_d.php';
require_once BASE . 'include/format.php';
require_once BASE . 'include/util_d.php';




if($_SERVER["REQUEST_METHOD"] == "GET") {
  
  $tid = $_GET['tid'];
  
  if ($tid == 0)
    $op = 'g';
  else 
	$op = 't';

  
} else {
 
   $sent =$_POST['sent'];
   $tid = $_POST['tid'];
   $op = $_POST['op'];
   $remote = isset($_POST['remote'])?'y':'n';
   $url = isset($_POST['url'])?$_POST['url']:NULL;
   //echo $_POST['url'];
	// filter input	
	
}


// validate input
if (!valid_int($tid))
   message($cfg_site,"Parâmetro inválido", "failure");

// check access rights
check_user_rights();


// validate input
//$topic = htmlspecialchars(get_topic($tid, 'name'));

if (empty($sent))
  form();

if ($remote == 'n') {
	
	if (file_exists($_FILES['file']['tmp_name'])) {
	   $file = $_FILES["file"]["tmp_name"];
     $file_type = $_FILES["file"]["type"];
	   $file_name = trim($_FILES['file']['name']);
	   $file_size = $_FILES["file"]["size"];
	   $parts = explode('.',$file_name);
	   $file_ext = end($parts);

     echo "$file_type";

	   list('fid' => $fid, 'cid' => $cid) = find_format($file_type);
    }

    if ($fid == 0)
       form("O tipo de arquivo não é aceito nesta coleção.");


// move file into incoming directory
 chmod($file, 0644);
 $file_name = random_string(4) . '-' .str_replace(' ', '_', basename($file_name));
 copy($file, "$cfg_dir_incoming/$file_name");
  
  $acesso_eletronico = '';
 

} else {
	$file_name = $acesso_eletronico= $url;
	$cid = 0;
	$fid = 0;
	$file_size = 0;
}

//
// insert document
db_command("INSERT INTO nr_document (topic_id,owner_id,category_id,remote,filename,size,format_id, acesso_eletronico) VALUES ('$tid','{$_SESSION['suid']}','$cid','$remote','$file_name','$file_size','$fid','$acesso_eletronico')");
$did = db_simple_query("SELECT CURRVAL('nr_document_seq')");
add_log('n', 'di', "did=$did&from={$_SERVER['REMOTE_ADDR']} - {$_SERVER['HTTP_USER_AGENT']}");

// finish
redirect("{$cfg_site}document/edit.php?did=$did");


/*-------------- functions --------------*/

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
    echo  $category_id;
  }
  else {
    $format_id = 0; 
	  $category_id = 0;
  }	  



  return ['fid' => $format_id, 'cid' =>  $category_id ];
}


function form ($msg = "")
{
  global $cid, $tid, $topic, $op, $remote, $url;

  page_begin();
  
  if ($tid == 0 && $op == 'g') {
   echo html_h("Arquivar documento ");
  }
  else {	  
	 $topic = htmlspecialchars(get_topic($tid, 'name'));  
	 echo html_h("Arquivar documento em: <b> {$topic} </b> ");
  } 


	//echo html_h("Arquivar documento em: <b> {$topic} </b> ");
	format_warning($msg);
    
	
  html_form_begin($_SERVER['PHP_SELF'], true, 'multipart/form-data');
	html_form_hidden('op', $op);
	
	 if ( $op == 'g') {      
		  
		     //html_form_select('Tópico', 'tip', $optt, $tid); 
		        echo "<p><label><b>Coleção:</b><span  class=\"spanAsteristico\" >*</span></label>";
				echo "<br><span class=\"spanForm\">Selecione a Coleção onde o documento será adicionado</span>";
				echo "<select name=\"tid\" id =\"tid\"  required>";
				echo "<option value= \"\">-- escolha uma destas coleções --</option>";
					
					$qt = db_query("SELECT topic.id,topic.name,parent_id,archieve  FROM users INNER JOIN (topic_users INNER JOIN topic ON topic_id = topic.id) ON users.id = users_id WHERE users_id ={$_SESSION['suid']} and  parent_id = 0  ORDER BY parent_id, topic.name");
				 	while ($at = db_fetch_array($qt)) {
						 if ($at['archieve']=='n') {
								echo "<optgroup label = '".$at['name']."'>";
						}  else {
                             if ($at['parent_id'] == 0) 
							  	 If ($tid == $at['id'] ) 
									echo '<option selected ';							   
								 else  
									echo '<option ';	 
								 echo " value='".$at['id']."'>{$at['name']}</option>"; 
						}	 				

                        $qsubtid = db_query("SELECT topic.id,topic.name,parent_id,archieve  FROM users INNER JOIN (topic_users INNER JOIN topic ON topic_id = topic.id) ON users.id = users_id WHERE users_id ={$_SESSION['suid']} AND parent_id = {$at['id']}  ORDER BY name");
					     while ($asubtid = db_fetch_array($qsubtid)) {	
							if ($asubtid['archieve']=='n') 
							   echo "<optgroup label = '".$asubtid['name']."'>";
							 else  
							  echo "<option value='".$asubtid['id']."'>{$asubtid['name']}</option>";
										
						      $qsubtid1 = db_query("SELECT topic.id,topic.name,parent_id,archieve  FROM users INNER JOIN (topic_users INNER JOIN topic ON topic_id = topic.id) ON users.id = users_id WHERE users_id ={$_SESSION['suid']} AND parent_id = {$asubtid['id']} ORDER BY name");
							  while ($asubtid1 = db_fetch_array($qsubtid1)) {
								   
								   If ($tid == $at['id'] ) 
									echo '<option selected ';							   
								 else  
									echo '<option ';	 
								   echo "< value='".$asubtid1['id']."'>{$asubtid1['name']}</option>";
							  }
                         }   
	                     echo "</optgroup>";	
        			}
				
				echo "</select>";
				echo "</p>";
		   
        
		 
	 } else {	 
	
		html_form_hidden('tid', $tid);
		
	  }	

      ?>

   <script>
          $(document).ready(function() {
		        $('#remote').change(function() {
                 if ($('#remote').is(":checked")) {
                   $('.remoteURL').show();
				   $('#url').attr("required",true);
			       $('.file').hide();
				   $('#file').removeAttr("required");
				 }else {
                   $('.remoteURL').hide();
				   $('#url').removeAttr("required");
			       $('.file').show();
				   $('#url').attr("required");
				 }  
          });
   });
            
  </script>


<?php
      echo "<p>";
      echo "<input type=\"checkbox\" id=\"remote\" name=\"remote\" value=\"on\" > O Conteúdo deste documento estará disponível em outro site.";
      echo "</p><br>";
  
       echo "<div class=\"remoteURL\">";
          html_form_text("Acesso Eletrônico", 'url', 80,'', 800, false, 'http://', 'URL do conteúdo hospedado remotamente');
      echo "</div>";
	
 
    echo "<div class=\"file\" >";
    html_form_file('Documento', 'file', false, "Selecione o arquivo principal.", true);
    echo "</div>"; 	
   
 
  

    html_form_submit('Enviar', 'sent');
    html_form_end();
    echo "<p>\n";
 

  page_end();
  exit();
}

?>
