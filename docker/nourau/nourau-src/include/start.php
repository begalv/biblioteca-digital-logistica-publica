<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

/*-------------- includes --------------*/

if (defined('TOPLEVEL'))
  define('BASE', dirname($_SERVER ['SCRIPT_FILENAME']) . '/');
else
  define('BASE', dirname(dirname($_SERVER ['SCRIPT_FILENAME'])) . '/');

require_once BASE . 'config.php';
require_once BASE . 'include/db.php';
require_once BASE . 'include/defs.php';
require_once BASE . 'include/util.php';


/*-------------- startup --------------*/

// halt system
 /*if (defined('HALT')){
	 fatal("Estamos realizando uma manutenção programada no momento. Retornaremos em breve.");
	}*/


// start database connection
db_connect();

session_start();


$cfg_domain = '.unicamp.br';

if (!defined('OFFLINE')) {
  // retrieve session (if available)
  if (!empty($HTTP_COOKIE_VARS[$cfg_session_name])) {
    session_name($cfg_session_name);
    session_start();
  }
  // connection parameters
  ignore_user_abort(true);
  set_time_limit(60);
}


/*-------------- support functions --------------*/

function _M ($msg, $p1 = '', $p2 = '', $p3 = '')
{
  $msg = _($msg);
  if (!empty($p1))
    $msg = str_replace('@1', $p1, $msg);
  if (!empty($p2))
    $msg = str_replace('@2', $p2, $msg);
  if (!empty($p3))
    $msg = str_replace('@3', $p3, $msg);
  return $msg;
}

function fatal ($msg)
{
  echo "$msg\n";
  exit();
}

?>
