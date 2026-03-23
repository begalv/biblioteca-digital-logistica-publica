<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.
//document action: /document/action.php

require_once '../include/start.php';
require_once BASE . 'include/defs_d.php';
require_once BASE . 'include/util_d.php';

$params = $_REQUEST;
$op = isset($params['op']) && $params['op'] !='' ? $params['op'] : '0'; 


//Inserir o Tipo de Informação
if ($op == 'i') { // ---------------- accept document after verification
  //validate access
 
   check_administrator_rights();
   $resp = array();
   
   $type_name = $_POST['type_name'];
   $status = false;
   $resp['status'] = false;
   $resp['id'] = 0;


   db_command("INSERT INTO type_information (name) VALUES ('$type_name')");
   $id = db_simple_query("select max(id) from type_information");

   //echo " $id ";
   $resp['status'] = true;
   $resp['id'] =$id;
   echo json_encode($resp); 
}


//Apaga o Tipo de Informação
if ($op == 'd') { // ---------------- accept document after verification
  //validate access
   check_administrator_rights();
     
  $id = $params['id'];
  
   $resp['status'] = false;
   
   db_command("DELETE FROM type_information WHERE id = ".$id);

   $resp['status'] = true;
   echo json_encode($resp); 
}

//Atualiza o Tipo de Informação
if ($op == 'u') { // ---------------- accept document after verification
  //validate access
   check_administrator_rights();
      
   $resp['status'] = false;
   
   $type_name = trim($_POST['type_name']);
   $id = $_POST['id'];
 
   db_command("UPDATE type_information SET name = '".$type_name."' WHERE id = ".$id);

   $resp['status'] = true;
  // $resp['value']="UPDATE FROM type_information SET type_name = '".$type_name."' WHERE id = ".$id;
   echo json_encode($resp); 
}


//Atribuir tipo de informações a Coleção 
if ($op == 'a') { // ---------------- accept document after verification
  //validate access
  check_administrator_rights();
      
  $resp['status'] = false;
   
  $tid = $_POST['tid'];
  $idtif = $_POST['idtif'];
  
  $topicos= carrega_topicos($tid);
  foreach ($topicos as $topico){
      db_command("insert INTO topic_type (topic_id, type_id ) VALUES ($topico[0], $idtif)");
  }
  
  $resp['status'] = true;
  //$resp['topicos'] = $topicos
  echo json_encode($resp); 
}

function carrega_topicos($tid){

  global $db_conn;
  $topico = array();
 
  $topic = db_query("SELECT id FROM topic WHERE parent_id = $tid ORDER BY name");
  $topico[]=array($tid); 
  while ($q = db_fetch_array($topic)){
      $topico[]=array($q['id']);
      $resulttopic = db_query("SELECT id From topic where parent_id =".$q['id']." order by name");
    while ($qtopic = pg_fetch_array($resulttopic)){
          $topico[]=array($qtopic['id']);
          $resulttopic1 = db_query("SELECT id From topic where parent_id =".$qtopic['id']." order by name");
        while ($qtopic1 = pg_fetch_array($resulttopic1)){
            $topico[]=array($qtopic1['id']);
            $resulttopic2 = db_query("SELECT id  From topic where parent_id =".$qtopic1['id']." order by name");
        while ($qtopic2 = pg_fetch_array($resulttopic2)){
            $topico[]=array($qtopic2['id']);
            $resulttopic3 = db_query("SELECT id  From topic where parent_id =".$qtopic2['id']." order by name");
          while ($qtopic3 = pg_fetch_array($resulttopic3)){
            $topico[]=array($qtopic3['id']);
          }
        }
      }
    }
  }
 
  return $topico;
 }


?>