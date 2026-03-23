<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

/*-------------- includes --------------*/

require_once BASE . 'include/page.php';


/*-------------- database functions --------------*/

function get_user ($uid, $field = ''){
 global $cfg_site;

  if (empty($uid))
     message("{$cfg_site}user/list.php", "Usuário não especificado", "failure");
  if (!empty($field))
    $q = db_query("SELECT $field FROM users WHERE id='$uid'");
  else
    $q = db_query("SELECT * FROM users WHERE id='$uid'");
  if (!db_rows($q))
     message("{$cfg_site}user/list.php", "Usuário não encontrado !", "failure");
  if (!empty($field))
    return db_result($q, 0, $field);
  else
    return db_fetch_array($q);
}


/*-------------- convenience functions --------------*/

function user_pending ()
{
  static $pending = -1;

  if ($pending == -1) {
    $pending = 0;
    if (is_administrator() && db_simple_query("SELECT COUNT(email) FROM user_registration WHERE status='w'"))
      $pending = 1;
  }
  return $pending;
}


/*-------------- validation functions --------------*/

function valid_username ($username)
{
  return preg_match('/^[0-9a-z]([-_.]?[0-9a-z])+$/', $username);
}

?>
