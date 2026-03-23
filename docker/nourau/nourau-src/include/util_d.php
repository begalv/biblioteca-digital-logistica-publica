<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

/*-------------- includes --------------*/

require_once BASE . 'include/control.php';
require_once BASE . 'include/page.php';
require_once BASE . 'include/util.php';


/*-------------- database functions --------------*/

function get_category ($cid, $field = '')
{
  global $cfg_site;
  if (empty($cid))
    error(_('Category not specified'));
  if (!empty($field))
    $q = db_query("SELECT $field FROM nr_category WHERE id='$cid'");
  else
    $q = db_query("SELECT * FROM nr_category WHERE id='$cid'");
  if (!db_rows($q))
    error(_('Category not found'));
  if (!empty($field))
    return db_result($q, 0, $field);
  else
    return db_fetch_array($q);
}

function get_document ($did, $field = '')
{
	global $cfg_site;
  if (empty($did))
      message($cfg_site,"Documento não encontrado", "failure"); 
  if (!empty($field))
    $q = db_query("SELECT $field FROM nr_document WHERE id='$did'");
  else
    $q = db_query("SELECT * FROM nr_document WHERE id='$did'");
  if (!db_rows($q))
      message($cfg_site,"Documento não encontrado", "failure");
  if (!empty($field))
    return db_result($q, 0, $field);
  else
    return db_fetch_array($q);
}

function get_format ($fid, $field = '')
{
  if (empty($fid))
    error(_('Format not specified'));
  if (!empty($field))
    $q = db_query("SELECT $field FROM nr_format WHERE id='$fid'");
  else
    $q = db_query("SELECT * FROM nr_format WHERE id='$fid'");
  if (!db_rows($q))
    error(_('Format not found'));
  if (!empty($field))
    return db_result($q, 0, $field);
  else
    return db_fetch_array($q);
}


function get_typeInformation ($tiid, $field = '')
{
  if (empty($tiid))
  //  error('Tipo de informação não encontrado');
  if (!empty($field))
    $q = db_query("SELECT $field FROM type_information WHERE id='$tiid'");
  else
    $q = db_query("SELECT * FROM type_information WHERE id='$tiid'");
  if (!db_rows($q))
    error(_('Information not found'));
  if (!empty($field))
    return db_result($q, 0, $field);
  else
    return db_fetch_array($q);
}


/*Localiza o tópico parente - não o tópico básico*/
function find_parent ($tid) {
	$topic = 0;
	$id = $tid;

	while ($id) {
		$sql = "SELECT name, parent_id FROM topic WHERE id='$id'";
		$q = db_query($sql);
		if ($id != $tid) {
			$topic = $id;
      		}
        	$id = pg_fetch_result($q, 0, 'parent_id');

	}
	return 	$topic;
}


/*-------------- convenience functions --------------*/

function document_pending ()
{
  global $session;
  static $pending = -1;

  if ($pending == -1) {
    $pending = 0;

    // check documents waiting for approval
    if (is_maintainer()) {
      if (is_administrator() && db_simple_query("SELECT COUNT(id) FROM nr_document WHERE status='w'"))
        $pending = 1;
      else if (db_simple_query("SELECT COUNT(id) FROM nr_document WHERE status='w' AND topic_id IN (SELECT id FROM topic WHERE maintainer_id='{$_SESSION['suid']}')"))
        $pending = 1;
    }

    // check documents waiting for verification
    if (is_administrator() && db_simple_query("SELECT COUNT(id) FROM nr_document WHERE status='v'"))
      $pending = 1;
  }
  return $pending;
}

function can_edit_document ($did)
{
  global $session;

  // check current user rights to edit/remove given document
  //if (!is_user())
    return false;
  //return (is_collab() || $session['uid'] == get_document($did, 'owner_id') || $session['uid'] == db_simple_query("SELECT T.maintainer_id FROM nr_document D,topic T WHERE D.id='$did' AND D.topic_id=T.id"));
}

function document_finish_user ($uid)
{
  // move ownership of documents to respective topic maintainer
  $q = db_query("SELECT D.id,T.maintainer_id FROM nr_document D,topic T WHERE D.owner_id='$uid' AND D.topic_id=T.id");
  while ($a = db_fetch_array($q))
    db_command("UPDATE nr_document SET owner_id='{$a['maintainer_id']}' WHERE id='{$a['id']}'");
}


//verifica os dados de download para contar por usuario e ip apenas um downloas por dia.

