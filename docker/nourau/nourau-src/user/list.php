<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.
// Listar usuários: /user/list.php

require_once '../include/start.php';
require_once BASE . 'include/format.php';
require_once BASE . 'include/page_u.php';
require_once BASE . 'include/util.php';
require_once BASE . 'include/control.php';

$desc = isset($_GET['desc']) ? $_GET['desc'] : 'n';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'b';

check_administrator_rights();


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


	$(document).ready(function() {
		$('#remove a').each(function() {
			var $link = $(this);
			var $dialog = $('<div></div>')
				.load($link.attr('href'))
				.dialog({
					autoOpen: false,
					modal:true,
					title: $link.attr('title'),
					width: 620
					
				});
 
			$link.click(function() {
				$dialog.dialog('open');
 
				return false;
			});
		});
	});
	</script> 

<?php

 //breadcrumb(array($cfg_site=>_('Home'), 'user/?uid='.$_SESSION['suid']=>_('Profile'), ''=>_('User System') ));
breadcrumb(array($cfg_site=>'Início','user/list.php'=>'Usuários Cadastrados'));
 echo html_h('Usuários Cadastrados');

 $url = $_SERVER['PHP_SELF'];

// set sort column
if (empty($sort))
  $sort = 'a';
switch ($sort) {
 case 'a':
   $ord = 'username';
   break;
 case 'b':
   $ord = 'name';
   break;
 case 'c':
   $ord = 'level';
   break;
 case 'd':
    $ord = 'accessed';
   break;
   
 default:
   $ord = 'username';
};
$ord .= ($desc == 'y') ? ' DESC' : ' ASC';

	if (is_administrator()) {
	  echo html_button(format_action("Adicionar Usuário", "{$cfg_site}user/edit.php"));
	}  

  $query= db_query("SELECT id,username,name,accessed,level FROM users WHERE level >= '0' ORDER BY $ord");
  $user = array (1=>'Depositante/Colaborador', 2=>'Curador/Publicador', 3=>'Responsável' ,4=> 'Administrador');
  echo "<div>";
  echo "<table class=\"tabela-bases\">";
  echo "<tr>";
  echo "<th></th>";
  echo "<th></th>";
  echo "<th></th>";
  format_header('Identificação', $url. '?sort=a', $sort == 'a', $desc == 'y');  
  format_header('Nome',          $url . '?sort=b', $sort == 'b', $desc == 'y');
  format_header('Nível de Acesso', $url . '?sort=c', $sort == 'c', $desc == 'y');
  format_header('Último Acesso',  $url . '?sort=d', $sort == 'd', $desc == 'y');
  echo "</tr>";
 
  $i=0;
 while ($a = pg_fetch_array($query)) {
    $uid = $a['id'];
    $username = $a['username'];
    $name = htmlspecialchars(($uid == '1') ? _($a['name']) : $a['name']);
  	$accessed = db_locale_date($a['accessed']);
    $level = $user[$a['level']];

     $class = ($i++ & 1) ? 'odd' : 'even';
     echo "<tr class=".$class.">"; 
	 echo "<td class='icon'><a href='{$cfg_site}user/edit.php?uid=$uid' alt=\"Alterar o Usuário\" title=\"Alterar o Usuário\"><i class='bx bx-edit-alt'></i></a></td>";
	 echo "<td class='icon' id='remove'><a href='{$cfg_site}user/action.php?op=d&uid=$uid' alt=\"Remover Usuário\" title=\"Remover Usuário\"> <i class='bx bx-trash' ></i></a></td>";
	 echo "<td id='change' class='icon'><a href='{$cfg_site}user/change.php?uid=$uid' alt=\"Trocar a senha\" title=\"Trocar a senha\"> <i class='bx bxs-key'></i></a></td>";
	 echo "<td class=\"texto\">".html_a(html_b($username), "{$cfg_site}user/?uid=$uid")."</td>";
     echo "<td class=\"texto\">$name</td>";
  	 echo "<td class=\"texto\">$level</td>";
	 echo "<td class=\"ndoc\">$accessed</td>";
  }

 echo "</table></div>";

page_end();


/*-------------- functions --------------*/


function format_header ($title, $url, $active, $descending)
{
  global $cfg_site;

  if ($active && !$descending)
    $url .= '&desc=y'; 
  echo "<th class=\"titulo\"><b>" . html_a($title, $url) . "</b>";
  if ($active) {
    if ($descending)
      echo " <img src=\"{$cfg_site}images/desc.gif\"></th>";
    else
      echo " <img src=\"{$cfg_site}images/asc.gif\"></th>";
  }
  else
    echo '</th>';
}

?>
