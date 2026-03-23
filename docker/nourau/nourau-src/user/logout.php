<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

require_once '../include/start.php';
require_once BASE . 'include/page.php';
require_once BASE . 'include/page_u.php';
require_once BASE . 'include/util.php';

// ensure that client is logged
if (!is_user())
  error(_('Access denied'));

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$conf = $_POST['conf'];
}

// finish session
if (empty($conf)) {
  		confirm('Você deseja sair do sistema?', $_SERVER['PHP_SELF']);
}   

if ($conf == 'Sim')
  session_destroy();
redirect($cfg_site);




?>
