<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.
//document action: /document/action.php

require_once '../include/start.php';
require_once BASE . 'include/defs_d.php';
require_once BASE . 'include/util_d.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$params = $_REQUEST;
$op = isset($params['op']) && $params['op'] !='' ? $params['op'] : '0'; 


//Inseriro ODS
if ($op == 'i') { // ---------------- accept document after verification
  //validate access
 
   check_administrator_rights();
   $resp = array();
   
   $ods_name= trim($_POST['ods_name']);
   $ods_ordem = isset($_POST['ods_ordem']) ? $_POST['ods_ordem']: 0;

   $status = false;
   $resp['status'] = false;
   $resp['id'] = 0;
 
   db_command("INSERT INTO nr_ods (description, ordem) VALUES ('$ods_name', $ods_ordem)");
   
   $id = db_simple_query("select max(id) from nr_ods");

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
   
   db_command("DELETE FROM nr_ods WHERE id = ".$id);

   $resp['status'] = true;
   echo json_encode($resp); 
}

//Atualiza o Tipo de Informação
if ($op == 'u') { // ---------------- accept document after verification
  //validate access
   check_administrator_rights();
      
   $resp['status'] = false;
   
   $ods_name= trim($_POST['ods_name']);
   $ods_ordem = isset($_POST['ods_ordem']) ? $_POST['ods_ordem']: 0;

   $id = $_POST['id'];
 
   db_command("UPDATE nr_ods SET description = '".$ods_name."', ordem =".  $ods_ordem ." WHERE id = ".$id);

   $resp['status'] = true;
  // $resp['value']="UPDATE FROM type_information SET type_name = '".$type_name."' WHERE id = ".$id;
   echo json_encode($resp); 
}


?>