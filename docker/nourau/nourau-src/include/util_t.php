<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

/*-------------- includes --------------*/

require_once BASE . 'include/page.php';


/*-------------- database functions --------------*/

function get_topic ($tid, $field = '')
{
 global $cfg_site;
	
  if (empty($tid))
     message("{$cfg_site}document/list.php", "Tópico não especificado!", "failure"); 
  if (!empty($field))
    $q = db_query("SELECT $field FROM topic WHERE id='$tid'");
  else
    $q = db_query("SELECT * FROM topic WHERE id='$tid'");
  if (!db_rows($q))
	   message("{$cfg_site}document/list.php", "Tópico não encontrado!", "failure"); 
  if (!empty($field))
    return db_result($q, 0, $field);
  else
    return db_fetch_array($q);
}


function check_topic_users ($tid, $uid)
{
 global $cfg_site;
	
  if (empty($tid))
     message("{$cfg_site}document/list.php", "Tópico não especificado!", "failure"); 
     $q = db_query("SELECT * FROM topic_users WHERE topic_id='$tid' and users_id ='$uid'");
  if (!db_rows($q))
	   message("{$cfg_site}document/list.php", "Acesso negado", "failure"); 
    else
    return  TRUE;
}

function check_topic_users_edit ($tid, $uid)
{
 global $cfg_site;
	
  if (empty($tid))
     message("{$cfg_site}document/list.php", "TÃ³pico nÃ£o especificado!", "failure"); 
     $q = db_query("SELECT * FROM topic_users WHERE topic_id='$tid' and users_id ='$uid'");
  if (!db_rows($q))
	 return FALSE;
    else
    return  TRUE;
}


/*-------------- convenience functions --------------*/

function topic_finish_user ($uid)
{
  // move maintained topics to administrator
  db_command("UPDATE topic SET maintainer_id='1' WHERE maintainer_id='$uid'");
}



?>
