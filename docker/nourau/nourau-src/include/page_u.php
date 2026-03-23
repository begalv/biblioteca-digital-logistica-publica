<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

/*-------------- includes --------------*/

require_once BASE . 'include/page.php';


/*-------------- page functions --------------*/

function page_begin ($title = '')
{

  page_begin_aux($title);
  page_menu_begin();
  page_head();
  
  echo "<div class=\"home-content\">";
  echo "<div class=\"sales-boxes\">";
  echo "<div class=\"recent-sales box\">";
  
}

?>