function downloadcheck($code, $user_id)
{

  global $cfg_base_zeus, $cfg_user,$cfg_pass, $cfg_port, $cfg_site, $cfg_host;
  global $zeus_auth; 

	$settings = "dbname=".$cfg_base_zeus." user=".$cfg_user." password=".$cfg_pass." port=".$cfg_port;
	$conexao = pg_pconnect($settings) or die ("N&atilde;o foi poss&iacute;vel conectar ao Banco de dados.");

  $ip = $_SERVER['REMOTE_ADDR'];

  $data = date("d/m/Y");

  $sql = "SELECT last_ip FROM z_log INNER JOIN z_user on user_id = id and user_id =".$user_id." and last_ip = '".$ip."' and to_char(stamp,'dd/mm/yyyy') = '".$data."' and code = '".$code."'";

  $q = pg_query($conexao, $sql);
  $num = pg_num_rows($q);
  pg_close($conexao);

  return  $num;

}


//verifica os dados de visitas para contar apenas uma visita por ip uma vez no dia por usuario.

function visitacheck($code,$tipo,$tid){

   global $cfg_base_zeus, $cfg_user, $cfg_pass, $cfg_port, $cfg_site, $cfg_host;
   global $zeus_auth;   
   $num = 0;
   //$user_id = isset($_SESSION['zeus_auth'])?$_SESSION['zeus_auth']:0;
   
 	/*$settings = "host=10.0.70.83 port=".$cfg_port." dbname=".$cfg_base_zeus." user=".$cfg_user." password=".$cfg_pass;*/
	$settings = "host=$cfg_host port=$cfg_port dbname=$cfg_base_zeus user=$cfg_user options='--client_encoding=UTF8'";
	 if ($cfg_pass)
    $settings .= " password=$cfg_pass";
    $conexao = pg_connect($settings)  or die('Não foi possível conectar');

   $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

   $data = date("d/m/Y");
  
  $origem = 'bd';
  if ($tipo == 'd') {
	 $sql = "SELECT ip FROM z_visitas where ip = '$ip' and to_char(data,'dd/mm/yyyy') = '$data' and code = '$code' and tipo = 'v'";
  	 $qcv = pg_query($conexao, $sql);
	 if (pg_num_rows($qcv)==0)
	   $origem = 'sb'; /*sites de buscas*/ 
	 else 
       $origem = 'bd'; /*biblioteca digital*/ 	
 }

 // $sql = "SELECT ip FROM z_visitas where ip = '$ip' and to_char(data,'dd/mm/yyyy') = '$data' and code = '$code' and tipo = '$tipo'";
 // $qcd = pg_query($conexao, $sql);
  
 // echo pg_num_rows($qcd);
  
/*  if (pg_num_rows($qcd)==0){
	   $sqlins = "INSERT INTO z_visitas (ip,code,tipo,topic_id,user_id,origem) Values('$ip','$code','$tipo',".$tid.",".$user_id.",'".$origem."')";
	//  $ins = pg_query($conexao, $sqlins);
 }	 */
																		  
// exit($ip ."<br>". $sql. "<br>". $r);
 // $num = pg_num_rows($qcd);
  pg_close($conexao); 
  //echo  $origem;
return  $num;

}


/*-------------- validation functions --------------*/

function valid_email_list ($email)
{
  $list = split('[ ,]+', $email);
  foreach ($list as $item)
    if (!valid_email($item))
      return false;
  return true;
}

function valid_size ($size)
{
  return preg_match('/^\d+([.,]\d+)?\s*([mk]b)?$/i', $size);
}


/*-------------- miscellaneous functions --------------*/

function int_to_size ($value, $show_mb = false)
{
  if ($show_mb && $value > 1024*1024)
    return (int)round($value / (1024*1024)) . ' Mb';
  if ($value > 1024)
    return (int)round($value / 1024) . ' Kb';
  return (int)$value;
}

function size_to_int ($size)
{
  $size = strtr($size, ',MKB', '.mkb'); // normalize
  if (!preg_match('/^(\d+(.\d+)?)\s*([mk]b)?$/', $size, $matches))
    return 0;
  $value = $matches[1];
  switch ($matches[3]) {
  case 'mb':
    $value *= 1024;
  case 'kb':
    $value *= 1024;
    break;
  }
  if ($value > 1<<30)
    return 0; // too big
  return ceil($value);
}

?>
