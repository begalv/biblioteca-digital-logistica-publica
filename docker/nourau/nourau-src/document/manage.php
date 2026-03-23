<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

require_once '../include/start.php';
require_once BASE . 'include/control.php';
require_once BASE . 'include/format.php';
require_once BASE . 'include/page_d.php';


check_administrator_maintainer_rights();


page_begin();


?>
<script type="text/javascript">
  var msg = sessionStorage.getItem("my_report");
  var type = sessionStorage.getItem("type");
   if( msg ){
	   mostraDialogo(msg, type, 4500); 	
       sessionStorage.removeItem("my_report");
	   sessionStorage.removeItem("type");
}
</script>
<?php

//echo "<p class='breadcrumb'><a href='{$cfg_site}'>In&iacute;cio</a><span>&gt;&gt;</span>Gerenciar</p>";
breadcrumb(array($cfg_site=>'Início', ''=>'Curadoria'));
echo html_h('Curadoria');

// show documents waiting for verification
$pending = false;

// show documents waiting for approval - COLABORADOR
if (is_maintainer())
  $q = db_query("SELECT nr_document.title, nr_document.code, nr_document.created, topic.id, topic.name FROM nr_document INNER JOIN (topic INNER JOIN topic_users on topic.id = topic_users.topic_id) on nr_document.topic_id = topic.id WHERE nr_document.status='w' AND topic_users.users_id = {$_SESSION['suid']} ORDER BY nr_document.created ASC");
else
  $q = db_query("SELECT D.title,D.code,D.created,T.id,T.name FROM nr_document D,topic T WHERE D.status='w' AND D.topic_id=T.id  ORDER BY D.created ASC");

echo '<table class="tabela-bases" cellpadding="3" cellspacing="0">';
echo "<tr><th colspan='3'>A aprovar</th></tr>";
if (db_rows($q)) {
 	list_documents($q);
  $pending = true;
}
echo "</table>";
echo "<br>";
if (!$pending)
  echo html_p(html_b("Não há documentos pendentes"));

/*
if (is_maintainer()) {
  $q = db_query("SELECT nr_document.title, nr_document.code, nr_document.created, topic.id, topic.name FROM nr_document INNER JOIN (topic INNER JOIN topic_users on topic.id = topic_users.topic_id) on nr_document.topic_id = topic.id WHERE nr_document.status='v' AND topic_users.users_id = {$_SESSION['suid']} ORDER BY nr_document.created ASC");
  if (db_rows($q)) {
	 	echo '<table id="tabela-bases" cellpadding="3" cellspacing="0">';
  	echo "<tr><th colspan='3'>"._('To verify')."</th></tr>";
    list_documents(_('To verify'), $q);
	echo "</table>";
   echo "<br>";
    $pending = true;
  }
}

if (!$pending)
  echo html_p(html_b(_('There are no pending documents')));

 //show documents waiting for approval
/*if (is_administrator())
  $q = db_query("SELECT D.title,D.code,D.created,T.id,T.name FROM nr_document D,topic T WHERE D.status='w' AND D.topic_id=T.id ORDER BY D.created ASC");
else
  $q = db_query("SELECT D.title,D.code,D.created,T.id,T.name FROM nr_document D,topic T WHERE D.status='w' AND D.topic_id=T.id AND T.maintainer_id='{$_SESSION['suid']}' ORDER BY D.created ASC");
if (db_rows($q)) {
  list_documents(_('To approve'), $q);
  $pending = true;
}*/

echo "</div> <!-- end #mainContent1 -->\n " ;
page_end();


/*-------------- functions --------------*/

function list_documents ($query)
{
  global $cfg_site;
  $i = 0;

  		while ($a = db_fetch_array($query)) {
        	  $class = ($i++ & 1) ? 'odd' : 'even';

			   echo "<tr class=".$class.">";
			   echo "<td class=\"ndoc\">".db_locale_date($a['created'])."</td>";
			   echo "<td class=\"texto\">". html_a($a['title'], "{$cfg_site}document/?code=" . rawurlencode($a['code']))."</td>";
			   echo "<td class=\"texto\">". _('in') . '&nbsp;' . html_a($a['name'], "{$cfg_site}document/list.php?tid={$a['id']}")."</td>";
			   echo"</tr>";
		}



/*  html_table_begin(false, 'right', true);
  html_table_item(html_b($title), 'title');
    html_table_row_begin();
    html_table_row_item(, '', '20%');
    html_table_row_item(html_a(convert_line($a['title'], 65), "{$cfg_site}document/?code=" . rawurlencode($a['code'])), 'left', '50%');
    html_table_row_item(_('in') . '&nbsp;' . html_a(convert_line($a['name'], 25), "{$cfg_site}document/list.php?tid={$a['id']}"), 'left', '30%');
    html_table_row_end();
  }
  html_table_end();
  echo "<p>\n";*/
}

?>
