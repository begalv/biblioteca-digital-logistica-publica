<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.
//user action: /user/action.php

require_once '../include/start.php';
require_once BASE . 'include/defs_d.php';
require_once BASE . 'include/format.php';
require_once BASE . 'include/page.php';
require_once BASE . 'include/util_d.php';
require_once BASE . 'include/util_t.php';
require_once BASE . 'include/control.php';


 	$uid = isset($_GET['uid'])?$_GET['uid']:'0';
	$op = isset($_GET['op'])?$_GET['op']:'0';
    
	if (isset($_POST['conf']) )
	$conf = $_POST['conf'];	



if (!is_administrator())
  error(_('Access denied'));

if ($op == 'a') { // ---------------- approve registration
  // validate input
  if (empty($email))
    error(_('E-mail not specified'));
  $q = db_query("SELECT code,motive FROM user_registration WHERE email='$email'");
  if (!db_rows($q))
    error(_('Registration not found'));

  // ask confirmation
  if (empty($conf)) {
    $motive = db_result($q, 0, 'motive');
    approve(_M("Do you want to approve the user with e-mail '@1'?", $email), "$PHP_SELF?op=$op&email=$email", $motive);
  }

  if ($conf == _('Yes')) {
    // approve registration
    db_command("UPDATE user_registration SET status='a' WHERE email='$email'");
    add_log('c', 'ua', "email=$email");

    // send e-mail confirmation
    $code = db_result($q, 0, 'code');
    $url = "{$cfg_site}user/edit.php?code=$code&email=$email";
    send_mail($email, _('Registration confirmation'), _("Your registration was approved.\nConfirm your registration by following the link below:") . "\n\n$url\n");

    // finish
    message(_('User approved'), "{$cfg_site}user/list.php");
  }
  else if ($conf == _('No'))
    redirect("{$cfg_site}user/action.php?op=r&email=$email");
  else
    redirect("{$cfg_site}user/list.php");
}
else if ($op == 'd') { // ---------------- remove user
  // validate input
  if (!valid_int($uid))
     message("{$cfg_site}user/list.php", "Parâmetro Inválido !", "failure");
     $a = get_user($uid);

  // ask confirmation
  if (empty($conf)) {
    $user = $a['name'] . ' (' . $a['username'] . ')';
    remove("Deseja remover o usuário $user ?","{$_SERVER['PHP_SELF']}?op=$op&uid=$uid", true);
  }

  if ($conf == 'Sim') {
    // update topics
    topic_finish_user($uid);

    // update documents
    document_finish_user($uid);

    // remove user
    db_command("DELETE FROM users WHERE id='$uid'");
    add_log('c', 'ud', "uid=$uid");

    // remove rights
    db_command("DELETE FROM topic_users WHERE users_id='$uid'");
    add_log('c', 'rd', "uid=$uid");

    if ($notify == 'y') {
      // send e-mail notification
      $msg = _('Your registration was removed by the administrator.') . "\n";
      if (!empty($reason))
        $msg .= _('The reason given was:') . "\n\n$reason\n";
      send_mail($a['email'], _('Registration removed'), $msg);
    }

    // finish
  // message(_('User removed'), "{$cfg_site}user/list.php");
   message("{$cfg_site}user/list.php","Tópico removido.", "success");
  }
  else
    redirect("{$cfg_site}user/list.php");
}
else if ($op == 'r') { // ---------------- reject registration
  // validate input
  if (empty($email))
    error(_('E-mail not specified'));
  if (!db_simple_query("SELECT COUNT(email) FROM user_registration WHERE email='$email'"))
    error(_('Registration not found'));

  // ask confirmation
  if (empty($conf))
    remove(_M("Do you want to reject the user with e-mail '@1'?", $email),
           "$PHP_SELF?op=$op&email=$email", false);

  if ($conf == _('Yes')) {
    // reject registration
    db_command("DELETE FROM user_registration WHERE email='$email'");
    add_log('c', 'ur', "email=$email");

    // send e-mail notification
    $msg = _('Your registration was rejected by the administrator.') . "\n";
    if (!empty($reason))
      $msg .= _('The reason given was:') . "\n\n$reason\n";
    send_mail($email, _('Registration rejected'), $msg);

    // finish
    message(_('User rejected'), "{$cfg_site}user/list.php");
  }
  else
    redirect("{$cfg_site}user/list.php");
}
else
  error(_('Invalid operation'));


/*-------------- functions --------------*/

function approve ($msg, $url, $content, $back = '')
{
  page_begin_aux($msg);

  $yes = _('Yes');
  $no = _('No');
  $cancel = _('Cancel');

echo <<<HTML
<form method="post" action="$url">
<table align="center" border="0" cellpadding="8" cellspacing="0">
<tr><td align="left" colspan="3">
HTML;

  format_block(_('Motive for the registration'), $content);

echo <<<HTML
</td></tr>
<tr><td align="center" colspan="3"><b>$msg</b></td></tr>
<tr><td align="center"><input type="submit" name="conf" value="$yes"></td>
<td align="center"><input type="submit" name="conf" value="$no"></td>
<td align="center"><input type="submit" name="conf" value="$cancel"></td></tr>
</table>
</form>
HTML;

  page_end($back);
  exit();
}

?>
