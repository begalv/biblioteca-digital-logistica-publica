<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

require_once '../include/start.php';
require_once BASE . 'include/page_u.php';
require_once BASE . 'include/format.php';


if (isset($_GET['uid'])){
 	$uid =  $_GET['uid'];
}
else {
	$uid =  $_POST['uid'];
	$old = isset($_POST['old'])?trim($_POST['old']):NULL;
    $new = trim($_POST['new']);
	$new2 = trim ($_POST['new2']);
	$sent = $_POST['sent'];
	
}

// validate input
  if (!valid_int($uid))
	  message("{$cfg_site}user/list.php", "Parâmetro Inválido !", "failure");

// validate access
if ($_SESSION['suid'] != $uid && !is_administrator())
	 message("{$cfg_site}user/list.php", "Acesso Negado !", "failure");
	

// validate input
if (empty($sent)) 
  form();

if (!empty($sent)) {
 if ($sent == "Cancelar") {
	// abort editing
	 redirect("{$cfg_site}user/?uid=$uid");
	}  
}	

if (!is_administrator()) {
	if ($_SESSION['suid'] == $uid && empty($old))
		form(_('Please specify your current password'));
}

if (empty($new) || empty($new2))
  form(_('Please specify a new password and its confirmation'));
if ($new != $new2) {
  unset($new);
  unset($new2);
  form("Senha de confirmação não corresponde com a senha.");
	}


// check old password
if ($_SESSION['suid'] == $uid && rot13($old) != get_user($uid, 'password'))
  form(_('Invalid password'));

// change password
db_command("UPDATE users SET password='" . rot13($new) . "' WHERE id='$uid'");
add_log('c', 'up', ($_SESSION['suid'] == $uid) ? '' : "uid=$uid");

// finishS
 message("{$cfg_site}user/?uid=$uid","A senha atualizada ", "success");


/*-------------- functions --------------*/

function form ($msg = "")
{
  global $cfg_site;
  global $PHP_SELF, $uid, $old, $new, $new2;

  page_begin();

//  echo "<p class='breadcrumb'><a href='{$cfg_site}'>In&iacute;cio</a><span>&gt;&gt;</span><a href='{$cfg_site}user/?uid=".$_SESSION['suid']."'>Perfil</a></p>";


if  (!is_administrator()) {
  echo html_h("Mudar a senha");
}else {
	 $a = get_user($uid);
	  $user = $a['name'] . ' (' . $a['username'] . ')';
	 echo html_h("Mudar a senha do usuário: ".$user );
 } 
  format_warning($msg);

  html_form_begin($_SERVER['PHP_SELF']);
  html_form_hidden('uid', $uid);
  
  	if (!is_administrator()) {
       html_form_password("Senha Atual", "old", 10, $old, 10, true, "Digite a senha atual.");
	}   
 
  html_form_password("Nova senha", "new", 10, $new, 10, true,"Entre com a nova senha.");
  html_form_password("Confirmação da Nova senha", "new2", 10, $new2, 10,  true, "Digite a senha novamante." );
  echo "<div class=\"botao\">";
  html_form_submit('Salvar', 'sent');
  echo "</div>";
  echo "<div class=\"botao\">";
  html_form_submit('Cancelar', 'sent', false);
  echo "</div>";
  html_form_end();
  
  echo "</div> <!--end class recent-sales-boxes-->";
   echo "</div> <!--end class sales-boxes-->";
   echo "</div><!--#mainContent1-->\n";
  page_end("{$cfg_site}user/?uid=$uid");
  exit();
}

?>
